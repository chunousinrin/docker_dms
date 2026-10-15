@if ($paginator->hasPages())
<nav>
    <ul class="pagination justify-content-center pagination-sm">

        {{-- 前へ --}}
        @if ($paginator->onFirstPage())
        <li class="page-item disabled">
            <span class="page-link">前へ</span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">前へ</a>
        </li>
        @endif

        {{-- ページ番号 --}}
        @foreach ($elements as $element)
        @if (is_string($element))
        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
        @endif

        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="page-item active" aria-current="page">
            <span class="page-link bg-primary text-white border-primary">{{ $page }}</span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- 次へ --}}
        @if ($paginator->hasMorePages())
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">次へ</a>
        </li>
        @else
        <li class="page-item disabled">
            <span class="page-link">次へ</span>
        </li>
        @endif

    </ul>
</nav>
@endif