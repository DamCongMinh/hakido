document.addEventListener("DOMContentLoaded", function () {
    // Khai báo các biến cần thiết
    const mainImage = document.querySelector(".product-show img");
    const thumbnails = document.querySelectorAll(".list-img img");
    const productName = document.querySelector(".detail-product h2");
    // const description = document.querySelector(".description");
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
    let currentProduct = null;

    const toggleBtn = document.getElementById('toggleBtn');
    const description = document.querySelector('.descripttion_title');
    const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', function () {
            description.classList.toggle('expanded');

            if (description.classList.contains('expanded')) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        });

 

    function displayProductInfo(thumbnail) {
        // Debug: kiểm tra dữ liệu từ thumbnail
        console.log('Thumbnail data:', {
            oldPrice: thumbnail.dataset.oldPrice,
            discountPercent: thumbnail.dataset.discountPercent
        });
    
        // Cập nhật sản phẩm hiện tại với giá trị mặc định phòng trường hợp undefined
        currentProduct = {
            id: thumbnail.dataset.id,
            type: thumbnail.dataset.type,
            name: thumbnail.dataset.name,
            description: thumbnail.dataset.description,
            oldPrice: parseFloat(thumbnail.dataset.oldPrice) || 0,
            discountPercent: parseFloat(thumbnail.dataset.discountPercent) || 0,
            quantity: parseInt(thumbnail.dataset.quantity) || 100,
            sizes: thumbnail.dataset.sizes ? JSON.parse(thumbnail.dataset.sizes.replace(/&quot;/g, '"')) : null
        };
    
        // Cập nhật ảnh và trạng thái active
        mainImage.src = thumbnail.src;
        thumbnails.forEach(img => img.classList.remove("active"));
        thumbnail.classList.add("active");
    
        // Cập nhật thông tin cơ bản
        productName.textContent = currentProduct.name;
        description.textContent = currentProduct.description;
    
        // Cập nhật input ẩn
        updateFormInputs(currentProduct, currentProduct.type, currentProduct.id);
    
        // Xử lý theo loại sản phẩm
        if (currentProduct.type === 'food') {
            updateFoodProductInfo();
        } else if (currentProduct.type === 'beverage') {
            updateBeverageProductInfo();
        }
    }

    function updatePriceDisplay(oldPrice, discountPercent, newPrice) {
        // Kiểm tra sự tồn tại của các phần tử
        if (!oldPriceEl || !discountEl || !newPriceEl) {
            console.error('Các phần tử hiển thị giá không tồn tại');
            return;
        }
    
        // Kiểm tra giá trị hợp lệ
        const isValidOldPrice = !isNaN(oldPrice) && oldPrice > 0;
        const isValidDiscount = !isNaN(discountPercent) && discountPercent > 0;
        const isValidNewPrice = !isNaN(newPrice) && newPrice > 0;
    
        // Cập nhật nội dung
        oldPriceEl.textContent = isValidOldPrice ? formatCurrency(oldPrice) : 'Liên hệ';
        discountEl.textContent = isValidDiscount ? `${discountPercent}%` : '0%';
        newPriceEl.textContent = isValidNewPrice ? formatCurrency(newPrice) : 'Liên hệ';
        
        // Điều chỉnh hiển thị phần giảm giá
        discountEl.style.display = isValidDiscount && discountPercent > 0 ? 'inline' : 'none';
        oldPriceEl.style.display = isValidDiscount && discountPercent > 0 ? 'inline' : 'none';
    }


    function initProductDetails() {
        resetTotalAmount();
        
        // Tìm thumbnail tương ứng với sản phẩm hiện tại từ server
        const initialProduct = window.productData.currentProduct;
        const initialThumbnail = Array.from(thumbnails).find(thumb => 
            thumb.dataset.id == initialProduct.id && 
            thumb.dataset.type == initialProduct.type
        );

        if (initialThumbnail) {
            displayProductInfo(initialThumbnail);
        } else if (thumbnails.length > 0) {
            displayProductInfo(thumbnails[0]);
        }
        
        bindSizeEvents();
    }

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener("click", function() {
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

    function updateFoodProductInfo() {
        // Bỏ phần xử lý size vì food không có size
        if (sizeSection) {
            sizeSection.style.display = 'none';
        }
        
        // Tính toán giá mới
        const newPrice = currentProduct.oldPrice * (100 - currentProduct.discountPercent) / 100;
        
        // Cập nhật hiển thị giá
        updatePriceDisplay(
            currentProduct.oldPrice, 
            currentProduct.discountPercent, 
            newPrice
        );
        
        // Cập nhật số lượng
        quantityInput.value = 1;
        quantityInput.max = currentProduct.quantity || 100;
        
        updateTotalPayouts();
    }



    function updateBeverageProductInfo() {
        sizeSection.style.display = 'flex';
        sizeContainer.innerHTML = '';
        resetTotalAmount();
    
        // Kiểm tra dữ liệu sizes hợp lệ
        if (!currentProduct.sizes || !Array.isArray(currentProduct.sizes)) {
            console.error('Invalid sizes data:', currentProduct.sizes);
            sizeSection.style.display = 'none';
            updatePriceDisplay(0, 0, 0);
            return;
        }
    
        // Kiểm tra mảng sizes không rỗng
        if (currentProduct.sizes.length === 0) {
            console.error('No sizes available for this beverage product');
            sizeSection.style.display = 'none';
            updatePriceDisplay(0, 0, 0);
            return;
        }
    
        // Tạo các nút size
        currentProduct.sizes.forEach((size, index) => {
            // Kiểm tra cấu trúc size hợp lệ
            if (!size.size || !size.old_price || !size.discount_percent || !size.quantity) {
                console.error('Invalid size structure:', size);
                return;
            }
    
            const sizeButton = document.createElement('button');
            sizeButton.type = 'button';
            sizeButton.className = `size-btn ${index === 0 ? 'active' : ''}`;
            sizeButton.value = size.size;
            sizeButton.textContent = size.size;
            
            // Tính toán giá sau discount
            const discountedPrice = size.old_price * (100 - size.discount_percent) / 100;
            
            // Thêm data attributes
            sizeButton.dataset.price = discountedPrice;
            sizeButton.dataset.old = size.old_price;
            sizeButton.dataset.discount = size.discount_percent;
            sizeButton.dataset.quantity = size.quantity;
            
            // Thêm sự kiện click
            sizeButton.addEventListener('click', function() {
                document.querySelectorAll('.size-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                this.classList.add('active');
                
                updatePriceDisplay(
                    parseFloat(this.dataset.old),
                    this.dataset.discount,
                    parseFloat(this.dataset.price)
                );
                
                quantityInput.max = this.dataset.quantity;
                quantityInput.value = 1;
                
                if (selectedSizeInput) selectedSizeInput.value = this.value;
                if (checkoutSelectedSizeInput) checkoutSelectedSizeInput.value = this.value;
                
                updateTotalPayouts();
            });
            
            sizeContainer.appendChild(sizeButton);
        });
    
        // Xử lý size đầu tiên
        const firstSize = currentProduct.sizes[0];
        if (firstSize) {
            const firstDiscountedPrice = firstSize.old_price * (100 - firstSize.discount_percent) / 100;
            
            updatePriceDisplay(
                firstSize.old_price,
                firstSize.discount_percent,
                firstDiscountedPrice
            );
            
            quantityInput.value = 1;
            quantityInput.max = firstSize.quantity;
            
            if (selectedSizeInput) selectedSizeInput.value = firstSize.size;
            if (checkoutSelectedSizeInput) checkoutSelectedSizeInput.value = firstSize.size;
        }
        
        updateTotalPayouts();
    }

    // Khởi tạo khi trang được tải
    initProductDetails();
});
