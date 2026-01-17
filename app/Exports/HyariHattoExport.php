<?php

namespace App\Exports;

use App\Models\HyariHatto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class HyariHattoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithDrawings
{
    protected $search;
    protected $rows; // Cache to hold data for drawings

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = HyariHatto::with(['ptas', 'ktas', 'pbs', 'user', 'section'])
            ->latest();

        // Apply search filter if exists
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('deskripsi', 'like', '%' . $this->search . '%')
                    ->orWhereHas('ptas', function ($q2) {
                        $q2->where('nama_pta', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('ktas', function ($q2) {
                        $q2->where('nama_kta', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $this->rows = $query->get();
        return $this->rows;
    }

    /**
     * Define the headings
     */
    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL LAPORAN',
            'SECTION',
            'PELAPOR',
            'PERILAKU TIDAK AMAN (PTA)',
            'KONDISI TIDAK AMAN (KTA)',
            'POTENSI BAHAYA (PB)',
            'DESKRIPSI KEJADIAN',
            'USULAN COUNTERMEASURE',
            'REKOMENDASI P2K3',
            'LOKASI',
            'TANGGAL DIBUAT',
            'TERAKHIR DIPERBARUI',
            'BUKTI VISUAL'
        ];
    }

    /**
     * Map each row dengan format list
     */
    public function map($laporan): array
    {
        // Handle null data
        $ptas = $laporan->ptas ? $laporan->ptas->pluck('nama_pta')->toArray() : [];
        $ktas = $laporan->ktas ? $laporan->ktas->pluck('nama_kta')->toArray() : [];
        $pbs = $laporan->pbs ? $laporan->pbs->pluck('nama_pb')->toArray() : [];

        return [
            $laporan->id,
            $laporan->created_at ? $laporan->created_at->format('d/m/Y') : '',
            $laporan->section ? ($laporan->section->section ?? 'N/A') : 'N/A',
            $laporan->user ? ($laporan->user->nama ?? 'N/A') : 'N/A',
            $this->formatList($ptas),
            $this->formatList($ktas),
            $this->formatList($pbs),
            $laporan->deskripsi ?? '',
            $laporan->usulan ?? '',
            $laporan->rekomendasi ?? '-',
            $laporan->lokasi ?? '-',
            $laporan->created_at ? $laporan->created_at->format('d/m/Y H:i') : '',
            $laporan->updated_at ? $laporan->updated_at->format('d/m/Y H:i') : '',
            '', // Placeholder for Image (Column N)
        ];
    }

    /**
     * Format data menjadi list bernomor
     */
    private function formatList(array $items): string
    {
        // Filter empty items
        $items = array_filter($items, function ($item) {
            return !empty($item) && trim($item) !== '';
        });

        if (empty($items)) {
            return '-';
        }

        $formatted = '';
        foreach ($items as $index => $item) {
            $number = $index + 1;
            $formatted .= "{$number}. " . $this->cleanText($item) . "\n";
        }

        return rtrim($formatted);
    }

    /**
     * Clean text untuk Excel
     */
    private function cleanText(string $text): string
    {
        // Remove control characters
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
    }

    /**
     * Apply styles dengan penanganan error
     */
    public function styles(Worksheet $sheet)
    {
        try {
            // Get highest row
            $highestRow = $sheet->getHighestRow();

            // Apply header style
            $sheet->getStyle('A1:N1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F172A'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            $sheet->getRowDimension(1)->setRowHeight(30);

            // Only apply data styles if there is data
            if ($highestRow > 1) {
                // Set wrap text for list columns
                $sheet->getStyle('E2:G' . $highestRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('H2:J' . $highestRow)->getAlignment()->setWrapText(true);

                // Apply borders to all data cells
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFDDDDDD'],
                        ],
                    ],
                ];

                $sheet->getStyle('A2:N' . $highestRow)->applyFromArray($borderStyle);

                // Alternate row colors
                for ($row = 2; $row <= $highestRow; $row++) {
                    $color = $row % 2 == 0 ? 'FFF8F9FA' : 'FFFFFFFF';

                    $sheet->getStyle("A{$row}:N{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($color);

                    // Set alignment
                    $sheet->getStyle("A{$row}:D{$row},K{$row}:N{$row}")->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("E{$row}:J{$row}")->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP);

                    // Auto adjust row height
                    $this->autoAdjustRowHeight($sheet, $row);
                }

                // Freeze header row
                $sheet->freezePane('A2');
            }

        } catch (\Exception $e) {
            // Log error but continue
            \Log::error('Excel export styling error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Auto adjust row height
     */
    private function autoAdjustRowHeight(Worksheet $sheet, int $row): void
    {
        try {
            $maxLines = 1;
            $columns = ['E', 'F', 'G', 'H', 'I', 'J'];

            foreach ($columns as $col) {
                $cell = $sheet->getCell($col . $row);
                if ($cell) {
                    $value = $cell->getValue();
                    if ($value) {
                        $lines = substr_count((string) $value, "\n") + 1;
                        $maxLines = max($maxLines, $lines);
                    }
                }
            }

            $rowHeight = max(85, $maxLines * 15);
            $sheet->getRowDimension($row)->setRowHeight($rowHeight);
        } catch (\Exception $e) {
            // Skip if error
        }
    }

    /**
     * Set column widths
     */
    public function setColumnWidths(): array
    {
        return [
            'A' => 8,    // NO
            'B' => 15,   // TANGGAL
            'C' => 20,   // SECTION
            'D' => 20,   // PELAPOR
            'E' => 35,   // PTA
            'F' => 35,   // KTA
            'G' => 35,   // PB
            'H' => 50,   // DESKRIPSI
            'I' => 40,   // USULAN
            'J' => 40,   // REKOMENDASI
            'K' => 20,   // LOKASI
            'L' => 20,   // CREATED
            'M' => 20,   // UPDATED
            'N' => 30,   // BUKTI VISUAL
        ];
    }

    /**
     * Format kolom tertentu sebagai text
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // NO sebagai text
            'E' => NumberFormat::FORMAT_TEXT, // PTA
            'F' => NumberFormat::FORMAT_TEXT, // KTA  
            'G' => NumberFormat::FORMAT_TEXT, // PB
            'H' => NumberFormat::FORMAT_TEXT, // Deskripsi
            'I' => NumberFormat::FORMAT_TEXT, // Usulan
            'J' => NumberFormat::FORMAT_TEXT, // Rekomendasi
        ];
    }
    public function drawings()
    {
        $drawings = [];

        if (!$this->rows) {
            return $drawings;
        }

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
                    $drawing->setDescription('Bukti Kejadian');
                    $drawing->setPath($imagePath);
                    $drawing->setHeight(80);
                    $drawing->setCoordinates('N' . ($index + 2));
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(10);
                    $drawings[] = $drawing;
                }
            }
        }

        return $drawings;
    }
}