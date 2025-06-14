document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('profile-form');
    const provinceSelect = document.getElementById('provinceSelect');
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');
    const addressDetail = document.getElementById('addressDetail');
    const addressInput = document.getElementById('address');
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const avatarUpload = document.getElementById('avatar-upload');
    const avatarPreview = document.getElementById('avatar-preview');
    const locationPreview = document.getElementById('location-preview');

    const oldProvince = provinceSelect.dataset.old;
    const oldDistrict = districtSelect.dataset.old;
    const oldWard = wardSelect.dataset.old;

    let userTouchedAddress = false;

    function updateAddress() {
        const province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const district = districtSelect.options[districtSelect.selectedIndex]?.text || '';
        const ward = wardSelect.options[wardSelect.selectedIndex]?.text || '';
        const detail = addressDetail.value.trim();
        const fullAddress = [detail, ward, district, province].filter(Boolean).join(', ');
        addressInput.value = fullAddress;

        console.log('Cập nhật địa chỉ:', { province, district, ward, detail });
    }

    async function fetchCoordinatesWithFallback(detail, ward, district, province) {
        const clean = text => text.replace(/^(TDP|Thị trấn|Xã|Phường|Huyện|Tỉnh)\s*/i, '').trim();
        const levels = [
            [clean(detail), clean(ward), clean(district), clean(province)],
            [clean(ward), clean(district), clean(province)],
            [clean(district), clean(province)],
            [clean(province)]
        ];
        for (const parts of levels) {
            const address = parts.filter(Boolean).join(', ');
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;
            try {
                const response = await fetch(url, {
                    headers: {
                        'User-Agent': 'CongMinhApp/1.0 (damminhk213@gmail.com)',
                        'Accept-Language': 'vi'
                    }
                });
                const data = await response.json();
                if (data.length > 0) {
                    return { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon), usedAddress: address };
                }
            } catch (err) {
                console.error("Lỗi khi gọi API:", err);
            }
        }
        return { lat: null, lon: null, usedAddress: null };
    }

    // Load provinces and auto-select old
    fetch('https://provinces.open-api.vn/api/p/')
        .then(res => res.json())
        .then(provinces => {
            provinces.forEach(p => {
                const option = new Option(p.name, p.code);
                if (p.code == oldProvince) option.selected = true;
                provinceSelect.add(option);
            });

            if (oldProvince) {
                fetch(`https://provinces.open-api.vn/api/p/${oldProvince}?depth=2`)
                    .then(res => res.json())
                    .then(data => {
                        data.districts.forEach(d => {
                            const option = new Option(d.name, d.code);
                            if (d.code == oldDistrict) option.selected = true;
                            districtSelect.add(option);
                        });

                        if (oldDistrict) {
                            fetch(`https://provinces.open-api.vn/api/d/${oldDistrict}?depth=2`)
                                .then(res => res.json())
                                .then(data => {
                                    data.wards.forEach(w => {
                                        const option = new Option(w.name, w.code);
                                        if (w.code == oldWard) option.selected = true;
                                        wardSelect.add(option);
                                    });
                                    updateAddress(); // Chỉ để hiển thị preview ban đầu
                                });
                        } else {
                            updateAddress();
                        }
                    });
            } else {
                updateAddress();
            }
        });

    // Đánh dấu khi người dùng thay đổi địa chỉ
    provinceSelect.addEventListener('change', function () {
        userTouchedAddress = true;
        districtSelect.innerHTML = '<option value="">--Chọn Huyện--</option>';
        wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
        updateAddress();

        const provinceId = this.value;
        if (provinceId) {
            fetch(`https://provinces.open-api.vn/api/p/${provinceId}?depth=2`)
                .then(res => res.json())
                .then(data => {
                    data.districts.forEach(d => {
                        const option = new Option(d.name, d.code);
                        districtSelect.add(option);
                    });
                });
        }
    });

    districtSelect.addEventListener('change', function () {
        userTouchedAddress = true;
        wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
        updateAddress();

        const districtId = this.value;
        if (districtId) {
            fetch(`https://provinces.open-api.vn/api/d/${districtId}?depth=2`)
                .then(res => res.json())
                .then(data => {
                    data.wards.forEach(w => {
                        const option = new Option(w.name, w.code);
                        wardSelect.add(option);
                    });
                });
        }
    });

    wardSelect.addEventListener('change', function () {
        userTouchedAddress = true;
        updateAddress();
    });

    addressDetail.addEventListener('input', function () {
        userTouchedAddress = true;
        updateAddress();
    });

    avatarUpload.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            avatarPreview.src = URL.createObjectURL(file);
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('profile-form');
        const provinceSelect = document.getElementById('provinceSelect');
        const districtSelect = document.getElementById('districtSelect');
        const wardSelect = document.getElementById('wardSelect');
        const addressDetail = document.getElementById('addressDetail');
        const addressInput = document.getElementById('address');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview');
        const locationPreview = document.getElementById('location-preview');
    
        const oldProvince = provinceSelect.dataset.old;
        const oldDistrict = districtSelect.dataset.old;
        const oldWard = wardSelect.dataset.old;
    
        let userTouchedAddress = false;
    
        function updateAddress() {
            const province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
            const district = districtSelect.options[districtSelect.selectedIndex]?.text || '';
            const ward = wardSelect.options[wardSelect.selectedIndex]?.text || '';
            const detail = addressDetail.value.trim();
            const fullAddress = [detail, ward, district, province].filter(Boolean).join(', ');
            addressInput.value = fullAddress;
    
            console.log('Cập nhật địa chỉ:', { province, district, ward, detail });
        }
    
        async function fetchCoordinatesWithFallback(detail, ward, district, province) {
            const clean = text => text.replace(/^(TDP|Thị trấn|Xã|Phường|Huyện|Tỉnh)\s*/i, '').trim();
            const levels = [
                [clean(detail), clean(ward), clean(district), clean(province)],
                [clean(ward), clean(district), clean(province)],
                [clean(district), clean(province)],
                [clean(province)]
            ];
            for (const parts of levels) {
                const address = parts.filter(Boolean).join(', ');
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;
                try {
                    const response = await fetch(url, {
                        headers: {
                            'User-Agent': 'CongMinhApp/1.0 (damminhk213@gmail.com)',
                            'Accept-Language': 'vi'
                        }
                    });
                    const data = await response.json();
                    if (data.length > 0) {
                        return { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon), usedAddress: address };
                    }
                } catch (err) {
                    console.error("Lỗi khi gọi API:", err);
                }
            }
            return { lat: null, lon: null, usedAddress: null };
        }
    
        // Load provinces and auto-select old
        fetch('https://provinces.open-api.vn/api/p/')
            .then(res => res.json())
            .then(provinces => {
                provinces.forEach(p => {
                    const option = new Option(p.name, p.code);
                    if (p.code == oldProvince) option.selected = true;
                    provinceSelect.add(option);
                });
    
                if (oldProvince) {
                    fetch(`https://provinces.open-api.vn/api/p/${oldProvince}?depth=2`)
                        .then(res => res.json())
                        .then(data => {
                            data.districts.forEach(d => {
                                const option = new Option(d.name, d.code);
                                if (d.code == oldDistrict) option.selected = true;
                                districtSelect.add(option);
                            });
    
                            if (oldDistrict) {
                                fetch(`https://provinces.open-api.vn/api/d/${oldDistrict}?depth=2`)
                                    .then(res => res.json())
                                    .then(data => {
                                        data.wards.forEach(w => {
                                            const option = new Option(w.name, w.code);
                                            if (w.code == oldWard) option.selected = true;
                                            wardSelect.add(option);
                                        });
                                        updateAddress(); // Chỉ để hiển thị preview ban đầu
                                    });
                            } else {
                                updateAddress();
                            }
                        });
                } else {
                    updateAddress();
                }
            });
    
        // Đánh dấu khi người dùng thay đổi địa chỉ
        provinceSelect.addEventListener('change', function () {
            userTouchedAddress = true;
            districtSelect.innerHTML = '<option value="">--Chọn Huyện--</option>';
            wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
            updateAddress();
    
            const provinceId = this.value;
            if (provinceId) {
                fetch(`https://provinces.open-api.vn/api/p/${provinceId}?depth=2`)
                    .then(res => res.json())
                    .then(data => {
                        data.districts.forEach(d => {
                            const option = new Option(d.name, d.code);
                            districtSelect.add(option);
                        });
                    });
            }
        });
    
        districtSelect.addEventListener('change', function () {
            userTouchedAddress = true;
            wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
            updateAddress();
    
            const districtId = this.value;
            if (districtId) {
                fetch(`https://provinces.open-api.vn/api/d/${districtId}?depth=2`)
                    .then(res => res.json())
                    .then(data => {
                        data.wards.forEach(w => {
                            const option = new Option(w.name, w.code);
                            wardSelect.add(option);
                        });
                    });
            }
        });
    
        wardSelect.addEventListener('change', function () {
            userTouchedAddress = true;
            updateAddress();
        });
    
        addressDetail.addEventListener('input', function () {
            userTouchedAddress = true;
            updateAddress();
        });
    
        avatarUpload.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                avatarPreview.src = URL.createObjectURL(file);
            }
        });
    
        
    });
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (userTouchedAddress) {
            const province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
            const district = districtSelect.options[districtSelect.selectedIndex]?.text || '';
            const ward = wardSelect.options[wardSelect.selectedIndex]?.text || '';
            const detail = addressDetail.value.trim();

            const fullAddress = [detail, ward, district, province].filter(Boolean).join(', ');
            addressInput.value = fullAddress;

            const coords = await fetchCoordinatesWithFallback(detail, ward, district, province);

            if (coords.lat && coords.lon) {
                latInput.value = coords.lat;
                lonInput.value = coords.lon;
                locationPreview.innerText = `Tọa độ: ${coords.lat}, ${coords.lon}`;
            } else {
                alert("Không thể tìm thấy tọa độ cho địa chỉ đã nhập.");
                return;
            }
        }

        form.submit();
    });
    
});
