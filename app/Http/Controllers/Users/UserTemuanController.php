<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
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
        if (!Session::has('login_id') || Session::get('login_type') != 2) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $patrol = Patrol::findOrFail($id);
        $member = Member::where('nik', Session::get('login_nik'))->first();
        if (!$member) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Sesi login sudah berakhir.']);
        }

        $temuans = Temuan::with('patrol')
            ->where('Id_Patrol', $id)
            ->where('Id_Member', $member->id)
            ->get();

        return view('users.temuans.index', compact('temuans', 'patrol', 'member'));
    }

    /**
     * Simpan temuan baru (dari AJAX + TUI Editor)
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'Desc_Temuan' => 'required|string|max:1000',
        ]);

        $member = Member::where('nik', Session::get('login_nik'))->first();
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.']);
        }

        // Terima base64 atau file upload untuk Path_Temuan
        $pathTemuan = null;
        if ($request->filled('Path_Temuan')) {
            $pathTemuan = $this->handleImageInput($request, 'Path_Temuan');
        }

        if (!$pathTemuan) {
            return response()->json(['success' => false, 'message' => 'Gagal memproses gambar.']);
        }

        $temuan = Temuan::create([
            'Path_Temuan'   => $pathTemuan,
            'Desc_Temuan'   => $request->Desc_Temuan,
            'Id_Patrol'     => $id,
            'Id_Member'     => $member->id,
            'Status_Temuan' => 'Pending',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update temuan (dari AJAX + TUI Editor)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Desc_Temuan' => 'required|string|max:1000',
        ]);

        $temuan = Temuan::findOrFail($id);

        // ✅ PISAHKAN CEK BASE64 DAN FILE
        if ($request->hasFile('Path_Temuan') || $request->filled('Path_Temuan')) {
            $newPath = $this->handleImageInput($request, 'Path_Temuan');

            if ($newPath) {
                if ($temuan->Path_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Temuan))) {
                    unlink(public_path('uploads/' . $temuan->Path_Temuan));
                }
                $temuan->Path_Temuan = $newPath;
            }
        }

        $temuan->Desc_Temuan = $request->Desc_Temuan;
        $temuan->save();

        return response()->json(['success' => true]);
    }

    /**
     * Hapus temuan
     */
    public function destroy($id)
    {
        $temuan = Temuan::findOrFail($id);

        // Hapus file gambar
        if ($temuan->Path_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Temuan))) {
            unlink(public_path('uploads/' . $temuan->Path_Temuan));
        }

        $temuan->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Helper: Proses input gambar (base64 atau file upload)
     * 
     * Input bisa berupa:
     * - String base64: "data:image/jpeg;base64,/9j/4AAQ..."
     * - File upload: $request->file('Path_Temuan')
     */
    private function handleImageInput(Request $request, $inputName)
    {
        $folder = public_path('uploads/temuans');
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        // === 1. Jika input adalah base64 dari TUI Editor ===
        if ($request->filled($inputName)) {
            $input = $request->input($inputName);

            // Cek apakah ini base64 image
            if (is_string($input) && Str::startsWith($input, 'data:image')) {
                if (!preg_match('/^data:image\/(\w+);base64,/', $input)) {
                    return null;
                }

                $data = preg_replace('/^data:image\/\w+;base64,/', '', $input);
                $binary = base64_decode($data);
                if ($binary === false) {
                    return null;
                }

                $filename = Str::uuid() . '.jpg';
                file_put_contents($folder . '/' . $filename, $binary);
                return 'temuans/' . $filename;
            }
        }

        // === 2. Jika input adalah file upload ===
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            if (!$file->isValid()) {
                return null;
            }

            $image = imagecreatefromstring(file_get_contents($file));
            if (!$image) {
                return null;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $maxDim = 1280;

            // Resize jika terlalu besar
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newWidth = intval($width * $ratio);
                $newHeight = intval($height * $ratio);
                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Handle transparansi (meski disimpan sebagai JPG)
                if (imageistruecolor($image)) {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Simpan sebagai JPG terkompresi
            $filename = Str::uuid() . '.jpg';
            $fullPath = $folder . '/' . $filename;

            $quality = 85;
            do {
                ob_start();
                imagejpeg($image, null, $quality);
                $data = ob_get_clean();
                $size = strlen($data);
                $quality -= 5;
            } while ($size > 1024 * 1024 && $quality > 10);

            file_put_contents($fullPath, $data);
            imagedestroy($image);

            return 'temuans/' . $filename;
        }

        return null;
    }
}
