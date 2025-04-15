document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    console.log('DEBUG: token =', token);

    const guests = document.querySelectorAll('.guest');
    const auths = document.querySelectorAll('.auth');

    if (token && token !== 'null' && user) {
        guests.forEach(el => el.style.display = 'none');
        auths.forEach(el => el.style.display = 'list-item');

        const nameEl = document.querySelector('.auth h3');
        if (nameEl) {
            nameEl.textContent = user.name || 'Người dùng';
        }

        const avatarEl = document.querySelector('.auth .avatar');
        if (avatarEl && user.avatar) {
            avatarEl.src = user.avatar;
        }
    } else {
        guests.forEach(el => el.style.display = 'list-item');
        auths.forEach(el => el.style.display = 'none');
    }

    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
            })
            .then(res => res.json())
            .then(data => {
                console.log('DEBUG: Logout:', data.message);
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/login';
            })
            .catch(err => {
                console.error('Logout error:', err);
            });
        });
    }
});
