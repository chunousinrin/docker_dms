{{-- 費用 --}}
<div class="tab-pane fade" id="costs" role="tabpanel">
    <table class="table-custom">
        <thead>
            <tr>
                <th>費用区分</th>
                <th>金額</th>
                <th class="text-center">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->costs as $cost)
            <tr>
                <td>{{ $cost->category }}</td>
                <td class="text-right">{{ number_format($cost->amount) }} 円</td>
                <td class="text-center" style="width: 0;white-space:nowrap;">
                    <!-- 編集ボタン -->
                    <button class="btn btn-sm btn-light shadow-sm" data-toggle="modal"
                        data-target="#costEditModal{{ $cost->id }}"><i class="fas fa-edit"></i></button>
                    <!-- 削除ボタン -->
                    <form action="{{ route('project-costs.destroy', $cost->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light shadow-sm"
                            onclick="return confirm('削除してよろしいですか？')"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>

            <!-- 費用編集モーダル -->
            <div class="modal fade" id="costEditModal{{ $cost->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('project-costs.update', $cost->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">費用編集</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <div class="form-group">
                                    <label>費用区分</label>
                                    <input type="text" name="category" value="{{ old('category', $cost->category) }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>金額</label>
                                    <input type="number" name="amount" value="{{ old('amount', $cost->amount) }}" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                                <button type="submit" class="btn btn-primary">更新</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>合計</td>
                <td class="text-right">{{ number_format($project->costs->sum('amount')) }} 円</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- 費用追加モーダル起動ボタン -->
    <button class="btn btn-primary btn-sm mt-2" data-toggle="modal" data-target="#costCreateModal">
        <i class="fas fa-plus"></i> 費用追加
    </button>
</div>

<!-- 費用追加モーダル -->
<div class="modal fade" id="costCreateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('project-costs.store') }}" method="POST">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">費用追加</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>費用区分</label>
                        <input type="text" name="category"
                            class="form-control" list="category-options"
                            value="{{ old('category') }}">
                        <datalist id="category-options">
                            <option value="資材費">
                            <option value="付帯費用">
                            <option value="下請費用">
                            <option value="指示金額">
                            <option value="その他">
                        </datalist>
                        @error('category') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>金額</label>
                        <input type="number" name="amount" class="form-control" min="0" value="{{ old('amount') }}">
                        @error('amount') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-4" data-dismiss="modal">
                        <i class="fa fa-arrow-left"></i> 戻る
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary px-4">
                        <i class="fa fa-check"></i> 保存
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>