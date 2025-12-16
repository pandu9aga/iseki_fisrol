<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Temuan;
use App\Models\Patrol;
use App\Models\Member;

class UserTemuanController extends Controller
{
    /**
     * Tampilkan daftar temuan berdasarkan patrol
     */
    public function index($id)
    {
        // Pastikan user sudah login
        if (!Session::has('login_id') || Session::get('login_type') != 2) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        // Ambil data patrol
        $patrol = Patrol::findOrFail($id);

        // Ambil data member berdasarkan NIK dari session
        $member = Member::where('nik', Session::get('login_nik'))->first();
        if (!$member) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Sesi login sudah berakhir. Silakan login kembali.']);
        }

        // Ambil semua temuan milik member ini di patrol terkait
        $temuans = Temuan::with('patrol')
            ->where('Id_Patrol', $id)
            ->where('Id_Member', $member->id)
            ->get();

        return view('users.temuans.index', compact('temuans', 'patrol', 'member'));
    }

    /**
     * Simpan temuan baru
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'Path_Temuan' => 'nullable|image|mimes:jpg,png,jpeg',
            'Desc_Temuan' => 'nullable|string|max:1000',
        ]);

        // Pastikan session masih valid
        $member = Member::where('nik', Session::get('login_nik'))->first();
        if (!$member) {
            return back()->withErrors(['error' => 'Data member tidak ditemukan atau sesi telah berakhir.']);
        }

        $pathTemuan = null;

        if ($request->hasFile('Path_Temuan')) {
            $file = $request->file('Path_Temuan');

            // Baca info gambar
            $imageInfo = getimagesize($file);
            if (!$imageInfo) {
                return back()->withErrors(['error' => 'File gambar tidak valid.']);
            }

            $mime = $imageInfo['mime'];

            // Buat resource GD sesuai tipe file
            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($file->getPathname());
                    break;
                default:
                    return back()->withErrors(['error' => 'Format gambar tidak didukung.']);
            }

            if (!$source) {
                return back()->withErrors(['error' => 'Gagal memuat gambar.']);
            }

            $width  = imagesx($source);
            $height = imagesy($source);

            // Resize agar maksimal dimensi 1280px
            $maxDim = 1280;
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newWidth  = intval($width * $ratio);
                $newHeight = intval($height * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Handle transparansi untuk PNG
                if ($mime === 'image/png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $resized;
            }

            // Pastikan folder upload ada
            $folder = public_path('uploads/temuans');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // Generate nama file unik dan simpan sebagai JPG untuk kompresi
            $filename = Str::uuid() . '.jpg';
            $pathTemuan = 'temuans/' . $filename;
            $fullPath = $folder . '/' . $filename;

            // Kompresi hingga ukuran < 1MB
            $quality = 85;
            do {
                ob_start();
                imagejpeg($source, null, $quality);
                $data = ob_get_clean();
                $size = strlen($data);
                $quality -= 5;
            } while ($size > 1024 * 1024 && $quality > 10);

            file_put_contents($fullPath, $data);
            imagedestroy($source);
        }

        // Simpan data temuan ke database
        Temuan::create([
            'Path_Temuan'   => $pathTemuan,
            'Desc_Temuan'   => $request->Desc_Temuan,
            'Id_Patrol'     => $id,
            'Id_Member'     => $member->id,
            'Status_Temuan' => 'Pending',
        ]);

        return redirect()->route('user_temuan.index', ['id' => $id])
            ->with('success', 'Data temuan berhasil disimpan.');
    }

    /**
     * Update temuan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Path_Temuan' => 'nullable|image|mimes:jpg,png,jpeg',
            'Desc_Temuan' => 'nullable|string|max:1000',
        ]);

        $temuan = Temuan::findOrFail($id);

        if ($request->hasFile('Path_Temuan')) {
            // Hapus file lama jika ada
            if ($temuan->Path_Temuan) {
                $oldPath = public_path('uploads/' . $temuan->Path_Temuan);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('Path_Temuan');

            // Validasi dan baca info gambar
            $imageInfo = getimagesize($file);
            if (!$imageInfo) {
                return back()->withErrors(['error' => 'File gambar tidak valid.']);
            }

            $mime = $imageInfo['mime'];
            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($file->getPathname());
                    break;
                default:
                    return back()->withErrors(['error' => 'Format gambar tidak didukung.']);
            }

            if (!$source) {
                return back()->withErrors(['error' => 'Gagal memuat gambar.']);
            }

            $width  = imagesx($source);
            $height = imagesy($source);

            // Resize jika melebihi 1280px
            $maxDim = 1280;
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newWidth  = intval($width * $ratio);
                $newHeight = intval($height * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Handle transparansi (meski nanti disimpan sebagai JPG)
                if ($mime === 'image/png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $resized;
            }

            // Pastikan folder ada
            $folder = public_path('uploads/temuans');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // Simpan sebagai JPG terkompresi
            $filename = Str::uuid() . '.jpg';
            $fullPath = $folder . '/' . $filename;

            // Kompresi hingga < 1MB
            $quality = 85;
            do {
                ob_start();
                imagejpeg($source, null, $quality);
                $data = ob_get_clean();
                $size = strlen($data);
                $quality -= 5;
            } while ($size > 1024 * 1024 && $quality > 10);

            file_put_contents($fullPath, $data);
            imagedestroy($source);

            // Update path di model
            $temuan->Path_Temuan = 'temuans/' . $filename;
        }

        // Update deskripsi (boleh kosong)
        $temuan->Desc_Temuan = $request->Desc_Temuan;
        $temuan->save();

        return redirect()->route('user_temuan.index', ['id' => $temuan->Id_Patrol])
            ->with('success', 'Data temuan berhasil diperbarui.');
    }

    /**
     * Hapus temuan
     */
    public function destroy($id)
    {
        $temuan = Temuan::findOrFail($id);
        $idPatrol = $temuan->Id_Patrol;

        // Hapus file gambar jika ada
        foreach (['Path_Temuan', 'Path_Update_Temuan'] as $path) {
            $filePath = public_path('uploads/' . $temuan->$path);
            if ($temuan->$path && file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $temuan->delete();

        return redirect()->route('user_temuan.index', ['id' => $idPatrol])
            ->with('success', 'Data temuan berhasil dihapus.');
    }
}
