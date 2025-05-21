@extends('adminlte::page')

@section('title', 'User Management')

@section('content_header')
    <h1>User Management</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">

            <a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Add User
            </a>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users List</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role_id == 1)
                                            Admin
                                        @elseif($user->role_id == 2)
                                            Member
                                        @else
                                            Unknown
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-pen"></i> Edit
                                        </a>

                                        <a href="{{ route('admin.users.results', $user->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-list"></i> Results
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@stop
