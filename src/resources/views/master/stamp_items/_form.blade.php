<div class="card-body">
    <div class="form-group">
        <label for="category">区分 <span class="text-danger">*</span></label>
        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
            @foreach (\App\Models\StampItem::CATEGORIES as $key => $label)
            <option value="{{ $key }}" @selected(old('category', $item->category) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="name">品目名 <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" maxlength="255" required
            value="{{ old('name', $item->name) }}"
            class="form-control @error('name') is-invalid @enderror">
        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="face_value">額面（円） <span class="text-danger">*</span></label>
            <input type="number" min="0" name="face_value" id="face_value" required
                value="{{ old('face_value', $item->face_value) }}"
                class="form-control @error('face_value') is-invalid @enderror">
            @error('face_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
            @if ($item->exists && $item->transactions()->exists())
            <small class="text-muted">取引履歴があるため額面は変更できません。</small>
            @endif
        </div>
        <div class="form-group col-md-6">
            <label for="sort_order">表示順</label>
            <input type="number" min="0" name="sort_order" id="sort_order"
                value="{{ old('sort_order', $item->sort_order ?? 0) }}"
                class="form-control @error('sort_order') is-invalid @enderror">
            @error('sort_order') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="custom-control custom-switch w-100">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
            @checked(old('is_active', $item->is_active))>
        <label class="custom-control-label" for="is_active">有効（入出庫・棚卸の対象にする）</label>
    </div>
</div>