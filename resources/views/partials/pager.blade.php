{{-- Pagination sederhana tanpa dependensi CSS framework. Pemakaian: @include('partials.pager', ['p' => $items]) --}}
@if($p->hasPages())
    <div class="pager">
        @if($p->onFirstPage())
            <span class="btn btn-sm btn-sec" style="opacity:.5">&laquo; Sebelumnya</span>
        @else
            <a class="btn btn-sm btn-sec" href="{{ $p->previousPageUrl() }}">&laquo; Sebelumnya</a>
        @endif

        <span class="muted">Halaman {{ $p->currentPage() }} dari {{ $p->lastPage() }}</span>

        @if($p->hasMorePages())
            <a class="btn btn-sm btn-sec" href="{{ $p->nextPageUrl() }}">Berikutnya &raquo;</a>
        @else
            <span class="btn btn-sm btn-sec" style="opacity:.5">Berikutnya &raquo;</span>
        @endif
    </div>
@endif
