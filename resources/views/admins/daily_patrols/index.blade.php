@extends('admins.layouts.index')

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Data Daily Patrol</h4>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDailyPatrolModal">Tambah
                Daily Patrol</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name Daily Patrol</th>
                            <th>Time Daily Patrol</th>
                            <th>Action</th>
                            <th>Temuan</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Name Daily Patrol</th>
                            <th>Time Daily Patrol</th>
                            <th>Action</th>
                            <th>Temuan</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($daily_patrols as $patrol)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $patrol->Name_Daily_Patrol }}</td>
                            <td>{{ \Carbon\Carbon::parse($patrol->Time_Daily_Patrol)->format('d F Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#editDailyPatrolModal" data-id="{{ $patrol->Id_Daily_Patrol }}"
                                    data-name="{{ $patrol->Name_Daily_Patrol }}"
                                    data-time="{{ \Carbon\Carbon::parse($patrol->Time_Daily_Patrol)->format('Y-m-d\TH:i') }}">
                                    Edit
                                </button>

                                <form action="{{ route('daily_patrol.destroy', $patrol->Id_Daily_Patrol) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('daily_temuan.index', ['id' => $patrol->Id_Daily_Patrol]) }}">
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
<div class="modal fade" id="addDailyPatrolModal" tabindex="-1" aria-labelledby="addDailyPatrolLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('daily_patrol.create') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addDailyPatrolLabel">Tambah Daily Patrol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Name_Daily_Patrol" class="form-label">Nama Daily Patrol</label>
                        <input type="text" class="form-control" id="Name_Daily_Patrol" name="Name_Daily_Patrol" required>
                    </div>
                    <div class="mb-3">
                        <label for="Time_Daily_Patrol" class="form-label">Tanggal Daily Patrol</label>
                        <input type="date" class="form-control" id="Time_Daily_Patrol" name="Time_Daily_Patrol" required>
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

<div class="modal fade" id="editDailyPatrolModal" tabindex="-1" aria-labelledby="editDailyPatrolLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDailyPatrolForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_daily_patrol_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editDailyPatrolLabel">Edit Daily Patrol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_daily_patrol" class="form-label">Nama Daily Patrol</label>
                        <input type="text" class="form-control" id="edit_name_daily_patrol" name="Name_Daily_Patrol" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_time_daily_patrol" class="form-label">Tanggal Daily Patrol</label>
                        <input type="date" class="form-control" id="edit_time_daily_patrol" name="Time_Daily_Patrol" required>
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
    const editModal = document.getElementById('editDailyPatrolModal');

    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const time = button.getAttribute('data-time');

        document.getElementById('edit_daily_patrol_id').value = id;
        document.getElementById('edit_name_daily_patrol').value = name;
        document.getElementById('edit_time_daily_patrol').value = time.split('T')[0];

        const form = document.getElementById('editDailyPatrolForm');
        form.action = `./daily_patrol/${id}`;
    });
</script>
@endsection
