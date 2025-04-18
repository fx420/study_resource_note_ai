@extends('layouts.index')

@section('title', 'Library - Study Resource Note AI')

@section('content')
<div class="library-page">
    <div class="container my-5 library-container">

        <h2 class="text-white mb-4">Your Note Library</h2>

        @if(count($notes))
        <div class="library-grid">
            @foreach($notes as $note)
            <div class="card library-card bg-dark border-secondary">
                <div class="card-body">
                    <h5 class="card-title text-white">{{ $note['title'] }}</h5>
                    <p class="card-text text-light">
                        {{ Str::limit($note['content'], 150) }}
                    </p>
                    <small class="text-muted">Generated on {{ $note['date'] }}</small>
                </div>

                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="data:text/plain;charset=utf-8,{{ rawurlencode($note['content']) }}"
                        download="{{ $note['title'] }}.txt"
                        class="btn btn-outline-light btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
            <p class="text-light">No notes found in your library.</p>
        @endif

  </div>
</div>
@endsection
