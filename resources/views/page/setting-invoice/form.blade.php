@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Setting App</a>
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
    <link href="{{ asset('assets/js/spectrum/spectrum.css') }}" rel="stylesheet">
@endpush

@push('script')
    @include('layouts.partials.js')
    <script src="{{ asset('assets/js/spectrum/spectrum.min.js') }}"></script>
    <script>
        $(document).ready(function() {
        });
    </script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="form" action="{{ route('invoice.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>Setting Dokumen Transaksi</h6>
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
                    <a class="btn btn-link color-softgray-5" title="Back" href="/dashboard">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <input type="hidden" id="id" value="">
                    <div class="mb-3 row">
                        <label for="nama_role"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Logo</label>
                        <div class="col-sm-5">
                            @if (!empty($setting_invoice['logo']))
                                <div class="edit-logo-login-container"><img
                                        src="{{ asset('assets/img/' . $setting_invoice['logo']) }}" /></div>
                            @endif
                            <input type="file" class="file form-control" name="logo">
                            <small class="form-text-12 text-muted"><strong>Gunakan file PNG transparan</strong>. Maksimal
                                300Kb, tipe file: .JPG, .JPEG, .PNG</small>
                            <div class="upload-img-thumb"><span class="img-prop"></span></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Footer</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" name="footer_text">{!! Helper::set_value('footer_text',@$setting_invoice['footer_text']) !!}</textarea>
                        </div>
                    </div>
                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Invoice</h5>
                    </div>
                    <div class="horizontal-line my-3"></div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nomor Invoice</label>
                        <div class="col-sm-5">
                            <input class="form-control" name="no_invoice" value="{{ Helper::set_value('no_invoice',@$setting_invoice['no_invoice']) }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Jumlah Digit</label>
                        <div class="col-sm-5">
                            <select name="jml_digit_invoice" class="form-select">
                                {{-- <option value="jml_digit_invoice" {{ @$setting_invoice['jml_digit'] == 'jml_digit_invoice' ? 'selected' : '' }}>jml_digit_invoice</option> --}}
                                <option value="4" {{ @$setting_invoice['jml_digit'] == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ @$setting_invoice['jml_digit'] == '5' ? 'selected' : '' }}>5</option>
                                <option value="6" {{ @$setting_invoice['jml_digit'] == '6' ? 'selected' : '' }}>6</option>
                            </select>
                        </div>
                    </div>
                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Nota Retur</h5>
                    </div>
                    <div class="horizontal-line my-3"></div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nomor Nota Retur</label>
                        <div class="col-sm-5">
                            <input class="form-control" name="no_nota_retur" value="{!! Helper::set_value('no_nota_retur',@$setting_nota_retur['no_nota_retur']) !!}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Jumlah Digit</label>
                        <div class="col-sm-5">
                            <select name="jml_digit_nota_retur" class="form-select">
                                {{-- <option value="jml_digit_invoice" {{ @$setting_nota_retur['jml_digit'] == 'jml_digit_invoice' ? 'selected' : '' }}>jml_digit_invoice</option> --}}
                                <option value="4" {{ @$setting_nota_retur['jml_digit'] == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ @$setting_nota_retur['jml_digit'] == '5' ? 'selected' : '' }}>5</option>
                                <option value="6" {{ @$setting_nota_retur['jml_digit'] == '6' ? 'selected' : '' }}>6</option>
                            </select>
                        </div>
                    </div>

                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Nota Transfer Barang</h5>
                    </div>
                    <div class="horizontal-line my-3"></div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nomor Nota Transfer</label>
                        <div class="col-sm-5">
                            <input class="form-control" name="no_nota_transfer" value="{!! Helper::set_value('no_nota_transfer',@$setting_nota_transfer['no_nota_transfer']) !!}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Jumlah Digit</label>
                        <div class="col-sm-5">
                            <select name="jml_digit_nota_transfer" class="form-select">
                                {{-- <option value="jml_digit_invoice" {{ @$setting_nota_transfer['jml_digit'] == 'jml_digit_invoice' ? 'selected' : '' }}>jml_digit_invoice</option> --}}
                                <option value="4" {{ @$setting_nota_transfer['jml_digit'] == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ @$setting_nota_transfer['jml_digit'] == '5' ? 'selected' : '' }}>5</option>
                                <option value="6" {{ @$setting_nota_transfer['jml_digit'] == '6' ? 'selected' : '' }}>6</option>
                            </select>
                        </div>
                    </div>


                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-md-4 col-12 button-group">
                            <button type="submit" class="btn btn-primary color-blue" id="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
