@extends('layouts.index')

@section('styles')
    <style>
        .hero-section,
        .features-section {
            display: none !important;
        }

        main.container {
            background-color: #fff;
            padding-bottom: 120px;
        }

        .chat-bubble.user .bubble-content,
        .chat-bubble.system .bubble-content {
            color: #000;
        }
    </style>
@endsection

@section('content')
<div class="container py-4">
    <h3 class="mb-4">{{ $session->title }}</h3>

    <div id="chatMessages" class="chat-messages mb-3">
        @foreach($session->messages as $msg)
            <div class="chat-bubble {{ $msg->sender }}">
                <div class="bubble-content">{{ $msg->message }}</div>
            </div>
        @endforeach
    </div>
</div>

{{-- Fixed input at bottom --}}
<div class="input-area">
    <form method="POST" action="{{ route('chat.session.submit', $session) }}" id="chatForm" enctype="multipart/form-data">
        @csrf
        <div class="input-wrapper">
            <textarea name="prompt" id="promptInput" class="form-control prompt-input" placeholder="Type your message…" rows="1" required></textarea>

            <button type="button" class="btn btn-outline-secondary btn-upload" onclick="document.getElementById('fileInput').click()">
              <i class="fas fa-paperclip"></i>
            </button>

            <input type="file" name="file" id="fileInput" class="d-none">

            <button type="submit" class="btn btn-primary btn-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/chat-box.js') }}"></script>
@endsection
