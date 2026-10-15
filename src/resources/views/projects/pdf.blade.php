<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>事業詳細PDF</title>
    <style>
        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-Italic.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: italic;
        }

        @font-face {
            font-family: 'NotoSansJP';
            src: url('file://{{ storage_path("fonts/NotoSansJP-BoldItalic.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: italic;
        }

        * {
            font-family: 'NotoSansJP';
            margin: 0;
            padding: 0;
            border-collapse: collapse;
        }

        body {
            padding: 15mm;
            font-size: 10pt;
        }

        /* 日本語フォント */
        table {
            width: 100%;
            border-collapse: collapse;
            box-sizing: border-box;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 0.25rem 0;
            line-height: 10pt;
            text-align: center;
            height: 1.5em;
        }

        .col-1 {
            width: 13mm;
        }

        .col-2 {
            width: 26mm;
        }

        .col-3 {
            width: 39mm;
        }

        .col-4 {
            width: 52mm;
        }

        .col-5 {
            width: 65mm;
        }

        .col-6 {
            width: 78mm;
        }

        .col-10 {
            width: 130mm;
        }

        .px-5 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .text-left {
            text-align: left;
            text-align-last: left;
        }
    </style>
</head>

<body>

    @php
    // create プレビュー時は stdClass、登録済みは Eloquent
    $project->destinations = $project->destinations ?? [];
    $project->ratios = $project->ratios ?? [];

    // site_alias の typo を吸収（あなたは直すと言ってたので不要なら削除OK）
    $project->site_alias = $project->site_alias ?? $project->site_ailas ?? '';

    // 日付は to_wareki() に統一
    $ordered_on = to_wareki($project->ordered_on ?? null);
    $logging_notice_date = to_wareki($project->logging_notice_date ?? null);
    $subsidy_grant_date = to_wareki($project->subsidy_grant_date ?? null);
    $started_on = to_wareki($project->started_on ?? null);
    $completed_on = to_wareki($project->completed_on ?? null);
    @endphp
    <table>
        <tr>
            <td class="col-6" colspan="6" rowspan="2">
                <h1 style="margin:0;padding:0;letter-spacing: 0.5rem;font-size:18pt;">{{ $project->order_type ?? '' ?? '' }}作業指示書</h1>
            </td>
            <td class="col-1" style="font-size: 7pt;height:3.5mm;line-height:6pt;padding:0;">組合長</td>
            <td class="col-1" style="font-size: 7pt;height:3.5mm;line-height:6pt;padding:0;">専務</td>
            <td class="col-1" style="font-size: 7pt;height:3.5mm;line-height:6pt;padding:0;">部長</td>
            <td class="col-3" colspan="3" style="font-size: 7pt;height:3.5mm;line-height:6pt;padding:0;">職員</td>
        </tr>
        <tr>
            <td class="col-1" style="height: 13mm;"></td>
            <td class="col-1"></td>
            <td class="col-1"></td>
            <td class="col-3" colspan="3"></td>
        </tr>
        <tr>
            <td class="col-2" colspan="2">指示書番号</td>
            <td class="col-5" colspan="5">{{ $project->order_no ?? '' }}</td>
            <td class="col-2" colspan="2">現場作業責任者</td>
            <td class="col-2 text-left px-5" colspan="2" style="border-right:none;">{{ $project->site_manager ?? '' }}</td>
            <td class="col-1" style="border-left: none;font-size:8pt;">印</td>
        </tr>
        <tr>
            <td class="col-2" colspan="2">指示日</td>
            <td class="col-5" colspan="5">
                {{ to_wareki($project->ordered_on) ?? '' }}
            </td>
            <td class="col-2" colspan="2">担当</td>
            <td class="col-2 text-left px-5" colspan="2" style="border-right:none;">{{ $project->staff_name ?? '' }}</td>
            <td class="col-1" style="border-left: none;font-size:8pt;">印</td>
        </tr>
        <tr>
            <td class="col-2" colspan="2">
                @if($project->order_type=='一般')
                発注者
                @else
                現場通称
                @endif
            </td>
            <td class="col-10 px-5 text-left" colspan="10">{{ $project->site_alias ?? '' }}</td>
        </tr>
        <tr>
            <td class="col-2" colspan="2" rowspan="2">森林所有者</td>
            <td class="col-1" style="border-right:none;">所有者</td>
            <td class="col-10 text-left" colspan="9" style="border-left:none;">：　{{ $project->owner_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="col-1" style="border-right:none;">連絡先</td>
            <td class="col-10 text-left" colspan="9" style="border-left:none;">：　{{ $project->owner_contact ?? '' }}</td>
        </tr>
        <tr>
            <td class="col-2" colspan="2" rowspan="2">作業場所</td>
            <td class="col-1" style="border-right:none;">住所</td>
            <td class="col-10 text-left" colspan="9" style="border-left:none;">：　{{ $project->location_address ?? '' }}</td>
        </tr>
        <tr>
            <td class="col-1" style="border-right:none;">林小班</td>
            <td class="col-10 text-left" colspan="9" style="border-left:none;">：　{{ $project->location_subcompartment ?? '' }}</td>
        </tr>
        @if($project->order_type!=='一般')
        <tr>
            <td class="col-2" colspan="2">保安林の有無</td>
            <td class="col-2" colspan="2">{{ $project->is_protection_forest ?? '' }}</td>
            <td class="col-4" colspan="4">保安林伐採届の提出日</td>
            <td class="col-4" colspan="4">
                {{ to_wareki($project->logging_notice_date) ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="col-2" colspan="2" rowspan="2">過去の施業履歴</td>
            <td class="col-10 px-5 text-left" colspan="10">{{ $project->operation_history ?? '' }}</td>
        </tr>
        <tr>
            <td class="col-2" colspan="2">一次確認者</td>
            <td class="col-3" colspan="3">{{ $project->first_approver ?? '' }}</td>
            <td class="col-2" colspan="2">二次確認者</td>
            <td class="col-3" colspan="3">{{ $project->second_approver ?? '' }}</td>
        </tr>
        @endif
        <tr>
            <td class="col-2" colspan="2" style="height: 13mm;">
                契約名<br>
                @if( $project->order_type=='一般')
                事業名
                @else
                補助事業名
                @endif
            </td>
            <td class="col-10 px-5 text-left" colspan="10">{{ $project->project_name ?? '' }}</td>
        </tr>
        @if($project->order_type!=='一般')
        <tr>
            <td class="col-2" colspan="2">補助金交付年月日</td>
            <td class="col-10 px-5 text-left" colspan="10">
                {{ to_wareki($project->subsidy_grant_date) ?? '' }}
            </td>
        </tr>
        @endif
        <tr>
            <td class="col-2" colspan="2" style="height: 13mm;">作業内容</td>
            <td class="col-10 px-5 text-left" colspan="10">{{ $project->work_content ?? '' }}</td>
        </tr>
        <tr>
            @if($project->order_type=='一般')
            <td class="col-2" colspan="2">作業量</td>
            <td class="col-10 px-5 text-left" colspan="10">
                {{ to_wareki($project->work_volume) ?? '' }}
            </td>
            @else
            <td class="col-2" colspan="2" rowspan="2">作業量</td>
            <td class="col-2" colspan="2">{{ $project->workloaded_title1 ?? '' }}</td>
            <td class="col-3" colspan="3">{{ $project->area_or_volume1 ?? ''}}</td>
            <td class="col-2" colspan="2">{{ $project->workloaded_title2 ?? '' }}</td>
            <td class="col-3" colspan="3">{{ $project->area_or_volume2 ?? ''}}</td>
        </tr>
        <tr>
            <td class="col-2" colspan="2">{{ $project->workloaded_title3 }}</td>
            <td class="col-3" colspan="3">{{ $project->area_or_volume3 ?? ''}}</td>
            <td class="col-2" colspan="2">{{ $project->workloaded_title4 ?? '' }}</td>
            <td class="col-3" colspan="3">{{ $project->area_or_volume4 ?? ''}}</td>
            @endif
        </tr>
        <tr>
            <td class="col-2" colspan="2">工期</td>
            <td class="col-10" colspan="10">
                {{ to_wareki($project->started_on) ?? '' }}

                @if(!empty($project->completed_on))
                　～　
                {{ to_wareki($project->completed_on) ?? '' }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="col-2" colspan="2">使用機械</td>
            <td class="col-10" colspan="10">{{ $project->machines ?? '' }}</td>
        </tr>
        <tr>
            @if($project->order_type=='一般')
            <td class="col-2" colspan="2">作業費</td>
            <td class="col-10" colspan="10">{{ $project->work_cost ?? '' }}</td>
            @else
            <td class="col-2" colspan="2">作業単価</td>
            <td class="col-10" colspan="10">{{ $project->unit_price ?? '' }}</td>
            @endif
        </tr>
        @if($project->order_type==='林産')
        <tr>
            <td class="col-2" colspan="2">出材量予定</td>
            <td class="col-3" colspan="3" style="border-right:none;">{{ $project->expected_yield1 ?? '' }}</td>
            <td class="col-1" style="border-right:none;">㎥</td>
            <td class="col-1" style="border-left:none;">～</td>
            <td class="col-3" colspan="3" style="border-left:none;">{{ $project->expected_yield2 ?? '' }}</td>
            <td class="col-2" colspan="2">㎥</td>
        </tr>
        @php
        // create プレビュー時は null → []
        $ratios = $project->ratios ?? [];

        // 配列/オブジェクト両対応で値を取り出す関数
        function ratio_val($item, $key) {
        return is_array($item) ? ($item[$key] ?? '') : ($item->$key ?? '');
        }

        // 最大3つに揃える（足りない分は空欄）
        $r0 = $ratios[0] ?? null;
        $r1 = $ratios[1] ?? null;
        $r2 = $ratios[2] ?? null;
        @endphp

        <tr>
            <td class="col-2" colspan="2">出材品質</td>
            <td class="col-2" colspan="2" style="border-right: none;"> {{ $r0 ? ratio_val($r0, 'ratio_type') : '' }} </td>
            <td class="col-1 text-left" style="border-left: none;"> {{ $r0 ? ratio_val($r0, 'ratio') : '' }}割 </td>

            <td class="col-2" colspan="2" style="border-right: none;"> {{ $r1 ? ratio_val($r1, 'ratio_type') : '' }} </td>
            <td class="col-1 text-left" style="border-left: none;"> {{ $r1 ? ratio_val($r1, 'ratio') : '' }}割 </td>

            <td class="col-2" colspan="2" style="border-right: none;"> {{ $r2 ? ratio_val($r2, 'ratio_type') : '' }} </td>
            <td class="col-2 text-left" colspan="2" style="border-left: none;"> {{ $r2 ? ratio_val($r2, 'ratio') : '' }}割 </td>
        </tr>
        <tr>
            <td class="col-2" colspan="2">出材先</td>
            <td class="col-10 text-left px-5" colspan="10">
                @php
                $destinations = $project->destinations ?? [];

                $getDest = function ($item, $key) {
                return is_array($item) ? ($item[$key] ?? '') : ($item->$key ?? '');
                };
                @endphp

                @forelse($destinations as $destination)
                <div>
                    <div style="display: inline-block;">
                        {{ $getDest($destination, 'destination_type') }}
                    </div>
                    <div style="display: inline-block;">
                        　：　{{ $getDest($destination, 'destination') }}
                    </div>
                </div>
                @empty
                <div>登録なし</div>
                @endforelse
            </td>
        </tr>
        @endif
        @if($project->order_type=='林産')
        <tr>
            <td class="col-2" colspan="2">平均売上額</td>
            <td class="col-10" colspan="10">{{ Illuminate\Support\Number::format($project->average_sales ?? 0, 0) }}円/㎥</td>
        </tr>
        @elseif($project->order_type=='保育')
        <tr>
            <td class="col-2" colspan="2">作業人数</td>
            <td class="col-10" colspan="10">{{ $project->personnel ?? '' }}</td>
        </tr>
        @endif
        <tr>
            <td class="col-2" colspan="2" style="height: 30mm;">作業留意点</td>
            <td class="col-10 px-5 text-left" colspan="10">{!! nl2br(e($project->notes ?? '')) !!}</td>
        </tr>
        <tr>
            <td class="col-2" colspan="2" style="height: 10mm;">添付書類</td>
            <td class="col-10 px-5 text-left" colspan="10">{{ $project->attachments ?? '' }}</td>
        </tr>
    </table>
</body>

</html>