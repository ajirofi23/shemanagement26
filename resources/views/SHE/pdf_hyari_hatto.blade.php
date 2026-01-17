<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: top; }
        .header-title { font-size: 14pt; font-weight: bold; text-align: center; }
        .bg-gray { background-color: #f0f4f7; font-weight: bold; text-align: center; }
        .checkbox { font-family: DejaVu Sans, sans-serif; width: 12px; }
        .img-container { text-align: center; padding: 10px; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td rowspan="3" width="15%" style="font-size: 20pt; font-weight: bold; text-align: center;">AICC</td>
            <td rowspan="3" width="55%" class="header-title">FORM LAPORAN<br>HYARI HATTO</td>
            <td width="30%">No. Dokumen : FPG-04-02</td>
        </tr>
        <tr><td>Rev : 02</td></tr>
        <tr><td>Tgl Berlaku : {{ $data->created_at->format('Y-m-d') }}</td></tr>
    </table>

    <table>
        <tr>
            <td>Nama Pelapor: {{ $data->user->nama ?? '' }}</td>
            <td>Seksi: {{ $data->section->section ?? '' }}</td>
            <td>Tgl: {{$data->created_at->format('d-m-Y') }}</td>
            <td>Lokasi: {{ $data->lokasi }}</td>
        </tr>
    </table>

    <table>
        <tr><td colspan="2" class="bg-gray">I. KONDISI TEMUAN</td></tr>
        <tr>
            <th width="50%">A. PERILAKU TIDAK AMAN</th>
            <th width="50%">B. KONDISI TIDAK AMAN</th>
        </tr>
        <tr>
            <td>
                @foreach($masterPtas as $pta)
                    <div>
                        <span class="checkbox">{{ $data->ptas->contains($pta->id) ? '[v]' : '[ ]' }}</span>
                        {{ $loop->iteration }}. {{ $pta->nama_pta }}
                    </div>
                @endforeach
            </td>
            <td>
                @foreach($masterKtas as $kta)
                    <div>
                        <span class="checkbox">{{ $data->ktas->contains($kta->id) ? '[v]' : '[ ]' }}</span>
                        {{ $loop->iteration }}. {{ $kta->nama_kta }}
                    </div>
                @endforeach
            </td>
        </tr>
    </table>

    <table>
        <tr><td colspan="4" class="bg-gray">II. POTENSI BAHAYA</td></tr>
        @foreach($masterPbs->chunk(4) as $chunk)
        <tr>
            @foreach($chunk as $pb)
                <td width="25%">
                    <span class="checkbox">{{ $data->pbs->contains($pb->id) ? '[v]' : '[ ]' }}</span>
                    {{ $pb->id }}. {{ $pb->nama_pb }}
                </td>
            @endforeach
        </tr>
        @endforeach
    </table>

    <table>
    <tr>
        <td colspan="2" class="bg-gray">III. DESKRIPSI TEMUAN / ILUSTRASI GAMBAR</td>
    </tr>
    <tr>
        <td width="35%" style="text-align: center; vertical-align: middle; padding: 10px; height: 180px;">
            @if($data->bukti && file_exists(public_path('storage/' . $data->bukti)))
                <img src="{{ public_path('storage/' . $data->bukti) }}" style="width: 180px; max-height: 160px; border: 1px solid #000;">
            @else
                <div style="border: 1px dashed #ccc; padding: 40px 10px; color: #999;">
                    [ Foto / Ilustrasi Gambar ]
                </div>
            @endif
        </td>
        
        <td width="65%" style="vertical-align: top; padding: 10px; line-height: 1.5;">
            {{ $data->deskripsi }}
        </td>
    </tr>
</table>

    <table>
        <tr><td class="bg-gray">IV. USULAN COUNTERMEASURE</td></tr>
        <tr><td height="40px">{{ $data->usulan ?? '-' }}</td></tr>
        <tr><td class="bg-gray">V. REKOMENDASI P2K3</td></tr>
        <tr><td height="50px">{{ $data->rekomendasi ?? '' }}</td></tr>
    </table>

    <table style="text-align: center;">
        <tr>
            <td width="33%">P2K3</td>
            <td width="33%">ATASAN PELAPOR</td>
            <td width="33%">PELAPOR</td>
        </tr>
        <tr style="height: 60px;">
            <td><br><br>__________</td>
            <td><br><br><strong>( TRI )</strong></td>
            <td><br><br><strong>( {{ $data->user->nama ?? '' }} )</strong></td>
        </tr>
    </table>

</body>
</html>