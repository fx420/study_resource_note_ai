<div class="chat-container">
  <div id="chatMessages" class="chat-messages">
    @if (View::hasSection('showWelcome'))
      <div id="welcomeMessage" class="text-white text-center mb-5">
        <h1>Welcome to Study Resource Note AI</h1>
        <p>Generate study notes effortlessly using AI‑powered summarization.</p>
      </div>
    @endif
  </div>

  <div class="input-area">
    <form method="POST" action="{{ route('chat.submit') }}" id="chatForm" enctype="multipart/form-data">
      @csrf
      <div class="input-wrapper">
        <textarea name="prompt" id="promptInput" class="form-control prompt-input" placeholder="Type your message…" rows="1"></textarea>
        <button type="button" class="btn btn-outline-light btn-upload" onclick="fileInput.click()">
          <i class="fas fa-paperclip"></i>
        </button>
        <input type="file" name="file" id="fileInput" class="d-none">
        <button type="submit" class="btn btn-primary btn-send">
          <i class="fas fa-paper-plane"></i>
        </button>
      </div>
    </form>
  </div>
  
</div>