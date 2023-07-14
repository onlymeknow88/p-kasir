@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Penjualan Tempo</a>
            </div>
        </div>
        <div class="sub-items-right">
            <div class="item-button">
                {{-- <a href="{{ route('role.create') }}"
                    class="btn btn-primary color-blue d-flex align-items-center justify-content-between">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7.46863 1.37573H6.53113C6.44779 1.37573 6.40613 1.4174 6.40613 1.50073V6.40698H1.75024C1.66691 6.40698 1.62524 6.44865 1.62524 6.53198V7.46948C1.62524 7.55282 1.66691 7.59448 1.75024 7.59448H6.40613V12.5007C6.40613 12.5841 6.44779 12.6257 6.53113 12.6257H7.46863C7.55196 12.6257 7.59363 12.5841 7.59363 12.5007V7.59448H12.2502C12.3336 7.59448 12.3752 7.55282 12.3752 7.46948V6.53198C12.3752 6.44865 12.3336 6.40698 12.2502 6.40698H7.59363V1.50073C7.59363 1.4174 7.55196 1.37573 7.46863 1.37573Z"
                            fill="white" />
                    </svg>
                    <span class="ml-10">Add New</span>
                </a> --}}
            </div>
        </div>
    </div>
@endsection

@push('css')
    @include('layouts.partials.css')
    <link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker.css') }}">
@endpush

@push('script')
    @include('layouts.partials.js')



    <script src="{{ asset('assets/js/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/page/penjualan-tempo.js') }}"></script>
@endpush

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="col-12 item-title">
                        <h6>Data Penjualan Tempo</h6>
                        {{-- <div class="btn-group">
                            <button class="btn btn-link color-softgray-5" id="btn-excel" title="Export Excel">
                                <i class="fas fa-file-excel me-2"></i> Export Excel
                            </button>
                        </div> --}}
                    </div>
                </div>
                <div class="horizontal-line my-3"></div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tanggal</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="daterange" id="daterange"
                            value="{{ $data['start_date'] }} s.d {{ $data['end_date'] }}" />
                        <input type="hidden" value="{{ $data['start_date_db'] }}" id="start-date" />
                        <input type="hidden" value="{{ $data['end_date_db'] }}" id="end-date" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tampilkan Piutang</label>
                    <div class="col-sm-5">
                        {!! Helper::options(
                            ['name' => 'jatuh_tempo', 'id' => 'jatuh-tempo'],
                            [
                                '' => 'Semua',
                                'lewat_jatuh_tempo' => 'Lewat ' . $setting_piutang['piutang_periode'] . ' hari',
                                'akan_jatuh_tempo' => 'Jatuh tempo dalam ' . $setting_piutang['notifikasi_periode'] . ' hari',
                            ],
                            @$data['jatuh_tempo'],
                        ) !!}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Total Penjualan</label>
                    <div class="col-sm-5">
                        <span class="form-text-12" id="total-neto">{{Helper::format_number($data['total_penjualan']->total_neto)}}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Total Dibayar</label>
                    <div class="col-sm-5">
                        <span class="form-text-12" id="total-bayar">{{Helper::format_number($data['total_penjualan']->total_bayar)}}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Total Piutang</label>
                    <div class="col-sm-5">
                        <span class="form-text-12" id="total-piutang">{{Helper::format_number($data['total_penjualan']->total_neto - $data['total_penjualan']->total_bayar)}}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-12">
                        @php
                            $column = [
                                'DT_RowIndex' => 'No',
                                'nama_customer' => 'Nama Customer',
                                'no_invoice' => 'No. Invoice',
                                'tgl_penjualan' => 'Tgl. Transkasi',
                                'neto' => 'Neto',
                                'total_bayar' => 'Bayar',
                                'kurang_bayar' => 'Kurang',
                            ];

                            $settings['order'] = [3, 'asc'];
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
                        <table id="table-result" class="table display table-hover table-striped"
                            style="width:100%">
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
                        <span id="dataTables-column" style="display:none">{{ json_encode($column_dt) }}</span>
                        <span id="dataTables-setting" style="display:none">{{ json_encode($settings) }}</span>
                        <span id="dataTables-url"
                            style="display:none">{{ url('penjualan-tempo/getDataDTPenjualanTempo?start_date=' . $data['start_date_db'] . '&end_date=' . $data['end_date_db'] . '&jatuh_tempo=' . $data['jatuh_tempo'] . '') }}</span>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
