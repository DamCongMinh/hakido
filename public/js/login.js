document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');

    // Chuyển tab Đăng ký / Đăng nhập
    if (registerBtn && loginBtn && container) {
        registerBtn.addEventListener('click', () => container.classList.add("active"));
        loginBtn.addEventListener('click', () => container.classList.remove("active"));
    }

    // Xử lý đăng ký
    const signupForm = document.getElementById('formsign-up');
    if (signupForm) {
        signupForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('signup--name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('signup--password').value;
            const repassword = document.getElementById('signup--repassword').value;
            const role = document.getElementById('signup-role').value;

            if (!name || !email || !password || !repassword) {
                alert("Vui lòng điền đầy đủ thông tin!");
                return;
            }

            if (password !== repassword) {
                alert("Mật khẩu nhập lại không khớp!");
                return;
            }

            try {
                const res = await fetch('http://127.0.0.1:8000/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password,
                        password_confirmation: repassword,
                        role
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    // Điều hướng theo role
                    window.location.href = data.redirect_url || "/home";
                } else {
                    alert(data.message || "Đăng ký thất bại");
                }
            } catch (error) {
                console.error("Lỗi đăng ký:", error);
                alert("Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại!");
            }
        });
    }

    // Xử lý đăng nhập
    const loginForm = document.getElementById('formsign-in');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;

            if (!email || !password) {
                alert("Vui lòng nhập email và mật khẩu!");
                return;
            }

            try {
                const res = await fetch('http://127.0.0.1:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await res.json();

                if (res.ok) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));

                    window.location.href = data.redirect_url || "/home";
                } else {
                    const errorMsg = document.getElementById('login-message');
                    if (errorMsg) {
                        errorMsg.innerText = data.message || "Sai thông tin đăng nhập";
                    } else {
                        alert(data.message || "Sai thông tin đăng nhập");
                    }
                }
            } catch (error) {
                console.error("Lỗi đăng nhập:", error);
                alert("Đã xảy ra lỗi khi đăng nhập. Vui lòng thử lại!");
            }
        });
    }
});
