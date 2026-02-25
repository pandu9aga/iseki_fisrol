@extends('admins.layouts.index')
@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-bar me-2"></i>Laporan Kontribusi
            </h4>
        </div>
        <div class="card-body">

            <!-- Filter Month & Year -->
            <form method="GET" action="{{ route('admins.reports.kontribusi') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="month" class="form-label fw-bold">Bulan</label>
                        <select name="month" id="month" class="form-select form-control">
                            @foreach($months as $key => $name)
                            <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="year" class="form-label fw-bold">Tahun</label>
                        <select name="year" id="year" class="form-select form-control">
                            @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="mt-3">
                    <h5 class="text-secondary">
                        Laporan Periode: <strong>{{ $months[$month] ?? $month }} {{ $year }}</strong>
                    </h5>
                    <!-- <small class="text-muted">
                        *Data dihitung berdasarkan tanggal patroli (Patrol Date)
                    </small> -->
                </div>
            </form>

            <!-- Daftar Patrol -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-light" data-bs-toggle="collapse" data-bs-target="#collapsePatrol" style="cursor:pointer;">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-calendar-check me-2"></i>Daftar Patrol yang Terhitung ({{ $patrolsList->count() }})
                        <small class="text-muted float-end"><i class="fas fa-chevron-down"></i></small>
                    </h6>
                </div>
                <div class="collapse show" id="collapsePatrol">
                    <div class="card-body">
                        @if($patrolsList->isEmpty())
                        <p class="text-muted mb-0 text-center">Tidak ada patrol pada periode ini.</p>
                        @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped mb-0">
                                <thead class="table-info">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Nama Patrol</th>
                                        <th>Waktu Patrol</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patrolsList as $p)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $p->Name_Patrol }}</td>
                                        <td>{{ \Carbon\Carbon::parse($p->Time_Patrol)->format('d F Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="row">
                <!-- Kolom 1: Penemu Temuan -->
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-search me-1"></i>
                                Penemu Temuan
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50" class="text-center">No</th>
                                            <th>Nama</th>
                                            <th width="120" class="text-center">Total Temuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($penemuData as $index => $row)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $row->nama }}</strong>
                                                <br><small class="text-muted">{{ $row->nik_penemu }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $row->total }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                Belum ada data
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if($penemuData->count() > 0)
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total Orang:</td>
                                            <td class="text-center fw-bold">{{ $penemuData->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total Temuan:</td>
                                            <td class="text-center fw-bold">{{ $penemuData->sum('total') }}</td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: PIC Proses Perbaikan -->
                <div class="col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-wrench me-1"></i>
                                PIC Proses
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50" class="text-center">No</th>
                                            <th>Nama</th>
                                            <th width="130" class="text-center">Total Perbaikan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($picData as $index => $row)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $row->nama }}</strong>
                                                <br><small class="text-muted">{{ $row->pic_proses_nik }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success fs-6">{{ $row->total }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                Belum ada data
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if($picData->count() > 0)
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total Orang:</td>
                                            <td class="text-center fw-bold">{{ $picData->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total Perbaikan:</td>
                                            <td class="text-center fw-bold">{{ $picData->sum('total') }}</td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection