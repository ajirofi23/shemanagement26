<?php

namespace App\Exports;

use App\Models\SafetyPatrol;
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

class SafetyPatrolExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithDrawings
{
    protected $sectionId;
    protected $rows;

    public function __construct($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    public function collection()
    {
        // Start query
        $query = SafetyPatrol::with(['section', 'user']);

        // Filter by section_id if provided (for PIC)
        if ($this->sectionId) {
            $query->where('section_id', $this->sectionId);
        }

        $this->rows = $query->latest()->get();

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'E-PORTE',
            'AREA',
            'PROBLEM',
            'COUNTER MEASURE',
            'SECTION',
            'DUE DATE',
            'STATUS',
            'FOTO BEFORE',
            'FOTO AFTER'
        ];
    }

    public function map($row): array
    {
        return [
            $this->rows->search($row) + 1,
            $row->tanggal ? Carbon::parse($row->tanggal)->format('d-m-Y') : '',
            $row->eporte,
            $row->area,
            $row->problem,
            $row->counter_measure,
            $row->section->section ?? 'N/A',
            $row->due_date ? Carbon::parse($row->due_date)->format('d-m-Y') : '',
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
            // Foto Before (Column J)
            if ($row->foto_before) {
                $this->addImage($drawings, $row->foto_before, 'J', $index, 'Foto Before');
            }

            // Foto After (Column K)
            if ($row->foto_after) {
                $this->addImage($drawings, $row->foto_after, 'K', $index, 'Foto After');
            }
        }

        return $drawings;
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
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        if ($highestRow > 1) {
            // Data Style
            $sheet->getStyle('A2:K' . $highestRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);

            $sheet->getStyle('E2:F' . $highestRow)->getAlignment()->setWrapText(true); // Wrap problem & counter measure

            // Row Height
            for ($row = 2; $row <= $highestRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(85);
            }

            // Set widths for image columns
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(20);
        }
    }
}
