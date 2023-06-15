@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Customer</a>
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
@endpush

@push('script')
    @include('layouts.partials.js')
    <script src="{{ asset('assets/js/page/customer.js') }}"></script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form">
                    @if ($customer->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>
                                {{ $customer->id ? 'Edit' : 'Tambah' }}
                                Customer</h6>
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
                    <a class="btn btn-link color-softgray-5" title="Back" href="{{ route('customer.index') }}">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <input type="hidden" id="id" value="{{ $customer->id }}">

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama
                            Customer</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <input class="form-control " type="text" name="nama_customer"
                                value="{{ $customer->nama_customer }}" placeholder="" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Alamat</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <textarea class="form-control " type="text" name="alamat_customer" placeholder="">{{ $customer->alamat_customer }}</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">No Telp</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <input class="form-control " type="text" id="no_telp" name="no_telp"
                                value="{{ $customer->no_telp }}" placeholder="">
                        </div>
                    </div>
                    {{-- <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Verified</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            @php
                                $options = collect([
                                    1 => 'Ya',
                                    2 => 'Tidak',
                                ]);
                            @endphp
                            <select class="form-select" type="text" name="verified">
                                @foreach ($options as $key => $val)
                                    <option value="{{ $key }}" {{ $key == $customer->verified ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-md-4 col-12 button-group">
                            <button type="button"
                                onclick="submitForm(`{{ $customer->id ? route('customer.update', $customer->id) : route('customer.store') }}`)"
                                class="btn btn-primary color-blue" id="submit">Submit</button>
                            <button type="button" class="btn btn-link mx-2" onclick="resetForm(this.form)">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
