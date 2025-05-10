document.addEventListener("DOMContentLoaded", function () {
    const mainImage = document.querySelector(".product-show img");
    const thumbnails = document.querySelectorAll(".list-img img");

    thumbnails.forEach((thumbnail) => {
        thumbnail.addEventListener("click", function () {
            mainImage.src = this.src;
            thumbnails.forEach(img => img.classList.remove("active"));
            this.classList.add("active");
        });
    });

    const listImg = document.querySelector(".list-img");
    const prevBtn = document.querySelector(".prev-btn");
    const nextBtn = document.querySelector(".next-btn");

    if (prevBtn && nextBtn && listImg) {
        prevBtn.addEventListener("click", () => listImg.scrollBy({ left: -120, behavior: "smooth" }));
        nextBtn.addEventListener("click", () => listImg.scrollBy({ left: 120, behavior: "smooth" }));
    }

    const sizeButtons = document.querySelectorAll('.size-btn');
    const newPriceEl = document.getElementById('new-price');
    const oldPriceEl = document.getElementById('old-price');
    const discountEl = document.getElementById('discount');
    const quantityInput = document.querySelector('#quantity');
    const formQuantityInput = document.querySelector('#form-quantity');
    const selectedSizeInput = document.querySelector('#selected-size');
    const totalAmountElement = document.querySelector('#total-amount');

    function formatCurrency(amount) {
        return amount.toLocaleString('vi-VN') + '₫';
    }

    function updateTotalPayouts() {
        const quantity = parseInt(quantityInput.value, 10);
        const priceText = newPriceEl.textContent.replace(/[^\d]/g, '');
        const price = parseFloat(priceText);
        const total = price * quantity;
        if (totalAmountElement) {
            totalAmountElement.textContent = formatCurrency(total);
        }
    }

    sizeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const newPrice = parseFloat(button.dataset.price);
            const oldPrice = parseFloat(button.dataset.old);
            const discount = Math.round(((oldPrice - newPrice) / oldPrice) * 100);
    
            newPriceEl.textContent = formatCurrency(newPrice);
            oldPriceEl.textContent = formatCurrency(oldPrice);
            discountEl.textContent = `${discount}%`;
    
            sizeButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            if (selectedSizeInput) {
                selectedSizeInput.value = button.value;
            }
    
            // Hiển thị phần tổng tiền nếu đang ẩn
            const totalBox = document.querySelector('.total-payouts');
            if (totalBox.style.display === 'none') {
                totalBox.style.display = 'block';
            }
    
            updateTotalPayouts();
        });
    });
    

    const decreaseButton = document.querySelector('.decrease');
    const increaseButton = document.querySelector('.increase');

    function syncFormQuantity() {
        if (formQuantityInput) {
            formQuantityInput.value = quantityInput.value;
        }
    }

    if (increaseButton && decreaseButton && quantityInput) {
        increaseButton.addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value, 10);
            const max = parseInt(quantityInput.max || 100);
            if (quantity < max) {
                quantity += 1;
                quantityInput.value = quantity;
                syncFormQuantity();
                updateTotalPayouts();
            }
        });

        decreaseButton.addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value, 10);
            if (quantity > 1) {
                quantity -= 1;
                quantityInput.value = quantity;
                syncFormQuantity();
                updateTotalPayouts();
            }
        });

        quantityInput.addEventListener('input', () => {
            let quantity = parseInt(quantityInput.value, 10);
            const max = parseInt(quantityInput.max || 100);
            if (isNaN(quantity) || quantity < 1) quantity = 1;
            if (quantity > max) quantity = max;
            quantityInput.value = quantity;
            syncFormQuantity();
            updateTotalPayouts();
        });
    }

    window.copyCoupon = function (code) {
        navigator.clipboard.writeText(code);
        alert("Đã sao chép mã: " + code);
    };

    const reviewList = document.querySelector('.review-list');
    const reviews = reviewList?.querySelectorAll('.review-container') ?? [];
    const paginationButtons = document.querySelectorAll('.page-btn');
    const commentsPerPage = 6;

    function showPage(page) {
        const start = (page - 1) * commentsPerPage;
        const end = start + commentsPerPage;

        reviews.forEach((review, index) => {
            review.style.display = (index >= start && index < end) ? 'block' : 'none';
        });

        paginationButtons.forEach(btn => btn.classList.remove('active'));
        const currentBtn = document.querySelector(`.page-btn[data-page="${page}"]`);
        currentBtn?.classList.add('active');
    }

    paginationButtons.forEach(button => {
        button.addEventListener('click', function () {
            const page = parseInt(this.dataset.page);
            showPage(page);
        });
    });

    if (reviews.length > 0) {
        showPage(1);
    }

    // Đồng bộ initial form quantity
    syncFormQuantity();

    updateTotalPayouts();

    // js của nút mở rộng mô tả
    const toggleBtn = document.getElementById('toggleBtn');
    const description = document.querySelector('.description');

    toggleBtn.addEventListener('click', function() {
        description.classList.toggle('expanded');
        toggleBtn.classList.toggle('expanded');

        // Đổi icon nếu cần (nếu không thích xoay)
        const icon = toggleBtn.querySelector('i');
        if (description.classList.contains('expanded')) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    });
});
