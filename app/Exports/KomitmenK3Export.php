<?php

namespace App\Exports;

use App\Models\KomitmenK3;
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

class KomitmenK3Export implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithDrawings
{
    protected $sectionId;
    protected $bulan;
    protected $tahun;
    protected $search;
    protected $rows;

    public function __construct($sectionId, $bulan, $tahun, $search = null)
    {
        $this->sectionId = $sectionId;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->search = $search;
    }

    public function collection()
    {
        $query = KomitmenK3::with('user.section');

        if ($this->sectionId) {
            $query->whereHas('user', function ($q) {
                $q->where('section_id', $this->sectionId);
            });
        }

        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }

        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('komitmen', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($sub) {
                        if ($this->sectionId) {
                            $sub->where('section_id', $this->sectionId);
                        }
                        $sub->where(function ($inner) {
                            $inner->where('nama', 'like', "%{$this->search}%")
                                ->orWhere('nip', 'like', "%{$this->search}%");
                        });
                    });
            });
        }

        $this->rows = $query->latest('updated_at')->get();
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NIP',
            'NAMA',
            'SECTION',
            'DEPARTEMEN',
            'KOMITMEN',
            'STATUS',
            'TANGGAL UPDATE',
            'BUKTI'
        ];
    }

    public function map($row): array
    {
        $fileStatus = '';
        if ($row->bukti) {
            $possiblePaths = [
                public_path('storage/' . $row->bukti),
                storage_path('app/public/' . $row->bukti),
                public_path($row->bukti)
            ];
            $found = false;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $fileStatus = 'File tidak ditemukan';
            }
        } else {
            $fileStatus = 'Tidak ada bukti';
        }

        return [
            $this->rows->search($row) + 1,
            $row->user->nip ?? 'N/A',
            $row->user->nama ?? 'N/A',
            $row->user->section->section ?? 'N/A',
            $row->user->section->department ?? 'N/A',
            $row->komitmen ?? 'Belum ada komitmen',
            $row->status,
            $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : 'N/A',
            $fileStatus // Text fallback if image missing
        ];
    }

    public function drawings()
    {
        $drawings = [];
        if (!$this->rows)
            return $drawings;

        foreach ($this->rows as $index => $row) {
            if ($row->bukti) {
                $imagePath = null;
                $possiblePaths = [
                    public_path('storage/' . $row->bukti),
                    storage_path('app/public/' . $row->bukti),
                    public_path($row->bukti)
                ];

                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $imagePath = $path;
                        break;
                    }
                }

                if ($imagePath) {
                    $drawing = new Drawing();
                    $drawing->setName('Bukti');
                    $drawing->setDescription('Bukti Komitmen');
                    $drawing->setPath($imagePath);
                    $drawing->setHeight(80);
                    $drawing->setCoordinates('I' . ($index + 2));
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(10);
                    $drawings[] = $drawing;
                }
            }
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Header Style
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        if ($highestRow > 1) {
            // Data Style
            $sheet->getStyle('A2:I' . $highestRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);

            // Text Wrapping for Komitmen
            $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension('F')->setWidth(50);

            // Row Height
            for ($row = 2; $row <= $highestRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(85);
            }
        }
    }
}
