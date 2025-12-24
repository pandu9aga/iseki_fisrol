@extends('admins.layouts.index')
@section('content')
    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary mb-2">Temuan 5S</h4>

                <!-- Baris info patrol + tombol -->
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="m-0 font-weight-bold">
                            Name Patrol 5S: {{ $patrol->Name_Patrol ?? '-' }}
                        </p>
                        <p class="m-0 font-weight">
                            Time Patrol 5S:
                            {{ $patrol->Time_Patrol ? \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d-m-Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        {{-- <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addTemuanModal">
                            Tambah Temuan
                        </button> --}}
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('temuan.export', $patrol->Id_Patrol) }}" class="btn btn-success">
                            <i class="fas fa-file-powerpoint me-1"></i> Export ke PPT
                        </a> {{-- <button class="btn btn-pink">Tambah Temuan</button> --}}
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
                                <th>Foto Perbaikan</th>
                                <th>Hasil Perbaikan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($temuans as $index => $temuan)
                                <tr class="{{ $temuan->Status_Temuan == 'Done' ? 'done-row' : '' }}">
                                    <td>
                                        {{ $loop->iteration }}</td>
                                    <td class="text-primary">
                                        <b>
                                            {{ $temuan->user && $temuan->user->Id_Type_User == 1
                                                ? $temuan->user->Name_User ?? '-'
                                                : $temuan->member->nama ?? '-' }}
                                        </b>
                                    </td>
                                    <td>
                                        @if ($temuan->Path_Temuan)
                                            <img src="{{ asset('uploads/' . $temuan->Path_Temuan) }}"
                                                style="max-height:100px;">
                                        @endif
                                    </td>
                                    <td>
                                        {{ $temuan->Desc_Temuan }}</td>
                                    <td>
                                        @if ($temuan->Path_Update_Temuan)
                                            <img src="{{ asset('uploads/' . $temuan->Path_Update_Temuan) }}"
                                                style="max-height:100px;">
                                        @endif
                                    </td>
                                    <td>
                                        {{ $temuan->Desc_Update_Temuan }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success view-temuan" data-bs-toggle="modal"
                                            data-bs-target="#viewTemuanModal" data-index="{{ $index }}"
                                            data-id="{{ $temuan->Id_Temuan }}"
                                            data-nama-penemu="{{ $temuan->user && $temuan->user->Id_Type_User == 1
                                                ? $temuan->user->Name_User ?? '-'
                                                : $temuan->member->nama ?? '-' }}"
                                            data-foto-temuan="{{ $temuan->Path_Temuan }}"
                                            data-desc-temuan="{{ $temuan->Desc_Temuan }}"
                                            data-foto-update="{{ $temuan->Path_Update_Temuan }}"
                                            data-desc-update="{{ $temuan->Desc_Update_Temuan }}"
                                            data-status="{{ $temuan->Status_Temuan }}">
                                            View
                                        </button>
                                        <form action="{{ route('temuan.destroy', $temuan->Id_Temuan) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('patrol') }}" class="btn btn-outline-primary">Back to patrol</a>
            </div>
        </div>
    </div>

    <!-- Modal Add Temuan -->
    <div class="modal fade" id="addTemuanModal" tabindex="-1" aria-labelledby="addTemuanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('temuan.store', ['id' => $patrol->Id_Patrol]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="Id_Patrol" value="{{ $patrol->Id_Patrol }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTemuanLabel">Tambah Temuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Foto Temuan -->
                        <div class="mb-3">
                            <label for="Path_Temuan" class="form-label">Foto Temuan</label>
                            <input type="file" name="Path_Temuan" id="Path_Temuan" class="form-control" accept="image/*"
                                capture="environment" onchange="previewTemuan(event)">
                            <img id="previewTemuan" src="" alt="Preview Foto" class="img-fluid mt-2 d-none"
                                style="max-height:200px;">
                        </div>
                        <!-- Deskripsi Temuan -->
                        <div class="mb-3">
                            <label for="Desc_Temuan" class="form-label">Deskripsi Temuan</label>
                            <textarea name="Desc_Temuan" id="Desc_Temuan" rows="3" class="form-control"></textarea>
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

    <!-- Script Preview Gambar -->
    <script>
        function previewTemuan(event) {
            const preview = document.getElementById('previewTemuan');
            const file = event.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            } else {
                preview.src = '';
                preview.classList.add('d-none');
            }
        }
    </script>

    <!-- View Temuan Modal -->
    <div class="modal fade" id="viewTemuanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl"
            style="width: 100vw;max-width: none;height: 100%;margin-left: 10px;margin-right: 10px;">
            <div class="modal-content">
                <div class="modal-header px-5 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">Detail Temuan</h5>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm me-2" id="prevTemuan">&laquo;
                            Prev</button>
                        <button type="button" class="btn btn-outline-primary btn-sm me-5" id="nextTemuan">Next
                            &raquo;</button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-0">
                                <label class="form-label">Penemu :</label>
                                <span id="modalNamaPenemu" class="fw-bold text-primary"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-0">
                                <form id="statusForm" action="{{ route('temuan.updateStatus', 0) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="Id_Temuan" id="statusTemuanId">
                                    <div class="d-flex align-items-center">
                                        <label class="form-label me-3 mb-0">Status :</label>
                                        <div class="form-check form-switch mb-0 ms-2">
                                            <input class="form-check-input" type="checkbox" id="statusSwitchInput"
                                                name="Status_Temuan" value="Done">
                                            <label class="form-check-label" for="statusSwitchInput">Selesai</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Foto Temuan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label">Foto Temuan</label>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="rotateImage('modalFotoTemuan', -90)">↺</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="rotateImage('modalFotoTemuan', 90)">↻</button>
                                    </div>
                                </div>
                                <img id="modalFotoTemuan" src="" alt="Foto Temuan"
                                    class="img-fluid rounded rotateable"
                                    style="max-height:500px; transition: transform 0.3s;">
                            </div>
                        </div>

                        <!-- Foto Perbaikan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label">Foto Perbaikan</label>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="rotateImage('modalFotoUpdate', -90)">↺</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="rotateImage('modalFotoUpdate', 90)">↻</button>
                                    </div>
                                </div>
                                <img id="modalFotoUpdate" src="" alt="Foto Perbaikan"
                                    class="img-fluid rounded rotateable"
                                    style="max-height:500px; transition: transform 0.3s;">
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Temuan :</label>
                                <p id="modalDescTemuan"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label">Perbaikan :</label>
                                <p id="modalDescUpdate"></p>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <!-- Form Update -->
                    <form action="{{ route('temuan.update', $temuan->Id_Temuan ?? 0) }}" method="POST"
                        enctype="multipart/form-data" id="updateTemuanForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="Path_Update_Temuan" class="form-label">Foto Perbaikan</label>
                            <input type="file" name="Path_Update_Temuan" id="Path_Update_Temuan" class="form-control"
                                accept="image/*" capture="environment">
                        </div>
                        <div class="mb-3">
                            <label for="Desc_Update_Temuan" class="form-label">Deskripsi Perbaikan</label>
                            <textarea name="Desc_Update_Temuan" id="Desc_Update_Temuan" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Perbaikan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .done-row td {
            background-color: #4DA8DA !important;
            color: white !important;
        }
    </style>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const viewModal = document.getElementById("viewTemuanModal");
            const statusTemuanId = document.getElementById("statusTemuanId");
            const statusSwitchInput = document.getElementById("statusSwitchInput");
            const statusForm = document.getElementById("statusForm");
            const updateTemuanForm = document.getElementById("updateTemuanForm");

            let temuanButtons = Array.from(document.querySelectorAll(".view-temuan"));
            let currentIndex = -1;

            // Saat modal dibuka
            viewModal.addEventListener("show.bs.modal", function(event) {
                const button = event.relatedTarget;
                if (!button) return;

                currentIndex = temuanButtons.indexOf(button);
                loadTemuanData(button);
            });

            // Auto submit kalau switch status diubah
            statusSwitchInput.addEventListener("change", () => {
                const formData = new FormData(statusForm);

                fetch(statusForm.action, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const idTemuan = formData.get('Id_Temuan');

                            // cari row terkait
                            const rowEl = document.querySelector(
                                `#example button[data-id="${idTemuan}"]`).closest("tr");

                            // update dataset status pada tombol "View" supaya konsisten saat redraw
                            const viewBtn = rowEl.querySelector('button.view-temuan');
                            viewBtn.dataset.status = (statusSwitchInput.checked ? 'Done' : 'Pending');

                            // tambah/hapus class biru sekarang juga (tanpa nunggu redraw)
                            if (statusSwitchInput.checked) {
                                rowEl.classList.add('done-row');
                            } else {
                                rowEl.classList.remove('done-row');
                            }

                            // (opsional) paksa DataTables re-apply untuk jaga-jaga
                            const table = $('#example').DataTable();
                            table.row(rowEl).invalidate().draw(false);

                            alert("Status berhasil diperbarui!");
                        } else {
                            alert("Gagal update status");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Error saat update status");
                    });
            });

            // Tombol Next
            document.getElementById("nextTemuan").addEventListener("click", () => {
                if (currentIndex < temuanButtons.length - 1) {
                    currentIndex++;
                    loadTemuanData(temuanButtons[currentIndex]);
                }
            });

            // Tombol Prev
            document.getElementById("prevTemuan").addEventListener("click", () => {
                if (currentIndex > 0) {
                    currentIndex--;
                    loadTemuanData(temuanButtons[currentIndex]);
                }
            });

            // Fungsi isi data ke modal
            function loadTemuanData(btn) {
                const id = btn.dataset.id;

                statusTemuanId.value = id;
                document.getElementById("modalNamaPenemu").textContent = btn.dataset.namaPenemu || "-";
                document.getElementById("modalDescTemuan").textContent = btn.dataset.descTemuan || "-";
                document.getElementById("modalDescUpdate").textContent = btn.dataset.descUpdate || "-";

                document.getElementById("modalFotoTemuan").src = btn.dataset.fotoTemuan ?
                    `../uploads/${btn.dataset.fotoTemuan}` :
                    "../storage/no-img.jpeg";

                document.getElementById("modalFotoUpdate").src = btn.dataset.fotoUpdate ?
                    `../uploads/${btn.dataset.fotoUpdate}` :
                    "../storage/no-img.jpeg";

                // Switch status
                statusSwitchInput.checked = (btn.dataset.status === "Done");

                // Update action form
                updateTemuanForm.action = `../temuan/${id}`;
                statusForm.action = `../temuan/${id}/status`;
            }
        });
    </script>

    <script>
        function previewTemuan(event) {
            const preview = document.getElementById('previewTemuan');
            const file = event.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            } else {
                preview.src = '';
                preview.classList.add('d-none');
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            var table;

            if ($.fn.DataTable.isDataTable('#example')) {
                table = $('#example').DataTable();
                table.page.len(100).draw(); // ✅ paksa default 100
            } else {
                table = $('#example').DataTable({
                    pageLength: 100,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ]
                });
            }

            // fungsi untuk highlight row status Done
            function applyRowHighlight() {
                $('#example tbody tr').each(function() {
                    const btn = this.querySelector('button.view-temuan');
                    if (!btn) return;
                    if ((btn.dataset.status || '').toLowerCase() === 'done') {
                        this.classList.add('done-row');
                    } else {
                        this.classList.remove('done-row');
                    }
                });
            }

            applyRowHighlight();
            table.on('draw', applyRowHighlight);
        });
    </script>

    <script>
        // Fungsi baca rotasi dari localStorage berdasarkan kunci unik
        function getRotationFromStorage(key) {
            const saved = localStorage.getItem(key);
            return saved ? parseInt(saved, 10) : 0;
        }

        // Fungsi simpan rotasi ke localStorage
        function saveRotationToStorage(key, degrees) {
            localStorage.setItem(key, degrees.toString());
        }

        function rotateImage(imgId, degrees) {
            const img = document.getElementById(imgId);
            if (!img) return;

            // Ambil ID temuan dari form atau hidden input
            const temuanId = document.getElementById('statusTemuanId')?.value;
            if (!temuanId) return;

            const storageKey = imgId === 'modalFotoTemuan' ?
                `rotate_temuan_${temuanId}` :
                `rotate_perbaikan_${temuanId}`;

            let currentRotation = getRotationFromStorage(storageKey);
            currentRotation += degrees;

            // Normalisasi agar tetap di rentang 0-359 (opsional)
            currentRotation = ((currentRotation % 360) + 360) % 360;

            // Simpan ke localStorage
            saveRotationToStorage(storageKey, currentRotation);

            // Terapkan transform
            img.style.transform = `rotate(${currentRotation}deg)`;
        }

        // Saat modal dibuka, terapkan rotasi yang tersimpan
        document.getElementById('viewTemuanModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const temuanId = button.dataset.id;

            // Terapkan rotasi untuk Foto Temuan
            const rotTemuan = getRotationFromStorage(`rotate_temuan_${temuanId}`);
            document.getElementById('modalFotoTemuan').style.transform = `rotate(${rotTemuan}deg)`;

            // Terapkan rotasi untuk Foto Perbaikan
            const rotPerbaikan = getRotationFromStorage(`rotate_perbaikan_${temuanId}`);
            document.getElementById('modalFotoUpdate').style.transform = `rotate(${rotPerbaikan}deg)`;
        });

        // Opsional: Reset localStorage jika perlu (misalnya tombol "Reset Rotation")
        // Tidak perlu reset otomatis saat modal ditutup!
    </script>
@endsection
