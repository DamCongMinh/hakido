document.addEventListener("DOMContentLoaded", function () {
    // Khai báo các biến cần thiết
    const mainImage = document.querySelector(".product-show img");
    const thumbnails = document.querySelectorAll(".list-img img");
    const productName = document.querySelector(".detail-product h2");
    const description = document.querySelector(".description");
    const oldPriceEl = document.getElementById('old-price');
    const discountEl = document.getElementById('discount');
    const newPriceEl = document.getElementById('new-price');
    const quantityInput = document.getElementById('quantity');
    const formQuantityInput = document.getElementById('form-quantity');
    const checkoutQuantityInput = document.getElementById('checkout-quantity');
    const selectedSizeInput = document.getElementById('selected-size');
    const checkoutSelectedSizeInput = document.getElementById('checkout-selected-size');
    const totalAmountElement = document.getElementById('total-amount');
    const sizeContainer = document.querySelector('.size-item');
    const sizeSection = document.querySelector('.size');
    const typeInput = document.querySelector('input[name="type"]');
    const productIdInput = document.querySelector('input[name="product_id"]');
    const checkoutProductIdInput = document.querySelector('input[name="selected_items[0][product_id]"]');
    const checkoutTypeInput = document.querySelector('input[name="selected_items[0][product_type]"]');
 

    // Hàm hiển thị thông tin sản phẩm (dùng chung cho cả 2 luồng)
    function displayProductInfo(thumbnail) {
        // Cập nhật ảnh chính và active thumbnail
        mainImage.src = thumbnail.src;
        thumbnails.forEach(img => img.classList.remove("active"));
        thumbnail.classList.add("active");

        // Cập nhật thông tin cơ bản
        productName.textContent = thumbnail.dataset.name;
        description.textContent = thumbnail.dataset.description;

        // Xử lý theo loại sản phẩm
        if (thumbnail.dataset.type === 'food') {
            updateFoodProductInfo(thumbnail);
        } else if (thumbnail.dataset.type === 'beverage') {
            updateBeverageProductInfo(thumbnail);
        }
    }

    // Sửa lại hàm initProductDetails
    function initProductDetails() {
        resetTotalAmount();
        
        // Tìm thumbnail tương ứng với sản phẩm hiện tại từ server
        const initialProduct = window.productData.currentProduct;
        const initialThumbnail = Array.from(thumbnails).find(thumb => 
            thumb.dataset.id == initialProduct.id && 
            thumb.dataset.type == initialProduct.type
        );

        if (initialThumbnail) {
            // Hiển thị thông tin từ thumbnail tương ứng
            displayProductInfo(initialThumbnail);
        } else if (thumbnails.length > 0) {
            // Fallback: hiển thị sản phẩm đầu tiên
            displayProductInfo(thumbnails[0]);
        }
        
        bindSizeEvents();
    }

    // Sửa lại sự kiện click thumbnail để cập nhật nguồn gốc
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener("click", function() {
            currentProductSource = 'thumbnail';
            displayProductInfo(this);
        });
    });

    const listImg = document.querySelector(".list-img");
    const prevBtn = document.querySelector(".prev-btn");
    const nextBtn = document.querySelector(".next-btn");

    if (prevBtn && nextBtn && listImg) {
        prevBtn.addEventListener("click", () => listImg.scrollBy({ left: -120, behavior: "smooth" }));
        nextBtn.addEventListener("click", () => listImg.scrollBy({ left: 120, behavior: "smooth" }));
    }

    function bindSizeEvents() {
        const sizeButtons = document.querySelectorAll('.size-btn');
        sizeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Xóa active khỏi tất cả các nút
                sizeButtons.forEach(btn => btn.classList.remove('active'));
                // Thêm active cho nút được click
                this.classList.add('active');
                
                // Cập nhật giá trị từ data attributes
                const price = parseFloat(this.dataset.price);
                const oldPrice = parseFloat(this.dataset.old);
                const discount = this.dataset.discount;
                const maxQuantity = this.dataset.quantity;
                
                // Cập nhật hiển thị giá
                newPriceEl.textContent = formatCurrency(price);
                oldPriceEl.textContent = formatCurrency(oldPrice);
                discountEl.textContent = discount + '%';
                
                // Cập nhật số lượng tối đa
                quantityInput.max = maxQuantity;
                quantityInput.value = 1;
                
                // Cập nhật giá trị cho input ẩn
                if (selectedSizeInput) selectedSizeInput.value = this.value;
                if (checkoutSelectedSizeInput) checkoutSelectedSizeInput.value = this.value;
                
                // Cập nhật tổng tiền
                updateTotalPayouts();
            });
        });
    }

    function resetTotalAmount() {
        totalAmountElement.textContent = formatCurrency(0);
    }

    function updateFormInputs(product, type, id) {
        if (typeInput) typeInput.value = type;
        if (productIdInput) productIdInput.value = id;
        if (checkoutProductIdInput) checkoutProductIdInput.value = id;
        if (checkoutTypeInput) checkoutTypeInput.value = type;
        
        // Cập nhật tổng tiền ngay khi thay đổi sản phẩm
        updateTotalPayouts();
    }


    
    // Hàm định dạng tiền tệ
    function formatCurrency(value) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0
        }).format(value);
    }

    function updateTotalPayouts() {
        const quantity = parseInt(quantityInput.value) || 1;
        let price = 0;
    
        // Lấy sản phẩm đang active
        const activeThumb = document.querySelector('.list-img img.active');
        if (!activeThumb) return; // Thoát nếu không có sản phẩm nào active
    
        // Kiểm tra loại sản phẩm
        if (activeThumb.dataset.type === 'beverage') {
            // Xử lý beverage - lấy giá từ size được chọn (đã là giá mới)
            const activeSize = document.querySelector('.size-btn.active');
            if (activeSize) {
                price = parseFloat(activeSize.dataset.price) || 0; // Sử dụng data-price (giá mới)
            }
        } else {
            // Xử lý food - tính toán giá mới từ thumbnail
            const oldPrice = parseFloat(activeThumb.dataset.oldPrice) || 0;
            const discount = parseFloat(activeThumb.dataset.discountPercent) || 0;
            price = oldPrice * (100 - discount) / 100; // Tính giá mới
        }
    
        const total = price * quantity;
        totalAmountElement.textContent = formatCurrency(total);
    }

    // Sửa lại hàm xử lý nút tăng/giảm
    const decreaseButton = document.querySelector('.decrease');
    const increaseButton = document.querySelector('.increase');

    if (increaseButton && decreaseButton) {
        increaseButton.addEventListener('click', (e) => {
            e.preventDefault();
            let quantity = parseInt(quantityInput.value, 10) || 1;
            const max = parseInt(quantityInput.max) || 100;
            if (quantity < max) {
                quantityInput.value = quantity + 1;
                syncQuantity();
            }
        });

        decreaseButton.addEventListener('click', (e) => {
            e.preventDefault();
            let quantity = parseInt(quantityInput.value, 10) || 1;
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
                syncQuantity();
            }
        });
    }

    // Sửa lại hàm syncQuantity
    function syncQuantity() {
        const quantity = parseInt(quantityInput.value) || 1;
        const max = parseInt(quantityInput.max) || 100;
        
        quantityInput.value = Math.min(Math.max(quantity, 1), max);
        
        if (formQuantityInput) formQuantityInput.value = quantityInput.value;
        if (checkoutQuantityInput) checkoutQuantityInput.value = quantityInput.value;
        
        // Cập nhật ngay lập tức
        updateTotalPayouts();
    }

    // Thêm sự kiện input cho quantity
    quantityInput.addEventListener('input', function() {
        syncQuantity();
    });

    function updateFoodProductInfo(thumbnail) {
        sizeSection.style.display = 'none';
        
        const oldPrice = parseFloat(thumbnail.dataset.oldPrice) || 0;
        const discount = parseFloat(thumbnail.dataset.discountPercent) || 0;
        const discountedPrice = oldPrice * (100 - discount) / 100;
        
        oldPriceEl.textContent = formatCurrency(oldPrice);
        discountEl.textContent = discount + '%';
        newPriceEl.textContent = formatCurrency(discountedPrice); 
        
        quantityInput.value = 1;
        quantityInput.max = thumbnail.dataset.quantity || 100;
        
        updateFormInputs({
            id: thumbnail.dataset.id,
            type: thumbnail.dataset.type,
            name: thumbnail.dataset.name,
            price: discountedPrice 
        }, thumbnail.dataset.type, thumbnail.dataset.id);
        
        updateTotalPayouts();
    }

    function updateBeverageProductInfo(thumbnail) {
        sizeSection.style.display = 'flex';
        sizeContainer.innerHTML = '';
        resetTotalAmount();
    
        const sizes = JSON.parse(thumbnail.dataset.sizes || '[]');
        
        sizes.forEach((size, index) => {
            const sizeButton = document.createElement('button');
            sizeButton.type = 'button';
            sizeButton.className = `size-btn ${index === 0 ? 'active' : ''}`;
            sizeButton.textContent = size.size;
            
            // Tính toán giá mới dựa trên giá gốc và phần trăm giảm giá
            const discountedPrice = size.old_price * (100 - size.discount_percent) / 100;
            
            sizeButton.dataset.price = discountedPrice; // Lưu giá sau giảm
            sizeButton.dataset.old = size.old_price;
            sizeButton.dataset.discount = size.discount_percent;
            sizeButton.dataset.quantity = size.quantity;
            
            sizeButton.addEventListener('click', function() {
                document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Hiển thị giá gốc và giá sau giảm
                oldPriceEl.textContent = formatCurrency(this.dataset.old);
                discountEl.textContent = this.dataset.discount + '%';
                newPriceEl.textContent = formatCurrency(this.dataset.price); 
                
                quantityInput.max = this.dataset.quantity;
                quantityInput.value = 1;
                
                if (selectedSizeInput) selectedSizeInput.value = this.textContent;
                if (checkoutSelectedSizeInput) checkoutSelectedSizeInput.value = this.textContent;
                
                updateTotalPayouts();
            });
            
            sizeContainer.appendChild(sizeButton);
        });
    
        if (sizes.length > 0) {
            const firstSize = sizes[0];
            const firstDiscountedPrice = firstSize.old_price * (100 - firstSize.discount_percent) / 100;
            
            // Hiển thị giá gốc và giá sau giảm
            oldPriceEl.textContent = formatCurrency(firstSize.old_price);
            discountEl.textContent = firstSize.discount_percent + '%';
            newPriceEl.textContent = formatCurrency(firstDiscountedPrice);
            
            quantityInput.value = 1;
            quantityInput.max = firstSize.quantity;
            
            if (selectedSizeInput) selectedSizeInput.value = sizes[0].size;
            if (checkoutSelectedSizeInput) checkoutSelectedSizeInput.value = sizes[0].size;
            
            updateTotalPayouts();
        }
        
        updateFormInputs({
            id: thumbnail.dataset.id,
            type: thumbnail.dataset.type,
            name: thumbnail.dataset.name
        }, thumbnail.dataset.type, thumbnail.dataset.id);
    }

    // Khởi tạo khi trang được tải
    initProductDetails();
});
