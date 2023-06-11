@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">User</a>
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
    <script>
        // var id = $('#id').val();

        // // function getData() {
        // //     $.get(`{{ url('/user') }}/${id}`, function(response) {

        // //     });
        // // }

        // // if (id) {
        // //     getData();
        // // }

        // // function submitForm(url) {
        // //     if (id) {
        // //         $("input[name='_method']").removeAttr('value', 'post').attr('value', 'patch');
        // //     }

        // //     var form = $('#form');
        // //     var formdata = false;
        // //     if (window.FormData) {
        // //         formdata = new FormData(form[0]);
        // //     }

        // //     $.ajax({
        // //         data: formdata ? formdata : form.serialize(),
        // //         url: url,
        // //         type: "POST",
        // //         dataType: 'json',
        // //         cache: false,
        // //         contentType: false,
        // //         processData: false,
        // //         success: function(data) {
        // //             if (data.meta.code == 200) {
        // //                 // console.log(data.meta.message.substr(0, 12));
        // //                 swal.fire({
        // //                     text: data.meta.message,
        // //                     type: 'success'
        // //                 }).then(function() {
        // //                     history.back();
        // //                     // datatable.ajax.reload();
        // //                 });
        // //             } else if (data.meta.code == 500) {
        // //                 swal.fire({
        // //                     text: data.meta.message,
        // //                     type: 'error'
        // //                 });
        // //             }
        // //         },
        // //         error: function(xhr, status, error) {
        // //             if (xhr.status === 422) {
        // //                 // Validation failed, handle error response
        // //                 var errors = xhr.responseJSON.errors;
        // //                 if (errors.hasOwnProperty('username')) {
        // //                     $('#username').prop('readonly', true);
        // //                 }

        // //                 if (errors.hasOwnProperty('email')) {
        // //                     $('#email').prop('readonly', true);
        // //                 }
        // //                 loopErrors(errors);
        // //                 return;
        // //             }
        // //         }
        // //         // error: function(data) {
        // //         //     console.log('Error:', data);
        // //         //     if (data.status == 422) {
        // //         //         loopErrors(data.responseJSON.errors);
        // //         //         return;
        // //         //     }
        // //         // }
        // //     });
        // // }

        // function resetForm() {
        //     $('#form')[0].reset();

        //     $('.form-control').removeClass('is-invalid');
        //     $('.invalid-feedback').remove();
        // }


        // function loopErrors(errors) {
        //     $('.invalid-feedback').remove();

        //     if (errors == undefined) {
        //         return;
        //     }

        //     for (error in errors) {
        //         $(`[name=${error}]`).addClass('is-invalid');


        //         if ($(`[name=${error}]`).hasClass('form-select')) {
        //             if ($('span').hasClass('select2')) {
        //                 $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
        //                     .insertAfter($(`[name=${error}]`).next());
        //             }
        //         } else {
        //             if ($(`[name=${error}]`).length == 0) {
        //                 $(`[name="${error}[]"]`).addClass('is-invalid');
        //                 $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
        //                     .insertAfter($(`[name="${error}[]"]`).next());
        //             } else {
        //                 $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
        //                     .insertAfter($(`[name=${error}]`));
        //             }

        //         }
        //     }
        // }
    </script>
    <script src="{{ asset('assets/js/page/user.js') }}"></script>
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form">
                    @if ($user->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <div class="col-12 item-title">
                            <h6>
                                {{ $user->id ? 'Edit' : 'Tambah' }}
                                User</h6>
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
                    <a class="btn btn-link color-softgray-5" title="Back" href="{{ route('user.index') }}">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <input type="hidden" id="id" value="{{ $user->id }}">
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Foto</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            @if (!empty($user->avatar))
                                <div class="img-choose" style="margin:inherit;margin-bottom:10px">
                                    <div class="img-choose-container">
                                        <img src="{{ asset('assets/img/user/' . $user->avatar) }}" />
                                        <a href="javascript:void(0)" class="remove-img"><i class="fas fa-times"></i></a>
                                    </div>
                                </div>
                            @endif
                            <input type="hidden" class="avatar-delete-img" name="avatar_delete_img" value="0">
                            <input type="file" class="file form-control" name="avatar">
                            <small class="form-text-12 text-muted">Maksimal
                                300Kb,Minimal 100px x 100px, tipe file: .JPG, .JPEG, .PNG</small>
                            <div class="upload-img-thumb mb-2"><span class="img-prop"></span></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Username</label>
                        <div class="col-sm-8 col-md-6 col-lg-4 is-loading">
                            <input class="form-control " type="text" id="username" name="username"
                                value="{{ $user->username }}" placeholder="" />
                            <div class="spinner-border spinner-border-sm text-success spin-u d-none"></div>
                            <span id="cek-usr"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama</label>
                        <div class="col-sm-8 col-md-6 col-lg-4 ">
                            <input class="form-control" type="text" name="name" value="{{ $user->name }}"
                                placeholder="" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Email</label>
                        <div class="col-sm-8 col-md-6 col-lg-4 is-loading">
                            <input class="form-control" type="text" id="email" name="email"
                                value="{{ $user->email }}" placeholder="" />
                            <div class="spinner-border spinner-border-sm text-success spin-e d-none"></div>
                            <span id="cek-email"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
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
                                    <option value="{{ $key }}" {{ $key == $user->verified ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endforeach
                                {{-- <option value="1">Tidak</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Status</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            @php
                                $options = collect([
                                    1 => 'Aktif',
                                    2 => 'Suspended',
                                    3 => 'Deleted',
                                ]);
                            @endphp
                            <select class="form-select" type="text" name="status">
                                @foreach ($options as $key => $val)
                                    <option value="{{ $key }}" {{ $key == $user->status ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Role</label>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <select class="form-select" type="text" name="role_id">
                                @foreach ($role as $key => $val)
                                    <option value="{{ $val->id }}" {{ $key == $user->role_id ? 'selected' : '' }}>
                                        {{ $val->nama_role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (empty($user->id))
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Password
                                Baru</label>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <input class="form-control" type="password" name="password" required="required" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Ulangi
                                Password Baru</label>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <input class="form-control" type="password" name="ulangi_password" required="required" />
                            </div>
                        </div>
                    @endif
                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-md-4 col-12 button-group">
                            <button type="button"
                                onclick="submitForm(`{{ $user->id ? route('user.update', $user->id) : route('user.store') }}`)"
                                class="btn btn-primary color-blue" id="submit">Submit</button>
                            <button type="button" class="btn btn-link mx-2" onclick="resetForm(this.form)">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
