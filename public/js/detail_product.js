document.addEventListener("DOMContentLoaded", function () {
    const mainImage = document.querySelector(".product-show img");
    const thumbnails = document.querySelectorAll(".list-img img");

    thumbnails.forEach((thumbnail) => {
        thumbnail.addEventListener("click", function () {
            // Đổi ảnh lớn thành ảnh nhỏ được click
            mainImage.src = this.src;

            // Xóa class active ở tất cả ảnh
            thumbnails.forEach(img => img.classList.remove("active"));

            // Thêm class active cho ảnh được chọn
            this.classList.add("active");
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const listImg = document.querySelector(".list-img");
    const prevBtn = document.querySelector(".prev-btn");
    const nextBtn = document.querySelector(".next-btn");

    // Scroll trái/phải khi nhấn nút
    prevBtn.addEventListener("click", () => {
        listImg.scrollBy({ left: -120, behavior: "smooth" });
    });

    nextBtn.addEventListener("click", () => {
        listImg.scrollBy({ left: 120, behavior: "smooth" });
    });

    // Khi nhấn vào ảnh nhỏ, thay đổi ảnh lớn
    const productShow = document.querySelector(".product-show img");
    document.querySelectorAll(".list-img img").forEach(img => {
        img.addEventListener("click", () => {
            productShow.src = img.src;
        });
    });
});


// Đổi giá trị của input khi nhấn vào nút
const decreaseButton = document.querySelector('.decrease');
const increaseButton = document.querySelector('.increase');
const quantityInput = document.querySelector('#quantity');
const totalAmountElement = document.querySelector('#total-amount');

// Hàm tính lại tổng tiền cần thanh toán
function updateTotalPayouts() {
    // Lấy giá từ HTML và chuyển thành số
    let priceText = document.getElementById('new-price').textContent;
    let price = parseFloat(priceText.replace('$', ''));

    const quantity = parseInt(quantityInput.value, 10); // Lấy số lượng sản phẩm
    const total = price * quantity; // Tính tổng tiền cần thanh toán
    totalAmountElement.textContent = `$${total}`; // Cập nhật tổng tiền
}

// Xử lý sự kiện khi nhấn nút tăng số lượng
increaseButton.addEventListener('click', () => {
    let quantity = parseInt(quantityInput.value, 10);
    if (quantity < 100) {
        quantity += 1;
        quantityInput.value = quantity;
        updateTotalPayouts();
    }
});

// Xử lý sự kiện khi nhấn nút giảm số lượng
decreaseButton.addEventListener('click', () => {
    let quantity = parseInt(quantityInput.value, 10);
    if (quantity > 1) {
        quantity -= 1;
        quantityInput.value = quantity;
        updateTotalPayouts();
    }
});

// Xử lý thay đổi số lượng trực tiếp từ input
quantityInput.addEventListener('input', () => {
    let quantity = parseInt(quantityInput.value, 10);
    if (quantity < 1) {
        quantity = 1;
    } else if (quantity > 100) {
        quantity = 100;
    }
    quantityInput.value = quantity;
    updateTotalPayouts();
});





// mã giảm giá
function copyCoupon(code) {
    navigator.clipboard.writeText(code);
    alert("Đã sao chép mã: " + code);
}


// phân trang cho comment   
  const commentsPerPage = 6;

  // Giả lập danh sách đánh giá (bạn có thể thay bằng dữ liệu từ server)
document.addEventListener('DOMContentLoaded', function () {
    const commentsPerPage = 6;
    const reviewList = document.querySelector('.review-list');
    const reviews = reviewList.querySelectorAll('.review-container');
    const paginationButtons = document.querySelectorAll('.page-btn');
  
    function showPage(page) {
        const start = (page - 1) * commentsPerPage;
        const end = start + commentsPerPage;
  
        reviews.forEach((review, index) => {
            if (index >= start && index < end) {
                review.style.display = 'block';
            } else {
                review.style.display = 'none';
            }
        });
  
        // Highlight current button
        paginationButtons.forEach(btn => btn.classList.remove('active'));
        const currentBtn = document.querySelector(`.page-btn[data-page="${page}"]`);
        if (currentBtn) currentBtn.classList.add('active');
    }
  
    paginationButtons.forEach(button => {
        button.addEventListener('click', function () {
            const page = parseInt(this.dataset.page);
            showPage(page);
        });
    });
  
      // Mặc định load trang đầu tiên
    showPage(1);
});
  
// btn đổi giá theo size
const sizeButtons = document.querySelectorAll('.size-btn');
const newPriceEl = document.getElementById('new-price');
const oldPriceEl = document.getElementById('old-price');
const discountEl = document.getElementById('discount');

// Hàm tính lại tổng tiền
function updateTotalPayouts() {
    const quantity = parseInt(document.querySelector('#quantity').value, 10);
    const priceText = newPriceEl.textContent.replace('$', '');
    const price = parseFloat(priceText);
    const total = price * quantity;
    document.querySelector('#total-amount').textContent = `$${total}`;
}

// Xử lý khi chọn size
sizeButtons.forEach(button => {
    button.addEventListener('click', () => {
        const newPrice = parseInt(button.dataset.price);
        const oldPrice = parseInt(button.dataset.old);
        const discount = Math.round(((oldPrice - newPrice) / oldPrice) * 100);

        newPriceEl.textContent = `$${newPrice}`;
        oldPriceEl.textContent = `$${oldPrice}`;
        discountEl.textContent = `${discount}%`;

        // Highlight size đang chọn
        sizeButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        // 👉 Cập nhật lại tổng tiền ngay sau khi đổi size
        updateTotalPayouts();
    });
});



