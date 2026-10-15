@extends('layouts.adminlte')

@section('title', 'ダッシュボード')

@push('js')
@vite([
'resources/js/components/fullcalendar.js',
])
<script src="https://cf444722.cloudfree.jp/js/attendance-widget.js"></script>
@endpush

@section('content_header')
@stop

@section('content')

<div class="row mt-1">
    <div class="col-12 col-md-9">
        <div class="row">
            <div class="col-3">
                <a href="{{ route('invoices.create') }}" class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="small-box m-0 text-center text-white font-weight-bold" style="background-color:#70BD29;">
                            <h4 class="inner py-4">請求書 </h4>
                            <div class="icon">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div class="small-box-footer  small text-white">invoices</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('estimates.create') }}" class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="small-box m-0 text-center text-white font-weight-bold" style="background-color:#70BD29;">
                            <h4 class="inner py-4">見積書 </h4>
                            <div class="icon">
                                <i class="fa-regular fa-file-lines"></i>
                            </div>
                            <div class="small-box-footer  small text-white">estimates</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('requests.create') }}" class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="small-box m-0 text-center text-white font-weight-bold" style="background-color:#70BD29;">
                            <h4 class="inner py-4">稟議書 </h4>
                            <div class="icon">
                                <i class="fa-solid fa-file-circle-check"></i>
                            </div>
                            <div class="small-box-footer  small text-white">requests</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('projects.create') }}" class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="small-box m-0 text-center text-white font-weight-bold" style="background-color:#70BD29;">
                            <h4 class="inner py-4">指示書 </h4>
                            <div class="icon">
                                <i class="fa-solid fa-person-chalkboard"></i>
                            </div>
                            <div class="small-box-footer  small text-white">projects</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-3">
                <a href="{{ route('daily-reports.create') }}" class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="small-box m-0 text-center text-white font-weight-bold" style="background-color:#70BD29;">
                            <h4 class="inner py-4">業務日報 </h4>
                            <div class="icon">
                                <i class="fa-solid fa-book"></i>
                            </div>
                            <div class="small-box-footer  small text-white">daily reports</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('purchase-orders.create') }}" class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="small-box m-0 text-center text-white font-weight-bold" style="background-color:#70BD29;">
                            <h4 class="inner py-4">発注書 </h4>
                            <div class="icon">
                                <i class="fa-solid fa-file-shield"></i>
                            </div>
                            <div class="small-box-footer  small text-white">purchase orders</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('stamps.transactions.create') }}" class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="small-box m-0 text-center text-white font-weight-bold" style="background-color:#70BD29;">
                            <h4 class="inner py-4">金券出納 </h4>
                            <div class="icon">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="small-box-footer  small text-white">stamps transactions</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="card col-12">
                <div class="card-body">
                    @can('manage-users')
                    <p>管理者だけに見えるテスト表示</p>
                    @endcan

                    @can('view-reports')
                    <p>一般ユーザー用</p>
                    @endcan

                    <p>ここに管理画面のメインコンテンツを配置します。</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card p-0 col-12" style="background-color: lightseagreen;color:white;font-weight:extra-bold;">
            <div class="card-body text-center small-box mb-0">
                <div class="inner">
                    <div class="card-text" style="font-size: 1.5rem;line-height:1.5rem;">{{ now()->format('F')}}</div>
                    <div class="card-text" style="font-size: 7rem;line-height:7rem;">{{ now()->format('d')}}</div>
                    <div class="card-text" style="font-size: 1.5rem;line-height:1.5rem">{{ now()->format('l') }}</div>
                </div>
                <div class="icon">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>
        </div>

        @if($approveCount > 0)
        <div class="card col-12 p-0 border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="small-box bg-warning m-0 text-center">
                    <div class="inner py-4">
                        <div class="d-flex justify-content-center align-items-baseline flex-wrap">
                            <span class="font-weight-bold mr-2" style="font-size: 1.2rem;">承認待ち</span>
                            <span class="font-weight-bold px-1" style="font-size: 3.5rem; line-height: 1;">{{ $approveCount }}</span>
                            <span class="text-muted small ml-1" style="font-size: 1.2rem;">件</span>
                        </div>
                    </div>

                    <div class="icon">
                        <i class="fas fa-stamp"></i>
                    </div>

                    <a href="{{ route('requests.index', ['mode' => 'approve']) }}" class="small-box-footer">
                        詳細を見る <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div id="attendance-widget"></div>

        <div class="card col-12">
            <div class="card-body calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection