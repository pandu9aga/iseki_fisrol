@extends('users.layouts.index')

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Data Daily Patrol</h4>
            <p class="m-0 font-weight">
                Member: <strong>{{ session('login_name') ?? '-' }}</strong>
            </p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name Daily Patrol</th>
                            <th>Time Daily Patrol</th>
                            <th>Temuan</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Name Daily Patrol</th>
                            <th>Time Daily Patrol</th>
                            <th>Temuan</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($daily_patrols as $patrol)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $patrol->Name_Daily_Patrol }}</td>
                            <td>{{ \Carbon\Carbon::parse($patrol->Time_Daily_Patrol)->format('d F Y') }}</td>
                            <td>
                                <a href="{{ route('user_daily_temuan.index', ['id' => $patrol->Id_Daily_Patrol]) }}">
                                    Temuan
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
