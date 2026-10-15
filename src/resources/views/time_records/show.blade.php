@extends('layouts.adminlte')

@section('title', '勤怠打刻')

@section('content_header')
@stop

@section('content')

<div class="row m-0 p-0" style="height: 100vh;">
    <div class="col-4 p-0" style="background-color: lightseagreen;">
        <div class="text-center" style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);background-color:rgba(255, 255, 255, 0.7);padding: 4em;">
            <h2 style="font-size: 4rem;border-bottom:1px solid #000;">{{ \Carbon\Carbon::now()->format('H:i') }}</h2>
            <h3>{{ \Carbon\Carbon::today()->format('m/d D') }}</h3>
        </div>
    </div>
    <div class="col-8 p-0" style="position: relative;">
        <a href="{{ route('time_records.users') }}"
            class="btn"
            style="position:absolute;top:2em;left:2em;" title="職員一覧へ戻る">
            <i class="fa-solid fa-circle-arrow-left fa-2xl" style="color:#17a2b8;"></i>
        </a>

        @php $allowed = $timeRecord->allowedPunchTypes(); @endphp

        <div class="text-center" style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);width: 100%;">
            <div class="w-50 m-auto mb-3" style="font-size: 1.5rem; font-weight: bold;">
                <i class="fa-solid fa-user-pen"></i>&nbsp;{{ $user->name }}
            </div>
            <div class="w-50 m-auto py-2">
                <button type="button"
                    class="btn btn-lg btn-block btn-outline-success punch-btn font-weight-bold"
                    data-type="start_time"
                    {{ in_array('start_time', $allowed) ? '' : 'disabled' }}>
                    勤務開始時間<br>
                    <span class="punch-time">
                        {{ $timeRecord->start_time ? \Carbon\Carbon::parse($timeRecord->start_time)->format('H:i') : '未打刻' }}
                    </span>
                </button>
            </div>
            <div class="w-50 m-auto py-2">
                <button type="button"
                    class="btn btn-lg btn-block btn-outline-warning punch-btn font-weight-bold"
                    data-type="break_start_time"
                    {{ in_array('break_start_time', $allowed) ? '' : 'disabled' }}>
                    時間内退勤<br>
                    <span class="punch-time">
                        {{ $timeRecord->break_start_time ? \Carbon\Carbon::parse($timeRecord->break_start_time)->format('H:i') : '未打刻' }}
                    </span>
                </button>
            </div>
            <div class="w-50 m-auto py-2">
                <button type="button"
                    class="btn btn-lg btn-block btn-outline-info punch-btn font-weight-bold"
                    data-type="break_end_time"
                    {{ in_array('break_end_time', $allowed) ? '' : 'disabled' }}>
                    時間内出勤<br>
                    <span class="punch-time">
                        {{ $timeRecord->break_end_time ? \Carbon\Carbon::parse($timeRecord->break_end_time)->format('H:i') : '未打刻' }}
                    </span>
                </button>
            </div>
            <div class="w-50 m-auto py-2">
                <button type="button"
                    class="btn btn-lg btn-block btn-outline-danger punch-btn font-weight-bold"
                    data-type="end_time"
                    {{ in_array('end_time', $allowed) ? '' : 'disabled' }}>
                    勤務終了時間<br>
                    <span class="punch-time">
                        {{ $timeRecord->end_time ? \Carbon\Carbon::parse($timeRecord->end_time)->format('H:i') : '未打刻' }}
                    </span>
                </button>
            </div>
            <div class="w-50 m-auto py-2">
                <div class="form-group mt-3 bg-white p-2 border rounded-lg">
                    <div class="d-flex justify-content-between align-items-center pb-2">
                        <label for="note" class="m-0">備考</label>
                        <button type="button" class="btn btn-light btn-sm" id="save-note">備考を保存</button>
                    </div>
                    <textarea class="form-control" id="note" rows="3">{{ $timeRecord->note }}</textarea>
                </div>
            </div>

        </div>
    </div>

</div>
@stop

@section('css')
<style>
    /* サイドバーを非表示にし、メインコンテンツの余白をなくす場合 */
    body:not(.layout-fixed) .main-sidebar,
    body:not(.layout-fixed) .main-header {
        Display: none !important;
    }

    body:not(.layout-fixed) .content-wrapper,
    body:not(.layout-fixed) .main-footer {
        Margin-left: 0 !important;
    }

    .content-wrapper,
    .content-wrapper>.content,
    .container-fluid {
        padding: 0 !important;
    }
</style>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const punchUrl = "{{ route('time_records.punch', $user->id) }}";
        const noteUrl = "{{ route('time_records.note', $user->id) }}";
        const csrfToken = "{{ csrf_token() }}";

        // 打刻ボタンの処理
        document.querySelectorAll('.punch-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (this.disabled) return;

                const type = this.dataset.type;
                this.disabled = true;

                try {
                    const response = await fetch(punchUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _token: csrfToken,
                            type: type
                        })
                    });

                    if (!response.ok) {
                        const errData = await response.json().catch(() => ({}));
                        throw errData;
                    }

                    const res = await response.json();

                    if (res.success) {
                        // 打刻時間のテキストを更新
                        const timeSpan = this.querySelector('.punch-time');
                        if (timeSpan) {
                            timeSpan.textContent = res.time;
                        }

                        // 次に押せる項目に応じて全ボタンの活性/非活性を更新
                        document.querySelectorAll('.punch-btn').forEach(b => {
                            const t = b.dataset.type;
                            b.disabled = !res.allowed_types.includes(t);
                        });
                    }
                } catch (error) {
                    const msg = error.message || '打刻に失敗しました。';
                    alert(msg);
                    location.reload(); // 状態がずれている可能性があるので画面を再読込
                }
            });
        });

        // 備考保存ボタンの処理
        const saveNoteBtn = document.getElementById('save-note');
        if (saveNoteBtn) {
            saveNoteBtn.addEventListener('click', async function() {
                const noteValue = document.getElementById('note').value;

                try {
                    const response = await fetch(noteUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _token: csrfToken,
                            note: noteValue
                        })
                    });

                    if (!response.ok) {
                        throw new Error('保存に失敗しました。');
                    }

                    alert('備考を保存しました。');
                } catch (error) {
                    alert(error.message || '保存に失敗しました。');
                }
            });
        }
    });
</script>
@stop