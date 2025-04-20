@extends('layouts.index')

@section('title', 'Home - Study Resource Note AI')

@section('content')
    <!-- Chat Messages Container -->
    <div class="chat-box">
        <div id="chatMessages" class="chat-messages">
            <div id="welcomeMessage" class="text-white text-center mb-5">
                <h1>Welcome to Study Resource Note AI</h1>
                <p>Generate study notes effortlessly using AI-powered summarization.</p>
            </div>

        </div>
    </div>
    
    <!-- Chat Input Area Fixed at the Bottom -->
    <div class="input-area">
        <form method="POST" action="/submit" id="chatForm" enctype="multipart/form-data">
            @csrf
            <div class="input-wrapper">
                <!-- Text Input -->
                <textarea name="prompt" id="promptInput" class="form-control prompt-input" placeholder="Type your message..." rows="1"></textarea>

                <!-- File Upload Button -->
                <button type="button" class="btn btn-outline-light btn-upload" onclick="document.getElementById('fileInput').click();">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" name="file" id="fileInput" style="display: none;">

                <!-- Send/Submit Button -->
                <button type="submit" class="btn btn-primary btn-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
@endsection
