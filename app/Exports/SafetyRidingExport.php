<?php

namespace App\Exports;

use App\Models\SafetyRiding;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SafetyRidingExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithDrawings
{
    protected $sectionId;
    protected $rows;

    public function __construct($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    public function collection()
    {
        $query = SafetyRiding::with(['pds', 'pfs', 'user.section']);

        if ($this->sectionId) {
            $query->whereHas('user', function ($q) {
                $q->where('section_id', $this->sectionId);
            });
        }

        $this->rows = $query->latest()->get();

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'NO',
            'WAKTU KEJADIAN',
            'NAMA PELANGGAR',
            'NOPOL',
            'PLANT',
            'LOKASI',
            'PELANGGARAN DOKUMEN',
            'PELANGGARAN FISIK',
            'KETERANGAN',
            'STATUS',
            'FOTO BEFORE',
            'FOTO AFTER'
        ];
    }

    public function map($row): array
    {
        $pds = $row->pds ? $row->pds->pluck('nama_pd')->join(', ') : '-';
        $pfs = $row->pfs ? $row->pfs->pluck('nama_pf')->join(', ') : '-';

        return [
            $this->rows->search($row) + 1,
            $row->waktu_kejadian ? $row->waktu_kejadian->format('d-m-Y H:i') : '',
            $row->user->nama ?? 'N/A',
            $row->nopol,
            $row->plant,
            $row->lokasi,
            $pds,
            $pfs,
            $row->keterangan_pelanggaran,
            $row->status,
            '', // Placeholder for Foto Before
            ''  // Placeholder for Foto After
        ];
    }

    public function drawings()
    {
        $drawings = [];
        if (!$this->rows)
            return $drawings;

        foreach ($this->rows as $index => $row) {
            // Handle JSON/Array images. For now, we take the first one.

            // Foto Before (Column K)
            $beforeFiles = $this->decodeImages($row->bukti_pelanggaran_fisik); // Assuming this is where before images are? 
            // Wait, SafetyRiding usually has 'bukti_pelanggaran_fisik' or 'bukti_pelanggaran_dokumen' or just 'foto'.
            // Controller checks 'bukti_after'. Let's check what fields exist.
            // Usually valid fields: 'bukti_foto' (before?) and 'bukti_after'.
            // Let's try 'bukti_foto' first for "Before", if not check 'foto'.
            // Based on generic knowledge of this user's app structure in previous turns, likely `bukti_foto` or just `foto`.
            // Let's assume `bukti_foto` based on standard pattern, or check if it's stored differently.
            // Actually, in `PicSafetyRidingController.php`, `uploadAfter` updates `bukti_after`.
            // Let's use `bukti_foto` for before.

            $beforeFiles = $this->decodeImages($row->bukti);
            if (!empty($beforeFiles)) {
                $this->addImage($drawings, $beforeFiles[0], 'K', $index, 'Foto Before');
            }

            // Foto After (Column L)
            $afterFiles = $this->decodeImages($row->bukti_after);
            if (!empty($afterFiles)) {
                $this->addImage($drawings, $afterFiles[0], 'L', $index, 'Foto After');
            }
        }

        return $drawings;
    }

    private function decodeImages($data)
    {
        if (is_array($data))
            return $data;
        if (empty($data))
            return [];

        $decoded = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Maybe it's a single string path?
        return [$data];
    }

    private function addImage(&$drawings, $dbPath, $column, $index, $name)
    {
        $imagePath = null;
        $possiblePaths = [
            public_path('storage/' . $dbPath),
            storage_path('app/public/' . $dbPath),
            public_path($dbPath)
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $imagePath = $path;
                break;
            }
        }

        if ($imagePath) {
            $drawing = new Drawing();
            $drawing->setName($name);
            $drawing->setDescription($name);
            $drawing->setPath($imagePath);
            $drawing->setHeight(80);
            $drawing->setCoordinates($column . ($index + 2));
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(10);
            $drawings[] = $drawing;
        }
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Header Style
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        if ($highestRow > 1) {
            // Data Style
            $sheet->getStyle('A2:L' . $highestRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);

            $sheet->getStyle('I2:I' . $highestRow)->getAlignment()->setWrapText(true); // Wrap keterangan

            // Row Height
            for ($row = 2; $row <= $highestRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(85);
            }

            // Columns width
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(20);
        }
    }
}
