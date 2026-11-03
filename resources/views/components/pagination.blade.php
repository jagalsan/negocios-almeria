@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Paginación">
        <p class="pagination-summary muted">Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados</p>
        <ul class="pagination-pages">
            @if ($paginator->onFirstPage())
                <li><span class="pagination-link is-disabled" aria-disabled="true" aria-label="Página anterior">&lsaquo;</span></li>
            @else
                <li><a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior">&lsaquo;</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination-link is-disabled" aria-disabled="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="pagination-link is-current" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a class="pagination-link" href="{{ $url }}" aria-label="Ir a la página {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Página siguiente">&rsaquo;</a></li>
            @else
                <li><span class="pagination-link is-disabled" aria-disabled="true" aria-label="Página siguiente">&rsaquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
