@extends('users.layouts.index')
@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Cards -->
        <div class="row">
            <!-- Total Patrol -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Patrol</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPatrol }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-search fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Patrol -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Patrol</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Patrol</th>
                                    <th>Waktu Patrol</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($patrols as $patrol)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $patrol->Name_Patrol }}</td>
                                        <td>{{ $patrol->Time_Patrol }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.js') }}"></script>
    <script>
        const ctx = document.getElementById('perbaikanChart').getContext('2d');
        const perbaikanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Patrol', 'Done', 'Pending'],
                datasets: [{
                    label: 'Jumlah',
                    data: [{{ $totalPatrol }}, {{ $totalPerbaikan }}, {{ $pendingTemuan }}],
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e'],
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
@endsection
