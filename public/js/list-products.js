document.addEventListener("DOMContentLoaded", function () {
    const imagesContainer = document.querySelector(".prominient");
    let images = Array.from(document.querySelectorAll(".prominient-product"));
    let centerIndex = Math.floor(images.length / 2);

    // Gán chỉ mục ban đầu cho từng ảnh
    images.forEach((img, index) => img.dataset.index = index);

    function updateImageOrder() {
        images.forEach((img, index) => {
            img.style.order = index; 
            img.classList.remove("main-product");
        });
        images[centerIndex].classList.add("main-product");
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
// bộ lọc
function toggleFilter() {
    let filter = document.querySelector(".products-filter");
    let filterIcon = document.querySelector(".filter-float");
    // Lấy phần tử input và span
    const priceInput = document.getElementById("price-filter");
    const priceValue = document.getElementById("price-value");
    //Lấy products-title
    let productsTitle = document.querySelector(".products-title");

    productsTitle.classList.add("active");
    // Kiểm tra xem bộ lọc có đang hiển thị không
    if (filter.classList.contains("active")) {
        filter.classList.remove("active"); // Ẩn bộ lọc
        filterIcon.style.display = "block"; // Hiển thị lại icon
        productsTitle.classList.remove("third");
        productsTitle.classList.add("active"); // Hiển thị lại title
    } else {
        filter.classList.add("active"); // Hiện bộ lọc
        filterIcon.style.display = "none"; // Ẩn icon
        productsTitle.classList.remove("active");
        productsTitle.classList.add("third");
    }

    // Hàm cập nhật giá trị hiển thị
    function updatePrice() {
        priceValue.textContent = parseInt(priceInput.value).toLocaleString("vi-VN") + "đ";
    }

    // Gán sự kiện khi kéo
    priceInput.addEventListener("input", updatePrice);

    // Cập nhật giá trị ngay khi load trang
    updatePrice();
}

// Lấy địa chỉ
document.addEventListener("DOMContentLoaded", function () {
    const provinceFilter = document.getElementById("province-filter");
    const districtFilter = document.getElementById("district-filter");
    const wardFilter = document.getElementById("ward-filter");

    // Gọi API để lấy danh sách tỉnh/thành phố
    fetch("https://provinces.open-api.vn/api/?depth=3")
        .then(response => response.json())
        .then(data => {
            data.forEach(province => {
                let option = document.createElement("option");
                option.value = province.code;
                option.textContent = province.name;
                provinceFilter.appendChild(option);
            });

            // Sự kiện thay đổi tỉnh/thành phố
            provinceFilter.addEventListener("change", function () {
                districtFilter.innerHTML = '<option value="all">Tất cả</option>'; // Reset huyện
                wardFilter.innerHTML = '<option value="all">Tất cả</option>'; // Reset xã

                let selectedProvince = data.find(p => p.code == this.value);
                if (selectedProvince) {
                    selectedProvince.districts.forEach(district => {
                        let option = document.createElement("option");
                        option.value = district.code;
                        option.textContent = district.name;
                        districtFilter.appendChild(option);
                    });
                }
            });

            // Sự kiện thay đổi huyện/quận
            districtFilter.addEventListener("change", function () {
                wardFilter.innerHTML = '<option value="all">Tất cả</option>'; // Reset xã

                let selectedProvince = data.find(p => p.code == provinceFilter.value);
                if (selectedProvince) {
                    let selectedDistrict = selectedProvince.districts.find(d => d.code == this.value);
                    if (selectedDistrict) {
                        selectedDistrict.wards.forEach(ward => {
                            let option = document.createElement("option");
                            option.value = ward.code;
                            option.textContent = ward.name;
                            wardFilter.appendChild(option);
                        });
                    }
                }
            });
        })
        .catch(error => console.error("Lỗi khi lấy dữ liệu:", error));
});

// phân trang cho list-product   

// Giả lập danh sách đánh giá (bạn có thể thay bằng dữ liệu từ server)
document.addEventListener('DOMContentLoaded', function () {
  const productsPage = 6;
  const listProducts = document.querySelector('.list-products');
  const title = listProducts.querySelectorAll('.title-list');
  const paginationButtons = document.querySelectorAll('.page-btn');

  function showPage(page) {
      const start = (page - 1) * productsPage;
      const end = start + productsPage;

      title.forEach((title, index) => {
          if (index >= start && index < end) {
              title.style.display = 'block';
          } else {
              title.style.display = 'none';
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





