@if($paginator->hasPages())
    <nav class="admin-pagination" aria-label="Navigasi halaman">
        @if($paginator->onFirstPage())
            <span aria-disabled="true">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
        @endif
        @foreach($elements as $element)
            @if(is_string($element))<span aria-hidden="true">{{ $element }}</span>@endif
            @if(is_array($element))
                @foreach($element as $page => $url)
                    @if($page === $paginator->currentPage())
                        <span aria-current="page" aria-label="Halaman {{ $page }}">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Selanjutnya</a>
        @else
            <span aria-disabled="true">Selanjutnya</span>
        @endif
    </nav>
@endif
