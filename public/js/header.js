document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));

    const guestEls = document.querySelectorAll('.guest');
    const authEls = document.querySelectorAll('.auth');

    if (token && user) {
        guestEls.forEach(el => el.style.display = 'none');
        authEls.forEach(el => el.style.display = 'list-item');

        const nameEl = document.querySelector('.auth h3');
        const avatarEl = document.querySelector('.auth .avatar');

        if (nameEl) nameEl.textContent = user.name || 'Người dùng';
        if (avatarEl && user.avatar) avatarEl.src = user.avatar;
    } else {
        guestEls.forEach(el => el.style.display = 'list-item');
        authEls.forEach(el => el.style.display = 'none');
    }

    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();

            fetch('/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(res => res.json())
            .then(data => {
                console.log('Logout:', data.message);
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
