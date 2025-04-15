function fetchDiscussions() {
    fetch('get_discussions.php')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('discussionList');
            list.innerHTML = '';

            data.reverse().forEach(entry => {
                const item = document.createElement('div');
                item.className = 'discussion-entry';

                const p = document.createElement('p');
                p.innerText = `${entry.time} | ${entry.name}: ${entry.question}`;
                item.appendChild(p);

                const editBtn = document.createElement('button');
                editBtn.innerText = 'Edit';
                editBtn.onclick = () => startEdit(entry);
                item.appendChild(editBtn);

                list.appendChild(item);
            });
        });
}

function postDiscussion(name, question) {
    fetch('discussion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, question })
    }).then(() => {
        fetchDiscussions();
        document.getElementById('questionInput').value = '';
    });
}

function startEdit(entry) {
    const newMsg = prompt("Edit your message:", entry.question);
    if (newMsg && newMsg.trim() !== '') {
        fetch('discussion.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: entry.id, question: newMsg })
        }).then(() => fetchDiscussions());
    }
}

// On load
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('submitBtn').onclick = () => {
        const name = document.getElementById('nameInput').value;
        const question = document.getElementById('questionInput').value;
        if (name && question) {
            postDiscussion(name, question);
        }
    };
    fetchDiscussions();
});
