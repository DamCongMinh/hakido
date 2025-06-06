<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị nội dung</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/content/admin_content.css') }}">
</head>
<body>
    @include('layout.header')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-container">
        {{-- Quản lý Slide --}}
        <section class="content-section">
            <h2>Danh sách Slide nội dung</h2>
            <a href="{{ route('admin.slides.create') }}" class="btn-add">+ Thêm slide mới</a>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Mô tả 1</th>
                        <th>Mô tả 2</th>
                        <th>Hình ảnh</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($slides as $slide)
                        <tr>
                            <td></td>
                            <td>{{ $slide->title }}</td>
                            <td>{{ $slide->description1 }}</td>
                            <td>{{ $slide->description2 }}</td>
                            <td>
                                <img src="{{ asset($slide->image_path) }}" width="80" style="border-radius: 8px">
                            </td>
                            <td>{{ $slide->is_active ? 'Hiển thị' : 'Ẩn' }}</td>
                            <td>
                                <!-- Lỗi có thể nằm ở đây -->
                                <a href="{{ route('admin.slides.edit', $slide->id) }}">
                                    <button class="btn btn-edit" type="button">Sửa</button>
                                </a>
                                <form action="{{ route('admin.slides.destroy', $slide->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class ="btn btn-danger" type="submit">Xoá</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </section>

        <hr style="margin: 80px 0;">

        {{-- Quản lý Danh mục --}}
        <section class="content-section">
            <h2>Danh mục</h2>
            <a href="{{ route('admin.categories.create') }}" class="btn-add">+ Thêm danh mục mới</a>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên danh mục</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td></td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->is_active ? 'Hiển thị' : 'Ẩn' }}</td>
                            <td>
                                <a class="btn btn-edit" href="{{ route('admin.categories.edit', $category->id) }}">Sửa</a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>

    @if(isset($categories) && count($categories) > 0)
    @foreach($categories as $category)
    @endforeach
    @endif

    @include('layout.footer')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".content-table").forEach(function (table) {
                const rows = table.querySelectorAll("tbody tr");
                rows.forEach((row, index) => {
                    const sttCell = row.querySelector("td:first-child");
                    if (sttCell) {
                        sttCell.textContent = index + 1;
                    }
                });
            });
        });
    </script>
    

</body>
</html>
