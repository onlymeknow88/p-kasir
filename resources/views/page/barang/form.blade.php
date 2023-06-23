@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Barang</a>
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
    <script src="{{ asset('assets/js/page/barang.js') }}"></script>
    <script src="{{ asset('assets/js/page/select2-kategori.js') }}"></script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form">
                    @if ($barang->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>
                                {{ $barang->id ? 'Edit' : 'Tambah' }}
                                Barang</h6>
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
                    <input type="hidden" id="id" value="{{ $barang->id }}">

                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Barang</h5>
                    </div>
                    <hr>
                    <div class="ps-3">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama
                                Barang</label>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <input class="form-control " type="text" name="nama_barang"
                                    value="{{ $barang->nama_barang }}" placeholder="" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Kode
                                Barang</label>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <input class="form-control" type="text" name="kode_barang"
                                    value="{{ $barang->kode_barang }}" placeholder="" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label
                                class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Barcode</label>
                            <div class="col-sm-4" style="position:relative">
                                <div class="input-group">
                                    <input class="form-control barcode" type="text" name="barcode"
                                        value="{{ $barang->barcode }}" />
                                    <button class="btn btn-secondary color-darkgray generate-barcode"
                                        type="button">Generate</button>
                                </div>
                                <div class="spinner-border spinner text-secondary spinner-border-sm"
                                    style="display:none; position:absolute; top:8px; right:110px"></div>
                                <small class="text-muted form-text-12"><span class="jml-digit">0</span> digit | 13 digit,
                                    Misal
                                    8993053131130. 899 adalah kode negara Indonesia</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label
                                class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Deskripsi</label>
                            <div class="col-sm-4">
                                <textarea class="form-control" name="deskripsi">{{ $barang->deskripsi }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label
                                class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Kategori</label>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <select class="form-select" id="list-kategori" type="text" name="kategori_id">
                                    @foreach ($list_kategori as $key => $node)
                                        <option value="{{ $key }}"
                                            {{ isset($node['disabled']) ? 'disabled' : '' }}
                                            data-parent="{{ $node['attr']['data-parent'] }}"
                                            data-icon="{{ $node['attr']['data-icon'] }}"
                                            data-new="{{ $node['attr']['data-new'] }}"
                                            {{ $barang->kategori_id == $key ? 'selected' : '' }}>{{ $node['text'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Satuan</label>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <select class="form-select" type="text" name="unit_id">
                                    @foreach ($satuan_unit as $val)
                                        <option value="{{ $val->id }}"
                                            {{ $barang->unit_id == $val->id ? 'selected' : '' }}>
                                            {{ $val->nama_satuan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Berat</label>
                            <div class="col-sm-4">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control number" name="berat"
                                        value="{{ Helper::format_ribuan($barang->berat) }}">
                                    <span class="input-group-text" id="basic-addon2">Gram</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Stok</h5>
                    </div>
                    <hr>
                    {{-- <div class="ps-3"> --}}

                        @foreach ($gudang as $key => $val)
                            @php
                                $total_stok = 0;
                                if (key_exists($val->id, $stok)) {
                                    $total_stok = $stok[$val->id]->total_stok;
                                }
                            @endphp
                            <div class="stok-container">
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama
                                        Gudang</label>
                                    <div class="col-sm-5 stok form-text-13 d-flex align-items-center">
                                        {{ $val->nama_gudang }}</div>
                                </div>
                                <div class="row mb-3">
                                    <label
                                        class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Adjusment</label>
                                    <div class="col-sm-5 stok-number">
                                        <div class="d-flex flex-start">
                                            {!! Helper::options(
                                                ['name' => 'operator[]', 'class' => 'operator me-2', 'style' => 'flex: 0 0 auto;width: 70px'],
                                                ['plus' => '+', 'minus' => '-'],
                                            ) !!}
                                            <div class="input-group" style="width:130px">
                                                <button type="button"
                                                    class="color-softgray-5 input-group-text decrement">-</button>
                                                <input type="text" size="2" value=""
                                                    class="form-control text-end stok">
                                                <button type="button"
                                                    class="color-softgray-5 input-group-text increment">+</button>
                                            </div>
                                        </div>
                                        <div class="text-muted form-text-12 adjusment fst-italic">
                                            Stok Awal: <span class="stok-awal"></span>,
                                            Adjusment: <span class="stok-adjusment">0</span>, Stok Akhir: <span
                                                class="stok-akhir"></span>
                                            <input type="hidden" name="gudang_id[]" value="{{ $val->id }}" />
                                            <input type="hidden" name="adjusment[]" value="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($key + 1 < count($stok))
                                <hr />
                            @endif
                        @endforeach

                        <div class="color-softgray-5 pt-2 pb-1 ps-4">
                            <h5 class="fw-bold">Harga Pokok</h5>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Harga
                                pokok</label>
                            <div class="col-sm-4">
                                <input name="harga_pokok" class="form-control number harga-pokok"
                                    value="{{ Helper::format_ribuan($harga_pokok) }}" />
                                <div class="text-muted form-text-12 adjusment fst-italic">
                                    Harga awal: <span
                                        class="harga-pokok-awal">{{ Helper::format_ribuan($harga_pokok) }}</span>,
                                    Adjusment: <span class="adjusment-harga-pokok">0</span>
                                </div>
                                <input type="hidden" name="adjusment_harga_pokok" value="0" />
                            </div>
                        </div>
                    {{-- </div> --}}
                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Harga Jual</h5>
                    </div>
                    <hr>
                    <div class="ps-3">
                        @foreach ($harga_jual as $index => $val)
                            <div class="stok-container">
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama
                                        Harga</label>
                                    <div class="col-sm-5 stok form-text-13 d-flex align-items-center">
                                        {{ $val->nama_jenis_harga }}</div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Harga
                                        Jual</label>
                                    <div class="col-sm-5 stok-number">
                                        <div class="input-group" style="width:170px">
                                            <button type="button"
                                                class="color-softgray-5 input-group-text decrement">-</button>
                                            <input type="text" size="2" name="harga_jual[]"
                                                value="{{ Helper::format_ribuan($val->harga) }}"
                                                class="form-control text-end number harga-jual">
                                            <button type="button"
                                                class="color-softgray-5 input-group-text increment">+</button>
                                        </div>
                                        <div class="text-muted form-text-12 adjusment fst-italic">
                                            Harga Awal: <span
                                                class="harga-jual-awal">{{ Helper::format_ribuan($val->harga) }}</span>,
                                            Adjusment: <span class="adjusment-harga-jual">0</span>
                                            <input type="hidden" name="jenis_harga_id[]"
                                                value="{{ $val->id }}" />
                                            <input type="hidden" name="harga_awal[]" value="{{ $val->harga }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-md-4 col-12 button-group">
                            <button type="button"
                                onclick="submitForm(`{{ $barang->id ? route('barang.update', $barang->id) : route('barang.store') }}`)"
                                class="btn btn-primary color-blue" id="submit">Submit</button>
                            <button type="button" class="btn btn-link mx-2"
                                onclick="resetForm(this.form)">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
