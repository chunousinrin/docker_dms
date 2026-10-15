<div class="form-group">
    <label class="font-weight-bold text-dark mb-1 small">文書番号</label>
    <input type="text" class="form-control required_field" value="{{ $ringi->document_no ?? $tempDocumentNo }}" readonly>
    <input type="hidden" name="document_no" value="{{ $ringi->document_no ?? $tempDocumentNo }}">
</div>

<div class="form-group">
    <label class="font-weight-bold text-dark mb-1 small">文書種類 <span class="text-danger">*</span></label>
    <select name="ringi_type_id" class="form-control required_field" required>
        <option value="">選択してください</option>
        @foreach($types as $type)
        <option value="{{ $type->id }}" {{ old('ringi_type_id', $ringi->ringi_type_id ?? '') == $type->id ? 'selected' : '' }}>
            {{ $type->name }}
        </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label class="font-weight-bold text-dark mb-1 small">申請日 <span class="text-danger">*</span></label>
    <input type="text" name="applied_date" class="form-control datepicker required_field" value="{{ old('applied_date', isset($ringi) && $ringi->applied_date ? $ringi->applied_date->format('Y-m-d') : '') }}" required>
</div>

<div class="form-group">
    <label class="font-weight-bold text-dark mb-1 small">表題 <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control required_field" required value="{{ old('title', $ringi->title ?? '') }}">
</div>

<div class="form-group">
    <label class="font-weight-bold text-dark mb-1 small">内容</label>
    <textarea name="content" class="form-control" style="height:30vh">{{ old('content', $ringi->content ?? '') }}</textarea>
</div>