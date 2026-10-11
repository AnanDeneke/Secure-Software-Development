// public/js/app.js
'use strict';

document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const form = document.getElementById('comment-form');
  const list = document.getElementById('comment-list');
  const status = document.getElementById('status');

  async function loadComments() {
    const res = await fetch('api/comments.php', {
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
    });
    if (!res.ok) throw new Error('Could not load comments.');

    const data = await res.json();
    list.replaceChildren();

    for (const comment of data.comments) {
      const item = document.createElement('li');
      const name = document.createElement('strong');
      const text = document.createElement('p');

      // textContent (never innerHTML) treats user data as plain text,
      // so injected <script> tags or HTML are displayed, not executed.
      name.textContent = comment.author;
      text.textContent = comment.body;

      item.append(name, text);
      list.append(item);
    }
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const author = form.elements.author.value.trim();
    const body = form.elements.body.value.trim();

    // Quick client-side check for usability. The server validates again,
    // because anything in the browser can be modified by an attacker.
    if (!author || author.length > 50 || !body || body.length > 1000) {
      status.textContent = 'Please enter a name (max 50) and a comment (max 1000).';
      return;
    }

    try {
      const res = await fetch('api/comments.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken,
        },
        body: JSON.stringify({ author, body }),
      });
      const data = await res.json();

      if (!res.ok) {
        status.textContent = data.error || 'Request failed.';
        return;
      }

      form.reset();
      status.textContent = 'Comment posted.';
      await loadComments();
    } catch {
      status.textContent = 'Network error. Please try again.';
    }
  });

  loadComments().catch((err) => {
    status.textContent = err.message;
  });
});
