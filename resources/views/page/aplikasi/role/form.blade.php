@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Roles</a>
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
        var id = $('#id').val();

        function getData() {
            $.get(`{{ url('aplikasi/role/') }}/${id}`, function(response) {
                $('#nama_role').val(response.data.nama_role);
                $('#judul_role').val(response.data.judul_role);
                $('#keterangan').val(response.data.keterangan);
                $('#menu_id').val(response.data.menu_id).change();

                $.ajax({
                    url: "/aplikasi/role/get-menu",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        role_id: response.data.id,
                        menu_kategori_id: response.data.menu_kategori_id,
                        parent_id: response.data.parent_id,
                        // _token: `{{ csrf_token() }}`,
                    },
                    success: function(data) {
                        $('#permission_list').html(data);
                    }
                });
            });
        }

        if (id) {
            getData();
        }

        $('.table').DataTable({
            // scrollY: '225px',
            // scrollCollapse: false,
            paging: false,
            bPaginate: false,
            bInfo: false,
            bFilter: false,
            pageLength: 20,
            ordering: false
        });

        // $('#role_name').keyup(function(e){
        //     var str = $('#role_name').val();
        //     str = str.replace(/\W+(?!$)/g, '-').toLowerCase();
        //     $('#role_slug').val(str);
        //     $('#role_slug').attr('placeholder',str);
        // });

        function submitForm(url) {
            if (id) {
                $("input[name='_method']").removeAttr('value', 'post').attr('value', 'patch');
            }

            var form = $('#form');
            var formdata = false;
            if (window.FormData) {
                formdata = new FormData(form[0]);
            }

            $.ajax({
                data: formdata ? formdata : form.serialize(),
                url: url,
                type: "POST",
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.meta.code == 200) {
                        console.log(data.meta.message.substr(0, 12));
                        swal.fire({
                            text: data.meta.message,
                            type: 'success'
                        }).then(function() {
                            history.back();
                            datatable.ajax.reload();
                        });
                    } else if (data.meta.code == 500) {
                        swal.fire({
                            text: data.meta.message,
                            type: 'error'
                        });
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    if (data.status == 422) {
                        loopErrors(data.responseJSON.errors);
                        return;
                    }
                }
            });
        }

        function resetForm() {
            $('#form')[0].reset();

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }


        function loopErrors(errors) {
            $('.invalid-feedback').remove();

            if (errors == undefined) {
                return;
            }

            for (error in errors) {
                $(`[name=${error}]`).addClass('is-invalid');

                if ($(`[name=${error}]`).hasClass('form-select')) {
                    if ($('span').hasClass('select2')) {
                        $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                            .insertAfter($(`[name=${error}]`).next());
                    }
                } else {
                    if ($(`[name=${error}]`).length == 0) {
                        $(`[name="${error}[]"]`).addClass('is-invalid');
                        $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                            .insertAfter($(`[name="${error}[]"]`).next());
                    } else {
                        $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                            .insertAfter($(`[name=${error}]`));
                    }

                }
            }
        }
    </script>
    {{-- <script src="{{ asset('assets/js/script.js') }}"></script> --}}
@endpush


@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form id="form">
                    @if ($role->id)
                        @method('post')
                    @endif
                    @csrf
                    <div class="card-title">
                        <h6>
                            {{ $role->id ? 'Edit' : 'Tambah' }}
                            Role</h6>
                    </div>
                    <div class="horizontal-line my-3"></div>
                    <a class="btn btn-link color-softgray-5" title="Add" href="{{ route('aplikasi.role.index') }}">
                        {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                        <i class="fas fa-arrow-left me-2"></i>
                        back
                    </a>
                    <div class="horizontal-line my-3"></div>
                    <input type="hidden" id="id" value="{{ $role->id }}">
                    <div class="mb-3 row">
                        <label for="nama_role"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Nama Role</label>
                        <div class="col-sm-4">
                            <input type="text" name="nama_role" id="nama_role" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="judul_role"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Judul Role</label>
                        <div class="col-sm-4">
                            <input type="text" name="judul_role" id="judul_role" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="keterangan"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Keterangan</label>
                        <div class="col-sm-4">
                            <input type="text" name="keterangan" id="keterangan" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="halaman_default"
                            class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Halaman
                            Default</label>
                        <div class="col-sm-4">
                            <select name="menu_id" id="menu_id" class="form-select">
                                @foreach ($menu as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_menu }} |
                                        ({{ $item->menu_status->nama_status }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="form-text-12 fw-light text-muted"><em>Halaman awal sesaat setelah user login.
                                    Pastikan role memiliki permission pada halaman yang dipilih</em></span>
                        </div>
                    </div>
                    @if ($role->id)
                        <div class="mb-3 row">
                            <label for="list-permission"
                                class="col-sm-2 col-form-label form-text-12 text-black text-right fw-bold">Permission</label>
                            <div class="col-sm-7">
                                <table class="table table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Nama Menu</th>
                                            <th>Access</th>
                                            <th>Create</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                            <th>View</th>
                                        </tr>
                                    </thead>
                                    <tbody id="permission_list"></tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    <div class="horizontal-line color-shadow"></div>
                    <div class="card-footer mb-10">
                        <div class="col-md-4 col-12 button-group">
                            <button type="button" class="btn btn-primary color-blue" id="submit"
                                onclick="submitForm(`{{ $role->id ? route('aplikasi.role.update', $role->id) : route('aplikasi.role.store') }}`)">Submit</button>
                            <button type="button" class="btn btn-link" onclick="resetForm(this.form)">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
