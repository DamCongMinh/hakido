<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>

    <link rel="stylesheet" href="{{ asset('css/profile/home_info.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <!-- Avatar khu vực -->
            <div class="avatar-wrapper">
                <div class="avatar-container">
                    <img class="profile-pic" id="avatar-preview" 
                         src="{{ $info->avatar ? asset('storage/' . $info->avatar) : asset('img/default-avatar.png') }}" 
                         alt="Avatar">
                </div>

                <label class="upload-button" for="avatar-upload">
                    <i class="fas fa-camera"></i>
                </label>

                <input class="file-upload" type="file" id="avatar-upload" name="avatar" accept="image/*">

                <div class="user-name">{{ $info->name }}</div>
            </div>

            <!-- Các input -->
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

            <select name="province" id="provinceSelect">
                <option value="">--Chọn Tỉnh--</option>
            </select>
        
            <select name="district" id="districtSelect">
                <option value="">--Chọn Huyện--</option>
            </select>
        
            <select name="ward" id="wardSelect">
                <option value="">--Chọn Xã--</option>
            </select>
        
            <input type="text" id="addressDetail" placeholder="Địa chỉ chi tiết (số nhà, thôn xóm...)" value="{{ old('address', $info->address) }}">
        
            <!-- Cái này mới là cái thực lưu xuống DB -->
            <input type="hidden" name="address" id="address" value="{{ old('address', $info->address) }}">


            <button type="submit">Cập nhật</button>
        </form>
        <hr>
        <!-- JS xử lý preview avatar -->
    <script>

        // Lấy danh sách tỉnh từ API ngoài
       document.addEventListener('DOMContentLoaded', function () {
            const provinceSelect = document.getElementById('provinceSelect');
            const districtSelect = document.getElementById('districtSelect');
            const wardSelect = document.getElementById('wardSelect');
            const addressDetail = document.getElementById('addressDetail');
            const addressInput = document.getElementById('address');

            function updateAddress() {
                const province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
                const district = districtSelect.options[districtSelect.selectedIndex]?.text || '';
                const ward = wardSelect.options[wardSelect.selectedIndex]?.text || '';
                const detail = addressDetail.value.trim();

                // Gộp lại thành 1 chuỗi address
                const fullAddress = [detail, ward, district, province].filter(Boolean).join(', ');
                addressInput.value = fullAddress;
            }

            fetch('https://provinces.open-api.vn/api/p/')
                .then(response => response.json())
                .then(data => {
                    data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.code;
                        option.text = province.name;
                        provinceSelect.appendChild(option);
                    });
                });

            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                districtSelect.innerHTML = '<option value="">--Chọn Huyện--</option>';
                wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
                updateAddress();

                if (provinceId) {
                    fetch(`https://provinces.open-api.vn/api/p/${provinceId}?depth=2`)
                        .then(response => response.json())
                        .then(data => {
                            data.districts.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.code;
                                option.text = district.name;
                                districtSelect.appendChild(option);
                            });
                        });
                }
            });

            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                wardSelect.innerHTML = '<option value="">--Chọn Xã--</option>';
                updateAddress();

                if (districtId) {
                    fetch(`https://provinces.open-api.vn/api/d/${districtId}?depth=2`)
                        .then(response => response.json())
                        .then(data => {
                            data.wards.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.code;
                                option.text = ward.name;
                                wardSelect.appendChild(option);
                            });
                        });
                }
            });

            wardSelect.addEventListener('change', updateAddress);
            addressDetail.addEventListener('input', updateAddress);
        });


        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview');

        avatarUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                avatarPreview.src = URL.createObjectURL(file);
            }
        });


    </script>
        
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

    
</body>
</html>
