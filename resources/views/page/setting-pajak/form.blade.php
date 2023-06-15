@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Setting Pajak</a>
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
                <form id="form" action="{{ route('pajak.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>Setting Pajak</h6>
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
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <input type="hidden" id="id" value="">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Display Text</label>
                        <div class="col-sm-5">
                            <input class="form-control" name="display_text" value="{!! Helper::set_value('display_text',@$setting_pajak['display_text']) !!}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Tarif</label>
                        <div class="col-sm-5">
                            <input class="form-control" name="tarif" value="{!! Helper::set_value('tarif',@$setting_pajak['tarif']) !!}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Status</label>
                        <div class="col-sm-5">
                            <select name="status" class="form-select">
                                <option value="aktif" {{ @$setting_pajak['status'] == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="non_aktif" {{ @$setting_pajak['status'] == 'non_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
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
