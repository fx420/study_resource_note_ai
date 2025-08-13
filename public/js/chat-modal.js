document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('openChatModal');
  const modal = document.getElementById('chatModal');
  if (btn && modal) {
    const bsModal = new bootstrap.Modal(modal);
    btn.addEventListener('click', e => {
      e.preventDefault();
      bsModal.show();
    });
  }

  const form = document.getElementById('chatForm');
  const promptEl = form?.querySelector('#promptInput');
  const fileEl = form?.querySelector('#fileInput');
  const messagesEl = document.getElementById('chatMessages');

  if (!form) return;

  const onChatPage = !! messagesEl;

  form.addEventListener('submit', async e => {
    const text    = promptEl.value.trim();
    const hasFile = fileEl.files.length > 0;

    if (!text && !hasFile) {
      e.preventDefault();
      return Swal.fire({
        icon: 'warning',
        text: 'Please type a message or attach a file before sending.'
      });
    }

    if (!onChatPage) {
      return; 
    }

    e.preventDefault();

    appendBubble('user', text || `[File: ${fileEl.files[0].name}]`);

    const formData = new FormData(form);
    const res = await fetch(form.action, {
      method: form.method,
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: formData
    });
    const json = await res.json();

    appendBubble('system', json.reply || 'No response.');
    form.reset();
  });

  function appendBubble(type, text) {
    const b = document.createElement('div');
    b.className = `chat-bubble ${type}`;
    b.innerHTML = `
      <div class="bubble-content">${text}</div>
      <div class="bubble-actions">
        <button type="button" class="btn-action" title="${
          type === 'user' ? 'Edit' : 'Download'
        }">
          <i class="fas fa-${
            type === 'user' ? 'edit' : 'download'
          }"></i>
        </button>
      </div>`;
    if (type === 'system') {
      b.querySelector('.btn-action').addEventListener('click', () => {
        const blob = new Blob([text], { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `note-${Date.now()}.txt`;
        a.click();
      });
    }
    messagesEl.appendChild(b);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }
});
