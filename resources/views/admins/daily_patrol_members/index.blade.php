@extends('admins.layouts.index')

@section('content')
    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold text-primary">Data Daily Patrol Member</h4>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add Daily Patrol Member
                </button>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Daily Patrol</th>
                                <th>Nama User</th>
                                <th>Nama Member</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patrol_members as $pm)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pm->dailyPatrol->Name_Daily_Patrol ?? '-' }}</td>
                                    <td>{{ $pm->user->Name_User ?? '-' }}</td>
                                    <td>{{ $pm->member->Name_Member ?? '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $pm->Id_Daily_Patrol_Member }}"
                                            data-patrol="{{ $pm->Id_Daily_Patrol }}" data-user="{{ $pm->Id_User }}"
                                            data-member="{{ $pm->Id_Member }}">
                                            Edit
                                        </button>

                                        <form action="{{ route('daily_patrol_member.destroy', $pm->Id_Daily_Patrol_Member) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('daily_patrol') }}" class="btn btn-outline-primary">Back to Daily Patrol</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('daily_patrol_member.create') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Daily Patrol Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Daily Patrol</label>
                            <select name="Id_Daily_Patrol" class="form-select select2" required>
                                <option value="" disabled selected>Pilih Daily Patrol</option>
                                @foreach ($daily_patrols as $patrol)
                                    <option value="{{ $patrol->Id_Daily_Patrol }}">{{ $patrol->Name_Daily_Patrol }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih User</label>
                            <select name="Id_User" class="form-select select2" required>
                                <option value="" disabled selected>Pilih User</option>
                                @foreach ($users as $user)
                                    @if (!in_array($user->Id_User, $usedUserIds))
                                        <option value="{{ $user->Id_User }}">{{ $user->Name_User }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Member</label>
                            <select name="Id_Member" class="form-select select2" required>
                                <option value="" disabled selected>Pilih Member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->Id_Member }}">{{ $member->Name_Member }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Daily Patrol Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label">Pilih Daily Patrol</label>
                            <select name="Id_Daily_Patrol" id="edit_patrol" class="form-select select2" required>
                                <option value="" disabled>Pilih Daily Patrol</option>
                                @foreach ($daily_patrols as $patrol)
                                    <option value="{{ $patrol->Id_Daily_Patrol }}">{{ $patrol->Name_Daily_Patrol }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih User</label>
                            <select name="Id_User" id="edit_user" class="form-select select2" required>
                                <option value="" disabled>Pilih User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->Id_User }}">{{ $user->Name_User }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Member</label>
                            <select name="Id_Member" id="edit_member" class="form-select select2" required>
                                <option value="" disabled>Pilih Member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->Id_Member }}">{{ $member->Name_Member }}</option>
                                @endforeach
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const editModal = document.getElementById('editModal');
        let currentButton = null;

        const usedUserIds = @json($usedUserIds);

        $(document).ready(function() {
            $('#addModal .select2').select2({
                dropdownParent: $('#addModal'),
                width: '100%',
                placeholder: "Pilih..."
            });

            $('#editModal .select2').select2({
                dropdownParent: $('#editModal'),
                width: '100%',
                placeholder: "Pilih..."
            });
        });

        editModal.addEventListener('show.bs.modal', function(event) {
            currentButton = event.relatedTarget;

            const id = currentButton.getAttribute('data-id');
            const patrol = currentButton.getAttribute('data-patrol');
            const user = currentButton.getAttribute('data-user');
            const member = currentButton.getAttribute('data-member');

            $('#edit_id').val(id);
            $('#edit_patrol').val(patrol).trigger('change');
            $('#edit_member').val(member).trigger('change');

            const editUserSelect = document.getElementById('edit_user');
            Array.from(editUserSelect.options).forEach(opt => opt.disabled = false);

            Array.from(editUserSelect.options).forEach(opt => {
                const val = parseInt(opt.value);
                if (!isNaN(val) && val !== parseInt(user) && usedUserIds.includes(val)) {
                    opt.disabled = true;
                }
            });

            $('#edit_user').val(user).trigger('change');

            document.getElementById('editForm').action = "{{ url('daily_patrol_member/update') }}/" + id;
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            const patrol = $('#edit_patrol').val();
            const user = $('#edit_user').val();
            const member = $('#edit_member').val();

            const initialPatrol = currentButton.getAttribute('data-patrol');
            const initialUser = currentButton.getAttribute('data-user');
            const initialMember = currentButton.getAttribute('data-member');

            if (patrol === initialPatrol && user === initialUser && member === initialMember) {
                e.preventDefault();
                alert('Tidak ada perubahan data.');
            }
        });
    </script>
@endsection
