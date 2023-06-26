@php
    $column = [
        'DT_RowIndex' => 'No',
        'nama_barang' => 'Nama',
        'barcode' => 'Barcode',
        'ignore_pilih' => 'Pilih',
    ];

    $settings['order'] = [1, 'asc'];
    $index = 0;
    $th = '';
    foreach ($column as $key => $val) {
        $th .= '<th>' . $val . '</th>';
        if (strpos($key, 'ignore') !== false) {
            $settings['columnDefs'][] = ['targets' => $index, 'orderable' => false];
        }
        $index++;
    }
@endphp
<table id="jwdmodal-table-result" class="table display table-hover table-striped" style="width:100%">
    <thead>
        <tr>
            {!! $th !!}
        </tr>
    </thead>
</table>
@php
    foreach ($column as $key => $val) {
        $column_dt[] = ['data' => $key];
    }
@endphp
<span id="jwdmodal-dataTables-column" style="display:none">{{ json_encode($column_dt) }}</span>
<span id="jwdmodal-dataTables-setting" style="display:none">{{ json_encode($settings) }}</span>
<span id="jwdmodal-dataTables-url"
    style="display:none">{{ url('/barcode-cetak/getDataBarang') }}</span>
