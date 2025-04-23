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
                <label for="address">Địa chỉ:</label>
                <input type="text" id="address" name="address" value="{{ old('address', $info->address) }}" required>
            </div>

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $info->email) }}" required>
            </div>
            
            

            @switch($user->role)
                @case('customer')
                    <div>
                        <label for="date_of_birth">Ngày sinh:</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $info->date_of_birth) }}">
                    </div>
                    @break

                @case('restaurant')
                    <div>
                        <label for="time_open">Giờ mở cửa:</label>
                        <input type="time" id="time_open" name="time_open" value="{{ old('time_open', $info->time_open) }}">
                    </div>

                    <div>
                        <label for="time_close">Giờ đóng cửa:</label>
                        <input type="time" id="time_close" name="time_close" value="{{ old('time_close', $info->time_close) }}">
                    </div>

                    <div>
                        <label for="is_active">Hoạt động:</label>
                        <select id="is_active" name="is_active">
                            <option value="1" {{ $info->is_active ? 'selected' : '' }}>Có</option>
                            <option value="0" {{ !$info->is_active ? 'selected' : '' }}>Không</option>
                        </select>
                    </div>
                    @break

                @case('shipper')
                    <div>
                        <label for="area">Khu vực hoạt động:</label>
                        <input type="text" id="area" name="area" value="{{ old('area', $info->area) }}">
                    </div>
                    @break
            @endswitch


            <button type="submit">Cập nhật</button>
            <a href="{{ route('profile.change_password_form') }}" class="change-password-btn">Đổi mật khẩu</a>
        </form>
        @endif

        <hr>

        <!-- Form xóa tài khoản -->
        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản?');">
            @csrf
            <button type="submit" style="color:red;">Xóa tài khoản</button>
        </form>
    </div>

    <!-- JS xử lý preview avatar -->
    <script>
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview');

        avatarUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                avatarPreview.src = URL.createObjectURL(file);
            }
        });
    </script>
</body>
</html>
