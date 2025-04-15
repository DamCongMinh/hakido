const slidesData = [
    {
        title: "Phở ngon cung đình, tâm tình với bạn",
        description1: "Nước dùng đậm đà, sợi phở tươi ngon",
        description2: "Chất lượng tốt",
        image: "/img/slide.png"
    },
    {
        title: "Bánh mì giòn tan, hương vị đậm đà",
        description1: "Nhân đầy đủ, sốt đặc biệt",
        description2: "Thơm ngon khó cưỡng",
        image: "/img/slide6.png"
    },
    {
        title: "Gỏi cuốn tươi ngon, ăn là mê",
        description1: "Nguyên liệu tươi sạch, nước chấm đậm vị",
        description2: "Sức khỏe và ngon miệng",
        image: "/img/slide3.png"
    },
    {
        title: "Gỏi cuốn tươi ngon, ăn là mê",
        description1: "Nguyên liệu tươi sạch, nước chấm đậm vị",
        description2: "Sức khỏe và ngon miệng",
        image: "/img/slide6.png"
    }
];

let currentIndex = 0;
let lastDirection = "next"; // Mặc định hướng đầu tiên là "next" (phải)

const title = document.querySelector(".show-left h1");
const description1 = document.querySelector(".show-left p:nth-child(2)");
const description2 = document.querySelector(".show-left p:nth-child(3)");
const image = document.querySelector(".show-right img");
const button = document.querySelector(".show-left button");

function updateSlide(direction) {
    // Xóa class cũ trước khi thêm mới
    title.classList.remove("slide-in-left", "slide-in-right", "slide-out-left", "slide-out-right");
    description1.classList.remove("slide-in-left", "slide-in-right", "slide-out-left", "slide-out-right");
    description2.classList.remove("slide-in-left", "slide-in-right", "slide-out-left", "slide-out-right");
    image.classList.remove("slide-in-left", "slide-in-right", "slide-out-left", "slide-out-right");
    button.classList.remove("button-slide-in-left", "button-slide-in-right", "button-slide-out-left", "button-slide-out-right");

    // Xác định class animation
    const slideOutClass = direction === "next" ? "slide-out-left" : "slide-out-right";
    const slideInClass = direction === "next" ? "slide-in-left" : "slide-in-right";
    const buttonOutClass = direction === "next" ? "button-slide-out-left" : "button-slide-out-right";
    const buttonInClass = direction === "next" ? "button-slide-in-left" : "button-slide-in-right";

    // Thêm hiệu ứng biến mất
    title.classList.add(slideOutClass);
    description1.classList.add(slideOutClass);
    description2.classList.add(slideOutClass);
    image.classList.add(slideOutClass);
    button.classList.add(buttonOutClass);

    setTimeout(() => {
        // Cập nhật nội dung mới
        title.innerText = slidesData[currentIndex].title;
        description1.innerText = slidesData[currentIndex].description1;
        description2.innerText = slidesData[currentIndex].description2;
        image.src = slidesData[currentIndex].image;

        // Xóa class cũ
        title.classList.remove(slideOutClass);
        description1.classList.remove(slideOutClass);
        description2.classList.remove(slideOutClass);
        image.classList.remove(slideOutClass);
        button.classList.remove(buttonOutClass);

        // Thêm hiệu ứng xuất hiện
        title.classList.add(slideInClass);
        description1.classList.add(slideInClass);
        description2.classList.add(slideInClass);
        image.classList.add(slideInClass);
        button.classList.add(buttonInClass);

        // Cập nhật hướng cuối cùng
        lastDirection = direction;

    }, 500); // Chờ hiệu ứng cũ chạy xong rồi mới đổi nội dung
}

// Xử lý sự kiện click
document.querySelector(".next-btn").addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % slidesData.length;
    updateSlide("next");
});

document.querySelector(".prev-btn").addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + slidesData.length) % slidesData.length;
    updateSlide("prev");
});

document.addEventListener("DOMContentLoaded", function () {
    const orderButton = document.querySelector(".show-left button");

    orderButton.addEventListener("click", function (event) {
        event.preventDefault(); // Ngăn chặn chuyển trang ngay lập tức

        // Hiển thị hiệu ứng nhấn button
        orderButton.classList.add("button-clicked");

        setTimeout(() => {
            orderButton.classList.remove("button-clicked");

            // Chuyển hướng đến trang order sau khi hiệu ứng chạy xong
            window.location.href = "{{ route('list-product') }}"; // Thay bằng link thật
        }, 500);
    });
});
// hiệu ứng chữ cho slide
function resetTextAnimation(element) {
    element.style.animation = 'none';
    element.offsetHeight; // Trigger reflow để reset animation
    element.style.animation = null;
}

// Thêm reset animation vào updateSlide
document.addEventListener("DOMContentLoaded", function () {
    updateSlide("next"); // Kích hoạt hiệu ứng chữ ngay từ lần đầu load
});
function updateSlide(direction) {
    // Xóa animation cũ
    title.style.animation = "none";
    description1.style.animation = "none";
    description2.style.animation = "none";

    // Đợi một chút để reset hoàn toàn
    setTimeout(() => {
        // Cập nhật nội dung mới
        title.innerText = slidesData[currentIndex].title;
        description1.innerText = slidesData[currentIndex].description1;
        description2.innerText = slidesData[currentIndex].description2;
        image.src = slidesData[currentIndex].image;

        // Kích hoạt lại hiệu ứng chữ
        title.style.animation = "typing 3s steps(20, end) forwards";
        description1.style.animation = "typing 3s steps(20, end) forwards";
        description2.style.animation = "typing 3s steps(20, end) forwards";
    }, 100); // Khoảng dừng nhỏ để reset hoàn toàn
}







