@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Pembelian</a>
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
    <link href="{{ asset('assets/js/jwdfilepicker/jwdfilepicker.css') . '?' . date('YmdHis') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdfilepicker/jwdfilepicker-loader.css') . '?' . date('YmdHis') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/jwdfilepicker/jwdfilepicker-modal.css') . '?' . date('YmdHis') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/dropzone/dropzone.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/gallery.css') . '?' . date('YmdHis') }}" rel="stylesheet">
@endpush

@push('script')
    @include('layouts.partials.js')
    <script>
        var filepicker_server_url = "{{ url('filepicker/') }}";
        var filepicker_icon_url = "{{ url('assets/img/filepicker_images/') }}";
    </script>
    <script src="{{ asset('assets/js/jwdmodal/jwdmodal.js') . '?' . date('YmdHis') }}"></script>
    <script src="{{ asset('assets/js/dropzone/dropzone.min.js') . '?' . date('YmdHis') }}"></script>
    <script src="{{ asset('assets/js/jwdfilepicker/jwdfilepicker.js') . '?' . date('YmdHis') }}"></script>
    <script src="{{ asset('assets/js/jwdfilepicker.js') . '?' . date('YmdHis') }}"></script>
    <script src="{{ asset('assets/js/dropzone/dropzone.min.js') . '?' . date('YmdHis') }}"></script>
    <script src="{{ asset('assets/js/page/pembelian.js') . '?' . date('YmdHis') }}"></script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form" enctype="multipart/form-data">
                    @if ($pembelian->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>
                                {{ $pembelian->id ? 'Edit' : 'Tambah' }}
                                Pembelian</h6>
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
                    <a class="btn btn-link color-softgray-5" title="Back" href="{{ route('daftar-pembelian.index') }}">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <div class="tab-content" id="myTabContent">
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nomor
                                Invoice</label>
                            <div class="col-sm-5">
                                <input class="form-control" type="text" name="no_invoice"
                                    value="{{ Helper::set_value('no_invoice', @$pembelian->no_invoice) }}" />
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label
                                class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Supplier</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    {!! Helper::options(
                                        ['name' => 'supplier_id', 'class' => 'select2'],
                                        $supplier,
                                        Helper::set_value('supplier_id', @$pembelian->supplier_id),
                                    ) !!}
                                    <a class="text-white input-group-text bg-success" target="_blank" href=""><i
                                            class="fas fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tanggal
                                Invoice</label>
                            <div class="col-sm-5">
                                <input class="form-control flatpickr tanggal-invoice" type="text" name="tgl_invoice"
                                    value="{{ Helper::set_value('tgl_invoice', Helper::format_tanggal(@$pembelian->tgl_invoice, 'dd-mm-yyyy')) }}" />
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tanggal Jatuh
                                Tempo</label>
                            <div class="col-sm-5">
                                <input class="form-control flatpickr tanggal-jatuh-tempo" type="text"
                                    name="tgl_jatuh_tempo"
                                    value="{{ Helper::set_value('tgl_jatuh_tempo', Helper::format_tanggal(@$pembelian->tgl_jatuh_tempo, 'dd-mm-yyyy')) }}" />
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Gudang</label>
                            <div class="col-sm-5">
                                {!! Helper::options(
                                    ['name' => 'gudang_id', 'id' => 'id-gudang'],
                                    $gudang,
                                    Helper::set_value('gudang_id', @$pembelian->gudang_id),
                                ) !!}
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Produk</label>
                            <div class="col-sm-5" style="position:relative">
                                <div class="input-group barcode-group">
                                    <input type="text" name="barcode" class="form-control barcode" value=""
                                        placeholder="13 Digit Barcode" />
                                    <a class="btn btn-outline-secondary add-barang" href="javascript:void(0)"><i
                                            class="fas fa-search"></i> Cari Barang</a>
                                    <a class="btn btn-outline-success" target="_blank" href=""><i
                                            class="fas fa-plus"></i> Tambah Barang</a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-lg-12">
                                @php
                                    $display = '';
                                    $using_detail_barang = 1;
                                    if (empty($pembelian_detail)) {
                                        $display = ' ;display:none';
                                        $using_detail_barang = 0;

                                        $pembelian_detail[] = [
                                            'nama_barang' => '',
                                            'keterangan' => '',
                                            'barang_id' => '',
                                            'qty' => 0,
                                            'harga_neto' => 0,
                                            'expired_date' => '',
                                            'harga_satuan' => '',
                                        ];
                                    }
                                @endphp
                                <div class="table-responsive">
                                    <table style="width:auto{{ $display }}" id="list-barang"
                                        class="table table-stiped table-bordered form-text-12">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">No</th>
                                                <th rowspan="2">Nama Barang</th>
                                                <th>Keterangan</th>
                                                <th>Expired Date</th>
                                                <th>Harga Satuan</th>
                                                <th>Kuantitas</th>
                                                <th>Total Harga</th>
                                                <th rowspan="2">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($pembelian_detail as $val)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>
                                                        <span>{{ $val['nama_barang'] }}</span>
                                                        <input type="hidden" name="barang_id[]"
                                                            value="{{ @$val['barang_id'] }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" size="15" name="keterangan[]"
                                                            class="form-control" value="{{ $val['keterangan'] }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" size="10" name="expired_date[]"
                                                            class="form-control text-end flatpickr"
                                                            value="{{ Helper::format_tanggal($val['expired_date'], 'dd-mm-yyyy') }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" size="2" name="harga_satuan[]"
                                                            class="form-control text-end format-ribuan harga-satuan"
                                                            value="{{ Helper::format_number($val['harga_satuan']) }}" />
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" size="1" name="qty[]"
                                                                class="form-control text-end format-ribuan kuantitas"
                                                                value="{{ @$val['qty'] }}" />
                                                            <span class="input-group-text satuan">pcs</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="2" name="harga_neto[]"
                                                            class="form-control text-end harga-total"
                                                            value="{{ Helper::format_number(@$val['harga_neto']) }}" />
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="btn text-red del-row"><i
                                                                class="fas fa-times"></i></a>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tbody class="total">
                                            <tr>
                                                <td></td>
                                                <td>Sub Total</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <input size="6"
                                                        class="form-control text-end format-ribuan sub-total"
                                                        type="text" name="sub_total"
                                                        value="{{ Helper::set_value('sub_total', Helper::format_number(@$pembelian['sub_total'])) }}"
                                                        readonly />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Diskon</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <input size="6"
                                                        class="form-control text-end format-ribuan diskon" type="text"
                                                        name="diskon"
                                                        value="{{ Helper::set_value('diskon', Helper::format_number(@$pembelian['diskon'])) }}" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Total</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <input size="6"
                                                        class="form-control text-end format-ribuan total" type="text"
                                                        name="total"
                                                        value="{{ Helper::set_value('total', Helper::format_number(@$pembelian['total'])) }}"
                                                        readonly />
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Terima
                                Barang</label>
                            <div class="col-sm-5">
                                {!! Helper::options(
                                    ['name' => 'terima_barang', 'class' => 'terima-barang-option'],
                                    ['Y' => 'Ya', 'N' => 'Belum'],
                                    Helper::set_value('terima_barang', isset($pembelian) ? $pembelian->tanda_terima : 'N'),
                                ) !!}
                            </div>
                        </div>
                        @php
                            $display = '';
                            if (empty($pembelian) || $pembelian->tanda_terima == 'N') {
                                $display = 'display:none';
                            }
                            $tgl_terima_barang = Helper::set_value('tgl_terima_barang', Helper::format_tanggal(@$pembelian->tgl_terima_barang, 'dd-mm-yyyy'));
                            $user_id_terima = Helper::set_value('user_id_terima', @$pembelian->user_id_terima);
                        @endphp
                        <div class="terima-barang-container" style="{{ $display }}"">
                            <div class="form-group row mb-3">
                                <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tanggal
                                    Terima</label>
                                <div class="col-sm-5">
                                    <input class="form-control flatpickr" type="text" name="tgl_terima_barang"
                                        value="{{ $tgl_terima_barang }}" />
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label
                                    class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Penerima</label>
                                <div class="col-sm-5">
                                    {!! Helper::options(['name' => 'user_id_terima', 'class' => 'select2'], $user, $user_id_terima) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label
                                class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Pembayaran</label>
                            <div class="col-sm-9">
                                <a class="btn btn-outline-secondary btn-xs mb-3 add-pembayaran form-text-12"
                                    href="javascript:void(0)"><i class="fas fa-plus"></i> Tambah Pembayaran</a>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-sm-8">
                                @php
                                    $display = '';
                                    $using_pembayaran = 1;
                                    if (empty($pembayaran)) {
                                        $display = ' ;display:none';
                                        $pembayaran[] = ['jml_bayar' => 0, 'tgl_bayar' => '', 'user_id_bayar' => ''];
                                        $using_pembayaran = 0;
                                    }
                                @endphp
                                <div class="table-responsive">
                                    <table style="width:auto{{ $display }}" id="list-pembayaran"
                                        class="table table-stiped table-bordered form-text-12 ">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Jumlah Bayar</th>
                                                <th>User</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                                $total_bayar = 0;
                                            @endphp

                                            @foreach ($pembayaran as $val)
                                                @php
                                                    $total_bayar += $val['jml_bayar'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>
                                                        <input type="text" size="1" name="tgl_bayar[]"
                                                            class="form-control flatpickr text-end format-ribuan"
                                                            value="{{ Helper::format_tanggal(@$val['tgl_bayar'], 'dd-mm-yyyy') }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" size="1" name="jml_bayar[]"
                                                            class="form-control text-end format-ribuan item-bayar"
                                                            value="{{ Helper::format_number(@$val['jml_bayar']) }}" />
                                                    </td>
                                                    <td>{!! Helper::options(['name' => 'user_id_bayar[]'], $user, @$val['user_id_bayar']) !!}</td>
                                                    <td><a href="javascript:void(0)" class="btn text-red del-row"><i
                                                                class="fas fa-times"></i></a></td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach

                                        <tbody class="total">
                                            <tr>
                                                <td></td>
                                                <td>Total Bayar</td>
                                                <td>
                                                    <input class="form-control text-end format-ribuan total-bayar"
                                                        type="text" name="total_bayar"
                                                        value="{{ Helper::set_value('total_bayar', Helper::format_number(@$pembelian->total_bayar)) }}"
                                                        readonly />
                                                </td>
                                                <td></td>
                                                <td colspan="5"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Total Tagihan</td>
                                                <td>
                                                    <input class="form-control text-end format-ribuan total-tagihan"
                                                        type="text"
                                                        value="{{ Helper::set_value('total', Helper::format_number(@$pembelian->total)) }}"
                                                        readonly />
                                                </td>
                                                <td></td>
                                                <td colspan="5"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Kurang</td>
                                                <td>
                                                    <input class="form-control text-end format-ribuan kurang-bayar"
                                                        type="text" name="kurang_bayar"
                                                        value="{{ Helper::set_value('kurang_bayar', Helper::format_number(@$pembelian->kurang_bayar)) }}"
                                                        readonly />
                                                </td>
                                                <td></td>
                                                <td colspan="5"></td>
                                            </tr>
                                        </tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-sm-5">
                            <button type="button" onclick="submitForm(`{{ route('daftar-pembelian.store') }}`)"
                                class="btn btn-primary color-blue" id="submit">Submit</button>
                            <input type="hidden" name="id" value="{{ $pembelian->id }}"/>
                            <input type="hidden" id="using-pembayaran" name="using_pembayaran" value="{{ $using_pembayaran }}"/>
                            <input type="hidden" id="using-list-barang" name="using_detail_barang" value="{{ $using_detail_barang }}"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
