document.querySelectorAll('.editable-name, .editable-email').forEach(cell => {
    cell.addEventListener('blur', () => {
        const id = cell.dataset.id;
        const name = document.querySelector(`.editable-name[data-id="${id}"]`).innerText;
        const email = document.querySelector(`.editable-email[data-id="${id}"]`).innerText;

        fetch(`/admin/accounts/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, email })
        }).then(res => res.json())
          .then(data => console.log('Updated:', data));
    });
});

document.querySelectorAll('.ban-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`/admin/accounts/${id}/toggle-ban`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => location.reload());
    });
});
