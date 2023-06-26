@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Barcode</a>
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
    <link href="{{ asset('assets/js/printjs/print.min.css') }}" rel="stylesheet">
@endpush

@push('script')
    @include('layouts.partials.js')
    <script>
        var url_css = "{{ URL::asset('assets/js/printjs/print.min.css') }}";
    </script>
    <script src="{{ asset('assets/js/jwdmodal/jwdmodal.js') }}"></script>
    <script src="{{ asset('assets/js/jsbarcode/JsBarcode.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/jspdf/jspdf.umd.js') }}"></script>
    <script src="{{ asset('assets/js/docxjs/index.js') }}"></script>
    <script src="{{ asset('assets/js/filesaver/FileSaver.js') }}"></script>
    <script src="{{ asset('assets/js/printjs/print.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/barcode.js') }}"></script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form" class="form-horizontal" enctype="multipart/form-data">
                    @method('post')
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>Barcode Cetak</h6>
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
                    <div class="col-lg-12">
                        <div class="tab-content" id="myTabContent">
                            <div class="form-group row mb-3">
                                <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Ukuran
                                    Kertas</label>
                                <div class="col-sm-5">
                                    <div class="d-flex">
                                        {!! Helper::options(
                                            ['name' => 'ukuran_kertas', 'style' => 'width:auto', 'id' => 'paper-size', 'class' => 'me-2'],
                                            ['a4' => 'A4', 'f4' => 'F4', 'custom' => 'Custom'],
                                        ) !!}
                                        <div class="input-group">
                                            <span class="input-group-text">W</span>
                                            <input type="text" class="form-control text-end" name="paper_width"
                                                id="paper-size-width" value="210" disabled />
                                            <span class="input-group-text bg-light">mm</span>
                                            <span class="input-group-text">X</span>
                                            <span class="input-group-text">H</span>
                                            <input type="text" class="form-control text-end" name="paper_height"
                                                id="paper-size-height" value="297" disabled />
                                            <span class="input-group-text bg-light">mm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tampilkan
                                Angka</label>
                            <div class="col-sm-5">
                                {!! Helper::options(['name' => 'tampilkan_angka', 'id' => 'display-value'], ['Y' => 'Ya', 'N' => 'Tidak']) !!}
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tinggi
                                Barcode</label>
                            <div class="col-sm-5">
                                <div class="d-flex">
                                    <input type="range" value="100" class="form-range me-3" min="30"
                                        id="barcode-height" oninput="this.nextElementSibling.value = this.value"><output
                                        class="form-text-13">100</output>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Lebar
                                Barcode</label>
                            <div class="col-sm-5">
                                <div class="d-flex">
                                    <input type="range" value="2" class="form-range me-3" min="1"
                                        max="4" id="barcode-width"
                                        oninput="this.nextElementSibling.value = this.value"><output
                                        class="form-text-13">2</output>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Produk</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" name="barcode" class="form-control barcode" value=""
                                        placeholder="13 Digit Barcode" />
                                    <button type="button" class="btn btn-outline-secondary add-barang"><i
                                            class="fas fa-search"></i> Cari Barang</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-sm-8">
                                @php
                                    $display = 'display:none';
                                @endphp
                                @if ($display === 'display:none')
                                    <table style="width:auto;{{ $display }}" id="list-barang"
                                        class="table table-stiped table-bordered form-text-12">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Barcode</th>
                                                <th>Jumlah Barcode</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <span></span>
                                                </td>
                                                <td class="barcode-barang" value=""></td>
                                                <td>
                                                    <input type="text" size="2" name="jml_cetak[]"
                                                        class="form-control text-end format-ribuan jml-cetak"
                                                        value="0" />
                                                </td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0)" class="btn text-red del-row">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-sm-5 d-flex flex-row">
                                <button type="button" name="print" id="print" value="print"
                                    class="btn btn-success form-text-13 me-2" disabled="disabled"><i
                                        class="fas fa-print me-2"></i>Print</button>
                                <button type="button" name="pdf" id="export-pdf" value="PDF"
                                    class="btn btn-dangerous form-text-13 color-red me-2" disabled="disabled"><i
                                        class="far fa-file-pdf me-2"></i>PDF</button>
                                <button type="button" name="word" id="export-word" value="PDF"
                                    class="btn btn-primary form-text-13" disabled="disabled"><i
                                        class="far fa-file-word me-2"></i>Word</button>

                            </div>
                        </div>
                    </div>

                    {{-- <span style="display:none" id="list-barang-terpilih"></span> --}}
                </form>
                <div id="barcode-print-container"
                    style="border: 1px solid #CCCCCC;text-align: center;padding:12px;width: 793.7007874px;min-width:377.953px;margin-top:10px;">
                    PREVIEW
                </div>
            </div>
        </div>
    </div>
@endsection
