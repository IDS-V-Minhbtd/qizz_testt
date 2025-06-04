@extends('layouts.combined')

@section('title', 'Profile Management')

@section('css')
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.css">
    <style>
        /* Custom styles for profile page */
        .content-header {
            padding: 40px 0;
            background: linear-gradient(135deg, #2c1f3b, #3a2b4f);
            min-height: calc(100vh - 60px); /* Adjust for navbar height */
            display: flex;
            justify-content: center;
            align-items: center; /* Căn giữa theo chiều dọc */
        }

        .profile-card {
            background: #3a2b4f;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            padding: 30px;
            max-width: 600px;
            width: 100%;
            color: #ffffff;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .profile-card h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #e9ecef;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #b0b0b0;
            margin-bottom: 5px;
        }

        .form-control {
            background: #4a355f;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: #5a406f;
            box-shadow: 0 0 8px rgba(94, 64, 111, 0.5);
            outline: none;
        }

        .form-control::placeholder {
            color: #a0a0a0;
        }

        #avatarPreview {
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        #avatarPreview:hover {
            transform: scale(1.05);
        }

        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #0088cc);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: linear-gradient(45deg, #bd2130, #a71d2a);
            transform: translateY(-2px);
        }

        .d-flex.gap-2 {
            justify-content: center;
            gap: 15px;
        }

        .text-danger {
            font-size: 0.9rem;
            margin-top: 5px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .content-header {
                padding: 20px 0;
            }

            .profile-card {
                padding: 20px;
                margin: 0 15px;
            }

            .form-control {
                font-size: 0.9rem;
            }

            #avatarPreview {
                width: 200px;
                height: 150px;
            }
        }
    </style>
@stop

@section('content')
<section class="content-header">
    <div class="container-fluid d-flex justify-content-center">
        <div class="profile-card">
            <h2>Edit Profile <i class="fas fa-user-edit"></i></h2>

            <!-- Hiển thị avatar -->
            <div class="form-group text-center mb-4">
                <img id="avatarPreview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('storage/avatars/default-avatar.png') }}" 
                     alt="Avatar" 
                     class="img-thumbnail rounded mx-auto d-block" 
                     style="width: 300px; height: 200px; object-fit: cover;">
            </div>

            <!-- Form cập nhật thông tin người dùng -->
            <form id="profileForm" enctype="multipart/form-data" method="POST" action="{{ route('profile.update') }}">
                @csrf
                {{-- @method('PUT') nếu bạn dùng method PUT --}}

                <div class="form-group mb-3">
                    <label for="name">Name <i class="fas fa-user"></i></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-control" 
                           value="{{ old('name', $user->name) }}" 
                           required>
                </div>

                <div class="form-group mb-3">
                    <label for="email">Email <i class="fas fa-envelope"></i></label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="form-control" 
                           value="{{ old('email', $user->email) }}" 
                           required>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="password">Password <i class="fas fa-lock"></i></label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="form-control" 
                           placeholder="Leave blank to keep the current password">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="avatar">Avatar <i class="fas fa-image"></i></label>
                    <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes <i class="fas fa-save"></i></button>
                    <button type="button" class="btn btn-danger" id="deleteAccount">Delete Account <i class="fas fa-trash"></i></button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Preview ảnh avatar khi chọn file mới
    $('#avatar').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result).addClass('animate__animated animate__fadeIn');
            }
            reader.readAsDataURL(file);
        }
    });

    // Submit form cập nhật profile bằng AJAX
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('profile.update') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Profile updated successfully!');
                    $('#name').val(response.data.name);
                    $('#email').val(response.data.email);
                    if (response.data.avatar_url) {
                        $('#avatarPreview').attr('src', response.data.avatar_url).addClass('animate__animated animate__fadeIn');
                    }
                } else {
                    alert('Update failed: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('There was an error while updating the profile.');
            }
        });
    });

    // Xóa tài khoản bằng AJAX
    $('#deleteAccount').on('click', function() {
        if (confirm('Bạn có chắc chắn muốn xóa tài khoản?')) {
            $.ajax({
                url: "{{ route('profile.delete') }}",
                type: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.href = response.redirect;
                    } else {
                        alert('Xóa tài khoản thất bại: ' + response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi xóa tài khoản.');
                }
            });
        }
    });
});
</script>
@endpush