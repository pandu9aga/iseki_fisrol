@extends('admins.layouts.index')

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Data Patrol</h4>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPatrolModal">Tambah
                Patrol</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name Patrol</th>
                            <th>Time Patrol</th>
                            <th>Action</th>
                            <th>Temuan</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Name Patrol</th>
                            <th>Time Patrol</th>
                            <th>Action</th>
                            <th>Temuan</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($patrols as $patrol)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $patrol->Name_Patrol }}</td>
                            <td>{{ \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d F Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#editPatrolModal" data-id="{{ $patrol->Id_Patrol }}"
                                    data-name="{{ $patrol->Name_Patrol }}"
                                    data-time="{{ \Carbon\Carbon::parse($patrol->Time_Patrol)->format('Y-m-d\TH:i') }}">
                                    Edit
                                </button>

                                <form action="{{ route('patrol.destroy', $patrol->Id_Patrol) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                                </form>

                                {{-- <a href="{{ route('patrol_member.index', ['id' => $patrol->Id_Patrol]) }}"
                                class="btn btn-outline-info">
                                View Member
                                </a> --}}
                            </td>
                            <td>
                                <a href="{{ route('temuan.index', ['id' => $patrol->Id_Patrol]) }}">
                                    Temuan
                                </a>
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
<!-- Modal Tambah Patrol -->
<div class="modal fade" id="addPatrolModal" tabindex="-1" aria-labelledby="addPatrolLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('patrol.create') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPatrolLabel">Tambah Patrol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Name_Patrol" class="form-label">Nama Patrol</label>
                        <input type="text" class="form-control" id="Name_Patrol" name="Name_Patrol" required>
                    </div>
                    <div class="mb-3">
                        <label for="Time_Patrol" class="form-label">Tanggal Patrol</label>
                        <input type="date" class="form-control" id="Time_Patrol" name="Time_Patrol" required>
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

<!-- Modal Edit Patrol -->
<!-- Modal Edit Patrol -->
<div class="modal fade" id="editPatrolModal" tabindex="-1" aria-labelledby="editPatrolLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPatrolForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_patrol_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editPatrolLabel">Edit Patrol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_patrol" class="form-label">Nama Patrol</label>
                        <input type="text" class="form-control" id="edit_name_patrol" name="Name_Patrol" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_time_patrol" class="form-label">Tanggal Patrol</label>
                        <input type="date" class="form-control" id="edit_time_patrol" name="Time_Patrol" required>
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
    const editModal = document.getElementById('editPatrolModal');

    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const time = button.getAttribute('data-time');

        document.getElementById('edit_patrol_id').value = id;
        document.getElementById('edit_name_patrol').value = name;
        // Format tanggal saja YYYY-MM-DD
        document.getElementById('edit_time_patrol').value = time.split('T')[0];

        const form = document.getElementById('editPatrolForm');
        form.action = `./patrol/${id}`;
    });
</script>
@endsection