@extends('users.layouts.index')

@section('content')
    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-0 font-weight-bold text-primary">Data Daily Patrol Member</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Daily Patrol</th>
                                <th>Nama User</th>
                                <th>Nama Member</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patrol_members as $pm)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pm->dailyPatrol->Name_Daily_Patrol ?? '-' }}</td>
                                    <td>{{ $pm->user->Name_User ?? '-' }}</td>
                                    <td>{{ $pm->member->Name_Member ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('user_daily_patrol') }}" class="btn btn-outline-primary">Back to Daily Patrol</a>
                </div>
            </div>
        </div>
    </div>
@endsection
