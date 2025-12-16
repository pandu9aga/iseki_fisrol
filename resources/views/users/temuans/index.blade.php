@extends('users.layouts.index')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary mb-2">Temuan 5S</h4>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="m-0 font-weight-bold">Name Patrol 5S: {{ $patrol->Name_Patrol ?? '-' }}</p>
                        <p class="m-0 font-weight">Time Patrol 5S:
                            {{ $patrol->Time_Patrol ? \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d-m-Y H:i') : '-' }}
                        </p>
                    </div>
                    <div>
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addTemuanModal">Tambah
                            Temuan</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0" id="example">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Penemu</th>
                                <th>Foto Temuan</th>
                                <th>Hasil Temuan</th>
                                {{-- <th>Foto Perbaikan</th>
                                <th>Hasil Perbaikan</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($temuans as $index => $temuan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-primary"><b>{{ $temuan->nama_member }}</b></td>
                                    <td>
                                        @if ($temuan->Path_Temuan)
                                            <img src="{{ asset('uploads/' . $temuan->Path_Temuan) }}"
                                                style="max-height:100px;">
                                        @endif
                                    </td>
                                    <td>{{ $temuan->Desc_Temuan }}</td>
                                    {{-- <td>
                                        @if ($temuan->Path_Update_Temuan)
                                            <img src="{{ asset('uploads/' . $temuan->Path_Update_Temuan) }}"
                                                style="max-height:100px;">
                                        @endif
                                    </td> --}}
                                    {{-- <td>{{ $temuan->Desc_Update_Temuan }}</td> --}}
                                    <td>
                                        <button type="button" class="btn btn-success view-temuan" data-bs-toggle="modal"
                                            data-bs-target="#editTemuanModal" data-index="{{ $index }}"
                                            data-id="{{ $temuan->Id_Temuan }}"
                                            data-nama-penemu="{{ $temuan->member->Name_Member ?? '-' }}"
                                            data-foto-temuan="{{ $temuan->Path_Temuan }}"
                                            data-desc-temuan="{{ $temuan->Desc_Temuan }}">
                                            {{-- data-foto-update="{{ $temuan->Path_Update_Temuan }}"
                                            data-desc-update="{{ $temuan->Desc_Update_Temuan }}"> --}}
                                            View
                                        </button>
                                        <form action="{{ route('user_temuan.destroy', $temuan->Id_Temuan) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('user_patrol') }}" class="btn btn-outline-primary">Back to patrol</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Temuan Modal -->
    <div class="modal fade" id="addTemuanModal" tabindex="-1" aria-labelledby="addTemuanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('user_temuan.store', ['id' => $patrol->Id_Patrol]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="Id_Patrol" value="{{ $patrol->Id_Patrol }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Temuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="Id_Member" class="form-label">Penemu (Member)</label>
                            <input type="text" name="Name_Member" class="form-control"
                                value="{{ session('login_name') ?? '-' }}" required readonly>
                            <input type="hidden" name="Id_Member" class="form-control"
                                value="{{ session('login_id') ?? '' }}" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="Path_Temuan" class="form-label">Foto Temuan</label>
                            <input type="file" name="Path_Temuan" class="form-control" accept="image/*"
                                capture="environment">
                        </div>
                        <div class="mb-3">
                            <label for="Desc_Temuan" class="form-label">Deskripsi Temuan</label>
                            <textarea name="Desc_Temuan" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Temuan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Temuan Modal -->
    <div class="modal fade" id="editTemuanModal" tabindex="-1" aria-labelledby="editTemuanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editTemuanForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Temuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Foto Temuan Saat Ini -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Foto Temuan</label><br>
                                    <img id="modalFotoTemuan" src="" alt="Foto Temuan" class="img-fluid rounded"
                                        style="max-height:500px;">
                                </div>
                            </div>
                        </div>

                        <!-- Ganti Foto Temuan -->
                        <div class="mb-3">
                            <label for="Path_Temuan" class="form-label">Ganti Foto Temuan</label>
                            <input type="file" name="Path_Temuan" class="form-control" accept="image/*"
                                capture="environment">
                        </div>
                        <!-- Deskripsi Temuan -->
                        <div class="mb-3">
                            <label for="Desc_Temuan" class="form-label">Deskripsi Temuan</label>
                            <textarea name="Desc_Temuan" id="editDescTemuan" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Temuan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modalFotoTemuan = document.getElementById("modalFotoTemuan");
            const editDescTemuan = document.getElementById("editDescTemuan");
            const editTemuanForm = document.getElementById("editTemuanForm");

            document.querySelectorAll(".view-temuan").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.dataset.id;

                    // set foto temuan ke modal
                    modalFotoTemuan.src = btn.dataset.fotoTemuan ?
                        `{{ asset('uploads') }}/${btn.dataset.fotoTemuan}` :
                        `{{ asset('storage/no-img.jpeg') }}`;

                    // set deskripsi temuan
                    editDescTemuan.value = btn.dataset.descTemuan || '';

                    // set action form ke route update temuan
                    editTemuanForm.action = `{{ url('user_temuan') }}/${id}`;
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable({
                    pageLength: 100,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ]
                });
            } else {
                // kalau sudah ada, tinggal ubah pageLength saja
                var table = $('#example').DataTable();
                table.page.len(100).draw();
            }
        });
    </script>
@endsection
