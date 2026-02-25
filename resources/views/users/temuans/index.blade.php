@extends('users.layouts.index')

<!-- Tambahkan CSS TUI di luar <style> -->
<link href="{{ asset('assets/css/tui-image-editor.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/tui-color-picker.css') }}" rel="stylesheet">

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header py-3 bg-white d-flex flex-column align-items-start"
            style="border-bottom: 1px solid #f0e6ea;">
            <h4 class="m-0 font-weight-bold" style="color: #e91e63;">Temuan Patrol 5s</h4>
            <div class="d-flex align-items-center justify-content-between w-100 mt-3">
                <div>
                    <p class="m-0 font-weight-bold text-dark">
                        Name Patrol : <span class="text-pink">{{ $patrol->Name_Patrol ?? '-' }}</span>
                    </p>
                    <p class="m-0 text-muted">
                        Time Patrol :
                        {{ $patrol->Time_Patrol ? \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d F Y ') : '-' }}
                    </p>
                </div>
            </div>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addTemuanModal">
                <i class="fas fa-plus me-1"></i> Tambah Temuan
            </button>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-bordered datatable" style="font-size: 0.95rem; width: 100%;">
                    <thead class="bg-light-pink text-pink">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Penemu</th>
                            <th class="py-3 px-4">Foto Temuan</th>
                            <th class="py-3 px-4">Hasil Temuan</th>
                            <th class="py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($temuans as $index => $temuan)
                        <tr>
                            <td class="py-3 px-4">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 text-pink font-weight-bold">{{ $temuan->nama_member }}</td>
                            <td class="py-3 px-4">
                                @if ($temuan->Path_Temuan)
                                <img src="{{ asset('uploads/' . $temuan->Path_Temuan) }}" class="img-thumbnail"
                                    style="max-height:80px; object-fit: cover;">
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ $temuan->Desc_Temuan }}</td>
                            <td class="py-3 px-4">
                                <button type="button" class="btn btn-sm btn-outline-success me-1 view-temuan"
                                    data-bs-toggle="modal" data-bs-target="#editTemuanModal"
                                    data-id="{{ $temuan->Id_Temuan }}" data-foto-temuan="{{ $temuan->Path_Temuan }}"
                                    data-desc-temuan="{{ $temuan->Desc_Temuan }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form action="{{ route('user_temuan.destroy', $temuan->Id_Temuan) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-temuan"
                                        data-id="{{ $temuan->Id_Temuan }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 text-end">
                <a href="{{ route('user_patrol') }}" class="btn btn-outline-primary">Back to patrol</a>
            </div>
        </div>
    </div>
</div>

<!-- Add Temuan Modal -->
<div class="modal fade" id="addTemuanModal" tabindex="-1" aria-labelledby="addTemuanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0"
            style="border-radius: 12px; box-shadow: 0 4px 20px rgba(233, 30, 99, 0.15);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #e91e63;">Tambah Temuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="form-label text-muted small">Penemu (Member)</label>
                    <input type="text" class="form-control" value="{{ session('login_name') ?? '-' }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Foto Temuan</label>
                    <input type="file" id="addFotoInput" class="form-control" accept="image/*" capture="environment"
                        required>
                </div>
                <div class="mb-3 d-none" id="previewFotoSection">
                    <label class="form-label text-muted small">Pratinjau Foto</label><br>
                    <img id="previewFoto" src="" alt="Pratinjau" class="img-fluid rounded"
                        style="max-height:200px; object-fit: cover;">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="editPreviewFotoBtn">
                            <i class="fas fa-edit"></i> Edit Foto
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Deskripsi Temuan</label>
                    <textarea id="addDescInput" rows="3" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary d-none" id="finalSaveTemuanBtn">Simpan Temuan</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Temuan Modal -->
<div class="modal fade" id="editTemuanModal" tabindex="-1" aria-labelledby="editTemuanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0"
            style="border-radius: 12px; box-shadow: 0 4px 20px rgba(233, 30, 99, 0.15);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #e91e63;">Edit Temuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label text-muted small">Foto Temuan Saat Ini</label><br>
                        <img id="modalFotoTemuan" src="" alt="Foto Temuan" class="img-fluid rounded"
                            style="max-height:300px; object-fit: cover;">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="editFotoBtn">
                                <i class="fas fa-edit"></i> Edit Foto
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="replaceFotoBtn">
                                <i class="fas fa-upload"></i> Ganti Foto
                            </button>
                        </div>
                        <input type="file" id="editNewFotoInput" class="form-control mt-2 d-none"
                            accept="image/*">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Deskripsi Temuan</label>
                    <textarea id="editDescTemuan" rows="3" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-outline-primary" id="updateTemuanBtn">Update Temuan</button>
            </div>
        </div>
    </div>
</div>

<!-- TUI Image Editor Modal -->
<div class="modal fade" id="tuiEditorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Foto Temuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-column" style="min-height:0;">
                <div id="custom-tui-toolbar"
                    class="p-2 border-bottom d-flex flex-wrap justify-content-start align-items-center gap-2 bg-light">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-tool="draw"><i
                                class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="rect"><i
                                class="fas fa-square"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="arrow"><i
                                class="fas fa-arrow-right"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="rotate"><i
                                class="fas fa-rotate"></i></button>
                    </div>
                    <div class="vr mx-3 d-none d-md-block"></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-tool="undo"><i
                                class="fas fa-undo"></i></button>
                        <button type="button" class="btn btn-outline-secondary" data-tool="redo"><i
                                class="fas fa-redo"></i></button>
                        <button type="button" class="btn btn-outline-danger" data-tool="delete"><i
                                class="fas fa-trash"></i></button>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-success" id="tui-save-btn">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
                <div id="tui-editor-container" class="d-flex justify-content-center align-items-center bg-dark-subtle"
                    style="flex:1; overflow:hidden;">
                    <div id="tui-image-editor" style="width:96%; height:96%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
    .btn-pink {
        background: #e91e63;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
    }

    .btn-pink:hover {
        background: #d81b60;
        color: white;
    }

    .text-pink {
        color: #e91e63 !important;
    }

    .bg-light-pink {
        background-color: #fdf2f8 !important;
    }

    .card {
        border-radius: 12px;
    }

    .img-thumbnail {
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .table thead th {
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* TUI Editor */
    #tui-editor-container {
        height: calc(100vh - 120px);
    }

    .tie-btn-history,
    .tie-btn-reset,
    .tie-btn-deleteAll,
    .tie-color-fill,
    .triangle,
    .circle,
    .tie-icon-add-button,
    .tui-image-editor-partition {
        display: none !important;
    }
</style>
@endsection

@section('script')
<!-- DataTables -->
<link href="{{ asset('assets/css/dataTables.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">

{{-- JS --}}
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

<script src="{{ asset('assets/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>

<script src="{{ asset('assets/js/tui-code-snippet.js') }}"></script>
<script src="{{ asset('assets/js/tui-color-picker.js') }}"></script>
<script src="{{ asset('assets/js/fabric.min.js') }}"></script>
<script src="{{ asset('assets/js/tui-image-editor.js') }}"></script>
<script>
    $(document).ready(function() {
        let tuiEditor = null;
        let currentImageUrl = null;
        let currentTemuanId = null;
        let editedImageData = null;
        let newFileImageData = null;

        // === Fungsi Resize Gambar (tetap sama) ===
        function resizeImage(file, maxWidth = 1280, maxHeight = 1280, quality = 0.8) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                const reader = new FileReader();
                reader.onload = e => {
                    img.onload = () => {
                        let ratio = Math.min(maxWidth / img.width, maxHeight / img.height, 1);
                        const canvas = document.createElement('canvas');
                        canvas.width = Math.round(img.width * ratio);
                        canvas.height = Math.round(img.height * ratio);
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        canvas.toBlob(blob => resolve(blob), 'image/jpeg', quality);
                    };
                    img.onerror = reject;
                    img.src = e.target.result;
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }

        // === TUI Editor (tetap sama) ===
        function openTuiEditor(imageUrl, isAddMode = false) {
            currentImageUrl = imageUrl;
            const modal = new bootstrap.Modal(document.getElementById('tuiEditorModal'));
            modal.show();

            modal._element.addEventListener('shown.bs.modal', () => {
                const container = document.getElementById('tui-image-editor');
                container.innerHTML = '';
                tuiEditor = new tui.ImageEditor(container, {
                    usageStatistics: false,
                    cssMaxWidth: 2000,
                    cssMaxHeight: 2000,
                });

                tuiEditor.loadImageFromURL(imageUrl, 'uploaded').then(() => {
                    const canvas = tuiEditor._graphics.getCanvas();
                    const img = canvas.getObjects()[0];
                    if (img) {
                        img.set({
                            originX: 'center',
                            originY: 'center',
                            left: canvas.getWidth() / 2,
                            top: canvas.getHeight() / 2
                        });
                        canvas.centerObject(img);
                        canvas.renderAll();
                    }
                });
            }, {
                once: true
            });
        }

        $(document).on('click', '[data-tool]', function(e) {
            if (!tuiEditor) return;
            const action = $(this).data('tool');
            tuiEditor.stopDrawingMode();

            if (action === 'draw') {
                tuiEditor.startDrawingMode('FREE_DRAWING');
                tuiEditor.setBrush({
                    width: 10,
                    color: '#FF1493'
                });
            } else if (action === 'rect') {
                const canvas = tuiEditor._graphics.getCanvas();
                // Create a Group to achieve: White Rect (Back) + Pink Rect (Front) + Shadow
                const rectWhite = new fabric.Rect({
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                    width: 200,
                    height: 100,
                    fill: 'transparent',
                    stroke: 'white',
                    strokeWidth: 12, // Thicker white stroke
                    originX: 'center',
                    originY: 'center',
                    shadow: {
                        color: 'black',
                        blur: 15,
                        offsetX: 5,
                        offsetY: 5
                    }
                });

                const rectPink = new fabric.Rect({
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                    width: 200,
                    height: 100,
                    fill: 'transparent',
                    stroke: '#FF1493',
                    strokeWidth: 6, // Thinner pink stroke inside
                    originX: 'center',
                    originY: 'center'
                });

                const group = new fabric.Group([rectWhite, rectPink], {
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                });

                canvas.add(group);
                canvas.setActiveObject(group);

            } else if (action === 'arrow') {
                const canvas = tuiEditor._graphics.getCanvas();
                tuiEditor.addIcon('arrow', {
                    fill: '#FF1493',
                    stroke: 'white',
                    strokeWidth: 2,
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() / 2,
                    shadow: {
                        color: 'black',
                        blur: 10,
                        offsetX: 5,
                        offsetY: 5
                    },
                    originX: 'center',
                    originY: 'center'
                });
            } else if (action === 'rotate') {
                tuiEditor.rotate(90);
            } else if (action === 'undo') {
                tuiEditor.undo();
            } else if (action === 'redo') {
                tuiEditor.redo();
            } else if (action === 'delete') {
                const canvas = tuiEditor._graphics.getCanvas();
                const active = canvas.getActiveObject();
                if (active) {
                    canvas.remove(active);
                    canvas.renderAll();
                }
            }
        });

        $(document).on('click', '#tui-save-btn', async function() {
            const dataURL = tuiEditor.toDataURL({
                format: 'jpeg',
                quality: 0.85
            });
            const tuiModal = bootstrap.Modal.getInstance(document.getElementById('tuiEditorModal'));
            tuiModal.hide();

            if ($('#addTemuanModal').hasClass('show')) {
                $('#previewFoto').attr('src', dataURL);
                editedImageData = dataURL;
                $('#finalSaveTemuanBtn').removeClass('d-none');
            } else if ($('#editTemuanModal').hasClass('show')) {
                $('#modalFotoTemuan').attr('src', dataURL);
                editedImageData = dataURL;
                newFileImageData = null;
            }
        });

        // === Fungsi Add Temuan (tetap sama) ===
        $('#addFotoInput').on('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            try {
                const blob = await resizeImage(file);
                const url = URL.createObjectURL(blob);
                $('#previewFoto').attr('src', url);
                $('#previewFotoSection').removeClass('d-none');

                const reader = new FileReader();
                reader.onload = () => {
                    editedImageData = reader.result;
                    $('#finalSaveTemuanBtn').removeClass('d-none');
                };
                reader.readAsDataURL(blob);
            } catch (err) {
                console.error('Gagal memuat gambar:', err);
            }
        });

        $(document).on('click', '#editPreviewFotoBtn', function() {
            if (editedImageData) {
                openTuiEditor(editedImageData, true);
            }
        });

        $(document).on('click', '#finalSaveTemuanBtn', async function() {
            const desc = $('#addDescInput').val().trim();
            const patrolId = "{{ $patrol->Id_Patrol }}";

            if (!editedImageData || !desc) return;

            const formData = new FormData();
            formData.append('Desc_Temuan', desc);
            formData.append('Path_Temuan', editedImageData);

            try {
                const res = await fetch(
                    "{{ route('user_temuan.store', ['id' => $patrol->Id_Patrol]) }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                const data = await res.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addTemuanModal')).hide();
                    location.reload();
                } else {
                    console.error('Gagal menyimpan temuan:', data.message);
                }
            } catch (err) {
                console.error('Error menyimpan temuan:', err);
            }
        });

        // === Fungsi View Temuan (tetap sama) ===
        $(document).on('click', '.view-temuan', function() {
            editedImageData = null;
            newFileImageData = null;

            const fotoPath = $(this).data('foto-temuan');
            const desc = $(this).data('desc-temuan');
            currentTemuanId = $(this).data('id');

            const fotoUrl = fotoPath ?
                "{{ asset('uploads') }}/" + fotoPath :
                "{{ asset('images/no-img.jpeg') }}";

            $('#modalFotoTemuan').attr('src', fotoUrl);
            $('#editDescTemuan').val(desc || '');
            currentImageUrl = fotoUrl;
            $('#editNewFotoInput').val('');
        });

        // === Fungsi Ganti Foto (tetap sama) ===
        $(document).on('click', '#replaceFotoBtn', function() {
            $('#editNewFotoInput').click();
        });

        $('#editNewFotoInput').on('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            try {
                const resizedBlob = await resizeImage(file);
                const newFile = new File([resizedBlob], 'foto-temuan-' + Date.now() + '.jpg', {
                    type: 'image/jpeg'
                });
                newFileImageData = newFile;
                const previewUrl = URL.createObjectURL(newFile);
                $('#modalFotoTemuan').attr('src', previewUrl);
                editedImageData = null;
            } catch (err) {
                console.error('Gagal memuat gambar baru:', err);
            }
        });

        // === Fungsi Edit Foto (tetap sama) ===
        $(document).on('click', '#editFotoBtn', function() {
            const currentSrc = $('#modalFotoTemuan').attr('src');
            if (currentSrc && !currentSrc.includes('no-img.jpeg')) {
                openTuiEditor(currentSrc, false);
            }
        });

        // ✅ PERBAIKAN: Update Temuan (tanpa alert)
        $(document).on('click', '#updateTemuanBtn', async function() {
            const desc = $('#editDescTemuan').val().trim();
            if (!currentTemuanId || !desc) {
                // Tidak ada notifikasi
                return;
            }

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('Desc_Temuan', desc);

            if (newFileImageData) {
                formData.append('Path_Temuan', newFileImageData);
            } else if (editedImageData && editedImageData.startsWith('data:image')) {
                formData.append('Path_Temuan', editedImageData);
            }

            try {
                const res = await fetch("{{ route('user_temuan.update', ['id' => '__ID__']) }}"
                    .replace('__ID__', currentTemuanId), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                const data = await res.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editTemuanModal')).hide();
                    location.reload();
                } else {
                    console.error('Gagal memperbarui temuan:', data.message);
                }
            } catch (err) {
                console.error('Error memperbarui temuan:', err);
            }
        });

        // ✅ PERBAIKAN: Delete Temuan (tanpa alert)
        $(document).on('click', '.delete-temuan', function() {
            const id = $(this).data('id');
            if (!confirm('Yakin ingin menghapus data ini?')) return;

            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', "{{ csrf_token() }}");

            fetch("{{ route('user_temuan.destroy', ['id' => '__ID__']) }}".replace('__ID__', id), {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        console.error('Gagal menghapus:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error menghapus:', error);
                });
        });

        // === DataTable (tetap sama) ===
        if (!$.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable({
                "pageLength": -1,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "»",
                        "previous": "«"
                    }
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": [0, 4]
                }]
            });
        }
    });
</script>
@endsection