<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\DailyTemuan;
use App\Models\DailyPatrol;
use App\Models\User;
use App\Models\Member;
use App\Models\DailyPatrolMember;

use PhpOffice\PhpPresentation\Shape\Rectangle;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;

use Illuminate\Support\Facades\Storage;

class DailyTemuanController extends Controller
{
    public function index(Request $request, $id)
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $query = DailyTemuan::with(['dailyPatrol', 'user'])
            ->where('Id_Daily_Patrol', $id);

        if ($request->has('filter_pic') && !empty($request->filter_pic)) {
            $query->where('pic_proses_nik_daily', $request->filter_pic);
        }

        $daily_temuans = $query->get();

        $daily_patrol = DailyPatrol::find($id);
        $daily_patrols = DailyPatrol::all();
        $users = User::all();

        $uniquePics = DailyTemuan::where('Id_Daily_Patrol', $id)
            ->whereNotNull('pic_proses_nik_daily')
            ->where('pic_proses_nik_daily', '!=', '')
            ->distinct()
            ->get(['pic_proses_nik_daily']);

        foreach ($uniquePics as $p) {
            $p->pic_name = $p->pic_proses_name ?? $p->pic_proses_nik_daily;
        }

        return view('admins.daily_temuans.index', compact('daily_temuans', 'daily_patrol', 'daily_patrols', 'users', 'uniquePics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Desc_Daily_Temuan' => 'nullable|string',
            'Id_Daily_Patrol'   => 'required|integer',
        ]);

        $pathTemuan = $this->handleImageUpload($request, 'Path_Daily_Temuan', 'daily_temuans');

        $temuan = DailyTemuan::create([
            'Path_Daily_Temuan'   => $pathTemuan,
            'Desc_Daily_Temuan'   => $request->input('Desc_Daily_Temuan', ''),
            'Id_Daily_Patrol'     => $request->input('Id_Daily_Patrol'),
            'Id_User'             => Session::get('login_id'),
            'Status_Daily_Temuan' => 'Pending'
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('daily_temuan.index', ['id' => $request->Id_Daily_Patrol])
            ->with('success', 'Data temuan berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Desc_Daily_Temuan'        => 'nullable|string',
            'Desc_Daily_Update_Temuan' => 'nullable|string',
            'pic_proses_nik_daily'           => 'required|string|max:50',
        ]);

        $temuan = DailyTemuan::findOrFail($id);

        if ($request->filled('Desc_Daily_Temuan')) {
            $temuan->Desc_Daily_Temuan = $request->input('Desc_Daily_Temuan');
        }

        $newPathTemuan = $this->handleImageUpload($request, 'Path_Daily_Temuan', 'daily_temuans');
        if ($newPathTemuan) {
            if ($temuan->Path_Daily_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Daily_Temuan))) {
                unlink(public_path('uploads/' . $temuan->Path_Daily_Temuan));
            }
            $temuan->Path_Daily_Temuan = $newPathTemuan;
            $temuan->Rotate_Daily_Temuan = 0;
        }

        if ($request->filled('Desc_Daily_Update_Temuan')) {
            $temuan->Desc_Daily_Update_Temuan = $request->input('Desc_Daily_Update_Temuan');
        }

        $newPathUpdate = $this->handleImageUpload($request, 'Path_Daily_Update_Temuan', 'daily_perbaikans');
        if ($newPathUpdate) {
            if ($temuan->Path_Daily_Update_Temuan && file_exists(public_path('uploads/' . $temuan->Path_Daily_Update_Temuan))) {
                unlink(public_path('uploads/' . $temuan->Path_Daily_Update_Temuan));
            }
            $temuan->Path_Daily_Update_Temuan = $newPathUpdate;
            $temuan->Rotate_Daily_Update = 0;
        }

        if ($request->has('pic_proses_nik_daily')) {
            $temuan->pic_proses_nik_daily = $request->input('pic_proses_nik_daily');
        }

        $temuan->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Temuan berhasil diperbarui.',
                'path_update' => $temuan->Path_Daily_Update_Temuan,
                'desc_update' => $temuan->Desc_Daily_Update_Temuan,
                'full_path_update' => $temuan->Path_Daily_Update_Temuan ? asset('uploads/' . $temuan->Path_Daily_Update_Temuan) : null,
                'pic_nik' => $temuan->pic_proses_nik_daily,
                'pic_name' => $temuan->pic_proses_name ?? $temuan->pic_proses_nik_daily,
            ]);
        }

        return redirect()->back()->with('success', 'Temuan berhasil diperbarui.');
    }

    private function handleImageUpload(Request $request, $inputName, $subfolder)
    {
        $yearMonth = now()->format('Y-m');
        $targetSubfolder = $subfolder . '/' . $yearMonth;
        $folder = public_path('uploads/' . $targetSubfolder);

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        if ($request->filled($inputName)) {
            $input = $request->input($inputName);
            if (is_string($input) && Str::startsWith($input, 'data:image')) {
                $data = preg_replace('/^data:image\/\w+;base64,/', '', $input);
                $binary = base64_decode($data);
                if ($binary === false) return null;

                $filename = Str::uuid() . '.jpg';
                file_put_contents($folder . '/' . $filename, $binary);
                return $targetSubfolder . '/' . $filename;
            }
        }

        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = uniqid() . '.' . ($file->getClientOriginalExtension() ?: 'jpg');
            $file->move($folder, $filename);
            return $targetSubfolder . '/' . $filename;
        }

        return null;
    }

    public function destroy($id)
    {
        $temuan = DailyTemuan::findOrFail($id);

        if ($temuan->Path_Daily_Temuan && Storage::disk('uploads')->exists($temuan->Path_Daily_Temuan)) {
            Storage::disk('uploads')->delete($temuan->Path_Daily_Temuan);
        }
        if ($temuan->Path_Daily_Update_Temuan && Storage::disk('uploads')->exists($temuan->Path_Daily_Update_Temuan)) {
            Storage::disk('uploads')->delete($temuan->Path_Daily_Update_Temuan);
        }

        $temuan->delete();

        return redirect()->back()->with('success', 'Data temuan berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $temuan = DailyTemuan::findOrFail($id);
        $temuan->Status_Daily_Temuan = $request->input('Status_Daily_Temuan') ?? 'Pending';
        $temuan->save();

        return response()->json([
            'success' => true,
            'status' => $temuan->Status_Daily_Temuan,
        ]);
    }

    public function updateRotation(Request $request, $id)
    {
        $temuan = DailyTemuan::findOrFail($id);
        $type = $request->input('type');
        $angle = $request->input('angle', 0);

        if ($type === 'temuan') {
            $temuan->Rotate_Daily_Temuan = $angle;
        } elseif ($type === 'perbaikan') {
            $temuan->Rotate_Daily_Update = $angle;
        }

        $temuan->save();

        return response()->json([
            'success' => true,
            'type' => $type,
            'angle' => $angle
        ]);
    }

    public function exportToPPT(Request $request, $id)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '300');

        $query = DailyTemuan::with(['dailyPatrol', 'user'])
            ->where('Id_Daily_Patrol', $id);

        if ($request->has('filter_pic') && !empty($request->filter_pic)) {
            $query->where('pic_proses_nik_daily', $request->filter_pic);
        }

        $daily_temuans = $query->get();

        if ($daily_temuans->isEmpty()) {
            return redirect()->back()->with('error', 'Data temuan kosong.');
        }

        $daily_patrol = DailyPatrol::find($id);
        $patrolName = $daily_patrol->Name_Daily_Patrol ?? 'Daily Patrol Tidak Bernama';

        $ppt = new PhpPresentation();
        $slide = $ppt->getActiveSlide();
        if ($slide) {
            $ppt->removeSlideByIndex(0);
        }

        $colorPrimary = new Color('FF0D3B66');
        $colorText = new Color('FF2D2D2D');
        $colorWhite = new Color('FFFFFFFF');
        $colorBlue = new Color('FF2E5AAB');

        $logoPath = public_path('images/logo.png');
        $logoExists = file_exists($logoPath);

        // ========== JUDUL SLIDE ==========
        $titleSlide = $ppt->createSlide();

        if ($logoExists) {
            $titleSlide->createDrawingShape()
                ->setName('Logo Header')
                ->setPath($logoPath)
                ->setWidth(120)
                ->setHeight(30)
                ->setOffsetX(10)
                ->setOffsetY(10);
        }

        $top1 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(12)->setOffsetX(0)->setOffsetY(100);
        $top1->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);
        $top2 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(4)->setOffsetX(0)->setOffsetY(112);
        $top2->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

        $title = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(160)->setOffsetX(0)->setOffsetY(150);
        $title->createTextRun("LAPORAN TEMUAN DAILY PATROL 5S")
            ->getFont()->setSize(50)->setBold(true)->setColor($colorPrimary);
        $title->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $div = $titleSlide->createRichTextShape()->setWidth(420)->setHeight(4)->setOffsetX(270)->setOffsetY(320);
        $div->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

        $sub = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(60)->setOffsetX(0)->setOffsetY(340);
        $sub->createTextRun("Daily Patrol: {$patrolName}")
            ->getFont()->setSize(28)->setColor($colorPrimary);
        $sub->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $tanggalPatrol = $daily_patrol->Time_Daily_Patrol
            ? \Carbon\Carbon::parse($daily_patrol->Time_Daily_Patrol)->format('d-m-Y')
            : 'Tanggal tidak tersedia';
        $date = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(40)->setOffsetX(0)->setOffsetY(390);
        $date->createTextRun("Tanggal: " . $tanggalPatrol)
            ->getFont()->setSize(18)->setColor($colorText);
        $date->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $bot1 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(4)->setOffsetX(0)->setOffsetY(510);
        $bot1->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);
        $bot2 = $titleSlide->createRichTextShape()->setWidth(960)->setHeight(12)->setOffsetX(0)->setOffsetY(514);
        $bot2->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

        // ========== SLIDE TEMUAN ==========
        $slideNumber = 1;
        foreach ($daily_temuans as $temuan) {
            $slide = $ppt->createSlide();

            // Background biru jika status Done
            if ($temuan->Status_Daily_Temuan === 'Done') {
                $bg = $slide->createRichTextShape()->setWidth(960)->setHeight(720)->setOffsetX(0)->setOffsetY(0);
                $bg->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFADD8E6'))->setEndColor(new Color('FFADD8E6'));
            }

            $num = $slide->createRichTextShape()->setWidth(100)->setHeight(30)->setOffsetX(850)->setOffsetY(10);
            $num->createTextRun((string)$slideNumber)->getFont()->setBold(true)->setSize(16)->setColor($colorPrimary);
            $num->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $header = $slide->createRichTextShape()->setWidth(800)->setHeight(40)->setOffsetX(80)->setOffsetY(50);
            $header->createTextRun("ITEM TEMUAN DAILY PATROL 5S")
                ->getFont()->setSize(20)->setBold(true)->setColor($colorWhite);
            $header->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor($colorBlue)
                ->setEndColor($colorBlue);
            $header->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $xLeft = 50;
            $xRight = 510;
            $yImageTop = 160;
            $maxImageWidth = 450;
            $maxImageHeight = 230;

            if (!empty($temuan->Path_Daily_Temuan) && file_exists(public_path('uploads/' . $temuan->Path_Daily_Temuan))) {
                list($w, $h) = @getimagesize(public_path('uploads/' . $temuan->Path_Daily_Temuan));
                if ($w && $h) {
                    $imgW = $maxImageWidth;
                    $imgH = (int)($h * ($imgW / $w));
                    if ($imgH > $maxImageHeight) {
                        $imgH = $maxImageHeight;
                        $imgW = (int)($w * ($imgH / $h));
                    }
                    $slide->createDrawingShape()
                        ->setPath(public_path('uploads/' . $temuan->Path_Daily_Temuan))
                        ->setWidth($imgW)
                        ->setHeight($imgH)
                        ->setOffsetX($xLeft + ($maxImageWidth - $imgW) / 2)
                        ->setOffsetY($yImageTop);
                }
            }

            $arrow = $slide->createRichTextShape()->setWidth(60)->setHeight(40)->setOffsetX(470)->setOffsetY($yImageTop + 90);
            $arrow->createTextRun("→")->getFont()->setSize(42)->setBold(true)->setColor($colorBlue);
            $arrow->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if (!empty($temuan->Path_Daily_Update_Temuan) && file_exists(public_path('uploads/' . $temuan->Path_Daily_Update_Temuan))) {
                list($w2, $h2) = @getimagesize(public_path('uploads/' . $temuan->Path_Daily_Update_Temuan));
                if ($w2 && $h2) {
                    $imgW2 = $maxImageWidth;
                    $imgH2 = (int)($h2 * ($imgW2 / $w2));
                    if ($imgH2 > $maxImageHeight) {
                        $imgH2 = $maxImageHeight;
                        $imgW2 = (int)($w2 * ($imgH2 / $h2));
                    }
                    $slide->createDrawingShape()
                        ->setPath(public_path('uploads/' . $temuan->Path_Daily_Update_Temuan))
                        ->setWidth($imgW2)
                        ->setHeight($imgH2)
                        ->setOffsetX($xRight + ($maxImageWidth - $imgW2) / 2)
                        ->setOffsetY($yImageTop);
                }
            }

            $labelHeight = 100;
            $labelY = 540 - $labelHeight - 10;

            $desc1 = trim($temuan->Desc_Daily_Temuan) ?: 'Tidak ada keterangan temuan';
            $label1 = $slide->createRichTextShape()->setWidth(400)->setHeight($labelHeight)->setOffsetX($xLeft)->setOffsetY($labelY);
            $run1 = $label1->createTextRun($desc1);
            $run1->getFont()->setSize(14)->setBold(true)->setColor($colorWhite);
            $label1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $label1->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $label1->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

            $desc2 = trim($temuan->Desc_Daily_Update_Temuan) ?: '-';
            $label2 = $slide->createRichTextShape()->setWidth(400)->setHeight($labelHeight)->setOffsetX($xRight)->setOffsetY($labelY);
            $run2 = $label2->createTextRun($desc2);
            $run2->getFont()->setSize(14)->setBold(true)->setColor($colorWhite);
            $label2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $label2->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $label2->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor($colorBlue)->setEndColor($colorBlue);

            $slideNumber++;
        }

        $fileName = 'Laporan_Daily_Patrol_5S_' . str_replace(' ', '_', $patrolName) . '_' . now()->format('d-m-Y') . '.pptx';

        if ($request->has('filter_pic') && !empty($request->filter_pic)) {
            $firstTemuan = $daily_temuans->first();
            $picName = $firstTemuan->pic_proses_name ?? $firstTemuan->pic_proses_nik_daily ?? 'UnknownPIC';
            $safePicName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $picName);
            $safePatrolName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $patrolName);
            $fileName = "{$safePicName}_{$safePatrolName}.pptx";
        }

        $tempFile = sys_get_temp_dir() . '/' . $fileName;
        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
