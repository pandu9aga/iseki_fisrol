@extends('admins.layouts.index')

<!-- TUI CSS -->
<link href="{{ asset('assets/css/tui-image-editor.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/tui-color-picker.css') }}" rel="stylesheet">

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary mb-2">Temuan 5S</h4>

            <!-- Baris info patrol + tombol -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <p class="m-0 font-weight-bold">
                        Name Patrol 5S: {{ $patrol->Name_Patrol ?? '-' }}
                    </p>
                    <p class="m-0 font-weight">
                        Time Patrol 5S:
                        {{ $patrol->Time_Patrol ? \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d F Y') : '-' }}
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Filter PIC -->
                    <form action="{{ route('temuan.index', $patrol->Id_Patrol) }}" method="GET" class="d-flex align-items-center">
                        <select name="filter_pic" class="form-select form-select-sm me-2" style="width: 200px;" onchange="this.form.submit()">
                            <option value="">Semua PIC Proses</option>
                            @foreach($uniquePics as $pic)
                            <option value="{{ $pic->pic_proses_nik }}" {{ request('filter_pic') == $pic->pic_proses_nik ? 'selected' : '' }}>
                                {{ $pic->pic_name }}
                            </option>
                            @endforeach
                        </select>
                        <!-- Preserve other query params if needed, but here likely none -->
                    </form>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemuanModal">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                    <!-- Update Export Link to include filter -->
                    <a href="{{ route('temuan.export', ['id' => $patrol->Id_Patrol, 'filter_pic' => request('filter_pic')]) }}" class="btn btn-success">
                        <i class="fas fa-file-powerpoint me-1"></i> Export PPT
                    </a>
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
                            <th>PIC Proses</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($temuans as $index => $temuan)
                        <tr class="{{ $temuan->Status_Temuan == 'Done' ? 'done-row' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-primary">
                                <div style="max-height: 100px; overflow-y: auto;">
                                    <b>
                                        @if ($temuan->nik_penemu)
                                        {{ $temuan->nik_penemu_name ?? $temuan->nik_penemu }}
                                        @elseif ($temuan->user && $temuan->user->Id_Type_User == 1)
                                        {{ $temuan->user->Name_User ?? '-' }}
                                        @else
                                        {{ $temuan->member->nama ?? '-' }}
                                        @endif
                                    </b>
                                </div>
                            </td>
                            <td>
                                @if ($temuan->Path_Temuan)
                                <img src="{{ asset('uploads/' . $temuan->Path_Temuan) }}"
                                    style="max-height:100px;">
                                @endif
                            </td>
                            <td>{{ $temuan->Desc_Temuan }}</td>
                            <td>
                                @if ($temuan->Path_Update_Temuan)
                                <img src="{{ asset('uploads/' . $temuan->Path_Update_Temuan) }}"
                                    style="max-height:100px;">
                                @endif
                            </td>
                            <td>{{ $temuan->Desc_Update_Temuan }}</td>
                            <td>
                                <div style="max-height: 100px; overflow-y: auto;">
                                    @if ($temuan->pic_proses_nik)
                                    <span class="badge bg-info text-white">{{ $temuan->pic_proses_name ?? $temuan->pic_proses_nik }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success view-temuan" data-bs-toggle="modal"
                                    data-bs-target="#viewTemuanModal" data-index="{{ $index }}"
                                    data-id="{{ $temuan->Id_Temuan }}"
                                    data-nama-penemu="{{ $temuan->nik_penemu ? ($temuan->nik_penemu_name ?? $temuan->nik_penemu) : ($temuan->user && $temuan->user->Id_Type_User == 1 ? $temuan->user->Name_User ?? '-' : $temuan->member->nama ?? '-') }}"
                                    data-foto-temuan="{{ $temuan->Path_Temuan }}"
                                    data-desc-temuan="{{ $temuan->Desc_Temuan }}"
                                    data-foto-update="{{ $temuan->Path_Update_Temuan }}"
                                    data-desc-update="{{ $temuan->Desc_Update_Temuan }}"
                                    data-status="{{ $temuan->Status_Temuan }}"
                                    data-pic-proses-nik="{{ $temuan->pic_proses_nik }}"
                                    data-pic-proses-name="{{ $temuan->pic_proses_name }}"
                                    data-rotate-temuan="{{ $temuan->Rotate_Temuan }}"
                                    data-rotate-update="{{ $temuan->Rotate_Update }}"
                                    data-iteration="{{ $loop->iteration }}">
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

<!-- ========== Modal Tambah Temuan (Admin - 2 Step) ========== -->
<div class="modal fade" id="addTemuanModal" tabindex="-1" aria-labelledby="addTemuanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTemuanLabel">Tambah Temuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Progress Steps -->
                <div class="d-flex justify-content-center mb-4">
                    <div class="step-indicator active" id="step-ind-1">
                        <span class="step-number">1</span>
                        <span class="step-text">Input NIK</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-indicator" id="step-ind-2">
                        <span class="step-number">2</span>
                        <span class="step-text">Upload Temuan</span>
                    </div>
                </div>

                <!-- Step 1: Input NIK -->
                <div id="add-step-1">
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIK Penemu</label>
                        <div class="input-group">
                            <input type="text" id="inputNikPenemu" class="form-control form-control-lg"
                                placeholder="Ketik NIK atau nama karyawan..." autocomplete="off">
                        </div>
                        <small class="form-text text-muted">Ketik NIK atau nama untuk mencari, lalu pilih dari daftar</small>
                    </div>

                    <!-- Search results -->
                    <div id="nikSearchResults" class="list-group mb-3" style="max-height: 250px; overflow-y: auto;"></div>
                </div>

                <!-- Step 2: Upload Foto + Keterangan -->
                <div id="add-step-2" class="d-none">
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="fas fa-user me-2"></i>
                        <span>Penemu: <strong id="step2PenemuName">-</strong> (<span id="step2PenemuNik">-</span>)</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="btnBackStep1">
                            <i class="fas fa-arrow-left me-1"></i> Ganti NIK
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Temuan <span class="text-danger">*</span></label>
                        <input type="file" id="addFotoTemuan" class="form-control" accept="image/*" capture="environment">
                    </div>
                    <div class="mb-3" id="previewAddFotoSection" style="display:none;">
                        <label class="form-label text-muted small">Pratinjau Foto</label><br>
                        <img id="previewAddFoto" src="" alt="Preview" class="img-fluid rounded" style="max-height:250px; cursor:pointer;" title="Klik untuk edit foto">
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btnEditAddFoto">
                            <i class="fas fa-edit me-1"></i> Edit Foto
                        </button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Temuan <span class="text-danger">*</span></label>
                        <textarea id="addDescTemuan" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success" id="btnSaveTemuan">
                            <i class="fas fa-save me-1"></i> Simpan Temuan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== View Temuan Modal ========== -->
<div class="modal fade" id="viewTemuanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"
        style="width: 100vw;max-width: none;height: 100%;margin-left: 10px;margin-right: 10px;">
        <div class="modal-content">
            <div class="modal-header px-5 d-flex justify-content-between align-items-center">
                <h5 class="modal-title">Detail Temuan <span id="modalTemuanNo"></span></h5>
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
                        <div class="mb-2">
                            <label class="form-label">PIC :</label>
                            <span id="modalPicProsesName" class="fw-bold text-info">-</span>
                        </div>
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
                <!-- Form Update Perbaikan -->
                <form action="{{ route('temuan.update', 0) }}" method="POST"
                    enctype="multipart/form-data" id="updateTemuanForm">
                    @csrf
                    @method('PUT')

                    <!-- PIC Proses -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">PIC Proses <span class="text-danger">*</span></label>

                        <!-- Type Selection -->
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pic_type" id="picTypeMember" value="member" checked>
                                <label class="form-check-label" for="picTypeMember">Member</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pic_type" id="picTypeLeader" value="leader">
                                <label class="form-check-label" for="picTypeLeader">Leader</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pic_type" id="picTypeTeam" value="team">
                                <label class="form-check-label" for="picTypeTeam">Team</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pic_type" id="picTypeOther" value="other">
                                <label class="form-check-label" for="picTypeOther">Lain-lain</label>
                            </div>
                        </div>

                        <!-- Hidden Input for Form Submission -->
                        <input type="hidden" name="pic_proses_nik" id="pic_proses_nik_input">

                        <!-- 1. Search Container (Member/Leader) -->
                        <div id="picSearchContainer">
                            <div class="position-relative">
                                <input type="text" id="pic_proses_search" class="form-control"
                                    placeholder="Ketik nama karyawan untuk mencari..." autocomplete="off">
                                <div id="picProsesResults" class="list-group position-absolute w-100" style="z-index:1050; max-height:200px; overflow-y:auto; display:none;"></div>
                            </div>
                            <div id="selectedPicProses" class="mt-2 d-none">
                                <span class="badge bg-primary fs-6 p-2" id="selectedPicProsesText"></span>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-1" id="clearPicProses"><i class="fas fa-times"></i></button>
                            </div>
                        </div>

                        <!-- 3. Manual Input Container -->
                        <div id="picManualContainer" class="d-none">
                            <input type="text" class="form-control" id="picManualInput" placeholder="Ketik PIC Proses...">
                        </div>
                    </div>

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

<!-- ========== TUI Image Editor Modal ========== -->
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
    .done-row td {
        background-color: #4DA8DA !important;
        color: white !important;
    }

    /* Step indicator styles */
    .step-indicator {
        display: flex;
        flex-direction: column;
        align-items: center;
        opacity: 0.4;
    }

    .step-indicator.active {
        opacity: 1;
    }

    .step-number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #4e73df;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .step-text {
        font-size: 0.8rem;
        color: #555;
    }

    .step-line {
        flex: 1;
        height: 2px;
        background: #ccc;
        align-self: center;
        margin: 0 10px;
        margin-bottom: 18px;
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

    /* Modal Font Size Increase */
    #viewTemuanModal .modal-body {
        font-size: 1.25rem;
    }

    #viewTemuanModal .modal-title {
        font-size: 1.6rem;
        font-weight: bold;
    }

    #viewTemuanModal .form-label {
        font-size: 1.35rem;
        font-weight: bold;
    }

    #viewTemuanModal .btn {
        font-size: 1.2rem;
    }

    #viewTemuanModal p,
    #viewTemuanModal span,
    #viewTemuanModal input,
    #viewTemuanModal textarea,
    #viewTemuanModal .input-group-text {
        font-size: 1.3rem;
    }
</style>
@endsection

@section('script')
<!-- TUI JS -->
<script src="{{ asset('assets/js/tui-code-snippet.js') }}"></script>
<script src="{{ asset('assets/js/tui-color-picker.js') }}"></script>
<script src="{{ asset('assets/js/fabric.min.js') }}"></script>
<script src="{{ asset('assets/js/tui-image-editor.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const viewModal = document.getElementById("viewTemuanModal");
        const statusTemuanId = document.getElementById("statusTemuanId");
        const statusSwitchInput = document.getElementById("statusSwitchInput");
        const statusForm = document.getElementById("statusForm");
        const updateTemuanForm = document.getElementById("updateTemuanForm");

        let temuanButtons = Array.from(document.querySelectorAll(".view-temuan"));
        let currentIndex = -1;

        // ============ TUI Image Editor ============
        let tuiEditor = null;
        let editedImageData = null;

        function openTuiEditor(imageUrl) {
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

        // TUI toolbar tools
        $(document).on('click', '[data-tool]', function(e) {
            if (!tuiEditor) return;
            const action = $(this).data('tool');
            tuiEditor.stopDrawingMode();

            if (action === 'draw') {
                tuiEditor.startDrawingMode('FREE_DRAWING', {
                    width: 10,
                    color: '#FF1493'
                });
                // Add shadow to brush
                const canvas = tuiEditor._graphics.getCanvas();
                if (canvas.freeDrawingBrush) {
                    canvas.freeDrawingBrush.shadow = new fabric.Shadow({
                        blur: 10,
                        offsetX: 5,
                        offsetY: 5,
                        color: 'black'
                    });
                }

            } else if (action === 'rect') {
                const canvas = tuiEditor._graphics.getCanvas();
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
                // Arrow: Pink Fill, White Stroke, Black Shadow
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
                    }
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

        // TUI save
        $(document).on('click', '#tui-save-btn', function() {
            const dataURL = tuiEditor.toDataURL({
                format: 'jpeg',
                quality: 0.85
            });
            const tuiModal = bootstrap.Modal.getInstance(document.getElementById('tuiEditorModal'));
            tuiModal.hide();

            // Update preview in add modal
            if ($('#addTemuanModal').hasClass('show')) {
                $('#previewAddFoto').attr('src', dataURL);
                editedImageData = dataURL;
            }
        });

        // ============ Add Temuan - 2 Step Flow ============
        let selectedNikPenemu = '';
        let selectedNamaPenemu = '';

        // Search NIK — when selected, go directly to step 2
        let nikSearchTimeout;
        document.getElementById('inputNikPenemu').addEventListener('input', function() {
            clearTimeout(nikSearchTimeout);
            const q = this.value.trim();
            selectedNikPenemu = '';

            if (q.length < 1) {
                document.getElementById('nikSearchResults').innerHTML = '';
                return;
            }
            nikSearchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.employee.search') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        const container = document.getElementById('nikSearchResults');
                        container.innerHTML = '';
                        if (data.length === 0) {
                            container.innerHTML = '<div class="list-group-item text-muted">Tidak ditemukan</div>';
                        }
                        data.forEach(emp => {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = `<strong>${emp.nama}</strong> <span class="text-muted">(${emp.nik})</span>`;
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                selectedNikPenemu = emp.nik;
                                selectedNamaPenemu = emp.nama;
                                // Go directly to step 2
                                document.getElementById('step2PenemuName').textContent = emp.nama;
                                document.getElementById('step2PenemuNik').textContent = emp.nik;
                                goToStep(2);
                            });
                            container.appendChild(item);
                        });
                    });
            }, 300);
        });

        // Step navigation
        document.getElementById('btnBackStep1').addEventListener('click', () => goToStep(1));

        function goToStep(step) {
            document.getElementById('add-step-1').classList.toggle('d-none', step !== 1);
            document.getElementById('add-step-2').classList.toggle('d-none', step !== 2);
            document.getElementById('step-ind-1').classList.toggle('active', step >= 1);
            document.getElementById('step-ind-2').classList.toggle('active', step >= 2);
        }

        // Photo preview + edit option
        document.getElementById('addFotoTemuan').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                document.getElementById('previewAddFoto').src = url;
                document.getElementById('previewAddFotoSection').style.display = 'block';
                editedImageData = null; // Reset edited data
            }
        });

        // Edit foto button -> open TUI editor
        document.getElementById('btnEditAddFoto').addEventListener('click', function() {
            const src = document.getElementById('previewAddFoto').src;
            if (src) openTuiEditor(src);
        });

        // Click on preview to edit
        document.getElementById('previewAddFoto').addEventListener('click', function() {
            if (this.src) openTuiEditor(this.src);
        });

        // Save temuan
        document.getElementById('btnSaveTemuan').addEventListener('click', async function() {
            const desc = document.getElementById('addDescTemuan').value.trim();
            const fileInput = document.getElementById('addFotoTemuan');
            const file = fileInput.files[0];

            if (!file && !editedImageData) {
                alert('Mohon pilih foto temuan.');
                return;
            }
            if (!desc) {
                alert('Mohon isi deskripsi temuan.');
                return;
            }

            const formData = new FormData();
            formData.append('Desc_Temuan', desc);
            formData.append('Id_Patrol', '{{ $patrol->Id_Patrol }}');
            formData.append('nik_penemu', selectedNikPenemu);

            // If edited via TUI, send base64. Otherwise send file.
            if (editedImageData) {
                formData.append('Path_Temuan', editedImageData);
            } else {
                formData.append('Path_Temuan', file);
            }

            try {
                const response = await fetch("{{ route('temuan.store', ['id' => $patrol->Id_Patrol]) }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menyimpan temuan: ' + (data.message || ''));
                }
            } catch (err) {
                console.error(err);
                alert('Error saat menyimpan temuan.');
            }
        });

        // Reset modal on close
        document.getElementById('addTemuanModal').addEventListener('hidden.bs.modal', function() {
            goToStep(1);
            selectedNikPenemu = '';
            selectedNamaPenemu = '';
            editedImageData = null;
            document.getElementById('inputNikPenemu').value = '';
            document.getElementById('nikSearchResults').innerHTML = '';
            document.getElementById('addFotoTemuan').value = '';
            document.getElementById('addDescTemuan').value = '';
            document.getElementById('previewAddFotoSection').style.display = 'none';
        });

        // ============ View Modal ============
        viewModal.addEventListener("show.bs.modal", function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            currentIndex = temuanButtons.indexOf(button);
            loadTemuanData(button);
        });

        // Status switch
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
                        const rowEl = document.querySelector(`#example button[data-id="${idTemuan}"]`).closest("tr");
                        const viewBtn = rowEl.querySelector('button.view-temuan');
                        viewBtn.dataset.status = (statusSwitchInput.checked ? 'Done' : 'Pending');
                        if (statusSwitchInput.checked) {
                            rowEl.classList.add('done-row');
                        } else {
                            rowEl.classList.remove('done-row');
                        }
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

        // Next / Prev
        document.getElementById("nextTemuan").addEventListener("click", () => {
            if (currentIndex < temuanButtons.length - 1) {
                currentIndex++;
                loadTemuanData(temuanButtons[currentIndex]);
            }
        });
        document.getElementById("prevTemuan").addEventListener("click", () => {
            if (currentIndex > 0) {
                currentIndex--;
                loadTemuanData(temuanButtons[currentIndex]);
            }
        });

        function loadTemuanData(btn) {
            const id = btn.dataset.id;
            if (typeof window.applyRotationFromState === 'function') {
                window.applyRotationFromState(id);
            }
            statusTemuanId.value = id;
            document.getElementById("modalNamaPenemu").textContent = btn.dataset.namaPenemu || "-";
            document.getElementById("modalDescTemuan").textContent = btn.dataset.descTemuan || "-";
            document.getElementById("modalDescUpdate").textContent = btn.dataset.descUpdate || "-";

            document.getElementById("modalFotoTemuan").src = btn.dataset.fotoTemuan ?
                `../uploads/${btn.dataset.fotoTemuan}` : "../storage/no-img.jpeg";
            document.getElementById("modalFotoUpdate").src = btn.dataset.fotoUpdate ?
                `../uploads/${btn.dataset.fotoUpdate}` : "../storage/no-img.jpeg";

            statusSwitchInput.checked = (btn.dataset.status === "Done");
            updateTemuanForm.action = `../temuan/${id}`;
            statusForm.action = `../temuan/${id}/status`;

            // PIC Proses
            const picNik = btn.dataset.picProsesNik || '';
            const picName = btn.dataset.picProsesName || '';

            // Fill Update Form
            document.getElementById('pic_proses_nik_input').value = picNik;
            document.getElementById('pic_proses_search').value = '';
            const picManualInput = document.getElementById('picManualInput');
            if (picManualInput) picManualInput.value = '';
            document.getElementById('selectedPicProses').classList.add('d-none');

            // Determine Type
            let type = 'member';
            if (picNik) {
                // If numeric, assume Member (default)
                if (/^\d+$/.test(picNik)) {
                    type = 'member';
                    document.getElementById('selectedPicProsesText').textContent = `${picName} (${picNik})`;
                    document.getElementById('selectedPicProses').classList.remove('d-none');
                } else if (picNik === 'Leader') {
                    type = 'leader';
                } else if (picNik === 'Team') {
                    type = 'team';
                } else {
                    type = 'other';
                    if (picManualInput) picManualInput.value = picNik;
                }
            }

            // Set Radio
            const radio = document.querySelector(`input[name="pic_type"][value="${type}"]`);
            if (radio) radio.checked = true;

            // Trigger UI Update
            if (typeof togglePicInput === 'function') {
                togglePicInput();
            }

            // Display PIC Proses in View
            const picDisplayEl = document.getElementById('modalPicProsesName');
            if (picDisplayEl) {
                picDisplayEl.textContent = picNik ? (picName || picNik) : '-';
            }

            // Sequence Number
            const seqEl = document.getElementById('modalTemuanNo');
            if (seqEl) {
                seqEl.textContent = '#' + (btn.dataset.iteration || '');
                seqEl.className = 'badge bg-secondary ms-2 fs-5'; // Badge style
            }

            // Init Rotation State from DB
            rotationState[`temuan_${id}`] = parseInt(btn.dataset.rotateTemuan || 0);
            rotationState[`perbaikan_${id}`] = parseInt(btn.dataset.rotateUpdate || 0);
            if (typeof window.applyRotationFromState === 'function') {
                window.applyRotationFromState(id);
            }

            // Auto-fill form inputs with existing data (prevent data loss)
            const descUpdateInput = document.getElementById('Desc_Update_Temuan');
            if (descUpdateInput) {
                descUpdateInput.value = btn.dataset.descUpdate || '';
            }

            // Clear file input (user must explicitly upload new photo)
            const fileInput = document.getElementById('Path_Update_Temuan');
            if (fileInput) {
                fileInput.value = '';
                // Show info about existing photo
                const existingInfo = fileInput.parentElement.querySelector('.existing-foto-info');
                if (existingInfo) existingInfo.remove();
                if (btn.dataset.fotoUpdate) {
                    const info = document.createElement('small');
                    info.className = 'form-text text-muted existing-foto-info';
                    info.textContent = 'Foto saat ini tetap tersimpan. Upload baru hanya jika ingin mengganti.';
                    fileInput.parentElement.appendChild(info);
                }
            }
        }

        // ============ PIC Proses Search ============
        let picSearchTimeout;
        document.getElementById('pic_proses_search').addEventListener('input', function() {
            clearTimeout(picSearchTimeout);
            const q = this.value.trim();
            if (q.length < 1) {
                document.getElementById('picProsesResults').style.display = 'none';
                return;
            }
            picSearchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.employee.search') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        const container = document.getElementById('picProsesResults');
                        container.innerHTML = '';
                        if (data.length === 0) {
                            container.innerHTML = '<div class="list-group-item text-muted">Tidak ditemukan</div>';
                        } else {
                            data.forEach(emp => {
                                const item = document.createElement('a');
                                item.href = '#';
                                item.className = 'list-group-item list-group-item-action';
                                item.textContent = `${emp.nama} (${emp.nik})`;
                                item.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    document.getElementById('pic_proses_nik_input').value = emp.nik;
                                    document.getElementById('pic_proses_search').value = '';
                                    document.getElementById('selectedPicProsesText').textContent = `${emp.nama} (${emp.nik})`;
                                    document.getElementById('selectedPicProses').classList.remove('d-none');
                                    container.style.display = 'none';
                                });
                                container.appendChild(item);
                            });
                        }
                        container.style.display = 'block';
                    });
            }, 300);
        });

        document.getElementById('clearPicProses').addEventListener('click', function() {
            document.getElementById('pic_proses_nik_input').value = '';
            document.getElementById('selectedPicProses').classList.add('d-none');
        });

        // ============ Multi-Type PIC Logic ============
        const picRadios = document.getElementsByName('pic_type');
        picRadios.forEach(radio => {
            radio.addEventListener('change', togglePicInput);
        });

        const picManualInput = document.getElementById('picManualInput');
        if (picManualInput) {
            picManualInput.addEventListener('input', function() {
                document.getElementById('pic_proses_nik_input').value = this.value;
            });
        }

        function togglePicInput() {
            const checkedRadio = document.querySelector('input[name="pic_type"]:checked');
            if (!checkedRadio) return;
            const type = checkedRadio.value;

            const searchContainer = document.getElementById('picSearchContainer');
            const manualContainer = document.getElementById('picManualContainer');
            const hiddenInput = document.getElementById('pic_proses_nik_input');

            // Hide all
            if (searchContainer) searchContainer.classList.add('d-none');
            if (manualContainer) manualContainer.classList.add('d-none');

            // Show based on type
            if (type === 'member') {
                if (searchContainer) searchContainer.classList.remove('d-none');
            } else if (type === 'leader') {
                hiddenInput.value = 'Leader';
            } else if (type === 'team') {
                hiddenInput.value = 'Team';
            } else {
                // Lain-lain
                if (manualContainer) manualContainer.classList.remove('d-none');
                if (picManualInput) hiddenInput.value = picManualInput.value;
            }
        }

        // Validate Update Form (PIC Proses Required)
        document.getElementById('updateTemuanForm').addEventListener('submit', function(e) {
            const picNik = document.getElementById('pic_proses_nik_input').value;
            if (!picNik) {
                e.preventDefault();
                alert('Mohon pilih PIC Proses terlebih dahulu.');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        var table;
        if ($.fn.DataTable.isDataTable('#example')) {
            table = $('#example').DataTable();
            table.page.len(100).draw();
        } else {
            table = $('#example').DataTable({
                pageLength: 100,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ]
            });
        }

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
    // Store rotation per ID (Session only)
    let rotationState = {};

    function applyRotationFromState(id) {
        const tKey = `temuan_${id}`;
        const pKey = `perbaikan_${id}`;

        const tDeg = rotationState[tKey] || 0;
        const pDeg = rotationState[pKey] || 0;

        const imgTemuan = document.getElementById('modalFotoTemuan');
        const imgPerbaikan = document.getElementById('modalFotoUpdate');

        if (imgTemuan) {
            const tIsVertical = (tDeg === 90 || tDeg === 270);
            const tScale = tIsVertical ? 0.7 : 1;
            imgTemuan.style.transform = `rotate(${tDeg}deg) scale(${tScale})`;
        }
        if (imgPerbaikan) {
            const pIsVertical = (pDeg === 90 || pDeg === 270);
            const pScale = pIsVertical ? 0.7 : 1;
            imgPerbaikan.style.transform = `rotate(${pDeg}deg) scale(${pScale})`;
        }
    }

    // Expose globally
    window.applyRotationFromState = applyRotationFromState;

    function rotateImage(imgId, degrees) {
        const id = document.getElementById('statusTemuanId').value;
        if (!id) return;

        const img = document.getElementById(imgId);
        if (!img) return;

        // Determine type based on ID
        const type = imgId === 'modalFotoTemuan' ? 'temuan' : 'perbaikan';
        const key = `${type}_${id}`;

        // Update rotation state
        let current = rotationState[key] || 0;
        current = ((current + degrees) % 360 + 360) % 360;
        rotationState[key] = current;

        // Apply transform with scaling if needed (90 or 270 degrees)
        const isVertical = (current === 90 || current === 270);
        const scale = isVertical ? 0.7 : 1;
        img.style.transform = `rotate(${current}deg) scale(${scale})`;

        // Save to DB
        fetch(`../temuan/${id}/rotate`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: type,
                    angle: current
                })
            }).then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update local button data attribute
                    const btn = document.querySelector(`button.view-temuan[data-id="${id}"]`);
                    if (btn) {
                        if (type === 'temuan') btn.dataset.rotateTemuan = current;
                        if (type === 'perbaikan') btn.dataset.rotateUpdate = current;
                    }
                }
            })
            .catch(err => console.error("Rotation save failed", err));
    }

    // Modal show listener not needed to reset, as loadTemuanData handles it.
    // We keep an empty listener or remove it to avoid errors if something expects it.
    document.getElementById('viewTemuanModal').addEventListener('show.bs.modal', function(event) {
        // Handled by loadTemuanData
    });
</script>
@endsection