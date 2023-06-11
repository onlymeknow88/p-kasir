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
            $('.colorpicker').spectrum({
                change: function(color) {
                    // alert(color);
                    hex_color = color.toHexString(); // #ff0000
                    // console.log(hex_color);
                    $(this).val(color);
                }
            });

            $(".colorpicker").on('move.spectrum', function(e, tinycolor) {
                $image_preview.css('background-color', tinycolor.toRgbString());
                $logo_container.css('background-color', tinycolor.toRgbString());
                // $(e.target).val(tinycolor.toHexString());
                // console.log(tinycolor.toHexString());
            });

            $(".colorpicker").on('hide.spectrum', function(e, tinycolor) {
                $image_preview.css('background-color', tinycolor.toRgbString());
                $logo_container.css('background-color', tinycolor.toRgbString());
            })
        });
    </script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="form" action="{{ route('aplikasi.setting.setting-app.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <div class="card-title">
                        <h6>
                            Edit
                            Setting App</h6>
                    </div>
                    <div class="horizontal-line my-3"></div>
                    <a class="btn btn-link color-softgray-5" title="Back" href="/dashboard">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Login</h5>
                    </div>
                    <div class="horizontal-line my-3"></div>

                    <input type="hidden" id="id" value="">
                    <div class="mb-3 row">
                        <label for="nama_role"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Logo Login</label>
                        <div class="col-sm-5">
                            @if (!empty($data['logo_login']))
                                <div class="edit-logo-login-container"><img
                                        src="{{ asset('assets/img/' . $data['logo_login']) }}" /></div>
                            @endif
                            <input type="file" class="file form-control" name="logo_login">
                            <small class="form-text-12 text-muted"><strong>Gunakan file PNG transparan</strong>. Maksimal
                                300Kb, tipe file: .JPG, .JPEG, .PNG</small>
                            <div class="upload-img-thumb"><span class="img-prop"></span></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Background
                            Logo</label>
                        <div class="col-sm-3 inline-form">
                            <input name="background_logo" class="form-control colorpicker"
                                value="{{ Helper::set_value('background_logo',@$data['background_logo']) }}" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Footer</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" name="footer_login">{!! Helper::set_value('footer_login',@$data['footer_login']) !!}</textarea>
                        </div>
                    </div>
                    <div class="color-softgray-5 pt-2 pb-1 ps-4">
                        <h5 class="fw-bold">Website</h5>
                    </div>
                    <hr />
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Judul Web</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" name="judul_web">{!! Helper::set_value('judul_web',@$data['judul_web']) !!}</textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nama_role"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Fav Icon</label>
                        <div class="col-sm-5">
                            @if (!empty($data['favicon']))
                                <div style="margin:inherit;margin-bottom:10px"><img
                                        src="{{ asset('assets/img/' . $data['favicon']) }}" /></div>
                            @endif
                            <input type="file" class="file form-control" name="favicon">
                            <small class="form-text-12 text-muted"><strong>Gunakan file PNG transparan</strong>. Maksimal
                                300Kb, tipe file: .JPG, .JPEG, .PNG</small>
                            <div class="upload-img-thumb"><span class="img-prop"></span></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="nama_role"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Logo Aplikasi</label>
                        <div class="col-sm-5">
                            @if (!empty($data['logo_app']))
                                <div style="margin:inherit;margin-bottom:10px"><img
                                        src="{{ asset('assets/img/' . $data['logo_app']) }}" /></div>
                            @endif
                            <input type="file" class="file form-control" name="logo_app">
                            <small class="form-text-12 text-muted"><strong>Gunakan file PNG transparan</strong>. Maksimal
                                300Kb, tipe file: .JPG, .JPEG, .PNG</small>
                            <div class="upload-img-thumb"><span class="img-prop"></span></div>
                        </div>
                    </div>
                    {{-- <div class="row mb-3 d-flex align-items-center">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Background
                            Logo</label>
                        <div class="col-sm-5 form-text-14 text-muted">
                            Ubah di menu setting tampilan
                        </div>
                    </div> --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Footer</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" name="footer_app">{!! Helper::set_value('footer_app',@$data['footer_app']) !!}</textarea>
                        </div>
                    </div>

                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary color-blue" id="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
