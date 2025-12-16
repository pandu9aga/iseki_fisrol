@extends('users.layouts.index') <style>
    /* Semua tabel patrol ukurannya seragam */
    /* Semua tabel patrol ukurannya seragam */
    .table-patrol th,
    .table-patrol td {
        vertical-align: middle;
        text-align: center;
    }

    .table-patrol thead th {
        text-align: center !important;
        vertical-align: middle !important;
    }

    .table-patrol th:nth-child(1),
    .table-patrol td:nth-child(1) {
        width: 50px;
    }

    .table-patrol th:nth-child(2),
    .table-patrol td:nth-child(2) {
        width: 200px;
    }

    .table-patrol th:nth-child(3),
    .table-patrol td:nth-child(3) {
        width: 400px;
        text-align: left;
        /* <-- ini bikin rata kiri */
    }

    .table-patrol th:nth-child(4),
    .table-patrol td:nth-child(4) {
        width: 80px;
    }

    .table-patrol th:nth-child(5),
    .table-patrol td:nth-child(5) {
        width: 250px;
    }
</style> @section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <form action="{{ route('user_nilai.store', ['id' => $patrol->Id_Patrol]) }}" method="POST"> @csrf <div
                    class="card-header py-3">
                    <h4 class="m-0 font-weight-bold text-primary mb-2">Formulir Patrol 5S</h4>
                    <!-- Baris info patrol + tombol -->
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="m-0 font-weight-bold"> Lokasi Patrol 5S: {{ $patrol->Name_Patrol ?? '-' }} </p>
                            <p class="m-0 font-weight"> Waktu Patrol 5S:
                                {{ $patrol->Time_Patrol ? \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d-m-Y H:i') : '-' }}
                            </p>
                            <p class="m-0 font-weight">
                                Member: <strong>{{ session('login_name') ?? '-' }}</strong>
                            </p>

                            <table border="1" cellspacing="0" cellpadding="6"
                                style="width:100%; border-collapse: collapse; text-align: center; font-weight:normal; color:#000; background-color:#fff;">
                                <thead>
                                    <tr style="background-color:#f2f2f2; font-weight:bold; color:#000;">
                                        <th style="width:15%;">Standar Poin</th>
                                        <th>0</th>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-weight:bold;">Keterangan</td>
                                        <td style="text-align: left;">Belum melakukan kegiatan 5S/belum ada usaha sama
                                            sekali.</td>
                                        <td style="text-align: left;">Sudah memulai kegiatan 5S tapi masih ada banyak
                                            perbaikan major (perbaikan butuh beberapa hari).</td>
                                        <td style="text-align: left;">Cukup baik, hanya perlu beberapa improvement minor
                                            (perbaikan bisa saat itu juga).</td>
                                        <td style="text-align: left;">Sudah baik hanya perlu sedikit improvement.</td>
                                        <td style="text-align: left;">Sudah sangat baik, dipertahankan seperti ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th colspan="2">Penilaian</th>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Step 1: SEIRI <br> Ringkas</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 80px;">0-4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Makanan</td>
                                    <td>Tidak ada makanan di tempat melakukan pekerjaan</td>
                                    <td>
                                        <input type="text" name="nilai[1]" value="{{ $existingNilai[1] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3">2</td>
                                    <td rowspan="3">Part, Alat dan Bahan</td>
                                    <td>Tidak ada barang yang tidak diperlukan di area kerja</td>
                                    <td>
                                        <input type="text" name="nilai[2]" value="{{ $existingNilai[2] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Tidak ada part dan alat yang tergeletak di lantai</td>
                                    <td>
                                        <input type="text" name="nilai[3]" value="{{ $existingNilai[3] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Stok persediaan barang tidak berlebihan</td>
                                    <td>
                                        <input type="text" name="nilai[4]" value="{{ $existingNilai[4] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Dokumen</td>
                                    <td>Tidak ada dokumen yang tidak diperlukan di area kerja</td>
                                    <td>
                                        <input type="text" name="nilai[5]" value="{{ $existingNilai[5] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Pengumuman/Tampilan</td>
                                    <td>Pengumuman dan tampilan sesuai standar</td>
                                    <td>
                                        <input type="text" name="nilai[6]" value="{{ $existingNilai[6] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td colspan="2">
                                        {{ app('App\Http\Controllers\Users\UserNilaiController')->totalPerStep($patrol->Id_Patrol, 1) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> {{-- Bagian 2 --}} <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th colspan="2">Penilaian</th>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Step 2: SEITON <br> Rapi</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 80px;">0-4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5</td>
                                    <td>Garis pembagi</td>
                                    <td>Semua ada garis pembagi</td>
                                    <td>
                                        <input type="text" name="nilai[7]" value="{{ $existingNilai[7] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Label Identitas</td>
                                    <td>Semua barang (part, alat, bahan) terdapat label yang jelas</td>
                                    <td>
                                        <input type="text" name="nilai[8]" value="{{ $existingNilai[8] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3">7</td>
                                    <td rowspan="3">Alat, Part dan Bahan</td>
                                    <td>Diletakkan di tempat yang ditentukan</td>
                                    <td>
                                        <input type="text" name="nilai[9]" value="{{ $existingNilai[9] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Ditempatkan di tempat yang memadai</td>
                                    <td>
                                        <input type="text" name="nilai[10]" value="{{ $existingNilai[10] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Tidak ada alat rusak/tidak berfungsi</td>
                                    <td>
                                        <input type="text" name="nilai[11]" value="{{ $existingNilai[11] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Bahan B3</td>
                                    <td>Area stok B3 terdapat simbol MSDS</td>
                                    <td>
                                        <input type="text" name="nilai[12]" value="{{ $existingNilai[12] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>Akses Darurat</td>
                                    <td>Perangkat keselamatan tidak terhalang</td>
                                    <td>
                                        <input type="text" name="nilai[13]" value="{{ $existingNilai[13] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>Pengendali Visual</td>
                                    <td>Kondisi area kerja sesuai standar</td>
                                    <td>
                                        <input type="text" name="nilai[14]" value="{{ $existingNilai[14] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td colspan="2">
                                        {{ app('App\Http\Controllers\Users\UserNilaiController')->totalPerStep($patrol->Id_Patrol, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> {{-- Bagian 3 --}} <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th colspan="2">Penilaian</th>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Step 3: SEISO <br> Resik</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 80px;">0-4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>11</td>
                                    <td>Lantai</td>
                                    <td>Lantai bersih, tidak licin, tidak ada part/sampah</td>
                                    <td>
                                        <input type="text" name="nilai[15]" value="{{ $existingNilai[15] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>Mesin/Peralatan</td>
                                    <td>Membersihkan area mesin yang memungkinkan dijangkau</td>
                                    <td>
                                        <input type="text" name="nilai[16]" value="{{ $existingNilai[16] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td>Tempat Sampah</td>
                                    <td>Tempat sampah tidak overload</td>
                                    <td>
                                        <input type="text" name="nilai[17]" value="{{ $existingNilai[17] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td>Peralatan Kebersihan</td>
                                    <td>Jumlah peralatan kebersihan cukup dan tersusun rapi/tidak rusak</td>
                                    <td>
                                        <input type="text" name="nilai[18]" value="{{ $existingNilai[18] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>15</td>
                                    <td>Jadwal Kebersihan</td>
                                    <td>Ada jadwal khusus kebersihan</td>
                                    <td>
                                        <input type="text" name="nilai[19]" value="{{ $existingNilai[19] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td colspan="2">
                                        {{ app('App\Http\Controllers\Users\UserNilaiController')->totalPerStep($patrol->Id_Patrol, 3) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> {{-- Bagian 4 --}} <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th colspan="2">Penilaian</th>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Step 4: SEIKETSU <br> Rawat</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 80px;">0-4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>16</td>
                                    <td>Pengisian Check Sheet</td>
                                    <td>Check sheet diisi sesuai jadwal</td>
                                    <td>
                                        <input type="text" name="nilai[20]" value="{{ $existingNilai[20] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>17</td>
                                    <td>Kondisi Lingkungan</td>
                                    <td>Bersih dan kerapian terjaga</td>
                                    <td>
                                        <input type="text" name="nilai[21]" value="{{ $existingNilai[21] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>18</td>
                                    <td>Visual Display</td>
                                    <td>Standard ditempel di tempat mudah terlihat</td>
                                    <td>
                                        <input type="text" name="nilai[22]" value="{{ $existingNilai[22] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td colspan="2">
                                        {{ app('App\Http\Controllers\Users\UserNilaiController')->totalPerStep($patrol->Id_Patrol, 4) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> {{-- Bagian 5 --}} <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-patrol">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No</th>
                                    <th colspan="2">Cek Item</th>
                                    <th colspan="2">Penilaian</th>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Step 5: SHITSUKE <br> Rajin</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 80px;">0-4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>19</td>
                                    <td>Peraturan Departemen</td>
                                    <td>Mematuhi peraturan internal yang berlaku</td>
                                    <td>
                                        <input type="text" name="nilai[23]" value="{{ $existingNilai[23] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>20</td>
                                    <td>Berpakaian</td>
                                    <td>Memakai pakaian dan atribut sesuai ketentuan</td>
                                    <td>
                                        <input type="text" name="nilai[24]" value="{{ $existingNilai[24] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td>Pemisahan Sampah</td>
                                    <td>Mematuhi peraturan pemilahan sampah</td>
                                    <td>
                                        <input type="text" name="nilai[25]" value="{{ $existingNilai[25] ?? '' }}"
                                            class="form-control nilai-input" placeholder="0–4 atau -" required>
                                    </td>
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score</td>
                                    <td colspan="2">
                                        {{ app('App\Http\Controllers\Users\UserNilaiController')->totalPerStep($patrol->Id_Patrol, 5) }}
                                    </td>
                                </tr>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">Total Score Keseluruhan</td>
                                    <td colspan="2">
                                        {{ app('App\Http\Controllers\Users\UserNilaiController')->totalNilai($patrol->Id_Patrol) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <!-- Tombol Back di kiri -->
                    <a href="{{ route('user_patrol') }}" class="btn btn-outline-primary">Back to Patrol</a>
                    <!-- Tombol Simpan di kanan -->
                    <button type="submit" class="btn btn-primary">
                        Simpan Nilai Patrol
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.nilai-input').forEach(input => {
            input.addEventListener('input', function(e) {
                let val = e.target.value.trim();

                // Izinkan kosong sementara (biar user bisa hapus dulu sebelum isi)
                if (val === '') return;

                // Jika "-", "–", atau "—" → biarkan (anggap valid)
                if (val === '-' || val === '–' || val === '—') return;

                // Jika bukan angka → hapus dan beri warning
                if (isNaN(val)) {
                    alert('Input hanya boleh angka 0–4 atau "-"');
                    e.target.value = '';
                    return;
                }

                // Jika angka di luar 0–4 → reset dan beri warning
                const num = parseInt(val);
                if (num < 0 || num > 4) {
                    alert('Nilai harus antara 0–4 atau "-"');
                    e.target.value = '';
                }
            });
        });
    </script>
@endsection
