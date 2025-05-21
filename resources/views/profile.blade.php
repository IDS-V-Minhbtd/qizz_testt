@extends('adminlte::page')

@section('title', 'Profile Management')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.css">
@stop

@section('content')
<div class="container">
    <h2>Edit Profile</h2>

    <div class="form-group text-center">
        <img id="avatarPreview" src="{{ $user->avatar ? Storage::url($user->avatar) : asset('storage/avatars/default.png') }}" alt="Avatar" class="img-thumbnail rounded" style="width: 300px; height: 200px; object-fit: cover;">
    </div>

    <form id="profileForm" enctype="multipart/form-data">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="PATCH">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank if not changing">
        </div>

        <div class="form-group">
            <label for="avatar">Avatar</label>
            <input type="file" name="avatar" id="avatar" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function(){
    console.log("🚀 jQuery loaded!");

    $('#avatar').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });



    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#profileForm').on('submit', function(e) {
        e.preventDefault();  // Ngừng việc submit mặc định

        console.log("🚀 Form submitted!");

        var formData = new FormData(this);
        var userId = parseInt("{{ $user->id }}", 10);
        console.log(typeof userId, userId); // number 1
        formData.append('_method', 'PATCH'); // Chuyển đổi POST thành PATCH

        $.ajax({
            url: "/dashboard/users/updateAvatar/" + userId, // Đường dẫn cố định thay vì dùng route()
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("✅ Response từ server:", response);
                alert("Cập nhật thành công!");

                // Cập nhật hình ảnh avatar mới nếu có thay đổi
                if (response.newAvatarUrl) {
                    $('#avatarPreview').attr('src', response.newAvatarUrl);
                }
            },
            error: function(xhr, status, error) {
                console.log("❌ Lỗi:", xhr.responseText);
                alert("Lỗi: " + xhr.status + " - " + xhr.responseText);
            }
        });
    });
});
</script>
@endsection