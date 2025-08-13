@props(['session' => null])

<form method="POST" action="{{ route('chat.session.create') }}" enctype="multipart/form-data" id="chatForm">
  @csrf

  <!-- Onboarding Fields -->
  <div class="mb-3">
    <label for="educationLevelInput" class="form-label">Education Level</label>
    <select name="education_level" id="educationLevelInput" class="form-select" required>
      <option value="" disabled selected>Select level</option>
      <option value="high_school">High School</option>
      <option value="undergraduate">Undergraduate</option>
      <option value="postgraduate">Postgraduate</option>
    </select>
  </div>

  <div class="mb-3">
    <label for="courseInput" class="form-label">Course</label>
    <input type="text" name="course" id="courseInput" class="form-control" placeholder="Enter Course" required>
  </div>

  <div class="mb-3">
    <label for="subjectInput" class="form-label">Subject</label>
    <input type="text" name="subject" id="subjectInput" class="form-control" placeholder="Enter Subject" required>
  </div>

  <div class="mb-3">
    <label for="topicInput" class="form-label">Topic</label>
    <input type="text" name="topic" id="topicInput" class="form-control" placeholder="Enter Topic" required>
  </div>

  <div class="mb-3">
    <label for="priorKnowledgeInput" class="form-label">Prior Knowledge</label>
    <input type="text" name="prior_knowledge" id="priorKnowledgeInput" class="form-control" placeholder="e.g., Basic algebra">
  </div>

  <div class="mb-3">
    <label for="learningGoalInput" class="form-label">Learning Goal</label>
    <input type="text" name="learning_goal" id="learningGoalInput" class="form-control" placeholder="e.g., Understand concepts">
  </div>

  <div class="mb-3">
    <label for="noteLevelInput" class="form-label">Difficulty Level</label>
    <select name="note_level" id="noteLevelInput" class="form-select">
      <option value="1">1 - Very Easy</option>
      <option value="2">2 - Easy</option>
      <option value="3" selected>3 - Medium</option>
      <option value="4">4 - Hard</option>
      <option value="5">5 - Very Hard</option>
    </select>
  </div>

  <div class="mb-3">
    <label for="examplesCountInput" class="form-label">Examples Count</label>
    <input type="number" name="examples_count" id="examplesCountInput" class="form-control" min="0" placeholder="Number of examples">
  </div>

  <div class="mb-3">
    <label for="contentFormatInput" class="form-label">Content Format</label>
    <select name="content_format" id="contentFormatInput" class="form-select">
      <option value="text">Text</option>
      <option value="bullet">Bullet Points</option>
      <option value="table">Table</option>
    </select>
  </div>

  <div class="mb-3">
    <label for="modeInput" class="form-label">Mode</label>
    <select name="mode" id="modeInput" class="form-select" required>
      <option value="direct">Direct Note</option>
      <option value="prompt">Custom Prompt</option>
    </select>
  </div>

  <!-- Chat Input Section -->
  <div class="input-wrapper d-flex align-items-center gap-2 mt-4">
    <textarea name="prompt_preview" id="promptPreview" class="form-control prompt-input" placeholder="Prompt" rows="2"></textarea>

    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('fileInput').click()">
      <i class="fas fa-paperclip"></i>
    </button>

    <input type="file" name="file" id="fileInput" class="d-none">
  </div>
  
  <button type="submit" id="submitBtn" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>

  <div id="chatStatus" class="mt-2" style="min-height:1.5em;"></div>
  
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modeInput = document.getElementById('modeInput');
  const promptPreview = document.getElementById('promptPreview');
  const form = document.getElementById('chatForm');
  const submitBtn = document.getElementById('submitBtn');
  const statusEl = document.getElementById('chatStatus');

  // Show/hide the small prompt helper - optional UX
  function togglePromptHint() {
    if (!modeInput) return;
    if (modeInput.value === 'prompt') {
      // we could add visual hint, but we don't need extra elements
      promptPreview.placeholder = '(Required) Enter your custom prompt here…';
    } else {
      promptPreview.placeholder = '(Optional) Add a short instruction or leave blank for Direct Note…';
    }
  }

  if (modeInput) {
    modeInput.addEventListener('change', togglePromptHint);
    togglePromptHint();
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    statusEl.innerText = '';
    submitBtn.disabled = true;

    // Client-side validation:
    if (modeInput && modeInput.value === 'prompt' && (!promptPreview || promptPreview.value.trim() === '')) {
      statusEl.innerHTML = '<span class="text-danger">Custom prompt is required when Mode = Custom Prompt.</span>';
      submitBtn.disabled = false;
      return;
    }

    // Build FormData
    const fd = new FormData(form);

    // Put prompt into 'prompt' field only when mode=prompt, otherwise send empty string
    if (modeInput && modeInput.value === 'prompt') {
      fd.set('prompt', promptPreview.value.trim());
    } else {
      fd.set('prompt', '');
    }

    const previousLabel = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Generating…';
    statusEl.innerHTML = '<span class="text-muted">Contacting AI — this may take a few seconds.</span>';

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });

      if (!res.ok) {
        let body = {};
        try { body = await res.json(); } catch (err) {}
        const msg = (body && (body.error || body.message)) ? (body.error || body.message) : 'Server error';
        statusEl.innerHTML = `<span class="text-danger">${msg}</span>`;
        submitBtn.disabled = false;
        submitBtn.innerHTML = previousLabel;
        return;
      }

      const json = await res.json();

      if (json.redirect) {
        window.location.href = json.redirect;
        return;
      } else if (json.session_id) {
        window.location.href = `/chat/${json.session_id}`;
        return;
      } else {
        statusEl.innerHTML = '<span class="text-danger">Unexpected server response.</span>';
      }
    } catch (err) {
      console.error(err);
      statusEl.innerHTML = '<span class="text-danger">Network or server error.</span>';
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = previousLabel;
    }
  });
});
</script>
@endpush