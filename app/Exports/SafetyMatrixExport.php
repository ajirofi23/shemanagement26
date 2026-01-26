<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SafetyMatrixExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $matrixData;

    public function __construct($matrixData)
    {
        $this->matrixData = $matrixData;
    }

    public function view(): View
    {
        return view('exports.safety_matrix', [
            'matrixData' => $this->matrixData
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
