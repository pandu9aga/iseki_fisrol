@extends('admins.layouts.index')

@section('content')
<style>
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        background: linear-gradient(90deg, #fff 0%, #ffe6ef 100%);
        border-bottom: none;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 1.2rem 1.5rem;
    }

    .card-header h4 {
        color: #d63384;
        font-weight: 600;
        margin: 0;
    }

    .btn-outline-primary {
        color: #d63384;
        border-color: #d63384;
        transition: 0.3s;
    }

    .btn-outline-primary:hover {
        background-color: #d63384;
        color: white;
    }

    .table-container {
        max-height: 700px;
        /* batas tinggi scroll */
        overflow-y: auto;
        border-radius: 10px;
    }

    table.dataTable {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead {
        background: #ffe6ef;
        color: #d63384;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-hover tbody tr:hover {
        background-color: #fff4f8;
    }

    .badge {
        border-radius: 8px;
        font-size: 0.85rem;
        padding: 5px 10px;
    }

    .badge-success {
        background-color: #ffb6c1 !important;
        color: #6a1b4d !important;
    }

    .badge-secondary {
        background-color: #f0f0f0 !important;
        color: #555 !important;
    }
</style>

<div class="container-fluid">

    <!-- Header -->
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0">Data Member</h4>
        </div>

        <!-- Body -->
        <div class="card-body">
            <div class="table-container">
                <table id="datatable" class="table table-bordered datatable table-striped table-hover align-middle">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Divisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $member->nik }}</td>
                            <td>{{ $member->nama }}</td>
                            <td>{{ $member->division->nama ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada data member.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    // Fitur pencarian nama
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var filter = this.value.toLowerCase();
        var rows = document.querySelectorAll('#memberTable tbody tr');
        rows.forEach(function(row) {
            var nama = row.cells[2].textContent.toLowerCase();
            if (nama.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection