document.addEventListener('DOMContentLoaded', () => {
  const openBtn = document.getElementById('openChatModal');
  const modalEl = document.getElementById('chatModal');
  if (openBtn && modalEl) {
    const chatModal = new bootstrap.Modal(modalEl);
    openBtn.addEventListener('click', e => {
      e.preventDefault();
      chatModal.show();
    });
  }

  const chatForm = document.getElementById('chatForm');
  const messagesEl = document.getElementById('chatMessages');
  if (!chatForm) return;

  const isChatPage = !!messagesEl;

  chatForm.addEventListener('submit', async e => {
    if (!isChatPage) {
      const debugData = new FormData(chatForm);
      console.group('Onboarding payload');
      for (let [key, val] of debugData.entries()) {
        console.log(key, val);
      }
      console.groupEnd();
      return;  
    }

    e.preventDefault();

    const formData = new FormData(chatForm);
    const userText = formData.get('prompt').trim();
    const fileObj = formData.get('file');
    const hasFile = fileObj && fileObj.name;

    if (!userText && !hasFile) {
      return Swal.fire({
        icon: 'warning',
        text: 'Please type a message or attach a file.'
      });
    }

    createBubble('user', userText || `[File: ${fileObj.name}]`);

    try {
      const res  = await fetch(chatForm.action, {
        method: chatForm.method,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
      });
      const json = await res.json();
      createBubble('system', json.reply || 'No response.');
      chatForm.reset();
    } catch (err) {
      console.error('Chat AJAX error', err);
      Swal.fire('Error', 'Failed to send message.', 'error');
    }
  });

  function createBubble(type, text) {
    if (!messagesEl) return;
    const b = document.createElement('div');
    b.className = `chat-bubble ${type}`;
    b.innerHTML = `
      <div class="bubble-content">${ text }</div>
      <div class="bubble-actions">
        <button type="button" class="btn-action" title="${
          type==='user' ? 'Edit' : 'Download'
        }">
          <i class="fas fa-${
            type==='user' ? 'edit' : 'download'
          }"></i>
        </button>
      </div>`;
    if (type !== 'user') {
      b.querySelector('.btn-action').addEventListener('click', () => {
        const blob = new Blob([text], { type:'text/plain' });
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
