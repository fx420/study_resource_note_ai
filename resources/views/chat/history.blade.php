@extends('layouts.index')

@section('title','Chat History')

@section('content')
<div class="container py-5 text-white">
  <h2 class="mb-4">Your Chat Sessions</h2>

  @if($sessions->isEmpty())
    <p class="text-light">No chat sessions found.</p>
  @else
    <div class="row row-cols-1 row-cols-md-2 g-4">
      @foreach($sessions as $session)
        <div class="col">
          <div class="card library-card bg-dark border-secondary h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title text-white">{{ $session->title }}</h5>

              <p class="card-text text-light flex-grow-1">
                {{ \Illuminate\Support\Str::limit(optional($session->messages->last())->message, 100) }}
              </p>

              <small class="text-muted">Started {{ $session->created_at->diffForHumans() }}</small>
            </div>
            <div class="card-footer bg-transparent border-0 text-end">
              <a href="{{ route('chat.session.show', $session) }}" class="btn btn-outline-light btn-sm">View</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
