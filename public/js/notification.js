
document.addEventListener('DOMContentLoaded', function () {
    const bellIcon = document.querySelector('.fa-bell');
    const dropdown = document.getElementById('notification-list');

    bellIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    document.querySelectorAll('.notification-item').forEach(function (item) {
        item.addEventListener('click', function () {
            const url = item.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            }
        });
    });
});

