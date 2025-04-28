document.addEventListener('DOMContentLoaded', () => {
  const form       = document.getElementById('chatForm');
  const messages   = document.getElementById('chatMessages');
  const promptIn   = form.querySelector('textarea[name="prompt"]');
  const fileIn     = form.querySelector('input[name="file"]');
  const welcome    = document.getElementById('welcomeMessage');

  function createBubble(type, text) {
    const b = document.createElement('div');
    b.classList.add('chat-bubble', type);
    // content
    const c = document.createElement('div');
    c.classList.add('bubble-content');
    c.textContent = text;
    b.appendChild(c);
    // actions
    const act = document.createElement('div');
    act.classList.add('bubble-actions');
    if (type === 'user') {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.classList.add('btn-action');
      btn.title = 'Edit';
      btn.innerHTML = '<i class="fas fa-edit"></i>';
      // wire up editing here...
      act.appendChild(btn);
    } else {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.classList.add('btn-action');
      btn.title = 'Download';
      btn.innerHTML = '<i class="fas fa-download"></i>';
      btn.addEventListener('click', () => {
        const blob = new Blob([text], { type:'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `note-${Date.now()}.txt`;
        a.click();
      });
      act.appendChild(btn);
    }
    b.appendChild(act);
    messages.appendChild(b);
    messages.scrollTop = messages.scrollHeight;
  }

  form.addEventListener('submit', async e => {
    e.preventDefault();

    const text    = promptIn.value.trim();
    const hasFile = fileIn.files.length > 0;
    if (!text && !hasFile) {
      return alert('Please type a message or attach a file.');
    }

    if (welcome) welcome.remove();

    // show user bubble
    createBubble('user', text || `[File: ${fileIn.files[0].name}]`);

    // send
    const fd = new FormData(form);
    form.reset();

    try {
      const res  = await fetch(form.action, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body:    fd
      });
      const data = await res.json();
      createBubble('system', data.reply || 'No response.');
    } catch (err) {
      console.error(err);
      createBubble('system', '⚠️ An error occurred.');
    }
  });
});
