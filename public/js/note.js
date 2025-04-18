document.addEventListener('DOMContentLoaded', function () {
    const chatForm = document.getElementById('chatForm');
    const promptInput = document.getElementById('promptInput');
    const chatMessages = document.getElementById('chatMessages');
    const fileInput = document.getElementById('fileInput');
    const welcomeMessage = document.getElementById('welcomeMessage');

    chatMessages.scrollTop = 0;

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const prompt = promptInput.value.trim();
        const hasFile = fileInput.files.length > 0;

        if (!prompt && !hasFile) {
            alert("Please enter a prompt or upload a file.");
            return;
        }

        // Hide welcome
        if (welcomeMessage) welcomeMessage.style.display = 'none';

        // Show user bubble
        if (prompt) appendChatBubble('user', prompt);

        promptInput.value = "";
        fileInput.value = "";

        // Simulated AI response
        setTimeout(() => {
            const noteId = Date.now();
            appendChatBubble('system', "This is a simulated AI-generated study note based on your input.", noteId);
        }, 1000);
    });
});

// Function to append a chat bubble
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

// Edit note content
function editBubble(index) {
    const bubble = document.querySelector(`.chat-bubble.system[data-id="bubble-${index}"]`);
    const contentDiv = document.getElementById(`bubble-content-${index}`);
    if (!bubble || !contentDiv) return;

    // Store original text
    const originalText = contentDiv.textContent;

    // Create editor container
    const editor = document.createElement('div');
    editor.className = 'bubble-editor';
    editor.style.width = bubble.offsetWidth + 'px';

    // Create textarea
    const textarea = document.createElement('textarea');
    textarea.className = 'bubble-editor-text';
    textarea.value = originalText;
    textarea.rows = 4;
    textarea.style.width = '100%';

    // Create buttons container
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

    // Cancel handler
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

// Download note
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
