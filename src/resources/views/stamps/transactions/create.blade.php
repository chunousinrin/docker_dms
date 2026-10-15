@extends('layouts.adminlte')

@section('title', '入出庫登録')

@section('content_header')
@stop

@section('content')
@include('stamps.partials.flash')

@php
$categories = \App\Models\StampItem::CATEGORIES;
$presentCats = $items->pluck('category')->unique();
@endphp

<form method="POST" action="{{ route('stamps.transactions.store') }}" id="tx-form">
    @csrf

    <div class="row mt-1">
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="m-0 h6 font-weight-bold">
                            <i class="fas fa-plus mr-1"></i>入出庫登録
                        </h5>
                        <a href="{{ route('master.stamp_items.index') }}" class="btn btn-sm btn-default">
                            <i class="nav-icon fas fa-database mr-1"></i>金券マスタ</a>
                    </div>
                </div>
                <input type="hidden" name="stamp_item_id" id="stamp_item_id" value="{{ old('stamp_item_id') }}">

                <div class="card-body">

                    {{-- 1. 種別 --}}
                    <div class="form-group mb-0">
                        <label hidden>① 種別 <span class="text-danger">*</span></label>
                        @php $typeVal = old('type', 'out'); @endphp
                        <div class="btn-group btn-group-toggle my-1" id="type-buttons">
                            <label class="btn btn-lg btn-outline-success px-5 py-4 {{ $typeVal === 'in' ? 'active' : '' }}">
                                <input type="radio" id="type_in" name="type" value="in" @checked($typeVal==='in' )>
                                <i class="fa-solid fa-cart-arrow-down"></i> 入庫
                            </label>
                            <label class="btn btn-lg btn-outline-success px-5 py-4 {{ $typeVal === 'out' ? 'active' : '' }}">
                                <input type="radio" id="type_out" name="type" value="out" @checked($typeVal==='out' )>
                                <i class="fa-solid fa-arrow-up-from-bracket"></i> 出庫
                            </label>
                        </div>
                        @error('type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <hr class="my-4">

                    {{-- 2. 区分ボタン --}}
                    <div class="form-group mb-0">
                        <label hidden>② 区分を選択 <span class="text-danger">*</span></label>
                        <div id="category-buttons">
                            @foreach ($categories as $key => $label)
                            @if ($presentCats->contains($key))
                            <button type="button" class="btn btn-outline-primary btn-lg mr-2 my-1 cat-btn py-4"
                                data-category="{{ $key }}">
                                {{ $label }}<small class="cat-note ml-1"></small>
                            </button>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- 3. 品目ボタン（区分選択後に表示） --}}
                    <div class="form-group mb-0" id="item-area" style="display:none">
                        <label hidden>③ 品目を選択 <span class="text-danger">*</span></label>
                        <div id="item-buttons">
                            @foreach ($items as $item)
                            <button type="button" class="btn btn-outline-secondary btn-lg mr-2 my-2 py-3 px-4 item-btn"
                                style="display:none"
                                data-id="{{ $item->id }}"
                                data-category="{{ $item->category }}"
                                data-stock="{{ $item->stock }}"
                                data-name="{{ $item->name }}">
                                {{ $item->name }}
                                <br>
                                <span class="badge badge-light ml-1">在庫 {{ number_format($item->stock) }}</span>
                            </button>
                            @endforeach
                        </div>
                        <div id="item-empty" class="text-muted" style="display:none">
                            出庫できる在庫がありません。
                        </div>
                        <hr class="my-4">
                    </div>

                    <div id="item-error" class="text-danger small mb-2">
                        @error('stamp_item_id') {{ $message }} @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="quantity" class="font-weight-bold text-dark mb-1 small">数量 <span class="text-danger">*</span></label>
                            <input type="number" min="1" name="quantity" id="quantity"
                                value="{{ old('quantity') }}"
                                class="form-control @error('quantity') is-invalid @enderror required_field" required>
                            @error('quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="transacted_on" class="font-weight-bold text-dark mb-1 small">日付 <span class="text-danger">*</span></label>
                            <input type="text" name="transacted_on" id="transacted_on"
                                value="{{ old('transacted_on', now()->format('Y-m-d')) }}"
                                max="{{ now()->format('Y-m-d') }}"
                                class="form-control @error('transacted_on') is-invalid @enderror datepicker required_field" required>
                            @error('transacted_on') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="counterparty" class="font-weight-bold text-dark mb-1 small">使用先・用途</label>
                        <input type="text" name="counterparty" id="counterparty" maxlength="255"
                            value="{{ old('counterparty') }}"
                            class="form-control @error('counterparty') is-invalid @enderror"
                            placeholder="例: ○○株式会社 契約書">
                        @error('counterparty') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="memo" class="font-weight-bold text-dark mb-1 small">メモ</label>
                        <input type="text" name="memo" id="memo" maxlength="255"
                            value="{{ old('memo') }}"
                            class="form-control @error('memo') is-invalid @enderror">
                        @error('memo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h5 class="m-0 h6 font-weight-bold">
                        <i class="fas fa-info-circle mr-1"></i>操作手順
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="pl-3 mb-0">
                        <li>入庫／出庫の種別を選択</li>
                        <li>種類を選択</li>
                        <li>金額を選択</li>
                        <li>数量、日付を入力(必須項目)</li>
                        <li>使用先・用途、メモを入力</li>
                        <li>登録ボタンを押す</li>
                    </ol>
                    <span class="small">※レターパックを使用する際は、メモに追跡番号を入力してください。</span>
                </div>
                <div class="card-footer">
                    <div id="selected-summary" class="alert alert-info py-2 my-2" style="display:none"></div>

                    <button type="submit" class="btn btn-sm btn-block btn-primary">
                        <i class="fas fa-save mr-1"></i>登録</button>
                    <a href="{{ route('stamps.transactions.index') }}" class="btn btn-sm btn-block btn-default">
                        <i class="fas fa-arrow-left mr-1"></i>戻る</a>
                </div>
            </div>
        </div>
    </div>
</form>

@stop

@section('js')
<script>
    (function() {
        const form = document.getElementById('tx-form');
        const hidden = document.getElementById('stamp_item_id');
        const qty = document.getElementById('quantity');
        const typeOut = document.getElementById('type_out');
        const catBtns = [...document.querySelectorAll('.cat-btn')];
        const itemBtns = [...document.querySelectorAll('.item-btn')];
        const itemArea = document.getElementById('item-area');
        const itemEmpty = document.getElementById('item-empty');
        const summary = document.getElementById('selected-summary');
        const errBox = document.getElementById('item-error');

        let currentCat = null;

        const isOut = () => typeOut.checked;
        // 出庫のときは在庫がある品目のみ、入庫のときは全品目を対象にする
        const available = b => !isOut() || parseInt(b.dataset.stock, 10) > 0;

        function refresh() {
            // 入庫／出庫ボタンの選択状態を反映
            document.querySelectorAll('#type-buttons label').forEach(l => {
                l.classList.toggle('active', l.querySelector('input').checked);
            });

            // 選択中の品目が出庫不可（在庫0）になったら選択解除
            const sel = itemBtns.find(b => b.dataset.id === hidden.value);
            if (sel && !available(sel)) hidden.value = '';

            // 区分ボタン：対象品目がない区分は無効化
            catBtns.forEach(b => {
                const has = itemBtns.some(i => i.dataset.category === b.dataset.category && available(i));
                b.disabled = !has;
                b.querySelector('.cat-note').textContent = has ? '' : '（在庫なし）';
            });
            const curBtn = catBtns.find(b => b.dataset.category === currentCat);
            if (curBtn && curBtn.disabled) currentCat = null;
            catBtns.forEach(b => b.classList.toggle('active', b.dataset.category === currentCat));

            // 品目ボタン
            itemArea.style.display = currentCat ? '' : 'none';
            let shown = 0;
            itemBtns.forEach(i => {
                const show = i.dataset.category === currentCat && available(i);
                i.style.display = show ? '' : 'none';
                i.classList.toggle('active', i.dataset.id === hidden.value);
                if (show) shown++;
            });
            itemEmpty.style.display = (currentCat && shown === 0) ? '' : 'none';

            // 選択内容の表示と数量上限
            const chosen = itemBtns.find(b => b.dataset.id === hidden.value);
            if (chosen) {
                const stock = parseInt(chosen.dataset.stock, 10);
                summary.style.display = '';
                summary.textContent = '選択中：' + chosen.dataset.name + '（現在庫 ' + stock.toLocaleString() + '）';
                if (isOut()) qty.max = stock;
                else qty.removeAttribute('max');
            } else {
                summary.style.display = 'none';
                qty.removeAttribute('max');
            }
        }

        catBtns.forEach(b => b.addEventListener('click', () => {
            if (currentCat !== b.dataset.category) {
                currentCat = b.dataset.category;
                hidden.value = ''; // 区分を変えたら品目選択はリセット
            }
            errBox.textContent = '';
            refresh();
        }));

        itemBtns.forEach(b => b.addEventListener('click', () => {
            hidden.value = b.dataset.id;
            errBox.textContent = '';
            refresh();
        }));

        document.querySelectorAll('input[name=type]').forEach(r => r.addEventListener('change', refresh));

        form.addEventListener('submit', e => {
            if (!hidden.value) {
                e.preventDefault();
                errBox.textContent = '区分と品目を選択してください。';
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });

        // バリデーションエラーで戻ってきた場合の復元
        const restored = itemBtns.find(b => b.dataset.id === hidden.value);
        if (restored) currentCat = restored.dataset.category;
        refresh();
    })();
</script>
@stop