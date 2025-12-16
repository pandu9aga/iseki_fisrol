@extends('admins.layouts.index')
@section('content')
    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary">Data User</h4>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add User</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Password</th>
                                <th>Type User</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Password</th>
                                <th>Type User</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->Username_User }}</td>
                                    <td>{{ $user->Name_User }}</td>
                                    <td>{{ $user->Password_User }}</td>
                                    <td>{{ $user->type_user->Name_Type_User ?? '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal" data-id="{{ $user->Id_User }}"
                                            data-username="{{ $user->Username_User }}" data-name="{{ $user->Name_User }}"
                                            data-password="{{ $user->Password_User }}"
                                            data-type_user="{{ $user->Id_Type_User }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('user.destroy', $user->Id_User) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('modal')
    <!-- Modal Tambah User -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('user.create') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="Username_User" class="form-label">Username</label>
                            <input type="text" class="form-control" id="Username_User" name="Username_User"
                                placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <label for="Name_User" class="form-label">Name</label>
                            <input type="text" class="form-control" id="Name_User" name="Name_User" placeholder="Name">
                        </div>
                        <div class="mb-3">
                            <label for="Password_User" class="form-label">Password</label>
                            <input type="text" class="form-control" id="Password_User" name="Password_User"
                                placeholder="Password">
                        </div>
                        <div class="mb-3">
                            <label for="Id_Type_User" class="form-label">Type User</label>
                            <select class="form-select" id="Id_Type_User" name="Id_Type_User" required>
                                <option value="" disabled selected>Type User</option>
                                <option value="1">Admin</option>
                                <option value="2">User</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editUserForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_user_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_user_id" name="id">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="Username_User" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_name_user" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name_user" name="Name_User" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password</label>
                            <input type="text" class="form-control" id="edit_password" name="Password_User">
                        </div>
                        <div class="mb-3">
                            <label for="edit_type_user" class="form-label">Type User</label>
                            <select class="form-select" id="edit_type_user" name="Id_Type_User" required>
                                <option value="1">Admin</option>
                                <option value="2">User</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const editModal = document.getElementById('editUserModal');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const id = button.getAttribute('data-id');
            const username = button.getAttribute('data-username');
            const name = button.getAttribute('data-name');
            const password = button.getAttribute('data-password');
            const typeUser = button.getAttribute('data-type_user');

            // Isi nilai ke input form edit
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_name_user').value = name;
            document.getElementById('edit_password').value = password;
            document.getElementById('edit_type_user').value = typeUser;

            // Ubah action form
            const form = document.getElementById('editUserForm');
            form.action = `./data_user/${id}`;
        });
    </script>
@endsection
