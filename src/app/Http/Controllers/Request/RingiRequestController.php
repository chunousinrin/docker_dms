<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Models\Request\ApprovalHistory;
use App\Models\Request\ApprovalStep;
use App\Models\Request\Attachment;
use App\Models\Request\RingiRequest;
use App\Models\Request\RingiType;
use App\Models\Master\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RingiRequestController extends Controller
{
    /**
     * 一覧
     */
    public function index(Request $request)
    {
        $query = RingiRequest::with([
            'user',
            'steps.approver',
            'ringiType',
        ]);

        // 【スッキリ化】modeに応じた条件分岐はここだけにまとめる
        switch ($request->mode) {
            case 'mine':
                $query->where('user_id', auth()->id());
                break;

            case 'approve':
                $query->whereHas('steps', function ($q) {
                    $q->where('approver_id', auth()->id())
                        ->where('status', 'pending');
                });
                break;

            case 'all':
            default:
                // modeが未指定（null）の場合も、デフォルトでallの動きにする
                $query->where('status', '!=', RingiRequest::STATUS_DRAFT);
                break;
        }

        // ステータス絞込
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // キーワード検索
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('document_no', 'like', "%{$keyword}%")
                    ->orWhere('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('ringiType', function ($typeQuery) use ($keyword) {
                        $typeQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // 現在のmodeをBladeに渡す（リクエストされたmodeがなければ'all'にする）
        $mode = $request->input('mode', 'all');

        $approveCount = RingiRequest::whereHas('steps', function ($q) {
            $q->where('approver_id', auth()->id())
                ->whereColumn(
                    'approval_steps.step_order',
                    'ringi_requests.current_step'
                )
                ->where('status', 'pending');
        })
            ->where('status', RingiRequest::STATUS_PENDING)
            ->count();

        $requests = $query
            ->withExists([
                'steps as can_approve' => function ($q) {
                    $q->where('approver_id', auth()->id())
                        ->whereColumn(
                            'approval_steps.step_order',
                            'ringi_requests.current_step'
                        )
                        ->where('status', 'pending');
                }
            ])
            ->latest()
            ->paginate(20)
            ->withQueryString(); // これが書いてあるので、ページネーションのリンクにも mode=all が自動で引き継がれます！

        return view('requests.index', compact('requests', 'mode', 'approveCount'));
    }

    /**
     * 詳細
     */
    public function show($id)
    {
        $ringi = RingiRequest::with([
            'user',
            'steps.approver',
            'histories.approver',
            'currentAttachments',
            'ringiType',
        ])->findOrFail($id);

        if (
            $ringi->status === RingiRequest::STATUS_DRAFT
            && $ringi->user_id !== auth()->id()
        ) {
            abort(403);
        }

        $canApprove = $ringi->steps()
            ->where('approver_id', auth()->id())
            ->where('step_order', $ringi->current_step)
            ->where('status', 'pending')
            ->exists();

        return view('requests.show', compact('ringi', 'canApprove'));
    }

    /**
     * 作成フォーム
     */
    public function create()
    {
        $types = RingiType::all();
        $user = User::findOrFail(auth()->id());
        $tempDocumentNo = RingiRequest::generateDocumentNo();

        return view('requests.create', compact('types', 'tempDocumentNo', 'user'));
    }

    /**
     * 保存
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'document_no' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'ringi_type_id' => 'required|exists:ringi_types,id',
        ]);

        $isSubmit = $request->input('action') === 'submit';

        DB::transaction(function () use ($request, $isSubmit, $user) {
            $ringi = RingiRequest::create([
                'document_no'  => $request->document_no,
                'title'        => $request->title,
                'ringi_type_id' => $request->ringi_type_id,
                'content'      => $request->content,
                'user_id'      => $user->id,
                'status'       => $isSubmit
                    ? RingiRequest::STATUS_PENDING
                    : RingiRequest::STATUS_DRAFT,
                'current_step' => $isSubmit ? 1 : null,
                'applied_date' => $request->filled('applied_date')
                    ? $request->applied_date
                    : null,
            ]);

            if ($isSubmit) {
                $manager = $user->manager;

                /*
                |--------------------------------------------------------------------------
                | 最終承認者は即承認
                |--------------------------------------------------------------------------
                */
                if (!$manager) {

                    $ringi->update([
                        'status'       => RingiRequest::STATUS_APPROVED,
                        'current_step' => null,
                    ]);

                    return;
                }

                $approver = $manager;
                $stepOrder = 1;
                $checked = [];

                while ($approver) {

                    if (in_array($approver->id, $checked)) {
                        throw ValidationException::withMessages([
                            'manager' => '承認経路設定に循環参照があります。',
                        ]);
                    }

                    $checked[] = $approver->id;

                    $ringi->steps()->create([
                        'approver_id' => $approver->id,
                        'step_order'  => $stepOrder++,
                        'status'      => 'pending',
                    ]);

                    $approver = $approver->manager;
                }

                $ringi->update([
                    'current_step' => 1,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 添付ファイル
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('files')) {
                $version = 1;

                foreach ($request->file('files') as $file) {
                    if (!$file) {
                        continue;
                    }

                    $path = $file->store('attachments');

                    Attachment::create([
                        'request_id'  => $ringi->id,
                        'file_path'   => $path,
                        'file_name'   => $file->getClientOriginalName(),
                        'version'     => $version++,
                        'is_current'  => true,
                        'uploaded_by' => $user->id,
                    ]);
                }

                $ringi->update([
                    'last_file_updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('requests.index', ['mode' => 'mine'])
            ->with(
                'success',
                $isSubmit ? '申請しました' : '下書き保存しました'
            );
    }

    /**
     * 編集フォーム
     */
    public function edit($id)
    {
        $ringi = RingiRequest::where('user_id', auth()->id())
            ->with([
                'attachments',
                'ringiType',
            ])
            ->findOrFail($id);

        if (!$ringi->isEditableBy(auth()->id())) {
            abort(403);
        }

        $types = RingiType::all();

        return view('requests.edit', compact('ringi', 'types'));
    }

    /**
     * 更新
     */
    public function update(Request $request, $id)
    {

        $user = auth()->user();
        $fileChanged = false;

        $ringi = RingiRequest::where('user_id', $user->id)->findOrFail($id);

        if (!$ringi->isEditableBy($user->id)) {
            abort(403);
        }

        $request->validate([
            'title' => 'required',
            'ringi_type_id' => 'required|exists:ringi_types,id',
        ]);

        $isSubmit = $request->input('action') === 'submit';

        $wasReturned = $ringi->status === RingiRequest::STATUS_RETURNED;

        DB::transaction(function () use ($request, $ringi, $isSubmit, $user, &$fileChanged, $wasReturned) {
            $ringi->update([
                'title'         => $request->title,
                'ringi_type_id' => $request->ringi_type_id,
                'content'       => $request->content,
                'status'        => $isSubmit ? RingiRequest::STATUS_PENDING : RingiRequest::STATUS_DRAFT,
                'applied_date'  => $request->filled('applied_date') ? $request->applied_date : null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 差戻し再申請
            |--------------------------------------------------------------------------
            */
            if ($isSubmit && $wasReturned) {

                $ringi->steps()->delete();
                $ringi->histories()->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | 承認経路生成
            |--------------------------------------------------------------------------
            */

            if ($isSubmit && $ringi->steps()->count() === 0) {
                $manager = $user->manager;

                /*
                |--------------------------------------------------------------------------
                | 最終承認者は即承認
                |--------------------------------------------------------------------------
                */
                if (!$manager) {

                    $ringi->update([
                        'status'       => RingiRequest::STATUS_APPROVED,
                        'current_step' => null,
                    ]);

                    return;
                }

                $approver = $manager;
                $stepOrder = 1;
                $checked = [];

                while ($approver) {

                    if (in_array($approver->id, $checked)) {
                        throw ValidationException::withMessages([
                            'manager' => '承認経路設定に循環参照があります。',
                        ]);
                    }

                    $checked[] = $approver->id;

                    $ringi->steps()->create([
                        'approver_id' => $approver->id,
                        'step_order'  => $stepOrder++,
                        'status'      => 'pending',
                    ]);

                    $approver = $approver->manager;
                }

                $ringi->update([
                    'current_step' => 1,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 添付削除
            |--------------------------------------------------------------------------
            */
            if ($request->filled('delete_files')) {
                Attachment::where('request_id', $ringi->id)
                    ->whereIn('id', $request->delete_files)
                    ->update([
                        'is_current' => false,
                    ]);

                $fileChanged = true;
            }

            /*
            |--------------------------------------------------------------------------
            | 添付追加
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('files')) {
                $version = Attachment::where('request_id', $ringi->id)->max('version') ?? 0;

                foreach ($request->file('files') as $file) {
                    if (!$file) {
                        continue;
                    }

                    $path = $file->store('attachments');

                    Attachment::create([
                        'request_id'  => $ringi->id,
                        'file_path'   => $path,
                        'file_name'   => $file->getClientOriginalName(),
                        'version'     => ++$version,
                        'is_current'  => true,
                        'uploaded_by' => $user->id,
                    ]);

                    $fileChanged = true;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 添付更新日時
            |--------------------------------------------------------------------------
            */
            if ($fileChanged) {
                $ringi->update([
                    'last_file_updated_at' => now(),
                ]);
            }
        });
        return redirect()
            ->route('requests.index', ['mode' => 'mine'])
            ->with(
                'success',
                $isSubmit ? '申請しました' : '下書きを更新しました'
            );
    }

    /**
     * PDFダウンロード
     */
    public function pdf($id)
    {
        $ringi = RingiRequest::with([
            'user',
            'currentAttachments',
            'steps.approver',
        ])->findOrFail($id);

        if (
            $ringi->status === RingiRequest::STATUS_DRAFT
            && $ringi->user_id !== auth()->id()
        ) {
            abort(403);
        }

        // 背景画像パス
        $bg = public_path('img/underline.png');
        //$bg = storage_path('app/public/img/keisen.png');

        $ringi->attachmentNames = $ringi->currentAttachments
            ->pluck('file_name')
            ->toArray();

        $pdf = Pdf::loadView('requests.pdf', [
            'ringi' => $ringi,
            'bg' => $bg,
        ]);

        return $pdf->stream('ringi_' . $ringi->document_no . '.pdf');
    }
    /**
     * PDFプレビュー
     */
    public function pdfPreview(Request $request)
    {

        $ringi = (object) [
            'document_no' => $request->document_no,
            'title' => $request->title,
            'content' => $request->content,
            'applied_date' => $request->applied_date,
        ];

        $attachmentNames = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file) {
                    $attachmentNames[] = $file->getClientOriginalName();
                }
            }
        }
        // userを追加
        $ringi->user = auth()->user();
        $ringi->ringiType = RingiType::find($request->ringi_type_id);

        $bg = public_path('img/underline.png');
        //$bg = storage_path('app/public/img/keisen.png');
        $ringi->attachmentNames = $attachmentNames;

        $pdf = Pdf::loadView('requests.pdf', [
            'ringi' => $ringi,
            'bg' => $bg,
        ]);

        return $pdf->stream('preview.pdf');
    }



    /**
     * 削除
     */
    public function destroy($id)
    {
        $ringi = RingiRequest::with('attachments')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // 下書きのみ削除可
        if ($ringi->status !== RingiRequest::STATUS_DRAFT) {
            abort(403, '下書き以外は削除できません');
        }

        DB::transaction(function () use ($ringi) {
            // 添付削除
            foreach ($ringi->attachments as $file) {
                Storage::delete($file->file_path);
            }
            $ringi->attachments()->delete();

            // 承認履歴削除
            $ringi->histories()->delete();

            // 承認ステップ削除
            $ringi->steps()->delete();

            // 本体削除
            $ringi->delete();
        });

        return redirect()->route('requests.index', ['mode' => 'mine'])
            ->with('success', '下書きを削除しました');
    }

    /**
     * 承認
     */
    public function approve($id)
    {
        $ringi = RingiRequest::with('steps')->findOrFail($id);
        $userId = auth()->id();

        $step = $ringi->steps()
            ->where('approver_id', $userId)
            ->where('step_order', $ringi->current_step)
            ->where('status', 'pending')
            ->first();

        if (!$step) {
            abort(403);
        }

        DB::transaction(function () use ($ringi, $step, $userId) {
            $step->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            ApprovalHistory::create([
                'request_id' => $ringi->id,
                'step_id' => $step->id,
                'action' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            $currentStep = $step->step_order + 1;

            $nextStep = $ringi->steps()
                ->where('step_order', $currentStep)
                ->first();

            if ($nextStep) {
                $ringi->update([
                    'current_step' => $currentStep,
                ]);
            } else {
                $ringi->update([
                    'status' => RingiRequest::STATUS_APPROVED,
                    'current_step' => null,
                ]);
            }
        });

        return back()->with('success', '承認しました');
    }

    /**
     * 差戻し
     */
    public function returnRequest(Request $request, $id)
    {
        $ringi = RingiRequest::with('steps')->findOrFail($id);
        $userId = auth()->id();

        $step = $ringi->steps()
            ->where('approver_id', $userId)
            ->where('step_order', $ringi->current_step)
            ->where('status', 'pending')
            ->first();

        if (!$step) {
            abort(403);
        }

        DB::transaction(function () use ($ringi, $step, $userId, $request) {

            $step->update([
                'status' => 'returned',
                'approved_at' => now(),
            ]);

            ApprovalHistory::create([
                'request_id'  => $ringi->id,
                'step_id'     => $step->id,
                'action'      => 'returned',
                'approved_by' => $userId,
                'comment'     => $request->comment,
                'approved_at' => now(),
            ]);

            $ringi->update([
                'status'       => RingiRequest::STATUS_RETURNED,
                'current_step' => null,
            ]);
        });

        return back()->with('success', '差戻しました');
    }
    /**
     * 取り下げ
     */
    public function withdraw($id)
    {
        $ringi = RingiRequest::findOrFail($id);

        if ($ringi->user_id !== auth()->id()) {
            abort(403);
        }

        if ($ringi->status !== RingiRequest::STATUS_PENDING) {
            abort(403);
        }

        ApprovalHistory::create([
            'request_id'  => $ringi->id,
            'action'      => 'withdrawn',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $ringi->update([
            'status' => RingiRequest::STATUS_WITHDRAWN,
            'current_step' => null,
        ]);

        return back()->with('success', '申請を取り下げました');
    }
}
