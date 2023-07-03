@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Pembelian Retur</a>
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
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal.css') . '?' . date('YmdHis') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal-loader.css') . '?' . date('YmdHis') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal-fapicker.css') . '?' . date('YmdHis') }}" rel="stylesheet">
@endpush

@push('script')
    @include('layouts.partials.js')
    <script src="{{ asset('assets/js/jwdmodal/jwdmodal.js') . '?' . date('YmdHis') }}"></script>
    <script src="{{ asset('assets/js/page/pembelian-retur.js') . '?' . date('YmdHis') }}"></script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form" enctype="multipart/form-data">
                    @if ($pembelian_retur->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>
                                {{ $pembelian_retur->id ? 'Edit' : 'Tambah' }}
                                Pembelian Retur</h6>
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
                    <a class="btn btn-link color-softgray-5" title="Back" href="{{ route('pembelian-retur.index') }}">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">No. Nota
                                Retur</label>
                            <div class="col-sm-6">
                                <input class="form-control" id="no-nota-retur" type="text" name="no_nota_retur"
                                    readonly="readonly"
                                    value="{{ Helper::set_value('no_nota_retur', @$pembelian_retur->no_nota_retur) }} " />
                                <small class="text-gray form-text-12">Otomatis digenerate oleh sistem</small>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tgl. Nota
                                Retur</label>
                            <div class="col-sm-6">
                                <input class="form-control flatpickr tanggal-nota-retur flatpickr" type="text"
                                    name="tgl_nota_retur"
                                    value="{{ Helper::set_value('tgl_nota_retur', Helper::format_tanggal(@$pembelian_retur->tgl_nota_retur, 'dd-mm-yyyy')) }}" />
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">No. Invoice
                                Pebelian</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="text" name="no_invoice" id="no-invoice" readonly="readonly"
                                        class="form-control barcode"
                                        value="{{ Helper::set_value('no_invoice', @$pembelian_retur->no_invoice) }}" />
                                    <button type="button" class="btn btn-outline-secondary cari-invoice"><i
                                            class="fas fa-search"></i> Cari Invoice</button>
                                    <input type="hidden" name="id_pembelian"
                                        value="{{ Helper::set_value('id_pembelian', @$pembelian_retur->id_pembelian) }}"
                                        id="id-pembelian" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama
                                Supplier</label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="nama_supplier" readonly="readonly"
                                    id="nama-supplier"
                                    value="{{ Helper::set_value('nama_supplier', @$pembelian_retur['nama_supplier']) }}" />
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Gudang</label>
                            <div class="col-sm-6">
                                {!! Helper::options(['name' => 'gudang_id', 'id' => 'id-gudang', 'disabled' => 'disabled'], $gudang) !!}
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col">
                                @php
                                    $display = '';
                                    if (empty($barang)) {
                                        $display = ' ;display:none';
                                    }
                                @endphp
                                <div class="table-responsive">
                                    <table style="width:auto{{ $display }}" id="list-produk"
                                        class="table table-stiped table-bordered form-text-12 mt-3">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Satuan</th>
                                                <th>Harga Satuan</th>
                                                <th>Qty Beli</th>
                                                <th>Qty Kembali</th>
                                                <th>Diskon</th>
                                                <th style="width: 175px">Total Beli</th>
                                                <th style="width: 175px">Total Retur</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;

                                                // Barang
                                                // $display = '';
                                                $sub_total = 0;
                                                if (empty($barang)) {
                                                    // $display = ' style="display:none"';
                                                    $barang[] = [];
                                                }
                                                // echo '<pre>'; print_r($barang); die;
                                            @endphp
                                            @foreach ($barang as $val)
                                                <tr class="barang">
                                                    <td>{{ $no }}</td>
                                                    <td><span class="nama-barang">{{ @$val['nama_barang'] }}</span>
                                                        <input type="hidden" name="id_pembelian_detail[]"
                                                            class="id-pembelian-detail"
                                                            value="{{ @$val['id_pembelian_detail'] }}" />
                                                    </td>
                                                    <td>{{ @$val['satuan'] }}</td>
                                                    <td>
                                                        <input type="text" size="4"
                                                            class="form-control text-end harga-satuan" readonly="readonly"
                                                            name="harga_satuan[]"
                                                            value="{{ Helper::format_number((int) @$val['harga_satuan']) }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-end qty-beli"
                                                            size="1" name="qty_barang[]" readonly="readonly"
                                                            value="{{ Helper::format_number(@$val['qty']) }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-end qty-retur"
                                                            size="1" name="qty_barang_retur[]"
                                                            value="{{ Helper::format_number(@$val['qty_retur']) }}" />
                                                    </td>
                                                    <td>
                                                        <div class="input-group" style="width:150px">
                                                            {!! Helper::options(
                                                                ['name' => 'diskon_barang_jenis[]', 'class' => 'diskon-barang-jenis', 'style' => 'flex: 0 0 auto;width: 65px'],
                                                                ['%' => '%', 'rp' => 'Rp'],
                                                                @$val['diskon_jenis_retur'],
                                                            ) !!}
                                                            <input type="text" size="4"
                                                                class="form-control text-end diskon-barang"
                                                                style="width:80px" name="diskon_barang[]"
                                                                value="{{ Helper::format_number(@$val['diskon_nilai_retur']) }}" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="4"
                                                            class="form-control text-end harga-total-beli"
                                                            name="harga_total_beli[]"
                                                            value="{{ Helper::format_number((int) @$val['harga_neto']) }}"
                                                            readonly />
                                                    </td>
                                                    <td>
                                                        <input type="text" size="4"
                                                            class="form-control text-end harga-total-retur"
                                                            name="harga_total_retur[]"
                                                            value="{{ Helper::format_number((int) @$val['harga_neto_retur']) }}"
                                                            readonly />
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn text-danger del-row"><i
                                                                class="fas fa-times"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        @php
                                            $sub_total += @$val['harga_neto'];
                                            $no++;
                                            $penyesuaian_operator = '-';
                                            $penyesuaian_nilai = 0;
                                            if (@$pembelian_retur['penyesuaian']) {
                                                $penyesuaian_operator = $pembelian_retur['penyesuaian'] > 0 ? '+' : '-';
                                                $penyesuaian_nilai = Helper::format_number((int) $pembelian_retur['penyesuaian']);
                                            }
                                        @endphp

                                        <tbody>
                                            <tr>
                                                <th colspan="8" class="text-start">Sub Total</th>
                                                <th>
                                                    <input name="sub_total_retur" class="form-control text-end"
                                                        id="subtotal" type="text"
                                                        value="{{ Helper::format_number(Helper::set_value('sub_total_retur', @$pembelian_retur['sub_total_retur'])) }}"
                                                        readonly />
                                                </th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td colspan="8" class="text-start">Diskon</td>
                                                <td>
                                                    <div class="input-group">
                                                        {!! Helper::options(
                                                            ['name' => 'diskon_total_jenis', 'id' => 'diskon-total-jenis', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                            ['%' => '%', 'rp' => 'Rp'],
                                                            Helper::set_value('diskon_total_jenis', @$pembelian_retur['diskon_jenis']),
                                                        ) !!}
                                                        <input name="diskon_total_nilai" id="diskon-total"
                                                            class="form-control text-end"
                                                            value="{{ Helper::set_value('diskon_total_nilai', @$pembelian_retur['diskon_nilai']) }}"
                                                            type="text" />
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="8" class="text-start">Penyesuaian</td>
                                                <td>
                                                    <div class="input-group">
                                                        {!! Helper::options(
                                                            ['name' => 'penyesuaian_operator', 'id' => 'operator-penyesuaian', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                            ['-' => '-', '+' => '+'],
                                                            Helper::set_value('penyesuaian_operator', $penyesuaian_operator),
                                                        ) !!}
                                                        <input name="penyesuaian_nilai" class="form-control text-end"
                                                            id="penyesuaian"
                                                            value="{{ Helper::set_value('penyesuaian_nilai', $penyesuaian_nilai) }}"
                                                            type="text" />
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th colspan="8" class="text-start">Total</th>
                                                <th>
                                                    <input name="neto" class="form-control text-end" id="total"
                                                        type="text"
                                                        value="{{ Helper::format_number(Helper::set_value('neto', @$pembelian_retur['neto_retur'])) }}"
                                                        readonly />
                                                </th>
                                                <th></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-sm-5">
                            <button type="button" onclick="submitForm(`{{ route('pembelian-retur.store') }}`)"
                                class="btn btn-primary color-blue" id="submit">Submit</button>
                            <input type="hidden" name="id" value="{{ $pembelian_retur->id }}" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
