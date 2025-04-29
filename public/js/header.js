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
                console.log(data);//tét
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



    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = document.getElementById('suggestionsBox');
    const suggestionsList = document.getElementById('suggestionsList');
    const searchForm = document.getElementById('searchForm');

    let timeout = null;
    let historyKey = 'searchHistory';
    let isFetching = false;

   //Hàm lấy lịch sử
    function getSearchHistory() {
        return JSON.parse(localStorage.getItem(historyKey)) || [];
    }

    //Hàm lưu lịch sử
    function saveSearchHistory(keyword) {
        let history = getSearchHistory();
        if (!history.includes(keyword)) {
            history.unshift(keyword);
            if (history.length > 10) history = history.slice(0, 10);
            localStorage.setItem(historyKey, JSON.stringify(history));
        }
    }

    // Render gợi ý
    function renderSuggestions(items, isHistory = false) {
        suggestionsList.innerHTML = '';
    
        if (items.length === 0) {
            suggestionsList.innerHTML = '<li style="padding: 10px; color: #999;">Không tìm thấy kết quả</li>';
        } else {
            items.forEach(item => {
                const li = document.createElement('li');
    
                if (isHistory) {
                    li.textContent = item + ' (Lịch sử)';
                    li.dataset.name = item;
                    li.dataset.from = 'history';
                } else {
                    li.textContent = (item.name || 'Không tên') + ' (Gợi ý)';
                    li.dataset.name = item.name || '';
                    li.dataset.from = 'server';
                }
    
                suggestionsList.appendChild(li);
            });
        }
    
        suggestionsBox.style.display = 'block';
    }
    

    // Khi nhập input
    searchInput.addEventListener('input', function () {
        const keyword = this.value.trim();
        clearTimeout(timeout);

        if (keyword.length === 0) {
            let history = getSearchHistory();
            renderSuggestions(history, true);
            return;
        }

        // 👉 Khi đang nhập, chỉ fetch suggestion, không hiện lịch sử nữa
        
        timeout = setTimeout(() => {
            isFetching = true;
            fetch(`/search-suggestions?keyword=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {
                    renderSuggestions(data, false);
                    isFetching = false;
                });
        }, 300);
    });

    // Khi focus vào input
    searchInput.addEventListener('focus', function () {
        const keyword = this.value.trim();
        if (keyword.length === 0) {
            let history = getSearchHistory();
            renderSuggestions(history, true);
        }
    });

    // Click chọn 1 gợi ý
    suggestionsList.addEventListener('mousedown', function (e) {
        if (isFetching) {
            e.preventDefault();
            return;
        }
    
        if (e.target.tagName === 'LI') {
            const selected = e.target.dataset.name;
            const from = e.target.dataset.from;
            const selectedType = e.target.dataset.type; // lấy type
    
            if (selected) {
                searchInput.value = selected;
    
                // Gán type vào input hidden
                if (selectedType) {
                    document.getElementById('searchType').value = selectedType;
                }
    
                if (from === 'server') {
                    saveSearchHistory(selected);
                }
    
                suggestionsBox.style.display = 'none';
    
                setTimeout(() => {
                    searchForm.submit();
                }, 0);
            }
        }
    });
    

    
    

    // Enter để submit
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const keyword = searchInput.value.trim();
            if (keyword) {
                saveSearchHistory(keyword);
                searchForm.submit();
            }
        }
    });

    // Click ra ngoài form thì ẩn box
    document.addEventListener('click', function (event) {
        if (!searchForm.contains(event.target)) {
            suggestionsBox.style.display = 'none';
        }
    });


    
});
