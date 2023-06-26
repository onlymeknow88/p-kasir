@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Transfer Barang</a>
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
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal-loader.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdmodal/jwdmodal-fapicker.css') }}" rel="stylesheet">
@endpush

@push('script')
    @include('layouts.partials.js')
    <script src="{{ asset('assets/js/jwdmodal/jwdmodal.js') }}"></script>
    <script src="{{ asset('assets/js/page/transfer-barang.js') }}"></script>
@endpush


@section('content')
    @php
        if (!@$trfBarang->tgl_nota_transfer) {
            $trfBarang->tgl_nota_transfer = date('Y-m-d');
        }
    @endphp

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form">
                    @if ($trfBarang->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>
                                {{ $trfBarang->id ? 'Edit' : 'Tambah' }}
                                Transfer Barang</h6>
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
                    <a class="btn btn-link color-softgray-5" title="Back" href="{{ route('transfer-barang.index') }}">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <input type="hidden" id="id" name="id" value="{{ $trfBarang->id }}">

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">No. Nota
                            Transfer</label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="no_nota_transfer" id="no-nota-transfer"
                                readonly="readonly" value="{{ $trfBarang->no_nota_transfer }}" />
                            <small class="text-muted form-text-12">Digenerate otomatis oleh sistem</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tanggal</label>
                        <div class="col-sm-6">
                            <input class="form-control tanggal-nota-transfer flatpickr" type="text"
                                name="tgl_nota_transfer"
                                value="{{ Helper::format_tanggal(@$trfBarang->tgl_nota_transfer, 'dd-mm-yyyy') }}" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Gudang
                            Asal</label>
                        <div class="col-sm-6">
                            {!! Helper::options(
                                ['name' => 'gudang_asal_id', 'id' => 'gudang-asal'],
                                $gudang,
                                Helper::set_value('gudang_asal_id', @$trfBarang->gudang_asal_id),
                            ) !!}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Gudang
                            Tujuan</label>
                        <div class="col-sm-6">
                            {!! Helper::options(
                                ['name' => 'gudang_tujuan_id', 'id' => 'gudang-tujuan'],
                                $gudang,
                                Helper::set_value('gudang_tujuan_id', @$trfBarang->gudang_tujuan_id),
                            ) !!}
                        </div>
                    </div>
                    <div class="row mb-3">
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
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Keterangan</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" type="text" name="keterangan"></textarea>
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
                                            <th>Stok</th>
                                            <th>Satuan</th>
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
                                                $stok = @$val->list_stok[$trfBarang->gudang_asal_id];
                                                $sub_total += @$val->harga_neto_transfer;
                                            @endphp
                                            <tr class="barang"{{ $display }}>
                                                <td>{{ $no }}</td>
                                                <td>{{ @$val->nama_barang }}</td>
                                                <td><span class="jml-stok">{{ @$stok }}</span></td>
                                                <td>{{ @$val->satuan }}</td>
                                                <td>
                                                    <input type="text" size="4"
                                                        class="form-control text-end harga-satuan" readonly="readonly"
                                                        name="harga_satuan[]"
                                                        value="{{ Helper::format_number((int) @$val->harga_satuan) }}" />
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-end qty" size="1"
                                                        name="qty_barang[]"
                                                        value="{{ Helper::format_number(@$val->qty_transfer) }}" />
                                                    <input type="hidden" name="barang_id[]" class="id-barang"
                                                        value="{{ @$val->id }}" />
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        {{-- <select name="diskon_barang_jenis[]"
                                                            class="form-select diskon-barang-jenis" id=""
                                                            style="width:auto">
                                                            <option value="%">%</option>
                                                            <option value="rp">Rp</option>
                                                        </select> --}}
                                                        {!! Helper::options(
                                                            ['name' => 'diskon_barang_jenis[]', 'class' => 'diskon-barang-jenis', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                            ['%' => '%', 'rp' => 'Rp'],
                                                            Helper::set_value('diskon_total_jenis', @$transfer_barang['diskon_jenis_transfer']),
                                                        ) !!}
                                                        <input type="text" size="4"
                                                            class="form-control text-end diskon-barang"
                                                            name="diskon_barang[]"
                                                            value="{{ Helper::format_number(@$val->diskon_nilai_transfer) }}" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" size="4"
                                                        class="form-control text-end harga-total" name="harga_total[]"
                                                        value="{{ Helper::format_number((int) @$val->harga_neto_transfer) }}"
                                                        readonly />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn text-red del-row"><i
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
                                        if (@$transfer_barang['penyesuaian_transfer']) {
                                            $penyesuaian_operator = $transfer_barang['penyesuaian_transfer'] > 0 ? '+' : '-';
                                            if ($transfer_barang['penyesuaian_transfer'] < 0) {
                                                $transfer_barang['penyesuaian_transfer'] = $transfer_barang['penyesuaian_transfer'] * -1;
                                            }
                                            $penyesuaian_nilai = Helper::format_number((int) $transfer_barang['penyesuaian_transfer']);
                                        }
                                    @endphp
                                    <tbody>
                                        <tr>
                                            <th colspan="7" class="text-start">Sub Total</th>
                                            <th>
                                                <input name="sub_total" class="form-control text-end" id="subtotal"
                                                    type="text" value="{{ $sub_total }}" readonly />
                                            </th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="text-start">Diskon</td>
                                            <td>
                                                <div class="input-group">
                                                    {{-- <select name="diskon_total_jenis" class="form-select"
                                                    id="diskon-total-jenis" style="flex: 0 0 auto;width: 70px">
                                                    <option value="%">%</option>
                                                    <option value="rp">Rp</option>
                                                </select> --}}
                                                    {!! Helper::options(
                                                        ['name' => 'diskon_total_jenis', 'id' => 'diskon-total-jenis', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                        ['%' => '%', 'rp' => 'Rp'],
                                                        Helper::set_value('diskon_total_jenis', @$trfBarang->diskon_jenis_transfer),
                                                    ) !!}
                                                    <input name="diskon_total_nilai" id="diskon-total"
                                                        class="form-control text-end"
                                                        value="{{ @$trfBarang->diskon_nilai_transfer }}"
                                                        type="text" />
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="text-start">Penyesuaian</td>
                                            <td>
                                                <div class="input-group">
                                                    {{-- <select name="penyesuaian_operator" class="form-select"
                                                    id="operator-penyesuaian" style="flex: 0 0 auto;width: 70px">
                                                    <option value="-">-</option>
                                                    <option value="+">+</option>
                                                </select> --}}
                                                    {!! Helper::options(
                                                        ['name' => 'penyesuaian_operator', 'id' => 'operator-penyesuaian', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                        ['-' => '-', '+' => '+'],
                                                        Helper::set_value('penyesuaian_operator', $penyesuaian_operator),
                                                    ) !!}
                                                    <input name="penyesuaian_nilai" class="form-control text-end"
                                                        id="penyesuaian" value="{{ $penyesuaian_nilai }}"
                                                        type="text" />
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-start">Total</th>
                                            <th>
                                                <input name="neto" class="form-control text-end" id="total"
                                                    type="text"
                                                    value="{{ Helper::format_number($trfBarang->neto_transfer) }}"
                                                    readonly />
                                            </th>
                                            <th></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-md-4 col-12 button-group">
                            <button type="button" onclick="submitForm(`{{ route('transfer-barang.store') }}`)"
                                class="btn btn-primary color-blue" id="submit">Submit</button>
                            <button type="button" class="btn btn-link mx-2"
                                onclick="resetForm(this.form)">Reset</button>
                        </div>
                    </div>
                    <span style="display:none" id="list-barang-terpilih"></span>
                </form>
            </div>
        </div>
    </div>
@endsection
