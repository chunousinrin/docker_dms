<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <style>
        .btn-lg {
            font-size: 1.5rem;
            padding: 1rem;
        }
    </style>
</head>

<body style="background-color:rgba(5, 151, 0, 0.15); padding-top:10%;">
    <div class="container">

        <div class="card text-center shadow">
            <div class="card-body">

                {{-- 作業員名 --}}
                <h3 class="mb-3">{{ $worker->name }}</h3>

                {{-- 班選択 --}}
                <form method="POST" action="{{ url('/v2/attendance/clock-in') }}">
                    @csrf
                    <input type="hidden" name="worker_id" value="{{ $worker->id }}">

                    <div class="form-group mb-4">
                        <select name="group_id" class="form-control text-center">

                            @foreach($groups as $group)
                            <option value="{{ $group->id }}"
                                {{ (old('group_id') ?? ($attendance->group_id ?? $defaultGroupId)) == $group->id ? 'selected' : '' }}>

                                {{ $group->name }}（{{ $group->id }}）

                            </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- 出勤ボタン --}}
                    <button
                        class="btn btn-success btn-lg btn-block my-3"
                        {{ $attendance && $attendance->clock_in ? 'disabled' : '' }}>
                        出勤する<br>
                        {{ $attendance && $attendance->clock_in ? $attendance->clock_in->format('H:i') : '--:--' }}
                    </button>
                </form>

                {{-- 退勤 --}}
                <form method="POST" action="{{ url('/v2/attendance/clock-out') }}">
                    @csrf

                    <input type="hidden" name="worker_id" value="{{ $worker->id }}">
                    <input type="hidden" name="group_id" value="{{ $attendance->group_id ?? $defaultGroupId }}">

                    <button
                        class="btn btn-danger btn-lg btn-block"
                        {{ !$attendance || $attendance->clock_out ? 'disabled' : '' }}>
                        退勤する<br>
                        {{ $attendance && $attendance->clock_out ? $attendance->clock_out->format('H:i') : '--:--' }}
                    </button>
                </form>

                <hr>

                <div class="form-group mb-4">

                    <form method="POST" action="/v2/attendance/comment" onsubmit="return confirmComment()">
                        @csrf

                        <input type="hidden" name="worker_id" value="{{ $worker->id }}">

                        <textarea
                            name="comment"
                            id="commentInput"
                            class="form-control mb-2"
                            rows="2"
                            placeholder="メモ（遅刻理由・応援など）">{{ old('comment') ?? ($attendance->comment ?? '') }}</textarea>

                        <button type="submit" class="btn btn-outline-primary btn-block">
                            保存 / 更新
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script>
        function confirmComment() {
            const comment = document.getElementById('commentInput').value;

            if (!comment.trim()) {
                return confirm("コメントが空ですが保存しますか？");
            }

            return confirm("この内容で保存しますか？\n\n" + comment);
        }
    </script>
</body>

</html>