@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Penjualan</a>
            </div>
        </div>
        <div class="sub-items-right">
            {{-- <div class="item-button">
                <a href="#" class="btn btn-link">Back</a>
            </div> --}}
        </div>
    </div>
@endsection

@push('css')
    @include('layouts.partials.css')
    <style>
        #video-preview {
            text-align: center;
            /* transform: scaleX(-1); */
            width: 100%;
            height: auto;
        }
    </style>
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal.css') . '?' . date('YmdHis') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal-loader.css') . '?' . date('YmdHis') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal-fapicker.css') . '?' . date('YmdHis') }}" rel="stylesheet">
@endpush

@push('script')
    @include('layouts.partials.js')
    {{-- <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script> --}}
    <script src="{{ asset('assets/js/jwdmodal/jwdmodal.js') . '?' . date('YmdHis') }}"></script>
    <script src="{{ asset('assets/js/page/penjualan.js') . '?' . date('YmdHis') }}"></script>
    {{-- <script>
        $(document).ready(function() {
            let scanner = new Instascan.Scanner({
                video: document.getElementById('video-preview'),
                mirror: false
            });

            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[1]);
                } else {
                    console.error('No cameras found.');
                }
            }).catch(function(e) {
                console.error(e);
            });

            scanner.addListener('scan', function(content) {
                $('.barcode').val(content);
                $('.barcode').trigger(jQuery.Event('keyup', {
                    keyCode: 13
                }));
                // $('#barcode-form').submit();

            });
        });
    </script> --}}
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form" enctype="multipart/form-data">
                    @if ($penjualan->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>
                                {{ $penjualan->id ? 'Edit' : 'Tambah' }}
                                Penjualan</h6>
                            {{-- <button class="btn btn-icon" title="Add" href="#"
                    onclick="addForm('')">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                            d="M12.8034 2.3584H11.1962C11.0534 2.3584 10.9819 2.42983 10.9819 2.57268V10.9834H3.00042C2.85756 10.9834 2.78613 11.0548 2.78613 11.1977V12.8048C2.78613 12.9477 2.85756 13.0191 3.00042 13.0191H10.9819V21.4298C10.9819 21.5727 11.0534 21.6441 11.1962 21.6441H12.8034C12.9462 21.6441 13.0176 21.5727 13.0176 21.4298V13.0191H21.0004C21.1433 13.0191 21.2147 12.9477 21.2147 12.8048V11.1977C21.2147 11.0548 21.1433 10.9834 21.0004 10.9834H13.0176V2.57268C13.0176 2.42983 12.9462 2.3584 12.8034 2.3584Z"
                            fill="#100F16" />
                        </svg>
                    </button> --}}
                        </div>
                    </div>
                    <div class="horizontal-line my-3"></div>
                    <a class="btn btn-link color-softgray-5" title="Back" href="{{ route('penjualan-list.index') }}">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama
                                Customer</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="nama-customer" name="nama_customer"
                                        disabled="disabled" readonly="readonly"
                                        value="{{ Helper::set_value('nama_customer', @$penjualan->nama_customer ?: 'Umum') }}" />
                                    @php
                                        $display = !empty(@$penjualan->nama_customer) ? '' : 'd-none';
                                    @endphp
                                    <a class="btn btn-outline-secondary {{ $display }}" id="del-customer"
                                        href="javascript:void(0)"><i class="fas fa-times"></i></a>
                                    <button type="button" class="btn btn-outline-secondary cari-customer"><i
                                            class="fas fa-search"></i> Cari</button>
                                    <a class="btn btn-outline-success add-customer" id="add-customer"
                                        href="javascript:void(0)"><i class="fas fa-plus"></i> Tambah</a>
                                </div>
                                <input class="form-control" type="hidden" name="customer_id" id="id-customer"
                                    value="{{ Helper::set_value('customer_id', @$penjualan->customer_id) }}" />
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">No.
                                Invoice</label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="no_invoice" id="no-invoice"
                                    value="{{ Helper::set_value('no_invoice', @$penjualan->no_invoice) }}"
                                    readonly="readonly" />
                                <small class="text-gray form-text-12">Digenerate otomatis oleh sistem</small>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label
                                class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tanggal</label>
                            <div class="col-sm-6">
                                <input class="form-control flatpickr tanggal-invoice flatpickr" type="text"
                                    name="tgl_invoice"
                                    value="{{ Helper::set_value('tgl_invoice', Helper::format_tanggal(@$penjualan->tgl_invoice, 'dd-mm-yyyy')) }}" />
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Gudang</label>
                            <div class="col-sm-6">
                                {!! Helper::options(
                                    ['name' => 'gudang_id', 'id' => 'gudang'],
                                    $gudang,
                                    Helper::set_value('gudang_id', @$penjualan->gudang_id),
                                ) !!}
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Harga</label>
                            <div class="col-sm-6">
                                {!! Helper::options(
                                    ['name' => 'jenis_harga_id', 'id' => 'jenis-harga'],
                                    $jenis_harga,
                                    Helper::set_value('jenis_harga_id', @$jenis_harga_selected),
                                ) !!}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Cari
                                Barang</label>
                            <div class="col-sm-6" style="position:relative">
                                <div class="input-group barcode-group">
                                    <input type="text" name="barcode" class="form-control barcode" value=""
                                        placeholder="13 Digit Barcode" />
                                    <button type="button" class="btn btn-outline-secondary add-barang"><i
                                            class="fas fa-search"></i> Cari Barang</button>
                                    <a class="btn btn-outline-success color-green form-text-12 text-white" target="_blank"
                                        href="{{ route('barang.create') }}"><i class="fas fa-plus"></i> Tambah Barang</a>
                                </div>
                                {{-- <video id="video-preview"></video> --}}
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    @php
                                        $display = '';
                                        if (empty($barang)) {
                                            $display = ' ;display:none';
                                        }
                                    @endphp

                                    <table style="width:auto{{ $display }};" id="list-produk"
                                        class="table table-stiped table-bordered mt-3 form-text-12">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Harga Satuan</th>
                                                <th>Qty</th>
                                                <th>Diskon</th>
                                                <th style="width: 200px">Total Harga</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                                $sub_total = 0;

                                                if (empty($barang)) {
                                                    $barang[] = [];
                                                }

                                            @endphp

                                            @foreach ($barang as $val)
                                                @php
                                                    $stok = @$val->list_stok[$penjualan->gudang_id];
                                                    $sub_total += @$val->harga_neto;
                                                @endphp
                                                <tr class="barang {{ $display }}">
                                                    <td>{{ $no }}</td>
                                                    <td>{{ @$val->nama_barang }}<div class="list-barang-detail"><small
                                                                class="rounded badge-clear-success">Stok: <span
                                                                    class="jml-stok-text">{{ $stok }}
                                                                    {{ @$val->satuan }}</small></div>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="4"
                                                            class="form-control text-end harga-satuan"
                                                            name="harga_satuan[]"
                                                            value="{{ Helper::format_number((int) @$val->harga_satuan) }}" />
                                                        <input type="hidden" name="harga_pokok[]" class="harga-pokok"
                                                            value="{{ @$val->harga_pokok }}" />
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <button type="button"
                                                                class="input-group-text qty-min">-</button>
                                                            <input type="text" class="form-control text-end qty"
                                                                style="width:20px" name="qty[]"
                                                                value="{{ Helper::format_number(@$val->qty) }}" />
                                                            <button type="button"
                                                                class="input-group-text qty-plus">+</button>
                                                        </div>
                                                        <input type="hidden" name="barang_id[]" class="id-barang"
                                                            value="{{ @$val->id }}" />
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            {!! Helper::options(
                                                                ['name' => 'diskon_barang_jenis[]', 'class' => 'diskon-barang-jenis', 'style' => 'width:10px'],
                                                                ['%' => '%', 'rp' => 'Rp'],
                                                                @$val->diskon_jenis,
                                                            ) !!}
                                                            <input type="text" size="4"
                                                                class="form-control text-end diskon-barang"
                                                                style="width:80px" name="diskon_barang_nilai[]"
                                                                value="{{ Helper::format_number(@$val->diskon_nilai) }}" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="4"
                                                            class="form-control text-end harga-total" name="harga_total[]"
                                                            value="{{ Helper::format_number((int) @$val->harga_neto) }}"
                                                            readonly />
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn text-danger del-row"><i
                                                                class="fas fa-times"></i></button>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        @php
                                            $penyesuaian_operator = '-';
                                            $penyesuaian_nilai = 0;
                                            if (@$penjualan->penyesuaian) {
                                                $penyesuaian_operator = $penjualan->penyesuaian > 0 ? '+' : '-';
                                                if ($penjualan->penyesuaian < 0) {
                                                    $penjualan->penyesuaian = $penjualan->penyesuaian * -1;
                                                }
                                                $penyesuaian_nilai = Helper::format_number((int) $penjualan->penyesuaian);
                                            }
                                        @endphp
                                        <tbody>
                                            <tr>
                                                <th colspan="5" class="text-start">Sub Total</th>
                                                <th><input name="sub_total" class="form-control text-end" id="subtotal"
                                                        type="text"
                                                        value="{{ Helper::format_number(old('sub_total', $sub_total)) }}"
                                                        readonly /></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-start">Diskon</td>
                                                <td>
                                                    <div class="input-group">
                                                        {!! Helper::options(
                                                            ['name' => 'diskon_total_jenis', 'id' => 'diskon-total-jenis', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                            ['%' => '%', 'rp' => 'Rp'],
                                                            old('diskon_total_jenis', @$penjualan['diskon_jenis']),
                                                        ) !!}
                                                        <input name="diskon_total_nilai" id="diskon-total"
                                                            class="form-control text-end"
                                                            value="{{ old('diskon_total_nilai', @$penjualan['diskon_nilai']) }}"
                                                            type="text" />
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-start">Penyesuaian</td>
                                                <td>
                                                    <div class="input-group">
                                                        {!! Helper::options(
                                                            ['name' => 'penyesuaian_operator', 'id' => 'operator-penyesuaian', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                            ['-' => '-', '+' => '+'],
                                                            old('penyesuaian_operator', $penyesuaian_operator),
                                                        ) !!}
                                                        <input name="penyesuaian_nilai" class="form-control text-end"
                                                            id="penyesuaian"
                                                            value="{{ old('penyesuaian_nilai', $penyesuaian_nilai) }}"
                                                            type="text" />
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>


                                            @if ($pajak['status'] == 'aktif')
                                                @php
                                                    $pajak_text = empty($penjualan->id) ? $pajak['display_text'] : @$penjualan->pajak_display_text;
                                                @endphp
                                                <tr>
                                                    <td colspan="5" class="text-start">{{ $pajak_text }}</td>
                                                    <td>
                                                        <div class="input-group">
                                                            <button type="button" class="input-group-text"
                                                                id="pajak-min">-</button>
                                                            <input inputmode="numeric" id="pajak-nilai" type="text"
                                                                class="form-control number text-end number"
                                                                style="width:80px" name="pajak_nilai"
                                                                value="{{ old('pajak_nilai', @$penjualan->pajak_persen) }}" />
                                                            <span class="input-group-text">%</span>
                                                            <button type="button" class="input-group-text"
                                                                id="pajak-plus">+</button>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <th colspan="5" class="text-start">Total</th>
                                                <th><input name="neto" class="form-control text-end" id="total"
                                                        type="text"
                                                        value="{{ Helper::format_number(old('neto', @$penjualan->neto)) }}"
                                                        readonly /></th>
                                                <th></th>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="7" class="py-3 text-start bg-light">Bayar</th>
                                            </tr>
                                            <tr>
                                                <td colspan="5">Jenis Bayar</td>
                                                <td>
                                                    <select name="jenis_bayar" class="form-select">
                                                        <option value="tunai"
                                                            {{ old('jenis_bayar', $penjualan->jenis_bayar ?? '') == 'tunai' ? 'selected' : '' }}>
                                                            Tunai</option>
                                                        <option value="tempo"
                                                            {{ old('jenis_bayar', $penjualan->jenis_bayar ?? '') == 'tempo' ? 'selected' : '' }}>
                                                            Tempo</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            @php
                                                $using_pembayaran = 1;
                                                if (empty($pembayaran)) {
                                                    $pembayaran[] = ['jml_bayar' => 0, 'tgl_bayar' => date('Y-m-d'), 'id_user_bayar' => ''];
                                                    $using_pembayaran = 0;
                                                }
                                                $no = 1;
                                                $total_bayar = 0;
                                            @endphp
                                            @foreach ($pembayaran as $index => $val)
                                                @php
                                                    $total_bayar += $val['jml_bayar'];
                                                    $button = $index == 0 ? '<button type="button" class="btn text-success add-pembayaran"><i class="fas fa-plus"></i></button>' : '<button type="button" class="btn text-danger del-pembayaran"><i class="fas fa-times"></i></button>';
                                                @endphp
                                                <tr class="row-bayar">
                                                    <td>{{ $no }}</td>
                                                    <td colspan="4">
                                                        <div class="input-group" style="width:250px; float:right">
                                                            <span class="input-group-text">Tanggal</span>
                                                            <input type="text" size="1" name="tgl_bayar[]"
                                                                class="form-control flatpickr text-end format-ribuan"
                                                                value="{{ date('d-m-Y', strtotime($val['tgl_bayar'])) }}" />
                                                        </div>
                                                    </td>
                                                    <td><input type="text" size="1" name="jml_bayar[]"
                                                            class="form-control text-end format-ribuan item-bayar"
                                                            value="{{ Helper::format_number($val['jml_bayar']) }}" /></td>
                                                    <td class="text-center">{!! $button !!}</td>
                                                </tr>
                                                @php $no++; @endphp
                                            @endforeach
                                            @php
                                                $text = 'Kurang';
                                                $class = '';
                                                if (isset($penjualan->kurang_bayar)) {
                                                    $text = $penjualan->kurang_bayar > 0 ? 'Kurang' : 'Kembali';
                                                    $class = ' text-danger';
                                                }
                                            @endphp
                                            <tr>
                                                <th colspan="5" class="text-start"><span
                                                        class="sisa">{{ $text }}</span></th>
                                                <td><input
                                                        class="form-control text-end format-ribuan kurang-bayar{{ $class }}"
                                                        type="text" name="kurang_bayar"
                                                        value="{{ Helper::format_number( (int) Helper::set_value('kurang_bayar', @$penjualan->kurang_bayar)) }}"
                                                        readonly /></td>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-sm-5">
                            <button type="button" onclick="submitForm(`{{ route('penjualan-list.store') }}`)"
                                class="btn btn-primary color-blue" id="submit">Submit</button>
                            <input type="hidden" name="id" value="{{ $penjualan->id }}" />
                            <input type="hidden" id="using-pembayaran" name="using_pembayaran" value="" />
                            <input type="hidden" id="using-list-barang" name="using_detail_barang" value="" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
