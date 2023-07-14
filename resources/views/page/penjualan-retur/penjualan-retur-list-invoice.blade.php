@php
    $column = [
        'DT_RowIndex' => 'No',
        'nama_customer' => 'Nama Customer',
        'alamat_customer' => 'Alamat Customer',
        'no_invoice' => 'Nomor Invoice',
        'tgl_invoice' => 'Tgl. Invoice',
        'ignore_pilih' => 'Pilih',
    ];

    $settings['order'] = [4, 'asc'];
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
{{-- <div class="col-lg-12"> --}}
{{-- <div class="table-responsive"> --}}
<table id="jwdmodal-table-result" class="table display table-hover table-striped" style="width:100%">
    <thead>
        <tr>
            {!! $th !!}
        </tr>
    </thead>
</table>
{{-- </div> --}}
{{-- </div> --}}
@php
    foreach ($column as $key => $val) {
        $column_dt[] = ['data' => $key];
    }
@endphp
<span id="jwdmodal-dataTables-column" style="display:none">{{ json_encode($column_dt) }}</span>
<span id="jwdmodal-dataTables-setting" style="display:none">{{ json_encode($settings) }}</span>
<span id="jwdmodal-dataTables-url" style="display:none">{{ url('/penjualan-retur/getDataInvoice') }}</span>
