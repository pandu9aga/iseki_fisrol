@extends('admins.layouts.index')
@section('content')
<div class="container-fluid">

                <!-- Begin Page Content -->
<div class="card-body">
  <div style="text-align: center; margin-bottom: 30px;">
    <h2 style="margin: 0 0 40px 0; text-transform: uppercase;">Perbaikan 5S</h2>

    <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 20px; padding: 20px; border-radius: 10px; background-color: #f9f9f9;">

     <!-- Tambah Keterangan -->
      <div style="width: 100%; text-align: left">
        <label for="keteranganTemuan" style="font-weight: bold;">Pilih Temuan 5S</label>
        <div class="input-group mt-2">
          <input type="text" class="form-control rounded-start" id="keteranganTemuan" placeholder="Masukkan keterangan temuan...">
          <label class="input-group-text rounded-end" for="keteranganTemuan">Choose</label>
        </div>
      </div>    

      <!-- Upload Gambar -->
      <div style="width: 100%; text-align: left">
        <label for="gambarTemuan" style="font-weight: bold;">Tambahkan Gambar Perbaikans 5S</label>
        <div class="input-group mt-2">
          <input type="file" class="form-control rounded-start" id="gambarTemuan">
          <label class="input-group-text rounded-end" for="gambarTemuan">Upload</label>
        </div>
      </div>

      <!-- Tambah Keterangan -->
      <div style="width: 100%; text-align: left">
        <label for="keteranganTemuan" style="font-weight: bold;">Tambahkan Keterangan</label>
        <div class="input-group mt-2">
          <input type="text" class="form-control rounded-start" id="keteranganTemuan" placeholder="Masukkan keterangan temuan...">
          <label class="input-group-text rounded-end" for="keteranganTemuan">Upload</label>
        </div>
      </div>

    </div>
  </div>
</div>
</div>

@endsection