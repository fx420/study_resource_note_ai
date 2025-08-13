@php $disabled = Auth::guest() ? 'disabled' : '' @endphp
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="offcanvasSidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Study Resource Note AI</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form class="d-flex mb-3">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
        </form>

        <!-- History Button -->
        <a class="btn btn-outline-light w-100 mb-3 d-flex align-items-center justify-content-center {{ $disabled }}" href="{{ Auth::check() ? route('library.index') : '#' }}">
            <i class="fas fa-book me-2"></i> History
        </a>
        
    </div>
</div>
