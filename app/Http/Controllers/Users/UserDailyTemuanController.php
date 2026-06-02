<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\DailyTemuan;
use App\Models\DailyPatrol;
use App\Models\Member;

class UserDailyTemuanController extends Controller
{
    public function index($id)
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $daily_patrol = DailyPatrol::findOrFail($id);

        $daily_temuans = DailyTemuan::with('dailyPatrol')
            ->where('Id_Daily_Patrol', $id)
            ->where('Id_User', Session::get('login_id'))
            ->get();

        return view('users.daily_temuans.index', compact('daily_temuans', 'daily_patrol'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'Desc_Daily_Temuan' => 'required|string|max:1000',
        ]);

        $pathTemuan = null;
        if ($request->filled('Path_Daily_Temuan')) {
            $pathTemuan = $this->handleImageInput($request, 'Path_Daily_Temuan');
        }

        if (!$pathTemuan) {
            return response()->json(['success' => false, 'message' => 'Gagal memproses gambar.']);
        }

        $temuan = DailyTemuan::create([
            'Path_Daily_Temuan'   => $pathTemuan,
            'Desc_Daily_Temuan'   => $request->Desc_Daily_Temuan,
            'Id_Daily_Patrol'     => $id,
            'Id_User'             => Session::get('login_id'),
            'Status_Daily_Temuan' => 'Pending',
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Desc_Daily_Temuan' => 'required|string|max:1000',
        ]);

        $temuan = DailyTemuan::findOrFail($id);

        if ($request->hasFile('Path_Daily_Temuan') || $request->filled('Path_Daily_Temuan')) {
            $newPath = $this->handleImageInput($request, 'Path_Daily_Temuan');

            if ($newPath) {
                if ($temuan->Path_Daily_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Daily_Temuan))) {
                    unlink(public_path('uploads/' . $temuan->Path_Daily_Temuan));
                }
                $temuan->Path_Daily_Temuan = $newPath;
            }
        }

        $temuan->Desc_Daily_Temuan = $request->Desc_Daily_Temuan;
        $temuan->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $temuan = DailyTemuan::findOrFail($id);

        if ($temuan->Path_Daily_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Daily_Temuan))) {
            unlink(public_path('uploads/' . $temuan->Path_Daily_Temuan));
        }

        $temuan->delete();

        return response()->json(['success' => true]);
    }

    private function handleImageInput(Request $request, $inputName)
    {
        $folder = public_path('uploads/daily_temuans');
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        if ($request->filled($inputName)) {
            $input = $request->input($inputName);

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
                return 'daily_temuans/' . $filename;
            }
        }

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

            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newWidth = intval($width * $ratio);
                $newHeight = intval($height * $ratio);
                $resized = imagecreatetruecolor($newWidth, $newHeight);

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

            return 'daily_temuans/' . $filename;
        }

        return null;
    }
}
