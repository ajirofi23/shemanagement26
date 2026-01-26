<table>
    <thead>
        <!-- Row 1 -->
        <tr>
            <th rowspan="3"
                style="background-color: #f8fafc; border: 1px solid #000000; text-align: center; vertical-align: center;">
                NO</th>
            <th rowspan="3"
                style="background-color: #f8fafc; border: 1px solid #000000; text-align: center; vertical-align: center;">
                ITEM</th>

            @foreach($matrixData['header']['historical_labels'] as $label)
                <th rowspan="3"
                    style="background-color: #f8fafc; border: 1px solid #000000; text-align: center; vertical-align: center;">
                    {{ $label }}</th>
            @endforeach

            <th colspan="13"
                style="background-color: #fdf2f8; border: 1px solid #000000; text-align: center; vertical-align: center;">
                {{ $matrixData['header']['fiscal_label'] }}</th>
            <th colspan="{{ $matrixData['header']['days_in_month'] + 1 }}"
                style="background-color: #ffffff; border: 1px solid #000000; text-align: center; vertical-align: center;">
                {{ $matrixData['header']['fiscal_label'] }}</th>
        </tr>

        <!-- Row 2 -->
        <tr>
            @php
                $monthNames = ['APR', 'MAY', 'JUN', 'JUL', 'AGS', 'SEP', 'OKT', 'NOP', 'DES', 'JAN', 'PEB', 'MAR'];
            @endphp
            @foreach($monthNames as $mName)
                <th rowspan="2"
                    style="background-color: #fdf2f8; border: 1px solid #000000; text-align: center; vertical-align: center;">
                    {{ $mName }}</th>
            @endforeach
            <th rowspan="2"
                style="background-color: #f0f9ff; border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: center;">
                JML</th>

            <th colspan="{{ $matrixData['header']['days_in_month'] + 1 }}"
                style="background-color: #ffffff; border: 1px solid #000000; text-align: center; vertical-align: center;">
                {{ $matrixData['header']['month_label'] }}</th>
        </tr>

        <!-- Row 3 -->
        <tr>
            @for($d = 1; $d <= 31; $d++)
                @if($d <= $matrixData['header']['days_in_month'])
                    <th
                        style="background-color: #ffffff; border: 1px solid #000000; text-align: center; vertical-align: center;">
                        {{ $d }}</th>
                @endif
            @endfor
            <th
                style="background-color: #f0f9ff; border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: center;">
                JML</th>
        </tr>
    </thead>
    <tbody>
        @php $rowNo = 1; @endphp
        @foreach($matrixData['matrix'] as $item => $data)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $rowNo++ }}</td>
                <td style="border: 1px solid #000000;">{{ $item }}</td>

                <!-- Historical -->
                @foreach($data['historical'] as $val)
                    <td style="border: 1px solid #000000; text-align: center;">{{ $val }}</td>
                @endforeach

                <!-- Months -->
                @foreach($data['months'] as $val)
                    <td style="border: 1px solid #000000; text-align: center;">{{ $val }}</td>
                @endforeach
                <td style="background-color: #f0f9ff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                    {{ $data['total_fiscal'] }}</td>

                <!-- Days -->
                @foreach($data['days'] as $val)
                    @if($val !== null)
                        <td style="border: 1px solid #000000; text-align: center;">{{ $val }}</td>
                    @endif
                @endforeach
                <td style="background-color: #f0f9ff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                    {{ $data['total_month'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>