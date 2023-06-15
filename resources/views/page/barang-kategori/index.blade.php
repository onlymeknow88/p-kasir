@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Kategori</a>
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
@endpush

@push('script')
    @include('layouts.partials.js')
    <script src="{{ asset('assets/js/js-yaml/js-yaml.min.js') }}"></script>

    <script src="{{ asset('assets/js/page/barang-kategori.js') }}"></script>
@endpush

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="col-12 item-title">
                        <h6>Data Kategori</h6>
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
                <a class="btn btn-primary" id="add-kategori" title="Add" href="{{ route('kategori.create') }}">
                    {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                    <i class="fa fa-plus me-2"></i>
                    Tambah Kategori
                </a>

                <div class="horizontal-line my-3"></div>
                <div class="dd" id="list_menu">
                    {!! $list_kategori !!}
                </div>
            </div>
        </div>
    </div>
@endsection
