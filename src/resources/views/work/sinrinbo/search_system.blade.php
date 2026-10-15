@extends('layouts.adminlte')

@section('title', '林地台帳検索システム')

@section('content_header')
<div class="d-flex justify-content-end align-items-center pr-2">
    <a href="{{ route('sinrinbo.logout') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-sign-out-alt mr-1"></i> 利用終了
    </a>
</div>
@stop
@section('content')
<div class="container-fluid">
    <div class="card search-card mb-4 mt-2">
        <div class="card-body p-4">
            <form action="{{ route('sinrinbo.search') }}" method="GET" id="search-form">
                @php
                $junList = ['', 'イ', 'ロ', 'ハ', 'ニ', 'ホ', 'ヘ', 'ト', 'チ', 'リ', 'ヌ', 'ル', 'ヲ', 'ワ', 'カ', 'ヨ', 'タ', 'レ', 'ソ', 'ツ', 'ネ', 'ナ', 'ラ', 'ム'];
                @endphp

                <div class="form-row mb-3">
                    <div class="form-group col-md-1">
                        <label class="field-label">年度</label>
                        <input type="number" name="年度" id="year-input" class="form-control form-control-sm text-center font-weight-bold" value="{{ request('年度') }}" required placeholder="西暦で入力">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="field-label">市町村</label>
                        <select name="市町村" id="city-select" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('市町村') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="field-label">大字</label>
                        <select name="大字" id="oaza-select" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            @if(request('大字')) <option value="{{ request('大字') }}" selected>{{ request('大字') }}</option> @endif
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="field-label">字</label>
                        <select name="字" id="aza-select" class="form-control form-control-sm">
                            <option value="">すべて</option>
                            @if(request('字')) <option value="{{ request('字') }}" selected>{{ request('字') }}</option> @endif
                        </select>
                    </div>
                    <div class="form-group col-md-5">
                        <label class="field-label">所有者</label>
                        <input type="text" name="所有者" class="form-control form-control-sm" value="{{ request('所有者') }}" placeholder="所有者">
                    </div>
                </div>

                <div class="range-flex-container">
                    <div class="range-box">
                        <label class="field-label text-primary">● 範囲開始 (林班 / 準林班 / 小班 / 枝番)</label>
                        <div class="form-row no-gutters">
                            <div class="col px-1"><input type="number" name="林班_from" class="form-control form-control-sm" value="{{ request('林班_from') }}" placeholder="林班"></div>
                            <div class="col px-1">
                                <select name="準林班_from" class="form-control form-control-sm">
                                    @foreach($junList as $jun) <option value="{{ $jun }}" {{ request('準林班_from') == $jun ? 'selected' : '' }}>{{ $jun ?: '準林班' }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col px-1"><input type="number" name="小班_from" class="form-control form-control-sm" value="{{ request('小班_from') }}" placeholder="小班"></div>
                            <div class="col px-1"><input type="number" name="枝番_from" class="form-control form-control-sm" value="{{ request('枝番_from') }}" placeholder="枝番"></div>
                        </div>
                    </div>

                    <div class="range-separator">～</div>

                    <div class="range-box">
                        <label class="field-label text-danger">● 範囲終了 (林班 / 準林班 / 小班 / 枝番)</label>
                        <div class="form-row no-gutters">
                            <div class="col px-1"><input type="number" name="林班_to" class="form-control form-control-sm" value="{{ request('林班_to') }}" placeholder="林班"></div>
                            <div class="col px-1">
                                <select name="準林班_to" class="form-control form-control-sm">
                                    @foreach($junList as $jun) <option value="{{ $jun }}" {{ request('準林班_to') == $jun ? 'selected' : '' }}>{{ $jun ?: '準林班' }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col px-1"><input type="number" name="小班_to" class="form-control form-control-sm" value="{{ request('小班_to') }}" placeholder="小班"></div>
                            <div class="col px-1"><input type="number" name="枝番_to" class="form-control form-control-sm" value="{{ request('枝番_to') }}" placeholder="枝番"></div>
                        </div>
                    </div>
                </div>

                <div class="action-bar d-flex justify-content-between align-items-center">
                    <a href="{{ route('sinrinbo.search') }}" class="btn-reset">
                        <i class="fas fa-undo mr-1"></i> リセット
                    </a>
                    <div>
                        <button type="button" id="btn-excel" class="btn btn-sm btn-secondary px-3">
                            <i class="fas fa-file-csv mr-1"></i> CSV出力
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary px-4">
                            <i class="fas fa-search mr-2"></i> 検索
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($results)
    <div class="card search-card mb-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <h5 class="m-0 h6 font-weight-bold text-success"><i class="fas fa-list-ul mr-1"></i> 検索結果: {{ number_format($results->total()) }} 件</h5>
            <small class="text-muted"><i class="fas fa-info-circle"></i> 横にスライドして全項目を確認できます。</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive-custom" style="max-height: 60vh;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>詳細</th>
                            <th>年度</th>
                            <th>計画区</th>
                            <th>県事務所</th>
                            <th>新市町村</th>
                            <th>市町村</th>
                            <th>林班</th>
                            <th>準林班</th>
                            <th>小班</th>
                            <th>枝番</th>
                            <th>大字</th>
                            <th>字</th>
                            <th>地番</th>
                            <th>現に所有者番号</th>
                            <th class="text-num">面積 (㎡)</th>
                            <th>施業方法</th>
                            <th>第1林種</th>
                            <th>第1樹種</th>
                            <th>第1林齢</th>
                            <th>標高</th>
                            <th>傾斜</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $row)
                        <tr>
                            <td>
                                <button type="button"
                                    class="btn btn-xs btn-success btn-detail"
                                    data-item="{{ json_encode($row) }}">
                                    <i class="fas fa-eye"></i> 詳細
                                </button>
                            </td>
                            <td>{{ $row->年度 }}</td>
                            <td>{{ $row->計画区 }}</td>
                            <td>{{ $row->県事務所 }}</td>
                            <td>{{ $row->新市町村 }}</td>
                            <td>{{ $row->市町村 }}</td>
                            <td>{{ $row->林班 }}</td>
                            <td>{{ $row->準林班 }}</td>
                            <td>{{ $row->小班 }}</td>
                            <td>{{ $row->枝番 }}</td>
                            <td>{{ $row->大字 }}</td>
                            <td>{{ $row->字 }}</td>
                            <td>{{ $row->地番 }}</td>
                            <td>{{ $row->現に所有者番号 }}</td>
                            <td class="text-num font-weight-bold">{{ number_format($row->面積, 2) }}</td>
                            <td>{{ $row->施業方法 }}</td>
                            <td>{{ $row->第1林種 }}</td>
                            <td>{{ $row->第1樹種 }}</td>
                            <td>{{ $row->第1林齢 }}</td>
                            <td>{{ $row->標高 }}</td>
                            <td>{{ $row->傾斜 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="20" class="text-center py-5 text-muted">データが見つかりませんでした。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-wrapper d-flex justify-content-center py-3">
            {{ $results->appends(request()->input())->links() }}
        </div>
    </div>
    @endif




    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="detailModalLabel"><i class="fas fa-file-alt mr-2"></i>林地台帳 詳細情報</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div id="detail-content" class="row">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary px-3" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection


@section('css')
<style>
    .form-control-sm {
        border: 1px solid #ced4da;
        border-radius: 3px;
    }


    /* 範囲指定 */
    .range-flex-container {
        display: flex;
        align-items: flex-end;
        width: 100%;
        background: var(--accent-light);
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #eee;
    }

    .range-box {
        flex: 1;
    }

    .range-separator {
        padding: 0 15px 8px 15px;
        color: #aaa;
        font-weight: bold;
    }

    /* アクションバー */
    .action-bar {
        border-top: 1px solid #f0f0f0;
        padding-top: 15px;
        margin-top: 15px;
    }


    .btn-csv {
        background: #546e7a;
        color: #fff;
        border: none;
        min-width: 120px;
        font-weight: bold;
    }

    .btn-csv:hover {
        background: #37474f;
        color: #fff;
    }


    /* テーブル */
    .table-responsive-custom {
        border-radius: 4px;
        overflow-x: auto;
        background: #fff;
    }

    .table-custom {
        width: 100%;
        font-size: 0.85rem;
        border-collapse: separate;
        border-spacing: 0;
    }


    .table-custom tbody tr:nth-of-type(even) {
        background-color: #fcfdfc;
    }

    .table-custom tbody tr:hover {
        background-color: #f0f7f0 !important;
    }

    .text-num {
        text-align: right;
        /*font-family: 'Roboto Mono', monospace;*/
    }

    .pagenation {
        margin-bottom: 0 !important;
    }

    .range-separator {
        padding: 0 15px;
        color: #aaa;
        font-weight: bold;
        /* ここがポイント：入力ボックスの高さ（約31px）の半分くらいに合わせる */
        height: 31px;
        display: flex;
        align-items: center;
        align-self: flex-end;
        /* 下側の入力エリアの高さに合わせる */
    }

    /* Chrome, Safari, Edge, Opera のスピンボタンを消す */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox のスピンボタンを消す */
    input[type=number] {
        -moz-appearance: textfield;
        text-align: center;
    }

    .detail-table th {
        background-color: #f8f9fa;
        color: #495057;
        font-size: 0.85rem;
        vertical-align: middle !important;
        border-right: 1px solid #dee2e6;
    }

    .detail-table td {
        font-size: 0.95rem;
        vertical-align: middle !important;
    }

    #detail-content .card {
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. 定数・ヘルパー関数 ---
        const getEl = (id) => document.getElementById(id);
        const searchForm = getEl('search-form');
        const yearInput = getEl('year-input');
        const citySelect = getEl('city-select');
        const oazaSelect = getEl('oaza-select');
        const azaSelect = getEl('aza-select');

        // --- 2. 年度入力の自動送信制御 ---
        if (yearInput) {
            // Enterキー押下時に二重送信を防ぎつつ確定させる
            yearInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur(); // フォーカスを外してchangeイベントを発火させる
                }
            });

            // 値が確定したらフォームを送信（市町村リスト等を更新するため）
            yearInput.addEventListener('change', function() {
                if (this.value && searchForm) {
                    console.log('年度確定: フォームを送信します');
                    searchForm.submit();
                }
            });
        }

        // --- 3. 非同期オプション更新ロジック ---
        async function updateOptions(type, params) {
            const year = yearInput?.value;
            if (!year) return;

            // 検索パラメータの組み立て
            const query = new URLSearchParams({
                year,
                type,
                ...params
            });

            try {
                const response = await fetch(`{{ route('sinrinbo.options') }}?${query}`);
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();

                // 対象となるセレクトボックスの決定
                let target = (type === '大字') ? oazaSelect : azaSelect;
                if (!target) return;

                // リストの再構築
                target.innerHTML = '<option value="">すべて</option>';
                data.forEach(opt => {
                    const el = document.createElement('option');
                    el.value = opt;
                    el.textContent = opt;
                    // 以前の選択値があれば復元する処理（必要に応じて）
                    target.appendChild(el);
                });
            } catch (e) {
                console.error('オプション取得エラー:', e);
            }
        }

        // --- 4. セレクトボックスの連動イベント ---
        document.addEventListener('change', function(e) {
            // 市町村が変更されたら「大字」を更新
            if (e.target.id === 'city-select') {
                if (oazaSelect) oazaSelect.innerHTML = '<option value="">すべて</option>';
                if (azaSelect) azaSelect.innerHTML = '<option value="">すべて</option>';

                if (e.target.value) {
                    updateOptions('大字', {
                        city: e.target.value
                    });
                }
            }

            // 大字が変更されたら「字」を更新
            if (e.target.id === 'oaza-select') {
                if (azaSelect) azaSelect.innerHTML = '<option value="">すべて</option>';

                if (e.target.value) {
                    updateOptions('字', {
                        city: citySelect?.value,
                        oaza: e.target.value
                    });
                }
            }
        });

        // --- 5. Excel出力ボタンの制御 ---
        const btnExcel = getEl('btn-excel');
        if (btnExcel) {
            btnExcel.addEventListener('click', function(e) {
                e.preventDefault();

                if (!yearInput?.value) {
                    alert('年度を入力してください');
                    yearInput.focus();
                    return;
                }

                // 現在の検索フォームの状態をクエリ文字列に変換
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData).toString();

                // 出力用ルートへリダイレクト
                window.location.href = "{{ route('sinrinbo.export') }}?" + params;
            });
        }
        // --- 詳細モーダルの制御 ---
        const detailModal = $('#detailModal'); // Bootstrap 4のモーダル操作にはjQueryが便利です
        const detailContainer = document.getElementById('detail-content');

        document.addEventListener('click', function(e) {
            // .btn-detail クラスを持つボタンがクリックされたら
            const btn = e.target.closest('.btn-detail');
            if (!btn) return;

            // ボタンに埋め込まれたJSONデータを取得
            const item = JSON.parse(btn.dataset.item);

            // モーダルの中身を生成
            renderDetail(item);

            // モーダルを表示
            detailModal.modal('show');
        });

        function renderDetail(item) {
            const detailContainer = document.getElementById('detail-content');

            // ヘルパー関数：値が空なら「-」を返す
            const v = (val) => (val !== null && val !== undefined && val !== '') ? val : '<span class="text-muted">-</span>';

            let html = `
        <div class="col-md-6">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-map-marker-alt mr-1"></i> 基本・所在情報</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 detail-table">
                        <tr><th width="35%">年度 / 計画区</th><td>${v(item.年度)} / ${v(item.計画区)}</td></tr>
                        <tr><th>県事務所 / 市町村</th><td>${v(item.県事務所)} / ${v(item.市町村)} (新:${v(item.新市町村)})</td></tr>
                        <tr><th>大字 / 字 / 地番</th><td>${v(item.大字)}${v(item.字)}${v(item.地番)}</td></tr>
                        <tr><th>林班 / 準 / 小班 / 枝</th><td>${v(item.林班)} - ${v(item.準林班)} - ${v(item.小班)} - ${v(item.枝番)}</td></tr>
                        <tr><th>合併 / 対象外</th><td>${v(item.合併)} / ${v(item.対象外)}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-user-tie mr-1"></i> 所有者・権利情報</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 detail-table">
                        <tr><th width="35%">現所有者住所</th><td><small>${v(item.現に所有者住所)}</small></td></tr>
                        <tr><th>現所有者 (番号/共有)</th><td>No.${v(item.現に所有者番号)} / ${v(item.現に所有者共有有無)}</td></tr>
                        <tr><th>林地所有者 (番号/共有)</th><td>No.${v(item.林地所有者番号)} / ${v(item.林地所有者共有有無)}</td></tr>
                        <tr><th>登記住所 / 更新日</th><td><small>${v(item.登記所有者住所)}</small> <br> <span class="badge badge-light">${v(item.林地更新年月日)}</span></td></tr>
                        <tr><th>所有形態 / 規模</th><td>林地:${v(item.林地所有形態)} / 立木:${v(item.立木所有形態)} / ${v(item.所有規模)}</td></tr>
                        <tr><th>面積 / 分収林 / 在不在</th><td>${v(item.面積)} ha / ${v(item.分収林)} / ${v(item.in_out)}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-tree mr-1"></i> 森林立木情報</h3></div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered table-sm mb-0 text-center">
                        <thead class="bg-light">
                            <tr>
                                <th>層区分</th><th>林種</th><th>針広</th><th>樹種</th><th>歩合</th><th>林齢</th><th>齢級</th><th>面積</th><th>蓄積</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>第1層:${v(item.第1層区分)}</td><td>${v(item.第1林種)}</td><td>${v(item.第1針広別)}</td><td><strong>${v(item.第1樹種)}</strong></td><td>${v(item.第1歩合)}</td><td>${v(item.第1林齢)}</td><td>${v(item.第1齢級)}</td><td>${v(item.第1面積)}</td><td>${v(item.第1蓄積)}</td>
                            </tr>
                            <tr>
                                <td>第2層:${v(item.第2層区分)}</td><td>${v(item.第2林種)}</td><td>${v(item.第2針広別)}</td><td>${v(item.第2樹種)}</td><td>${v(item.第2歩合)}</td><td>${v(item.第2林齢)}</td><td>${v(item.第2齢級)}</td><td>${v(item.第2面積)}</td><td>${v(item.第2蓄積)}</td>
                            </tr>
                            <tr>
                                <td>第3層</td><td>${v(item.第3林種)}</td><td>${v(item.第3針広別)}</td><td>${v(item.第3樹種)}</td><td>${v(item.第3歩合)}</td><td>${v(item.第3林齢)}</td><td>${v(item.第3齢級)}</td><td>${v(item.第3面積)}</td><td>${v(item.第3蓄積)}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="p-2 bg-white small text-muted">
                        材積表: ${v(item.第1材積表)} | 材積計算樹種: ${v(item.第1材積計算樹種)} | HA材積: ${v(item.第1ＨＡ材積)}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card card-outline card-danger shadow-sm">
                <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-gavel mr-1"></i> 制限事項・法指定</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 detail-table">
                        <tr><th width="30%">制普別</th><td>${v(item.制普別)}</td></tr>
                        <tr><th>保安林</th><td>(1)${v(item.保安林1)} (2)${v(item.保安林2)} (3)${v(item.保安林3)}</td></tr>
                        <tr><th>各種法/条例</th><td>砂防:${v(item.砂防法)} / 自然公園:${v(item.自然公園法)} / 急傾斜:${v(item.急傾斜地法)}</td></tr>
                        <tr><th>岐阜県条例</th><td>保全:${v(item.岐阜県自然環境保全条例)} / 公園:${v(item.岐阜県立自然公園条例)}</td></tr>
                        <tr><th>その他</th><td>鳥獣害防止:${v(item.鳥獣害防止森林区域)}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header"><h3 class="card-title font-weight-bold"><i class="fas fa-mountain mr-1"></i> 施業・立地条件</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 detail-table">
                        <tr><th width="40%">施業 / 伐採方法</th><td>${v(item.施業方法)} / ${v(item.伐採方法)}</td></tr>
                        <tr><th>標準伐期齢</th><td>${v(item.標準伐期齢)} 年</td></tr>
                        <tr><th>林道距離 / 標高</th><td>${v(item.林道距離現在)} m / ${v(item.標高)} m</td></tr>
                        <tr><th>傾斜</th><td>${v(item.傾斜)} 度</td></tr>
                        <tr><th class="align-middle">施業履歴</th><td><div style="max-height:60px; overflow-y:auto; font-size:0.85rem;">${v(item.施業履歴)}</div></td></tr>
                    </table>
                </div>
            </div>
        </div>
    `;
            detailContainer.innerHTML = html;
        }
    });
</script>
@endsection