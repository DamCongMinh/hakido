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
                        src="{{ $info->avatar ? asset('storage/' . $info->avatar) : asset('img/shiper_avt.jpg') }}" 
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
    
    <script src="{{ url('js/address-handler.js') }}"></script>
        
    
</body>
</html>
