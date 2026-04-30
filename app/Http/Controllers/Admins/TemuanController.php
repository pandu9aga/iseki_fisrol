<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        $query = Temuan::with(['patrol', 'user', 'member'])
            ->where('Id_Patrol', $id);

        // Filter by PIC Proses
        if ($request->has('filter_pic') && !empty($request->filter_pic)) {
            $query->where('pic_proses_nik', $request->filter_pic);
        }

        $temuans = $query->get();

        $patrol = Patrol::find($id);
        $patrols = Patrol::all();
        $users = User::all();

        $patrolmembers = PatrolMember::where('Id_Patrol', $id)
            ->where('Id_User', Session::get('login_id'))
            ->pluck('Id_Member');

        $members = Member::whereIn('Id_Member', $patrolmembers)->get();

        // Get unique PICs for filter dropdown from this patrol
        $uniquePics = Temuan::where('Id_Patrol', $id)
            ->whereNotNull('pic_proses_nik')
            ->where('pic_proses_nik', '!=', '')
            ->distinct()
            ->get(['pic_proses_nik']);

        // Attach names
        foreach ($uniquePics as $p) {
            $p->pic_name = $p->pic_proses_name ?? $p->pic_proses_nik;
        }

        return view('admins.temuans.index', compact('temuans', 'patrol', 'patrols', 'users', 'members', 'uniquePics'));
    }

    /**
     * Search employee by NIK or nama from rifa.employees (AJAX)
     */
    public function searchEmployee(Request $request)
    {
        $query = $request->input('q', '');
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $employees = DB::connection('rifa')
            ->table('employees')
            ->where('nik', 'like', "%{$query}%")
            ->orWhere('nama', 'like', "%{$query}%")
            ->limit(20)
            ->get(['nik', 'nama']);

        return response()->json($employees);
    }

    /**
     * Validate NIK and return employee data (AJAX)
     */
    public function validateNik(Request $request)
    {
        $nik = $request->input('nik', '');
        if (empty($nik)) {
            return response()->json(['found' => false]);
        }

        $employee = DB::connection('rifa')
            ->table('employees')
            ->where('nik', $nik)
            ->first(['nik', 'nama']);

        if ($employee) {
            return response()->json(['found' => true, 'nik' => $employee->nik, 'nama' => $employee->nama]);
        }

        return response()->json(['found' => false]);
    }

    // Menyimpan temuan baru (admin bisa input nik_penemu)
    public function store(Request $request)
    {
        $request->validate([
            'Desc_Temuan' => 'nullable|string',
            'Id_Patrol'   => 'required|integer',
            'nik_penemu'  => 'nullable|string|max:50'
        ]);

        $pathTemuan = $this->handleImageUpload($request, 'Path_Temuan', 'temuans');

        $temuan = Temuan::create([
            'Path_Temuan'   => $pathTemuan,
            'Desc_Temuan'   => $request->input('Desc_Temuan', ''),
            'Id_Patrol'     => $request->input('Id_Patrol'),
            'Id_User'       => Session::get('login_id'),
            'nik_penemu'    => $request->input('nik_penemu'),
            'Status_Temuan' => 'Pending'
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('temuan.index', ['id' => $request->Id_Patrol])
            ->with('success', 'Data temuan berhasil disimpan.');
    }

    // Update temuan (perbaikan + PIC Proses + Original Temuan if needed)
    public function update(Request $request, $id)
    {
        $request->validate([
            'Desc_Temuan'        => 'nullable|string',
            'Desc_Update_Temuan' => 'nullable|string',
            'pic_proses_nik'     => 'required|string|max:50',
        ]);

        $temuan = Temuan::findOrFail($id);

        // 1. Update deskripsi temuan (Original) - filled() prevents empty overwrite
        if ($request->filled('Desc_Temuan')) {
            $temuan->Desc_Temuan = $request->input('Desc_Temuan');
        }

        // 2. Upload foto temuan (Original)
        $newPathTemuan = $this->handleImageUpload($request, 'Path_Temuan', 'temuans');
        if ($newPathTemuan) {
            // hapus file lama
            if ($temuan->Path_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Temuan))) {
                unlink(public_path('uploads/' . $temuan->Path_Temuan));
            }
            $temuan->Path_Temuan = $newPathTemuan;
            $temuan->Rotate_Temuan = 0; // Reset rotation on new upload
        }

        // 3. Update deskripsi perbaikan - filled() prevents empty overwrite
        if ($request->filled('Desc_Update_Temuan')) {
            $temuan->Desc_Update_Temuan = $request->input('Desc_Update_Temuan');
        }

        // 4. Upload foto perbaikan
        $newPathUpdate = $this->handleImageUpload($request, 'Path_Update_Temuan', 'perbaikans');
        if ($newPathUpdate) {
            // hapus file lama
            if ($temuan->Path_Update_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Update_Temuan))) {
                unlink(public_path('uploads/' . $temuan->Path_Update_Temuan));
            }
            $temuan->Path_Update_Temuan = $newPathUpdate;
            $temuan->Rotate_Update = 0; // Reset rotation
        }

        // 5. Update PIC Proses
        if ($request->has('pic_proses_nik')) {
            $temuan->pic_proses_nik = $request->input('pic_proses_nik');
        }

        $temuan->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Temuan berhasil diperbarui.',
                'path_update' => $temuan->Path_Update_Temuan,
                'desc_update' => $temuan->Desc_Update_Temuan,
                'full_path_update' => $temuan->Path_Update_Temuan ? asset('uploads/' . $temuan->Path_Update_Temuan) : null,
                'pic_nik' => $temuan->pic_proses_nik,
                'pic_name' => $temuan->pic_proses_name ?? $temuan->pic_proses_nik,
            ]);
        }

        return redirect()->back()->with('success', 'Temuan berhasil diperbarui.');
    }

    /**
     * Helper: Handle image upload (file or base64)
     */
    private function handleImageUpload(Request $request, $inputName, $subfolder)
    {
        $folder = public_path('uploads/' . $subfolder);
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        // 1. Check base64 input
        if ($request->filled($inputName)) {
            $input = $request->input($inputName);
            if (is_string($input) && Str::startsWith($input, 'data:image')) {
                $data = preg_replace('/^data:image\/\w+;base64,/', '', $input);
                $binary = base64_decode($data);
                if ($binary === false) return null;

                $filename = Str::uuid() . '.jpg';
                file_put_contents($folder . '/' . $filename, $binary);
                return $subfolder . '/' . $filename;
            }
        }

        // 2. Check file upload
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = uniqid() . '.' . ($file->getClientOriginalExtension() ?: 'jpg');

            // Simply move the file (no GD required)
            $file->move($folder, $filename);
            return $subfolder . '/' . $filename;
        }

        return null;
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

    public function updateRotation(Request $request, $id)
    {
        $temuan = Temuan::findOrFail($id);
        $type = $request->input('type'); // 'temuan' or 'perbaikan'
        $angle = $request->input('angle', 0);

        if ($type === 'temuan') {
            $temuan->Rotate_Temuan = $angle;
        } elseif ($type === 'perbaikan') {
            $temuan->Rotate_Update = $angle;
        }

        $temuan->save();

        return response()->json([
            'success' => true,
            'type' => $type,
            'angle' => $angle
        ]);
    }

    // Export temuan ke PPT
    public function exportToPPT(Request $request, $id)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '300'); // 5 minutes

        $query = Temuan::with(['patrol', 'user', 'member'])
            ->where('Id_Patrol', $id);

        // Filter by PIC Proses
        if ($request->has('filter_pic') && !empty($request->filter_pic)) {
            $query->where('pic_proses_nik', $request->filter_pic);
        }

        $temuans = $query->get();

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
            $num = $slide->createRichTextShape()->setWidth(100)->setHeight(30)->setOffsetX(850)->setOffsetY(10);
            $num->createTextRun((string)$slideNumber)->getFont()->setBold(true)->setSize(16)->setColor($colorPrimary);
            $num->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

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

        // Jika ada filter PIC, ubah nama file menjadi "[Nama PIC] [Nama Patrol].pptx"
        if ($request->has('filter_pic') && !empty($request->filter_pic)) {
            // Ambil nama PIC dari salah satu temuan (karena sudah difilter, semua sama)
            // Atau cari di DB employees jika perlu kepastian
            $firstTemuan = $temuans->first();
            $picName = $firstTemuan->pic_proses_name ?? $firstTemuan->pic_proses_nik ?? 'UnknownPIC';

            // Bersihkan karakter aneh untuk nama file
            $safePicName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $picName);
            $safePatrolName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $patrolName);

            $fileName = "{$safePicName}_{$safePatrolName}.pptx";
        }

        $tempFile = sys_get_temp_dir() . '/' . $fileName;
        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportToPPTold($id)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '300'); // 5 minutes

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
