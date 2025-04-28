// Xử lý ảnh nổi bật (carousel giữa các ảnh)
document.addEventListener("DOMContentLoaded", function () {
    const imagesContainer = document.querySelector(".prominient");
    let images = Array.from(document.querySelectorAll(".prominient-product"));

    if (images.length === 0) {
        return; // Không có ảnh thì không làm gì cả
    }

    let centerIndex = Math.floor(images.length / 2);

    images.forEach((img, index) => img.dataset.index = index);

    function updateImageOrder() {
        if (images.length === 0) return; // Bảo vệ thêm trong function
        images.forEach((img, index) => {
            img.style.order = index;
            img.classList.remove("main-product");
        });
        if (images[centerIndex]) {
            images[centerIndex].classList.add("main-product");
        }
    }

    function moveImagesToCenter(clickedIndex) {
        const currentOrder = images.map(img => parseInt(img.dataset.index, 10));
        const shift = currentOrder.indexOf(clickedIndex) - centerIndex;

        if (shift > 0) {
            for (let i = 0; i < shift; i++) {
                images.push(images.shift());
            }
        } else {
            for (let i = 0; i < Math.abs(shift); i++) {
                images.unshift(images.pop());
            }
        }

        updateImageOrder();
    }

    images.forEach(img => {
        img.addEventListener("click", function () {
            const clickedIndex = parseInt(this.dataset.index, 10);
            if (clickedIndex !== parseInt(images[centerIndex].dataset.index, 10)) {
                moveImagesToCenter(clickedIndex);
            }
        });
    });

    updateImageOrder();
});


// Bộ lọc trượt (filter slide)
function toggleFilter() {
    const filterForm = document.querySelector(".filter-form"); // <<< đổi từ getElementById thành querySelector
    const priceInput = document.getElementById("price-filter");
    const priceValue = document.getElementById("price-value");

    filterForm.classList.toggle("show");

    function updatePrice() {
        priceValue.textContent = parseInt(priceInput.value).toLocaleString("vi-VN") + "đ";
    }

    priceInput.addEventListener("input", updatePrice);
    updatePrice();
}


// Lấy địa chỉ từ API tỉnh/huyện/xã
document.addEventListener("DOMContentLoaded", function () {
    const provinceFilter = document.getElementById("province-filter");
    const districtFilter = document.getElementById("district-filter");
    const wardFilter = document.getElementById("ward-filter");

    provinceFilter.addEventListener("change", function () {
        const provinceId = this.value;

        if (provinceId === "all") {
            districtFilter.innerHTML = '<option value="all">Tất cả quận huyện</option>';
            wardFilter.innerHTML = '<option value="all">Tất cả phường xã</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/p/${provinceId}?depth=2`)
            .then(response => response.json())
            .then(data => {
                districtFilter.innerHTML = '<option value="all">Tất cả quận huyện</option>';
                wardFilter.innerHTML = '<option value="all">Tất cả phường xã</option>';

                data.districts.forEach(district => {
                    const option = document.createElement("option");
                    option.value = district.code;  // chú ý dùng code
                    option.textContent = district.name;
                    districtFilter.appendChild(option);
                });
            })
            .catch(error => console.error("Lỗi khi lấy danh sách quận huyện:", error));
    });

    districtFilter.addEventListener("change", function () {
        const districtId = this.value;

        if (districtId === "all") {
            wardFilter.innerHTML = '<option value="all">Tất cả phường xã</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/d/${districtId}?depth=2`)
            .then(response => response.json())
            .then(data => {
                wardFilter.innerHTML = '<option value="all">Tất cả phường xã</option>';

                data.wards.forEach(ward => {
                    const option = document.createElement("option");
                    option.value = ward.code; // chú ý dùng code
                    option.textContent = ward.name;
                    wardFilter.appendChild(option);
                });
            })
            .catch(error => console.error("Lỗi khi lấy danh sách phường xã:", error));
    });
});



// Phân trang list-products
document.addEventListener('DOMContentLoaded', function () {
    const productsPage = 6;
    const listProducts = document.querySelector('.list-products');
    const titles = listProducts.querySelectorAll('.title-list');
    const paginationButtons = document.querySelectorAll('.page-btn');

    function showPage(page) {
        const start = (page - 1) * productsPage;
        const end = start + productsPage;

        titles.forEach((title, index) => {
            if (index >= start && index < end) {
                title.style.display = 'flex';
            } else {
                title.style.display = 'none';
            }
        });

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

    showPage(1);
});
