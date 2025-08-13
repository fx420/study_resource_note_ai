document.addEventListener('DOMContentLoaded', () => {
    const chatForm     = document.getElementById('chatForm');
    const promptInput  = document.getElementById('promptInput');
    const fileInput    = document.getElementById('fileInput');
    const chatMessages = document.getElementById('chatMessages');
    const welcomeMsg   = document.getElementById('welcomeMessage');
  
    chatForm.addEventListener('submit', e => {
      e.preventDefault();
      sendUserMessage(promptInput.value.trim());
    });
  
    function sendUserMessage(text) {
      if (!text && fileInput.files.length === 0) {
        alert("Please enter a prompt or upload a file.");
        return;
      }
      if (welcomeMsg) welcomeMsg.style.display = 'none';
  
      appendChatBubble('user', text);
  
      promptInput.value = '';
      fileInput.value  = '';
  
      setTimeout(() => {
        appendChatBubble('system',
          "This is a simulated AI-generated study note based on your input."
        );
      }, 1000);
    }
  
    window.appendChatBubble = (type, content) => {
      const bubble = document.createElement('div');
      bubble.className = `chat-bubble ${type}`;
  
      const contentDiv = document.createElement('div');
      contentDiv.className = 'bubble-content';
      contentDiv.textContent = content;
      bubble.appendChild(contentDiv);
  
      const actions = document.createElement('div');
      actions.className = 'bubble-actions';
  
      if (type === 'user') {
        actions.innerHTML = `<button class="btn-action" title="Edit" onclick="editUserBubble(this)"><i class="fas fa-edit"></i></button>`;
      } else {
        actions.innerHTML = `<button class="btn-action" title="Download" onclick="downloadBubble(this)"><i class="fas fa-download"></i></button>`;
      }
      bubble.appendChild(actions);
  
      chatMessages.appendChild(bubble);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    };
  
    window.editUserBubble = (btn) => {
      const bubble    = btn.closest('.chat-bubble');
      const contentEl = bubble.querySelector('.bubble-content');
      const original  = contentEl.textContent;
      
      const ta = document.createElement('textarea');
      ta.className  = 'prompt-input';
      ta.value      = original;
      ta.rows       = 2;
      ta.style.width = '100%';
  
      const saveBtn = document.createElement('button');
      saveBtn.textContent = 'Save';
      saveBtn.className = 'btn btn-sm btn-primary mt-2';
  
      // swap content → editor
      contentEl.replaceWith(ta);
      btn.parentNode.replaceWith(saveBtn);
  
      saveBtn.addEventListener('click', () => {
        const newText = ta.value.trim() || original;
        bubble.remove();
        sendUserMessage(newText);
      });
    };
  
    // Download system bubble
    window.downloadBubble = (btn) => {
      const bubble   = btn.closest('.chat-bubble');
      const content  = bubble.querySelector('.bubble-content').textContent;
      const blob     = new Blob([content], { type: "text/plain" });
      const link     = document.createElement('a');
      link.href      = URL.createObjectURL(blob);
      link.download  = `note-${Date.now()}.txt`;
      link.click();
    };
  });

/* Chat Bubble */
function appendChatBubble(type, content, index = null) {
    const chatMessages = document.getElementById('chatMessages');
    const bubble = document.createElement('div');
    bubble.className = `chat-bubble ${type}`;
    
    if (type === 'system') {
        bubble.dataset.id = `bubble-${index}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'bubble-content';
        contentDiv.id = `bubble-content-${index}`;
        contentDiv.textContent = content;

        const actions = document.createElement('div');
        actions.className = 'bubble-actions';
        actions.innerHTML = `
            <button onclick="editBubble(${index})" class="btn-action" title="Edit"><i class="fas fa-edit"></i></button>
            <button onclick="downloadBubble(${index})" class="btn-action" title="Download"><i class="fas fa-download"></i></button>
        `;

        bubble.appendChild(contentDiv);
        bubble.appendChild(actions);
    } else {
        bubble.textContent = content;
    }

    chatMessages.appendChild(bubble);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

/* Edit Bubble */
function editBubble(index) {
    const bubble = document.querySelector(`.chat-bubble.system[data-id="bubble-${index}"]`);
    const contentDiv = document.getElementById(`bubble-content-${index}`);
    if (!bubble || !contentDiv) return;

    const originalText = contentDiv.textContent;

    const editor = document.createElement('div');
    editor.className = 'bubble-editor';
    editor.style.width = bubble.offsetWidth + 'px';

    const textarea = document.createElement('textarea');
    textarea.className = 'bubble-editor-text';
    textarea.value = originalText;
    textarea.rows = 4;
    textarea.style.width = '100%';

    const btnContainer = document.createElement('div');
    btnContainer.className = 'bubble-editor-actions';
    btnContainer.innerHTML = `
        <button class="btn-editor-save">Save</button>
        <button class="btn-editor-cancel">Cancel</button>
    `;

    editor.appendChild(textarea);
    editor.appendChild(btnContainer);

    // Replace contentDiv + actions with editor
    bubble.innerHTML = '';
    bubble.appendChild(editor);
    textarea.focus();

    // Save handler
    btnContainer.querySelector('.btn-editor-save').addEventListener('click', () => {
        const newText = textarea.value.trim() || originalText;
        // Rebuild bubble-content + actions
        bubble.innerHTML = `
            <div class="bubble-content" id="bubble-content-${index}">${newText}</div>
            <div class="bubble-actions">
                <button onclick="editBubble(${index})" class="btn-action" title="Edit"><i class="fas fa-edit"></i></button>
                <button onclick="downloadBubble(${index})" class="btn-action" title="Download"><i class="fas fa-download"></i></button>
            </div>
        `;
        Swal.fire({ icon:'success', title:'Saved!', showConfirmButton:false, timer:800 });
    });

    btnContainer.querySelector('.btn-editor-cancel').addEventListener('click', () => {
        bubble.innerHTML = `
            <div class="bubble-content" id="bubble-content-${index}">${originalText}</div>
            <div class="bubble-actions">
                <button onclick="editBubble(${index})" class="btn-action" title="Edit"><i class="fas fa-edit"></i></button>
                <button onclick="downloadBubble(${index})" class="btn-action" title="Download"><i class="fas fa-download"></i></button>
            </div>
        `;
    });
}

/* Download bubble */
function downloadBubble(index) {
    const content = document.getElementById(`bubble-content-${index}`)?.textContent;
    if (!content) return;

    const blob = new Blob([content], { type: "text/plain;charset=utf-8" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "study-note.txt";
    link.click();

    Swal.fire({
        icon: 'success',
        title: 'Downloaded!',
        showConfirmButton: false,
        timer: 1000
    });
}
