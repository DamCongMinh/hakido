<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>

    <link rel="stylesheet" href="{{ asset('css/profile/home_info.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
</head>
<body>
    @include('layout.header')

    <div class="container">
        <h1>Thông tin cá nhân</h1>

        @php
            $info = $user->getProfileInfo();
        @endphp

        @if(session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif

        @if ($info)
        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            <!-- Avatar -->
            <div class="avatar-wrapper">
                <div class="avatar-container">
                    <img class="profile-pic" id="avatar-preview" 
                        src="{{ $info->avatar ? asset('storage/' . $info->avatar) : asset('img/default-avatar.png') }}" 
                        alt="Avatar"
                        style="cursor: pointer; width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
                    <!-- icon camera nhấp vào để chọn ảnh -->
                    <label class="upload-button" for="avatar-upload" style="cursor:pointer; position:absolute; bottom:10px; right:10px;">
                        <i class="fas fa-camera" style="font-size: 20px;"></i>
                    </label>
                </div>
                <input class="file-upload" type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;">
                <div class="user-name">{{ $info->name }}</div>
            </div>

        
            <!-- Inputs -->
            <div>
                <label for="name">Tên:</label>
                <input type="text" id="name" name="name" value="{{ old('name', $info->name) }}" required>
            </div>
        
            <div>
                <label for="phone">Số điện thoại:</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $info->phone) }}" required>
            </div>
        
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $info->email) }}" required>
            </div>
        
            <!-- Địa chỉ -->
            <label for="email">Chọn địa chỉ:</label>
            <select name="province" id="provinceSelect" data-old="{{ old('province') }}"></select>
            <select name="district" id="districtSelect" data-old="{{ old('district') }}"></select>
            <select name="ward" id="wardSelect" data-old="{{ old('ward') }}"></select>
        
            <input type="text" id="addressDetail" name="address_detail" placeholder="Địa chỉ chi tiết..." value="{{ old('address_detail') }}">

            @if ($user->role === 'customer' && $user->customer)
                <div>
                    <strong>Địa chỉ:</strong> {{ $user->customer->address }}
                </div>
            @endif
            
            @if ($user->role === 'restaurant' && $user->restaurant)
                <div>
                    <strong>Địa chỉ:</strong> {{ $user->restaurant->address }}
                </div>
            @endif
            
            @if ($user->role === 'shipper' && $user->shipper)
                <div>
                    <strong>Địa chỉ:</strong> {{ $user->shipper->address }}
                </div>
            @endif
            


            
            
        
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <input type="hidden" name="address" id="address" value="{{ old('address', $info->address) }}">

        
            <button type="submit">Cập nhật</button>
            <p id="location-preview" style="display: none; margin-top:10px;"></p>


        </form>
        
        <hr>
                
        <!-- Đổi mật khẩu -->
        <form action="{{ route('profile.change_password_form') }}" method="GET">
            <button type="submit">Đổi mật khẩu</button>
        </form>
        @endif

        <hr>

        <!-- Form xóa tài khoản -->
        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản?');">
            @csrf
            <button type="submit" style="color:red;">Xóa tài khoản</button>
        </form>       
    </div>

    @include('layout.footer')
    
    <script>
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
                            headers: { 'User-Agent': 'CongMinhApp/1.0 (damminhk213@gmail.com)', 'Accept-Language': 'vi' }
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
                                            // Gọi update sau khi đã load & chọn xong
                                            updateAddress();
                                        });
                                } else {
                                    updateAddress();
                                }
                            });
                    } else {
                        updateAddress();
                    }
                });
        
            provinceSelect.addEventListener('change', function () {
                const provinceId = this.value;
                districtSelect.innerHTML = '<option value="">--Chọn Huyện--</option>';
                wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
                updateAddress();
        
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
                const districtId = this.value;
                wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
                updateAddress();
        
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
        
            wardSelect.addEventListener('change', updateAddress);
            addressDetail.addEventListener('input', updateAddress);
        
            avatarUpload.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    avatarPreview.src = URL.createObjectURL(file);
                }
            });
        
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                updateAddress(); // đảm bảo địa chỉ đã cập nhật

                const province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
                const district = districtSelect.options[districtSelect.selectedIndex]?.text || '';
                const ward = wardSelect.options[wardSelect.selectedIndex]?.text || '';
                const detail = addressDetail.value.trim();

                if (!province || !district || !ward || !detail) {
                    alert("Vui lòng nhập đầy đủ địa chỉ.");
                    return;
                }

                const fullAddress = [detail, ward, district, province].filter(Boolean).join(', ');
                addressInput.value = fullAddress; // ✅ Ghi lại địa chỉ đầy đủ chính xác vào input

                const coords = await fetchCoordinatesWithFallback(detail, ward, district, province);

                if (coords.lat && coords.lon) {
                    latInput.value = coords.lat;
                    lonInput.value = coords.lon;
                    locationPreview.innerText = `Tọa độ: ${coords.lat}, ${coords.lon}`;
                    form.submit(); // Gửi form sau khi đã có tọa độ
                } else {
                    alert("Không thể tìm thấy tọa độ cho địa chỉ đã nhập.");
                }
            });

        });
    </script>
        
    
</body>
</html>
