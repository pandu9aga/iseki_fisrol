@extends('admins.layouts.index')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Import Members</h1>
    
    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('member.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="excel" class="form-label">File Excel</label>
                    <input type="file" name="excel" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Upload</button>
            </form>
        </div>
    </div>
</div>
@endsection
