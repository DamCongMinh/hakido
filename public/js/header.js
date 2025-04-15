document.addEventListener('DOMContentLoaded', function () {
    // 1. Kiểm tra token
    const token = localStorage.getItem('token');
    console.log('DEBUG: token =', token);

    // 2. Hiển thị/ẩn menu tương ứng
    const guests = document.querySelectorAll('.guest');
    const auths = document.querySelectorAll('.auth');

    if (token && token !== 'null') {
        guests.forEach(el => el.style.display = 'none');
        auths.forEach(el => el.style.display = 'list-item');
    } else {
        guests.forEach(el => el.style.display = 'list-item');
        auths.forEach(el => el.style.display = 'none');
    }

    // 3. Xử lý logout
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
                window.location.href = '/login';
            })
            .catch(err => {
                console.error('Logout error:', err);
            });
        });
    }
});
