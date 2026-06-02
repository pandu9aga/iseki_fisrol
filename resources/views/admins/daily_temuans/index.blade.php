@extends('admins.layouts.index')

<link href="{{ asset('assets/css/tui-image-editor.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/tui-color-picker.css') }}" rel="stylesheet">

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary mb-2">Daily Temuan 5S</h4>

            <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row gap-3">
                <div>
                    <p class="m-0 font-weight-bold">
                        Name Daily Patrol: {{ $daily_patrol->Name_Daily_Patrol ?? '-' }}
                    </p>
                    <p class="m-0 font-weight">
                        Time Daily Patrol:
                        {{ $daily_patrol->Time_Daily_Patrol ? \Carbon\Carbon::parse($daily_patrol->Time_Daily_Patrol)->format('d F Y') : '-' }}
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-column flex-sm-row w-100 w-md-auto">
                    <form action="{{ route('daily_temuan.index', $daily_patrol->Id_Daily_Patrol) }}" method="GET" class="w-100 w-sm-auto">
                        <select name="filter_pic" class="form-select form-select-sm w-100" style="min-width: 200px;" onchange="this.form.submit()">
                            <option value="">Semua PIC Proses</option>
                            @foreach($uniquePics as $pic)
                            <option value="{{ $pic->pic_proses_nik_daily }}" {{ request('filter_pic') == $pic->pic_proses_nik_daily ? 'selected' : '' }}>
                                {{ $pic->pic_name }}
                            </option>
                            @endforeach
                        </select>
                    </form>

                    <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-start justify-content-sm-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDailyTemuanModal">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                        <a href="{{ route('daily_temuan.export', ['id' => $daily_patrol->Id_Daily_Patrol, 'filter_pic' => request('filter_pic')]) }}" class="btn btn-success text-nowrap">
                            <i class="fas fa-file-powerpoint me-1"></i> Export PPT
                        </a>
                    </div>
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
                        @foreach ($daily_temuans as $index => $temuan)
                        <tr class="{{ $temuan->Status_Daily_Temuan == 'Done' ? 'done-row' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-primary">
                                <b>{{ $temuan->user->Name_User ?? session('login_name') }}</b>
                            </td>
                            <td>
                                @if ($temuan->Path_Daily_Temuan)
                                <img src="{{ asset('uploads/' . $temuan->Path_Daily_Temuan) }}"
                                    style="max-height:100px;">
                                @endif
                            </td>
                            <td>{{ $temuan->Desc_Daily_Temuan }}</td>
                            <td>
                                @if ($temuan->Path_Daily_Update_Temuan)
                                <img src="{{ asset('uploads/' . $temuan->Path_Daily_Update_Temuan) }}"
                                    style="max-height:100px;">
                                @endif
                            </td>
                            <td>{{ $temuan->Desc_Daily_Update_Temuan }}</td>
                            <td>
                                <div style="max-height: 100px; overflow-y: auto;">
                                    @if ($temuan->pic_proses_nik_daily)
                                    <span class="badge bg-info text-white">{{ $temuan->pic_proses_name ?? $temuan->pic_proses_nik_daily }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success view-temuan" data-bs-toggle="modal"
                                    data-bs-target="#viewDailyTemuanModal" data-index="{{ $index }}"
                                    data-id="{{ $temuan->Id_Daily_Temuan }}"
                                    data-nama-penemu="{{ $temuan->user->Name_User ?? session('login_name') }}"
                                    data-foto-temuan="{{ $temuan->Path_Daily_Temuan }}"
                                    data-desc-temuan="{{ $temuan->Desc_Daily_Temuan }}"
                                    data-foto-update="{{ $temuan->Path_Daily_Update_Temuan }}"
                                    data-desc-update="{{ $temuan->Desc_Daily_Update_Temuan }}"
                                    data-status="{{ $temuan->Status_Daily_Temuan }}"
                                    data-pic-proses-nik="{{ $temuan->pic_proses_nik_daily }}"
                                    data-pic-proses-name="{{ $temuan->pic_proses_name }}"
                                    data-rotate-temuan="{{ $temuan->Rotate_Daily_Temuan }}"
                                    data-rotate-update="{{ $temuan->Rotate_Daily_Update }}"
                                    data-iteration="{{ $loop->iteration }}">
                                    View
                                </button>
                                <form action="{{ route('daily_temuan.destroy', $temuan->Id_Daily_Temuan) }}" method="POST"
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
            <a href="{{ route('daily_patrol') }}" class="btn btn-outline-primary">Back to Daily Patrol</a>
        </div>
    </div>
</div>

<!-- ========== Modal Tambah Daily Temuan (Direct, no NIK search) ========== -->
<div class="modal fade" id="addDailyTemuanModal" tabindex="-1" aria-labelledby="addDailyTemuanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDailyTemuanLabel">Tambah Daily Temuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center mb-3">
                    <i class="fas fa-user me-2"></i>
                    <span>Penemu: <strong>{{ session('login_name') }}</strong></span>
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

<!-- ========== View Daily Temuan Modal ========== -->
<div class="modal fade" id="viewDailyTemuanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"
        style="width: 100vw;max-width: none;height: 100%;margin-left: 10px;margin-right: 10px;">
        <div class="modal-content">
            <div class="modal-header px-5 d-flex justify-content-between align-items-center">
                <h5 class="modal-title">Detail Daily Temuan <span id="modalTemuanNo"></span></h5>
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
                            <form id="statusForm" action="{{ route('daily_temuan.updateStatus', 0) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="Id_Daily_Temuan" id="statusTemuanId">
                                <div class="d-flex align-items-center">
                                    <label class="form-label me-3 mb-0">Status :</label>
                                    <div class="form-check form-switch mb-0 ms-2">
                                        <input class="form-check-input" type="checkbox" id="statusSwitchInput"
                                            name="Status_Daily_Temuan" value="Done">
                                        <label class="form-check-label" for="statusSwitchInput">Selesai</label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

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
                <form action="{{ route('daily_temuan.update', 0) }}" method="POST"
                    enctype="multipart/form-data" id="updateTemuanForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">PIC Proses <span class="text-danger">*</span></label>
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
                        <input type="hidden" name="pic_proses_nik_daily" id="pic_proses_nik_daily_input">

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

                        <div id="picManualContainer" class="d-none">
                            <input type="text" class="form-control" id="picManualInput" placeholder="Ketik PIC Proses...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="Path_Daily_Update_Temuan" class="form-label">Foto Perbaikan</label>
                        <input type="file" name="Path_Daily_Update_Temuan" id="Path_Daily_Update_Temuan" class="form-control"
                            accept="image/*" capture="environment">
                    </div>
                    <div class="mb-3">
                        <label for="Desc_Daily_Update_Temuan" class="form-label">Deskripsi Perbaikan</label>
                        <textarea name="Desc_Daily_Update_Temuan" id="Desc_Daily_Update_Temuan" rows="3" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Perbaikan</button>
                </form>
            </div>
        </div>
    </div>
</div>

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
                        <button type="button" class="btn btn-outline-primary" data-tool="draw"><i class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="rect"><i class="fas fa-square"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-tool="arrow"><i class="fas fa-arrow-right"></i></button>
                    </div>
                    <div class="vr mx-3 d-none d-md-block"></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-tool="undo"><i class="fas fa-undo"></i></button>
                        <button type="button" class="btn btn-outline-secondary" data-tool="redo"><i class="fas fa-redo"></i></button>
                        <button type="button" class="btn btn-outline-danger" data-tool="delete"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-success" id="tui-save-btn">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
                <div id="tui-editor-container" class="d-flex justify-content-center align-items-center bg-dark-subtle"
                    style="flex:1; overflow:hidden;">
                    <div id="tui-image-editor" style="width:100%; height:100%;"></div>
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

    #tui-editor-container {
        height: calc(100vh - 120px);
        min-height: 0;
    }
    #tui-image-editor {
        width: 100%;
        height: 100%;
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

    /* Toolbar buttons - bigger for mobile */
    #custom-tui-toolbar .btn {
        padding: 0.6rem 0.8rem;
        font-size: 1.2rem;
        min-width: 48px;
        min-height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
    #custom-tui-toolbar .btn i {
        font-size: 1.3rem;
    }
    #custom-tui-toolbar .btn-success,
    #custom-tui-toolbar .btn-secondary {
        padding: 0.6rem 1rem;
        font-size: 1rem;
        min-width: auto;
        min-height: 40px;
    }
    @media (max-width: 576px) {
        #custom-tui-toolbar {
            gap: 0.5rem !important;
            padding: 0.5rem !important;
        }
        #custom-tui-toolbar .btn {
            padding: 0.7rem 0.9rem;
            font-size: 1.3rem;
            min-width: 52px;
            min-height: 52px;
        }
        #custom-tui-toolbar .btn i {
            font-size: 1.4rem;
        }
        #custom-tui-toolbar .btn-success,
        #custom-tui-toolbar .btn-secondary {
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
            min-height: 44px;
        }
    }

    #viewDailyTemuanModal .modal-body {
        font-size: 1.25rem;
    }
    #viewDailyTemuanModal .modal-title {
        font-size: 1.6rem;
        font-weight: bold;
    }
    #viewDailyTemuanModal .form-label {
        font-size: 1.35rem;
        font-weight: bold;
    }
    #viewDailyTemuanModal .btn {
        font-size: 1.2rem;
    }
    #viewDailyTemuanModal p,
    #viewDailyTemuanModal span,
    #viewDailyTemuanModal input,
    #viewDailyTemuanModal textarea,
    #viewDailyTemuanModal .input-group-text {
        font-size: 1.3rem;
    }
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/tui-code-snippet.js') }}"></script>
<script src="{{ asset('assets/js/tui-color-picker.js') }}"></script>
<script src="{{ asset('assets/js/fabric.min.js') }}"></script>
<script src="{{ asset('assets/js/tui-image-editor.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const viewModal = document.getElementById("viewDailyTemuanModal");
        const statusTemuanId = document.getElementById("statusTemuanId");
        const statusSwitchInput = document.getElementById("statusSwitchInput");
        const statusForm = document.getElementById("statusForm");
        const updateTemuanForm = document.getElementById("updateTemuanForm");

        let temuanButtons = Array.from(document.querySelectorAll(".view-temuan"));
        let currentIndex = -1;

        let tuiEditor = null;
        let editedImageData = null;

        function openTuiEditor(imageUrl) {
            const modal = new bootstrap.Modal(document.getElementById('tuiEditorModal'));
            modal.show();

            modal._element.addEventListener('shown.bs.modal', () => {
                const container = document.getElementById('tui-image-editor');
                container.innerHTML = '';
                const editorContainer = document.getElementById('tui-editor-container');
                const containerW = editorContainer.clientWidth || window.innerWidth;
                const containerH = editorContainer.clientHeight || (window.innerHeight - 120);

                tuiEditor = new tui.ImageEditor(container, {
                    usageStatistics: false,
                    cssMaxWidth: containerW,
                    cssMaxHeight: containerH,
                });

                tuiEditor.loadImageFromURL(imageUrl, 'uploaded').then(() => {
                    const canvas = tuiEditor._graphics.getCanvas();

                    const sc = getDisplayScale(canvas);
                    canvas.selectionColor = 'rgba(255,20,147,0.15)';
                    canvas.selectionBorderColor = '#FF1493';
                    fabric.Object.prototype.set({
                        borderColor: '#FF1493',
                        cornerColor: '#FF1493',
                        cornerStrokeColor: '#FF1493',
                        cornerSize: Math.round(20 * sc),
                        transparentCorners: false,
                        touchCornerSize: Math.round(28 * sc),
                        padding: Math.round(10 * sc),
                        rotatingPointOffset: Math.round(55 * sc)
                    });

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
            }, { once: true });

            modal._element.addEventListener('hidden.bs.modal', () => {
                if (tuiEditor) {
                    tuiEditor.destroy();
                    tuiEditor = null;
                }
                setTimeout(() => {
                    const otherModal = document.querySelector('.modal.show');
                    if (otherModal) {
                        document.body.classList.add('modal-open');
                        document.body.style.overflow = 'hidden';
                        const modalBody = otherModal.querySelector('.modal-body');
                        if (modalBody) modalBody.style.overflowY = 'auto';
                        if (!document.querySelector('.modal-backdrop')) {
                            const backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade show';
                            document.body.appendChild(backdrop);
                        }
                    } else {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                    }
                }, 100);
            }, { once: true });
        }

        function getDisplayScale(canvas) {
            const displayW = canvas.getElement().offsetWidth || canvas.getElement().clientWidth || canvas.getWidth();
            return canvas.getWidth() / displayW;
        }

        function toCanvas(displayPx, canvas) {
            return displayPx * getDisplayScale(canvas);
        }

        $(document).on('click', '[data-tool]', function(e) {
            if (!tuiEditor) return;
            const action = $(this).data('tool');
            tuiEditor.stopDrawingMode();
            const canvas = tuiEditor._graphics.getCanvas();

            if (action === 'draw') {
                tuiEditor.startDrawingMode('FREE_DRAWING', { width: toCanvas(10, canvas), color: '#FF1493' });
            } else if (action === 'rect') {
                const rectW = toCanvas(250, canvas), rectH = toCanvas(180, canvas);
                const rect = new fabric.Rect({ left: canvas.getWidth()/2, top: canvas.getHeight()/2, width: rectW, height: rectH, fill: 'transparent', stroke: '#FF1493', strokeWidth: toCanvas(6, canvas), originX: 'center', originY: 'center' });
                canvas.add(rect);
                canvas.setActiveObject(rect);
            } else if (action === 'arrow') {
                const sz = toCanvas(80, canvas), cx = canvas.getWidth()/2, cy = canvas.getHeight()/2, sw = toCanvas(4, canvas), headSz = sz*0.35, shaftLen = sz*0.6;
                const arrow = new fabric.Group([
                    new fabric.Rect({ left: -shaftLen*0.45, top: -sw/2, width: shaftLen, height: sw, fill: '#FF1493', originX: 'center', originY: 'center' }),
                    new fabric.Triangle({ left: shaftLen*0.25, width: headSz, height: headSz, fill: '#FF1493', originX: 'center', originY: 'center', angle: 90 })
                ], { left: cx, top: cy, originX: 'center', originY: 'center' });
                canvas.add(arrow);
                canvas.setActiveObject(arrow);
                canvas.renderAll();
            } else if (action === 'undo') {
                tuiEditor.undo();
            } else if (action === 'redo') {
                tuiEditor.redo();
            } else if (action === 'delete') {
                const active = canvas.getActiveObject();
                if (active) { canvas.remove(active); canvas.renderAll(); }
            }
        });

        $(document).on('click', '#tui-save-btn', function() {
            const dataURL = tuiEditor.toDataURL({ format: 'jpeg', quality: 0.85 });
            const tuiModal = bootstrap.Modal.getInstance(document.getElementById('tuiEditorModal'));
            tuiModal.hide();

            if ($('#addDailyTemuanModal').hasClass('show')) {
                $('#previewAddFoto').attr('src', dataURL);
                editedImageData = dataURL;
            }
        });

        // Add temuan - direct, no NIK search
        document.getElementById('addFotoTemuan').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                document.getElementById('previewAddFoto').src = url;
                document.getElementById('previewAddFotoSection').style.display = 'block';
                editedImageData = null;
            }
        });

        document.getElementById('btnEditAddFoto').addEventListener('click', function() {
            const src = document.getElementById('previewAddFoto').src;
            if (src) openTuiEditor(src);
        });

        document.getElementById('previewAddFoto').addEventListener('click', function() {
            if (this.src) openTuiEditor(this.src);
        });

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
            formData.append('Desc_Daily_Temuan', desc);
            formData.append('Id_Daily_Patrol', '{{ $daily_patrol->Id_Daily_Patrol }}');

            if (editedImageData) {
                formData.append('Path_Daily_Temuan', editedImageData);
            } else {
                formData.append('Path_Daily_Temuan', file);
            }

            try {
                const response = await fetch("{{ route('daily_temuan.store', ['id' => $daily_patrol->Id_Daily_Patrol]) }}", {
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

        document.getElementById('addDailyTemuanModal').addEventListener('hidden.bs.modal', function() {
            editedImageData = null;
            document.getElementById('addFotoTemuan').value = '';
            document.getElementById('addDescTemuan').value = '';
            document.getElementById('previewAddFotoSection').style.display = 'none';
        });

        // View modal
        viewModal.addEventListener("show.bs.modal", function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            currentIndex = temuanButtons.indexOf(button);
            loadTemuanData(button);
        });

        statusSwitchInput.addEventListener("change", () => {
            const formData = new FormData(statusForm);
            fetch(statusForm.action, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const idTemuan = formData.get('Id_Daily_Temuan');
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
            .catch(err => { console.error(err); alert("Error saat update status"); });
        });

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
            statusTemuanId.value = id;
            document.getElementById("modalNamaPenemu").textContent = btn.dataset.namaPenemu || "-";
            document.getElementById("modalDescTemuan").textContent = btn.dataset.descTemuan || "-";
            document.getElementById("modalDescUpdate").textContent = btn.dataset.descUpdate || "-";

            document.getElementById("modalFotoTemuan").src = btn.dataset.fotoTemuan ?
                `../uploads/${btn.dataset.fotoTemuan}` : "../storage/no-img.jpeg";
            document.getElementById("modalFotoUpdate").src = btn.dataset.fotoUpdate ?
                `../uploads/${btn.dataset.fotoUpdate}` : "../storage/no-img.jpeg";

            statusSwitchInput.checked = (btn.dataset.status === "Done");
            updateTemuanForm.action = `../daily_temuan/${id}`;
            statusForm.action = `../daily_temuan/${id}/status`;

            const picNik = btn.dataset.picProsesNik || '';
            const picName = btn.dataset.picProsesName || '';

            document.getElementById('pic_proses_nik_daily_input').value = picNik;
            document.getElementById('pic_proses_search').value = '';
            const picManualInput = document.getElementById('picManualInput');
            if (picManualInput) picManualInput.value = '';
            document.getElementById('selectedPicProses').classList.add('d-none');

            let type = 'member';
            if (picNik) {
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

            const radio = document.querySelector(`input[name="pic_type"][value="${type}"]`);
            if (radio) radio.checked = true;
            if (typeof togglePicInput === 'function') togglePicInput();

            const picDisplayEl = document.getElementById('modalPicProsesName');
            if (picDisplayEl) picDisplayEl.textContent = picNik ? (picName || picNik) : '-';

            const seqEl = document.getElementById('modalTemuanNo');
            if (seqEl) {
                seqEl.textContent = '#' + (btn.dataset.iteration || '');
                seqEl.className = 'badge bg-secondary ms-2 fs-5';
            }

            rotationState[`temuan_${id}`] = parseInt(btn.dataset.rotateTemuan || 0);
            rotationState[`perbaikan_${id}`] = parseInt(btn.dataset.rotateUpdate || 0);
            if (typeof window.applyRotationFromState === 'function') window.applyRotationFromState(id);

            const descUpdateInput = document.getElementById('Desc_Daily_Update_Temuan');
            if (descUpdateInput) descUpdateInput.value = btn.dataset.descUpdate || '';

            const fileInput = document.getElementById('Path_Daily_Update_Temuan');
            if (fileInput) {
                fileInput.value = '';
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

        // PIC Proses Search
        let picSearchTimeout;
        document.getElementById('pic_proses_search').addEventListener('input', function() {
            clearTimeout(picSearchTimeout);
            const q = this.value.trim();
            if (q.length < 1) { document.getElementById('picProsesResults').style.display = 'none'; return; }
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
                                    document.getElementById('pic_proses_nik_daily_input').value = emp.nik;
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
            document.getElementById('pic_proses_nik_daily_input').value = '';
            document.getElementById('selectedPicProses').classList.add('d-none');
        });

        const picRadios = document.getElementsByName('pic_type');
        picRadios.forEach(radio => radio.addEventListener('change', togglePicInput));

        const picManualInput = document.getElementById('picManualInput');
        if (picManualInput) {
            picManualInput.addEventListener('input', function() {
                document.getElementById('pic_proses_nik_daily_input').value = this.value;
            });
        }

        function togglePicInput() {
            const checkedRadio = document.querySelector('input[name="pic_type"]:checked');
            if (!checkedRadio) return;
            const type = checkedRadio.value;
            const searchContainer = document.getElementById('picSearchContainer');
            const manualContainer = document.getElementById('picManualContainer');
            const hiddenInput = document.getElementById('pic_proses_nik_daily_input');

            if (searchContainer) searchContainer.classList.add('d-none');
            if (manualContainer) manualContainer.classList.add('d-none');

            if (type === 'member') {
                if (searchContainer) searchContainer.classList.remove('d-none');
            } else if (type === 'leader') {
                hiddenInput.value = 'Leader';
            } else if (type === 'team') {
                hiddenInput.value = 'Team';
            } else {
                if (manualContainer) manualContainer.classList.remove('d-none');
                if (picManualInput) hiddenInput.value = picManualInput.value;
            }
        }

        const updateTemuanFormElement = document.getElementById('updateTemuanForm');
        if (updateTemuanFormElement) {
            updateTemuanFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                const picNik = document.getElementById('pic_proses_nik_daily_input').value;
                if (!picNik) { alert('Mohon pilih PIC Proses terlebih dahulu.'); return; }

                const formData = new FormData(this);
                const actionUrl = this.action;
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.textContent = "Menyimpan...";

                fetch(actionUrl, {
                    method: "POST",
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.full_path_update) {
                            const modalImg = document.getElementById("modalFotoUpdate");
                            if (modalImg) modalImg.src = data.full_path_update;
                        }
                        const modalDesc = document.getElementById("modalDescUpdate");
                        if (modalDesc) modalDesc.textContent = data.desc_update || "-";

                        const statusTemuanIdVal = document.getElementById("statusTemuanId").value;
                        const btn = document.querySelector(`button.view-temuan[data-id="${statusTemuanIdVal}"]`);
                        if (btn) {
                            btn.dataset.fotoUpdate = data.path_update || "";
                            btn.dataset.descUpdate = data.desc_update || "";
                            btn.dataset.picProsesNik = data.pic_nik || "";
                            btn.dataset.picProsesName = data.pic_name || "";

                            const row = btn.closest("tr");
                            if (row) {
                                const cells = row.querySelectorAll("td");
                                if (data.full_path_update && cells[4]) {
                                    cells[4].innerHTML = `<img src="${data.full_path_update}" style="max-height:100px;">`;
                                }
                                if (cells[5]) cells[5].textContent = data.desc_update || "";
                                if (cells[6]) {
                                    let picDisplay = data.pic_name || data.pic_nik || "-";
                                    cells[6].innerHTML = `<div style="max-height: 100px; overflow-y: auto;"><span class="badge bg-info text-white">${picDisplay}</span></div>`;
                                    const modalPicProsesName = document.getElementById('modalPicProsesName');
                                    if (modalPicProsesName) modalPicProsesName.textContent = picDisplay;
                                }
                            }
                        }
                        const fileInput = this.querySelector('input[type="file"]');
                        if (fileInput) fileInput.value = "";
                        if (data.path_update) {
                            const existingInfo = fileInput.parentElement ? fileInput.parentElement.querySelector('.existing-foto-info') : null;
                            if (existingInfo) existingInfo.remove();
                        }
                    } else {
                        alert("Gagal update perbaikan: " + (data.message || ''));
                    }
                })
                .catch(err => { console.error(err); alert("Error saat update perbaikan"); })
                .finally(() => { submitBtn.disabled = false; submitBtn.innerHTML = originalText; });
            });
        }
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
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
            });
        }

        function applyRowHighlight() {
            $('#example tbody tr').each(function() {
                const btn = this.querySelector('button.view-temuan');
                if (!btn) return;
                if ((btn.dataset.status || '').toLowerCase() === 'done') this.classList.add('done-row');
                else this.classList.remove('done-row');
            });
        }
        applyRowHighlight();
        table.on('draw', applyRowHighlight);
    });
</script>

<script>
    let rotationState = {};

    function applyRotationFromState(id) {
        const tDeg = rotationState[`temuan_${id}`] || 0;
        const pDeg = rotationState[`perbaikan_${id}`] || 0;
        const imgTemuan = document.getElementById('modalFotoTemuan');
        const imgPerbaikan = document.getElementById('modalFotoUpdate');
        if (imgTemuan) {
            const tScale = (tDeg === 90 || tDeg === 270) ? 0.7 : 1;
            imgTemuan.style.transform = `rotate(${tDeg}deg) scale(${tScale})`;
        }
        if (imgPerbaikan) {
            const pScale = (pDeg === 90 || pDeg === 270) ? 0.7 : 1;
            imgPerbaikan.style.transform = `rotate(${pDeg}deg) scale(${pScale})`;
        }
    }
    window.applyRotationFromState = applyRotationFromState;

    function rotateImage(imgId, degrees) {
        const id = document.getElementById('statusTemuanId').value;
        if (!id) return;
        const img = document.getElementById(imgId);
        if (!img) return;

        const type = imgId === 'modalFotoTemuan' ? 'temuan' : 'perbaikan';
        const key = `${type}_${id}`;
        let current = rotationState[key] || 0;
        current = ((current + degrees) % 360 + 360) % 360;
        rotationState[key] = current;

        const isVertical = (current === 90 || current === 270);
        img.style.transform = `rotate(${current}deg) scale(${isVertical ? 0.7 : 1})`;

        fetch(`../daily_temuan/${id}/rotate`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: type, angle: current })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const btn = document.querySelector(`button.view-temuan[data-id="${id}"]`);
                if (btn) {
                    if (type === 'temuan') btn.dataset.rotateTemuan = current;
                    if (type === 'perbaikan') btn.dataset.rotateUpdate = current;
                }
            }
        })
        .catch(err => console.error("Rotation save failed", err));
    }
</script>
@endsection
