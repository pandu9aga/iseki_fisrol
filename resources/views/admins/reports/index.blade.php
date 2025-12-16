@extends('admins.layouts.index')

<style>
    .table-patrol th,
    .table-patrol td {
        vertical-align: middle;
        text-align: center;
    }

    .table-patrol th:nth-child(1),
    .table-patrol td:nth-child(1) {
        width: 50px;
    }

    .table-patrol th:nth-child(2),
    .table-patrol td:nth-child(2) {
        width: 200px;
        text-align: left;
    }

    .table-patrol th:nth-child(4),
    .table-patrol td:nth-child(4) {
        width: 80px;
    }

    .table-patrol th:nth-child(n+5),
    .table-patrol td:nth-child(n+5) {
        width: 100px;
    }
</style>

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <form action="{{ route('nilai.store', ['id' => $patrol->Id_Patrol]) }}" method="POST">
                @csrf
                <div class="card-header py-3">
                    <h3 class="m-0 font-weight-bold text-primary mb-2">Hasil Score Patrol 5S</h3>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="m-0 font-weight-bold">Lokasi Patrol 5S: {{ $patrol->Name_Patrol ?? '-' }}</p>
                            <p class="m-0 font-weight">Waktu Patrol 5S:
                                {{ $patrol->Time_Patrol ? \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d-m-Y H:i') : '-' }}
                            </p>
                            <h3 class="mt-3">TOTAL SCORE:
                                {{ app('App\Http\Controllers\Admins\AverageController')->averageTotal($patrol->Id_Patrol) }}
                            </h3>
                        </div>
                    </div>
                </div>

                {{-- Bagian 1: SEIRI --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            @php
                                $auditorCount = count($auditors);
                            @endphp
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th>Penilaian</th>
                                    <th colspan="{{ $auditorCount }}">Auditor</th>
                                </tr>
                                <tr>
                                    <th>Step 1: SEIRI <br> Ringkas</th>
                                    <th>Deskripsi</th>
                                    <th>0-4</th>
                                    @foreach ($auditors as $auditor)
                                        <th>{{ $auditor->auditor_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Makanan</td>
                                    <td style="text-align: left;">Tidak ada makanan di tempat melakukan pekerjaan</td>
                                    <td>{{ $averagePerValue[1] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[1] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td rowspan="3">2</td>
                                    <td rowspan="3">Part, Alat dan Bahan</td>
                                    <td style="text-align: left;">Tidak ada barang yang tidak diperlukan di area kerja</td>
                                    <td>{{ $averagePerValue[2] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[2] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Tidak ada part dan alat yang tergeletak di lantai</td>
                                    <td style="text-align: center;">{{ $averagePerValue[3] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[3] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Stok persediaan barang tidak berlebihan</td>
                                    <td style="text-align: center;">{{ $averagePerValue[4] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[4] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Dokumen</td>
                                    <td style="text-align: left;">Tidak ada dokumen yang tidak diperlukan di area kerja</td>
                                    <td>{{ $averagePerValue[5] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[5] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Pengumuman/Tampilan</td>
                                    <td style="text-align: left;">Pengumuman dan tampilan sesuai standar</td>
                                    <td>{{ $averagePerValue[6] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[6] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td style="text-align: center;">
                                        {{ app('App\Http\Controllers\Admins\AverageController')->averagePerStep($patrol->Id_Patrol, 1) }}
                                    </td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $total = app(
                                                'App\Http\Controllers\Admins\AverageController',
                                            )->calculateTotalForAuditor(
                                                $patrol->Id_Patrol,
                                                1, // step 1
                                                $auditor->Id_User,
                                                $auditor->Id_Member,
                                            );
                                        @endphp
                                        <td>{{ $total }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bagian 2: SEITON --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th>Penilaian</th>
                                    <th colspan="{{ $auditorCount }}">Auditor</th>
                                </tr>
                                <tr>
                                    <th>Step 2: SEITON <br> Rapi</th>
                                    <th>Deskripsi</th>
                                    <th>0-4</th>
                                    @foreach ($auditors as $auditor)
                                        <th>{{ $auditor->auditor_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5</td>
                                    <td>Garis pembagi</td>
                                    <td style="text-align: left;">Semua ada garis pembagi</td>
                                    <td>{{ $averagePerValue[7] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[7] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Label Identitas</td>
                                    <td style="text-align: left;">Semua barang (part, alat, bahan) terdapat label yang jelas
                                    </td>
                                    <td>{{ $averagePerValue[8] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[8] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td rowspan="3">7</td>
                                    <td rowspan="3">Alat, Part dan Bahan</td>
                                    <td style="text-align: left;">Diletakkan di tempat yang ditentukan</td>
                                    <td>{{ $averagePerValue[9] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[9] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Ditempatkan di tempat yang memadai</td>
                                    <td style="text-align: center;">{{ $averagePerValue[10] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[10] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Tidak ada alat rusak/tidak berfungsi</td>
                                    <td style="text-align: center;">{{ $averagePerValue[11] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[11] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Bahan B3</td>
                                    <td style="text-align: left;">Area stok B3 terdapat simbol MSDS</td>
                                    <td>{{ $averagePerValue[12] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[12] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>Akses Darurat</td>
                                    <td style="text-align: left;">Perangkat keselamatan tidak terhalang</td>
                                    <td>{{ $averagePerValue[13] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[13] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>Pengendali Visual</td>
                                    <td style="text-align: left;">Kondisi area kerja sesuai standar</td>
                                    <td>{{ $averagePerValue[14] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[14] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td style="text-align: center;">
                                        {{ app('App\Http\Controllers\Admins\AverageController')->averagePerStep($patrol->Id_Patrol, 2) }}
                                    </td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $total = app(
                                                'App\Http\Controllers\Admins\AverageController',
                                            )->calculateTotalForAuditor(
                                                $patrol->Id_Patrol,
                                                2, // step 1
                                                $auditor->Id_User,
                                                $auditor->Id_Member,
                                            );
                                        @endphp
                                        <td>{{ $total }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bagian 3: SEISO --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th>Penilaian</th>
                                    <th colspan="{{ $auditorCount }}">Auditor</th>
                                </tr>
                                <tr>
                                    <th>Step 3: SEISO <br> Resik</th>
                                    <th>Deskripsi</th>
                                    <th>0-4</th>
                                    @foreach ($auditors as $auditor)
                                        <th>{{ $auditor->auditor_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>11</td>
                                    <td>Lantai</td>
                                    <td style="text-align: left;">Lantai bersih, tidak licin, tidak ada part/sampah</td>
                                    <td>{{ $averagePerValue[15] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[15] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>Mesin/Peralatan</td>
                                    <td style="text-align: left;">Membersihkan area mesin yang memungkinkan dijangkau</td>
                                    <td>{{ $averagePerValue[16] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[16] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td>Tempat Sampah</td>
                                    <td style="text-align: left;">Tempat sampah tidak overload</td>
                                    <td>{{ $averagePerValue[17] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[17] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td>Peralatan Kebersihan</td>
                                    <td style="text-align: left;">Jumlah peralatan kebersihan cukup dan tersusun rapi/tidak
                                        rusak</td>
                                    <td>{{ $averagePerValue[18] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[18] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>15</td>
                                    <td>Jadwal Kebersihan</td>
                                    <td style="text-align: left;">Ada jadwal khusus kebersihan</td>
                                    <td>{{ $averagePerValue[19] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[19] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td style="text-align: center;">
                                        {{ app('App\Http\Controllers\Admins\AverageController')->averagePerStep($patrol->Id_Patrol, 3) }}
                                    </td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $total = app(
                                                'App\Http\Controllers\Admins\AverageController',
                                            )->calculateTotalForAuditor(
                                                $patrol->Id_Patrol,
                                                3, // step 1
                                                $auditor->Id_User,
                                                $auditor->Id_Member,
                                            );
                                        @endphp
                                        <td>{{ $total }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bagian 4: SEIKETSU --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th>Penilaian</th>
                                    <th colspan="{{ $auditorCount }}">Auditor</th>
                                </tr>
                                <tr>
                                    <th>Step 4: SEIKETSU <br> Rawat</th>
                                    <th>Deskripsi</th>
                                    <th>0-4</th>
                                    @foreach ($auditors as $auditor)
                                        <th>{{ $auditor->auditor_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>16</td>
                                    <td>Pengisian Check Sheet</td>
                                    <td style="text-align: left;">Check sheet diisi sesuai jadwal</td>
                                    <td>{{ $averagePerValue[20] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[20] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>17</td>
                                    <td>Kondisi Lingkungan</td>
                                    <td style="text-align: left;">Bersih dan kerapian terjaga</td>
                                    <td>{{ $averagePerValue[21] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[21] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>18</td>
                                    <td>Visual Display</td>
                                    <td style="text-align: left;">Standard ditempel di tempat mudah terlihat</td>
                                    <td>{{ $averagePerValue[22] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[22] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td style="text-align: center;">
                                        {{ app('App\Http\Controllers\Admins\AverageController')->averagePerStep($patrol->Id_Patrol, 4) }}
                                    </td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $total = app(
                                                'App\Http\Controllers\Admins\AverageController',
                                            )->calculateTotalForAuditor(
                                                $patrol->Id_Patrol,
                                                4, // step 1
                                                $auditor->Id_User,
                                                $auditor->Id_Member,
                                            );
                                        @endphp
                                        <td>{{ $total }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bagian 5: SHITSUKE --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th>Penilaian</th>
                                    <th colspan="{{ $auditorCount }}">Auditor</th>
                                </tr>
                                <tr>
                                    <th>Step 5: SHITSUKE <br> Rajin</th>
                                    <th>Deskripsi</th>
                                    <th>0-4</th>
                                    @foreach ($auditors as $auditor)
                                        <th>{{ $auditor->auditor_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>19</td>
                                    <td>Peraturan Departemen</td>
                                    <td style="text-align: left;">Mematuhi peraturan internal yang berlaku</td>
                                    <td>{{ $averagePerValue[23] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[23] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>20</td>
                                    <td>Berpakaian</td>
                                    <td style="text-align: left;">Memakai pakaian dan atribut sesuai ketentuan</td>
                                    <td>{{ $averagePerValue[24] ?? '' }}</td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[24] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td>Pemisahan Sampah</td>
                                    <td style="text-align: left;">Mematuhi peraturan pemilahan sampah</td>
                                    <td>{{ $averagePerValue[25] ?? '' }}</td> <!-- Diperbaiki dari [1] ke [25] -->
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $record = $nilaiRecords->first(function ($n) use ($auditor) {
                                                return ($auditor->Id_User && $n->Id_User == $auditor->Id_User) ||
                                                    ($auditor->Id_Member && $n->Id_Member == $auditor->Id_Member);
                                            });
                                            $value = $record ? json_decode($record->Value_Nilai, true)[25] ?? '' : '';
                                        @endphp
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td style="text-align: center;">
                                        {{ app('App\Http\Controllers\Admins\AverageController')->averagePerStep($patrol->Id_Patrol, 5) }}
                                    </td>
                                    @foreach ($auditors as $auditor)
                                        @php
                                            $total = app(
                                                'App\Http\Controllers\Admins\AverageController',
                                            )->calculateTotalForAuditor(
                                                $patrol->Id_Patrol,
                                                5, // step 1
                                                $auditor->Id_User,
                                                $auditor->Id_Member,
                                            );
                                        @endphp
                                        <td>{{ $total }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('patrol') }}" class="btn btn-primary">Back to Patrol</a>
                </div>
            </form>
        </div>
    </div>
@endsection
