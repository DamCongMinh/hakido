document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('provinceSelect');
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');

    // Load tỉnh/thành
    function loadProvinces() {
        fetch('https://provinces.open-api.vn/api/p/')
            .then(res => res.json())
            .then(provinces => {
                provinceSelect.innerHTML = '<option value="">--Chọn Tỉnh/TP--</option>';
                provinces.forEach(p => {
                    provinceSelect.add(new Option(p.name, p.code));
                });
            });
    }

    // Xử lý khi chọn tỉnh
    provinceSelect?.addEventListener('change', function() {
        const provinceId = this.value;
        districtSelect.innerHTML = '<option value="">--Chọn Quận/Huyện--</option>';
        wardSelect.innerHTML = '<option value="">--Chọn Phường/Xã--</option>';

        if (provinceId) {
            fetch(`https://provinces.open-api.vn/api/p/${provinceId}?depth=2`)
                .then(res => res.json())
                .then(province => {
                    province.districts.forEach(d => {
                        districtSelect.add(new Option(d.name, d.code));
                    });
                });
        }
    });

    // Xử lý khi chọn quận/huyện
    districtSelect?.addEventListener('change', function() {
        const districtId = this.value;
        wardSelect.innerHTML = '<option value="">--Chọn Phường/Xã--</option>';

        if (districtId) {
            fetch(`https://provinces.open-api.vn/api/d/${districtId}?depth=2`)
                .then(res => res.json())
                .then(district => {
                    district.wards.forEach(w => {
                        wardSelect.add(new Option(w.name, w.code));
                    });
                });
        }
    });

    // Khởi tạo ban đầu
    loadProvinces();
});