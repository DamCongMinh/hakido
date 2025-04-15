document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');

    // Chuyển tab
    if (registerBtn && loginBtn && container) {
        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });
    }

    // Xử lý đăng ký
    const signupForm = document.getElementById('formsign-up');
    if (signupForm) {
        signupForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('signup--name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('signup--password').value;
            const repassword = document.getElementById('signup--repassword').value;

            try {
                const res = await fetch('http://127.0.0.1:8000/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password,
                        password_confirmation: repassword
                    })
                });

                const data = await res.json();
                if (res.ok) {
                    alert("Đăng ký thành công!");
                    localStorage.setItem('token', data.token);
                    container.classList.remove("active"); // chuyển sang tab login
                } else {
                    const errorMsg = document.getElementById('signup-message');
                    if (errorMsg) {
                        errorMsg.innerText = data.message || "Đăng ký thất bại";
                    }
                }
            } catch (error) {
                console.error("Lỗi đăng ký:", error);
            }
        });
    }

    // Xử lý đăng nhập
    const loginForm = document.getElementById('formsign-in');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            try {
                const res = await fetch('http://127.0.0.1:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await res.json();
                if (res.ok) {
                    alert("Đăng nhập thành công!");
                    localStorage.setItem('token', data.token);
                    window.location.href = "home";
                } else {
                    const errorMsg = document.getElementById('login-message');
                    if (errorMsg) {
                        let message = data.message || "Sai thông tin đăng nhập";
                    
                        // Dịch một số thông báo quen thuộc sang tiếng Việt
                        if (message === "Invalid login details") {
                            message = "Thông tin đăng nhập không hợp lệ";
                        }
                    
                        errorMsg.innerText = message;
                    }
                }
            } catch (error) {
                console.error("Lỗi đăng nhập:", error);
            }
        });
    }
});
