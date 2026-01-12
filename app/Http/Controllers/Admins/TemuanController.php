<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Temuan;
use App\Models\Patrol;
use App\Models\User;
use App\Models\Member;
use App\Models\PatrolMember;

use PhpOffice\PhpPresentation\Shape\Rectangle;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;

use Illuminate\Support\Facades\Storage;

class TemuanController extends Controller
{
    // Menampilkan temuan berdasarkan patrol tertentu
    public function index(Request $request, $id)
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $temuans = Temuan::with(['patrol', 'user', 'member'])
            ->where('Id_Patrol', $id)
            ->get();

        $patrol = Patrol::find($id);
        $patrols = Patrol::all();
        $users = User::all();

        $patrolmembers = PatrolMember::where('Id_Patrol', $id)
            ->where('Id_User', Session::get('login_id'))
            ->pluck('Id_Member');

        $members = Member::whereIn('Id_Member', $patrolmembers)->get();

        return view('admins.temuans.index', compact('temuans', 'patrol', 'patrols', 'users', 'members'));
    }

    // Menyimpan temuan baru
    public function store(Request $request)
    {
        $request->validate([
            'Path_Temuan' => 'nullable|image|mimes:jpg,png,jpeg',
            'Desc_Temuan' => 'nullable|string',
            'Id_Patrol'   => 'required|integer'
        ]);

        $pathTemuan = null;

        if ($request->hasFile('Path_Temuan')) {
            $file = $request->file('Path_Temuan');
            $imageInfo = getimagesize($file);
            $mime = $imageInfo['mime'];

            // buat resource GD sesuai tipe file
            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($file->getPathname());
                    break;
                default:
                    $source = imagecreatefromstring(file_get_contents($file->getPathname()));
            }

            $width  = imagesx($source);
            $height = imagesy($source);

            // resize max dimension 1280px
            $maxDim = 1280;
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newWidth  = intval($width * $ratio);
                $newHeight = intval($height * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $resized;
            }

            // pastikan folder ada
            $folder = public_path('uploads/temuans');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // generate nama file unik
            $filename = uniqid() . '.jpg'; // konversi ke jpg biar ukuran lebih kecil
            $pathTemuan = 'temuans/' . $filename;

            // kompresi sampai < 1MB
            $quality = 85;
            do {
                ob_start();
                imagejpeg($source, null, $quality);
                $data = ob_get_clean();
                $size = strlen($data);
                $quality -= 5;
            } while ($size > 1024 * 1024 && $quality > 10);

            file_put_contents($folder . '/' . $filename, $data);
            imagedestroy($source);
        }

        $temuan = Temuan::create([
            'Path_Temuan'   => $pathTemuan,
            'Desc_Temuan'   => $request->input('Desc_Temuan', ''),
            'Id_Patrol'     => $request->input('Id_Patrol'),
            'Id_User'       => Session::get('login_id'),
            'Status_Temuan' => 'Pending'
        ]);

        return redirect()->route('temuan.index', ['id' => $request->Id_Patrol])
            ->with('success', 'Data temuan berhasil disimpan.');
    }

    // Update temuan
    public function update(Request $request, $id)
    {
        $request->validate([
            'Path_Update_Temuan' => 'nullable|image|mimes:jpg,png,jpeg',
            'Desc_Update_Temuan' => 'nullable|string',
        ]);

        $temuan = Temuan::findOrFail($id);

        // Upload foto perbaikan jika ada
        if ($request->hasFile('Path_Update_Temuan')) {
            $file = $request->file('Path_Update_Temuan');
            $imageInfo = getimagesize($file);
            $mime = $imageInfo['mime'];

            // buat resource GD
            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($file->getPathname());
                    break;
                default:
                    $source = imagecreatefromstring(file_get_contents($file->getPathname()));
            }

            $width  = imagesx($source);
            $height = imagesy($source);

            // resize max 1280px
            $maxDim = 1280;
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newWidth  = intval($width * $ratio);
                $newHeight = intval($height * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $resized;
            }

            // pastikan folder ada
            $folder = public_path('uploads/perbaikans');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // hapus file lama kalau ada
            if ($temuan->Path_Update_Temuan && Storage::disk('uploads')->exists($temuan->Path_Update_Temuan)) {
                Storage::disk('uploads')->delete($temuan->Path_Update_Temuan);
            }

            // nama file baru
            $filename = uniqid() . '.jpg'; 
            $pathUpdate = 'perbaikans/' . $filename;

            // kompres <= 1MB
            $quality = 85;
            do {
                ob_start();
                imagejpeg($source, null, $quality);
                $data = ob_get_clean();
                $size = strlen($data);
                $quality -= 5;
            } while ($size > 1024 * 1024 && $quality > 10);

            file_put_contents($folder . '/' . $filename, $data);
            imagedestroy($source);

            $temuan->Path_Update_Temuan = $pathUpdate;
        }

        // Update deskripsi perbaikan
        $temuan->Desc_Update_Temuan = $request->input('Desc_Update_Temuan', $temuan->Desc_Update_Temuan);
        $temuan->save();

        return redirect()->back()->with('success', 'Temuan berhasil diperbarui.');
    }

    // Hapus temuan
    public function destroy($id)
    {
        $temuan = Temuan::findOrFail($id);

        if ($temuan->Path_Temuan && Storage::disk('uploads')->exists($temuan->Path_Temuan)) {
            Storage::disk('uploads')->delete($temuan->Path_Temuan);
        }
        if ($temuan->Path_Update_Temuan && Storage::disk('uploads')->exists($temuan->Path_Update_Temuan)) {
            Storage::disk('uploads')->delete($temuan->Path_Update_Temuan);
        }

        $temuan->delete();

        return redirect()->back()->with('success', 'Data temuan berhasil dihapus.');
    }

    // Update status temuan
    // public function updateStatus(Request $request, $id)
    // {
    //     $temuan = Temuan::findOrFail($id);
    //     $temuan->Status_Temuan = $request->has('Status_Temuan') ? 'Done' : null;
    //     $temuan->save();

    //     return redirect()->back()->with('success', 'Temuan berhasil diperbarui.');
    // }

    public function updateStatus(Request $request, $id)
    {
        $temuan = Temuan::findOrFail($id);
        $temuan->Status_Temuan = $request->input('Status_Temuan') ?? 'Pending';
        $temuan->save();

        return response()->json([
            'success' => true,
            'status' => $temuan->Status_Temuan,
        ]);
    }

    // Export temuan ke PPT
    public function exportToPPT($id)
    {
        $temuans = Temuan::with(['patrol', 'user', 'member'])
            ->where('Id_Patrol', $id)
            ->get();

        if ($temuans->isEmpty()) {
            return redirect()->back()->with('error', 'Data temuan kosong.');
        }

        $patrol = Patrol::find($id);
        $patrolName = $patrol->Name_Patrol ?? 'Patrol Tidak Bernama';

        $ppt = new PhpPresentation();
        $slide = $ppt->getActiveSlide();
        if ($slide) {
            $ppt->removeSlideByIndex(0);
        }

        // Warna
        $colorPrimary = new Color('FF0D3B66');
        $colorText = new Color('FF2D2D2D');
        $colorWhite = new Color('FFFFFFFF');
        $colorBlue = new Color('FF2E5AAB');

        // Path logo
        $logoPath = public_path('images/logo.png');
        $logoExists = file_exists($logoPath);

        // ========== JUDUL SLIDE ==========
        $titleSlide = $ppt->createSlide();

        // Logo di pojok kiri atas
        if ($logoExists) {
            $titleSlide->createDrawingShape()
                ->setName('Logo Header')
                ->setPath($logoPath)
                ->setWidth(120)
                ->setHeight(30)
                ->setOffsetX(10)
                ->setOffsetY(10);
        }

        // Garis atas
        $top1 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(12)->setOffsetX(0)->setOffsetY(100);
        $top1->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);
        $top2 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(4)->setOffsetX(0)->setOffsetY(112);
        $top2->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

        // Judul
        $title = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(160)->setOffsetX(0)->setOffsetY(150);
        $title->createTextRun("LAPORAN TEMUAN PATROL 5S")
            ->getFont()->setSize(50)->setBold(true)->setColor($colorPrimary);
        $title->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Garis pemisah
        $div = $titleSlide->createRichTextShape()->setWidth(420)->setHeight(4)->setOffsetX(270)->setOffsetY(320);
        $div->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

        // Subjudul
        $sub = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(60)->setOffsetX(0)->setOffsetY(340);
        $sub->createTextRun("Patrol: {$patrolName}")
            ->getFont()->setSize(28)->setColor($colorPrimary);
        $sub->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Tanggal dari Time_Patrol (tanpa jam)
        $tanggalPatrol = $patrol->Time_Patrol
            ? \Carbon\Carbon::parse($patrol->Time_Patrol)->format('d-m-Y')
            : 'Tanggal tidak tersedia';
        $date = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(40)->setOffsetX(0)->setOffsetY(390);
        $date->createTextRun("Tanggal: " . $tanggalPatrol)
            ->getFont()->setSize(18)->setColor($colorText);
        $date->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Garis bawah
        $bot1 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(4)->setOffsetX(0)->setOffsetY(510);
        $bot1->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);
        $bot2 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(12)->setOffsetX(0)->setOffsetY(514);
        $bot2->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

        // ========== SLIDE TEMUAN ==========
        $slideNumber = 1;
        foreach ($temuans as $temuan) {
            $slide = $ppt->createSlide();

            // Nomor slide
            $num = $slide->createRichTextShape()->setWidth(50)->setHeight(30)->setOffsetX(900)->setOffsetY(10);
            $num->createTextRun((string)$slideNumber)->getFont()->setBold(true)->setSize(16)->setColor($colorPrimary);

            // Header
            $header = $slide->createRichTextShape()->setWidth(800)->setHeight(40)->setOffsetX(80)->setOffsetY(50);
            $header->createTextRun("ITEM TEMUAN PATROL 5S")
                ->getFont()->setSize(20)->setBold(true)->setColor($colorWhite);
            $header->getFill()
                ->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)
                ->setStartColor($colorBlue)
                ->setEndColor($colorBlue);
            $header->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // POSISI & UKURAN
            $xLeft = 50;
            $xRight = 510;
            $yImageTop = 160;
            $maxImageWidth = 450;
            $maxImageHeight = 230;

            // === GAMBAR KIRI ===
            if (!empty($temuan->Path_Temuan) && file_exists(public_path('uploads/' . $temuan->Path_Temuan))) {
                list($w, $h) = @getimagesize(public_path('uploads/' . $temuan->Path_Temuan));
                if ($w && $h) {
                    // ✅ PRIORITASKAN LEBAR, TAPI BATASI TINGGI
                    $imgW = $maxImageWidth;
                    $imgH = (int)($h * ($imgW / $w));
                    if ($imgH > $maxImageHeight) {
                        $imgH = $maxImageHeight;
                        $imgW = (int)($w * ($imgH / $h));
                    }
                    $slide->createDrawingShape()
                        ->setPath(public_path('uploads/' . $temuan->Path_Temuan))
                        ->setWidth($imgW)
                        ->setHeight($imgH)
                        ->setOffsetX($xLeft + ($maxImageWidth - $imgW) / 2)
                        ->setOffsetY($yImageTop);
                }
            }

            // === PANAH ===
            $arrow = $slide->createRichTextShape()->setWidth(60)->setHeight(40)->setOffsetX(470)->setOffsetY($yImageTop + 90);
            $arrow->createTextRun("→")->getFont()->setSize(42)->setBold(true)->setColor($colorBlue);
            $arrow->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // === GAMBAR KANAN ===
            if (!empty($temuan->Path_Update_Temuan) && file_exists(public_path('uploads/' . $temuan->Path_Update_Temuan))) {
                list($w2, $h2) = @getimagesize(public_path('uploads/' . $temuan->Path_Update_Temuan));
                if ($w2 && $h2) {
                    $imgW2 = $maxImageWidth;
                    $imgH2 = (int)($h2 * ($imgW2 / $w2));
                    if ($imgH2 > $maxImageHeight) {
                        $imgH2 = $maxImageHeight;
                        $imgW2 = (int)($w2 * ($imgH2 / $h2));
                    }
                    $slide->createDrawingShape()
                        ->setPath(public_path('uploads/' . $temuan->Path_Update_Temuan))
                        ->setWidth($imgW2)
                        ->setHeight($imgH2)
                        ->setOffsetX($xRight + ($maxImageWidth - $imgW2) / 2)
                        ->setOffsetY($yImageTop);
                }
            }

            // === KETERANGAN DI BAWAH ===
            $labelHeight = 100;
            $labelY = 540 - $labelHeight - 10; // margin 20 dari bawah

            $desc1 = trim($temuan->Desc_Temuan) ?: 'Tidak ada keterangan temuan';
            $label1 = $slide->createRichTextShape()->setWidth(400)->setHeight($labelHeight)->setOffsetX($xLeft)->setOffsetY($labelY);
            $run1 = $label1->createTextRun($desc1);
            $run1->getFont()->setSize(14)->setBold(true)->setColor($colorWhite);
            $label1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $label1->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $label1->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

            $desc2 = trim($temuan->Desc_Update_Temuan) ?: '-';
            $label2 = $slide->createRichTextShape()->setWidth(400)->setHeight($labelHeight)->setOffsetX($xRight)->setOffsetY($labelY);
            $run2 = $label2->createTextRun($desc2);
            $run2->getFont()->setSize(14)->setBold(true)->setColor($colorWhite);
            $label2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $label2->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $label2->getFill()->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

            // Logo di kiri bawah (slide isi)

            $slideNumber++;
        }

        // Simpan
        $fileName = 'Laporan_5S_' . str_replace(' ', '_', $patrolName) . '_' . now()->format('d-m-Y') . '.pptx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;
        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    public function exportToPPTold($id)
    {
        $temuans = Temuan::with(['patrol', 'user', 'member'])
            ->where('Id_Patrol', $id)
            ->get();

        if ($temuans->isEmpty()) {
            return redirect()->back()->with('error', 'Data temuan kosong.');
        }

        $ppt = new PhpPresentation();
        $ppt->removeSlideByIndex(0); // hapus slide default

        $slideNumber = 1;

        foreach ($temuans as $temuan) {
            $slide = $ppt->createSlide();

            // ---------- Background ----------
            if ($temuan->Status_Temuan === 'Done') {
                // buat rectangle penuh sebagai background biru
                $bgShape = $slide->createShape('rectangle'); // shape rectangle
                $bgShape->setHeight(540)
                    ->setWidth(960)
                    ->setOffsetX(0)
                    ->setOffsetY(0)
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getFill()->setStartColor(new Color('FFADD8E6')); // biru muda
            }
            // default putih tidak perlu di-set

            // ---------- Nomor slide pojok kanan atas ----------
            $numberShape = $slide->createRichTextShape();
            $numberShape->setHeight(30)
                ->setWidth(50)
                ->setOffsetX(880)
                ->setOffsetY(10);
            $numberShape->createTextRun($slideNumber)
                ->getFont()->setBold(true)
                ->setSize(14);
            $slideNumber++;

            $alignment = new Alignment();
            $alignment->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // ---------- KIRI: Temuan ----------
            $pathTemuan = public_path('uploads/' . $temuan->Path_Temuan);
            if ($temuan->Path_Temuan && file_exists($pathTemuan)) {
                $drawingTemuan = $slide->createDrawingShape();
                $drawingTemuan->setName('Gambar Temuan')
                    ->setPath($pathTemuan)
                    ->setHeight(250)
                    ->setOffsetX(75)
                    ->setOffsetY(50);
            }

            $descTemuanShape = $slide->createRichTextShape();
            $descTemuanShape->setHeight(200)
                ->setWidth(300)
                ->setOffsetX(50)
                ->setOffsetY(310);
            $descTemuanShape->createParagraph()->setAlignment($alignment);
            $userName = $temuan->user ? $temuan->user->Name_User : 'Tidak Ada';
            $descTemuanShape->createTextRun(
                "Temuan:\n{$temuan->Desc_Temuan}\nDibuat oleh: {$userName}"
            )->getFont()->setSize(14);

            // ---------- KANAN: Perbaikan ----------
            $pathPerbaikan = public_path('uploads/' . $temuan->Path_Update_Temuan);
            if ($temuan->Path_Update_Temuan && file_exists($pathPerbaikan)) {
                $drawingPerbaikan = $slide->createDrawingShape();
                $drawingPerbaikan->setName('Gambar Perbaikan')
                    ->setPath($pathPerbaikan)
                    ->setHeight(250)
                    ->setOffsetX(450)
                    ->setOffsetY(50);
            }

            $descPerbaikanShape = $slide->createRichTextShape();
            $descPerbaikanShape->setHeight(200)
                ->setWidth(300)
                ->setOffsetX(450)
                ->setOffsetY(310);
            $descPerbaikanShape->createParagraph()->setAlignment($alignment);
            $descPerbaikan = $temuan->Desc_Update_Temuan ?: 'Belum diperbaiki';
            $descPerbaikanShape->createTextRun(
                "Perbaikan:\n{$descPerbaikan}"
            )->getFont()->setSize(14);
        }

        // Buat file PPT
        $fileName = 'Temuan_Patrol_' . $id . '.pptx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}
