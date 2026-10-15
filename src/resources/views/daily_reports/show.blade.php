@extends('layouts.adminlte')

@section('title', '業務日報詳細')

@section('content')
<div class="card border-0 shadow-sm rounded-4">

    {{-- ヘッダー --}}
    <div class="card-header bg-white border-bottom px-4 py-3">
        <div class="d-flex justify-content-between align-items-center">

            <div class="d-flex align-items-center gap-3">
                <div class="report-icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1 pl-2">日報詳細</h5>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('daily-reports.index') }}"
                    class="btn btn-sm btn-light shadow-sm px-5" title="一覧へ">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    戻る
                </a>

                <a href="{{ route('daily-reports.edit', $dailyReport) }}"
                    class="ml-2 btn btn-sm btn-primary shadow-sm px-5" title="編集">
                    <i class="fa-solid fa-pen me-1"></i>
                    編集
                </a>
            </div>

        </div>
    </div>


    <div class="card-body p-4">

        {{-- 勤務情報 --}}
        <div class="mb-4">

            <div class="section-title">
                <i class="fa-solid fa-calendar-day"></i>
                <span>勤務情報</span>
            </div>

            <div class="detail-list">

                <div class="detail-row">
                    <div class="detail-label">
                        勤務日
                    </div>

                    <div class="detail-value fw-semibold">
                        {{ $dailyReport->work_date->format('Y年m月d日') }}
                    </div>
                </div>

            </div>

        </div>


        {{-- 午前 --}}
        <div class="mb-4">

            <div class="section-title">
                <i class="fa-regular fa-file-lines"></i>
                <span>午前</span>
            </div>

            <div class="detail-list">

                {{-- 業種 --}}
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fa-solid fa-briefcase"></i>
                        業種
                    </div>

                    <div class="detail-value">
                        {{ $dailyReport->amBusinessType->name ?? '-' }}
                    </div>
                </div>

                {{-- 天気 --}}
                <div class="detail-row">
                    <div class="detail-label">
                        天気
                    </div>

                    <div class="detail-value">
                        @php
                        $weather = $dailyReport->amWeather->name ?? null;

                        $weatherIcon = match ($weather) {
                        '晴' => 'fa-sun',
                        '曇' => 'fa-cloud',
                        '雨' => 'fa-cloud-rain',
                        '雪' => 'fa-snowflake',
                        '雷' => 'fa-bolt',
                        default => 'fa-cloud-sun',
                        };

                        $weatherColor = match ($weather) {
                        '晴' => 'weather-sunny',
                        '曇' => 'weather-cloudy',
                        '雨' => 'weather-rainy',
                        '雪' => 'weather-snowy',
                        '雷' => 'weather-thunderous',
                        default => 'weather-default',
                        };
                        @endphp

                        @if ($weather)
                        <span class="weather-value {{ $weatherColor }}">
                            <i class="fa-solid {{ $weatherIcon }}"></i>
                            {{ $weather }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                {{-- 摘要 --}}
                <div class="detail-row align-items-start">
                    <div class="detail-label">
                        <i class="fa-solid fa-align-left"></i>
                        摘要
                    </div>

                    <div class="detail-value text-pre-line">
                        {!! nl2br(e($dailyReport->am_summary)) !!}
                    </div>
                </div>

            </div>

        </div>


        {{-- 午後 --}}
        <div class="mb-4">

            <div class="section-title">
                <i class="fa-regular fa-file-lines"></i>
                <span>午後</span>
            </div>

            <div class="detail-list">

                {{-- 業種 --}}
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fa-solid fa-briefcase"></i>
                        業種
                    </div>

                    <div class="detail-value">
                        {{ $dailyReport->pmBusinessType->name ?? '-' }}
                    </div>
                </div>

                {{-- 天気 --}}
                <div class="detail-row">
                    <div class="detail-label">
                        天気
                    </div>

                    <div class="detail-value">
                        @php
                        $weather = $dailyReport->pmWeather->name ?? null;

                        $weatherIcon = match ($weather) {
                        '晴' => 'fa-sun',
                        '曇' => 'fa-cloud',
                        '雨' => 'fa-cloud-rain',
                        '雷' => 'fa-bolt',
                        '雪' => 'fa-snowflake',
                        default => 'fa-cloud-sun',
                        };

                        $weatherColor = match ($weather) {
                        '晴' => 'weather-sunny',
                        '曇' => 'weather-cloudy',
                        '雨' => 'weather-rainy',
                        '雷' => 'weather-thunderous',
                        '雪' => 'weather-snowy',
                        default => 'weather-default',
                        };
                        @endphp

                        @if ($weather)
                        <span class="weather-value {{ $weatherColor }}">
                            <i class="fa-solid {{ $weatherIcon }}"></i>
                            {{ $weather }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                {{-- 摘要 --}}
                <div class="detail-row align-items-start">
                    <div class="detail-label">
                        <i class="fa-solid fa-align-left"></i>
                        摘要
                    </div>

                    <div class="detail-value text-pre-line">
                        {!! nl2br(e($dailyReport->pm_summary)) !!}
                    </div>
                </div>

            </div>

        </div>


        {{-- 備考 --}}
        <div>

            <div class="section-title">
                <i class="fa-solid fa-note-sticky text-secondary"></i>
                <span>備考</span>
            </div>

            <div class="remarks">
                {!! nl2br(e($dailyReport->remarks)) !!}
            </div>

        </div>

    </div>
</div>
@endsection

@section('css')
<style>
    .card {
        color: #343a40;
    }

    /* ========================================
       ヘッダー
    ======================================== */

    .report-icon {
        width: 44px;
        height: 44px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #eef4ff;
        color: #0d6efd;

        border-radius: 12px;

        font-size: 18px;
    }


    /* ========================================
       セクションタイトル
    ======================================== */

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;

        padding-bottom: 10px;

        font-size: 15px;
        font-weight: 700;

        border-bottom: 2px solid #f0f1f3;
    }

    .section-title>i {
        width: 20px;
        text-align: center;
    }


    /* ========================================
       詳細リスト
    ======================================== */

    .detail-list {
        border: 1px solid #e9ecef;
        border-top: 0;

        border-radius: 0 0 8px 8px;

        overflow: hidden;
    }

    .detail-row {
        display: flex;
        align-items: center;

        min-height: 56px;

        border-bottom: 1px solid #edf0f2;
    }

    .detail-row:last-child {
        border-bottom: 0;
    }


    /* ========================================
       ラベル
    ======================================== */

    .detail-label {
        flex: 0 0 180px;

        padding: 16px 20px;

        color: #6c757d;
        background: #f8f9fa;

        font-size: 14px;
        font-weight: 600;
    }

    .detail-label i {
        width: 20px;

        margin-right: 8px;

        text-align: center;

        color: #8a939c;
    }


    /* ========================================
       値
    ======================================== */

    .detail-value {
        flex: 1;

        padding: 16px 20px;

        font-size: 14px;
        line-height: 1.8;
    }


    /* ========================================
       天気
    ======================================== */

    .weather-value {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        font-weight: 600;
    }

    .weather-value i {
        font-size: 17px;
    }

    .weather-sunny {
        color: #e9a400;
    }

    .weather-cloudy {
        color: #6c757d;
    }

    .weather-rainy {
        color: #1985c5;
    }

    .weather-thunderous {
        color: #f0ad4e;
    }

    .weather-snowy {
        color: #65a9d6;
    }

    .weather-default {
        color: #6c757d;
    }


    /* ========================================
       備考
    ======================================== */

    .remarks {
        min-height: 100px;

        padding: 18px 20px;

        border: 1px solid #e9ecef;
        border-top: 0;

        border-radius: 0 0 8px 8px;

        background: #fafbfc;

        font-size: 14px;
        line-height: 1.9;
    }


    /* ========================================
       ボタン
    ======================================== */

    .btn {
        font-size: 14px;
        font-weight: 600;
    }


    /* ========================================
       スマートフォン
    ======================================== */

    @media (max-width: 767.98px) {

        .card-header {
            padding: 16px !important;
        }

        .card-body {
            padding: 16px !important;
        }

        .detail-row {
            display: block;
        }

        .detail-label {
            width: 100%;

            padding: 10px 14px;

            border-bottom: 1px solid #edf0f2;
        }

        .detail-value {
            padding: 12px 14px;
        }

        .report-icon {
            width: 40px;
            height: 40px;
        }

        .btn {
            padding: 7px 10px;
        }
    }
</style>
@endsection