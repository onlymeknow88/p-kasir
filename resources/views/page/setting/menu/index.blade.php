@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Menu</a>
            </div>
        </div>
        <div class="sub-items-right">
            {{-- <div class="item-button">
            <a href="{{ route('setting.menu.create') }}" class="btn btn-primary color-blue d-flex align-items-center justify-content-between">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.46863 1.37573H6.53113C6.44779 1.37573 6.40613 1.4174 6.40613 1.50073V6.40698H1.75024C1.66691 6.40698 1.62524 6.44865 1.62524 6.53198V7.46948C1.62524 7.55282 1.66691 7.59448 1.75024 7.59448H6.40613V12.5007C6.40613 12.5841 6.44779 12.6257 6.53113 12.6257H7.46863C7.55196 12.6257 7.59363 12.5841 7.59363 12.5007V7.59448H12.2502C12.3336 7.59448 12.3752 7.55282 12.3752 7.46948V6.53198C12.3752 6.44865 12.3336 6.40698 12.2502 6.40698H7.59363V1.50073C7.59363 1.4174 7.55196 1.37573 7.46863 1.37573Z" fill="white"/>
                </svg>
                <span class="ml-10">Add New</span>
            </a>
        </div> --}}
        </div>
    </div>
@endsection

@push('css')
    @include('layouts.partials.css')
@endpush

@push('script')
    @include('layouts.partials.js')

<script src="{{ asset('assets/js/page/admin-menu.js') }}"></script>
    <script >
        // let modalKategori = '#modalKategori';
        // let modalMenu = '#modalMenu';

        // $(function() {

        //     $('.kategori-container').delegate('.kategori-item', 'click', function() {
        //         var $this = $(this);
        //         // if ($this.hasClass('processing'));
        //         // return false;

        //         var id_kategori = $this.attr('data-kategori-id');

        //         var $list_menu = $('#list_menu');
        //         var $group_container = $('.menu-kategori-container');

        //         $group_container.find('li').removeClass('list-group-item-primary');
        //         $this.addClass('list-group-item-primary');

        //         $list_menu.empty();
        //         $loader = $(
        //                 '<div class="text-center"><div class="spinner-border text-secondary"></div></div>')
        //             .appendTo(
        //                 $list_menu);

        //         $.get('/aplikasi/menu/kategori/' + id_kategori, function(data) {
        //             $loader.remove();
        //             if (data) {
        //                 $('#list_menu').html(data);
        //             } else {
        //                 $('#list_menu').html(
        //                     '<div class="alert alert-danger">Data tidak ditemukan</div>');
        //             }

        //             $('#list_menu').wdiMenuEditor('customInit');
        //         })
        //     });

        //     $('.kategori-container').delegate('.btn-edit', 'click', function(e) {
        //         e.stopPropagation();
        //         if ($(this).hasClass('disabled'))
        //             return false;
        //         editFormKategori('edit', $(this).parents('li').attr('data-kategori-id'))
        //         return false;
        //     });

        //     $('.kategori-container').delegate('.btn-remove', 'click', function(e) {
        //         e.stopPropagation();
        //         $this = $(this);

        //         $li = $this.parents('li');
        //         $li.addClass('list-group-item-secondary');
        //         $li.find('a').prop('disabled', true);
        //         $li.find('a').addClass('disabled');
        //         $li.prepend('<div class="custom-loader"></div>');

        //         var kategori_id = $li.attr('data-kategori-id');
        //         // console.log(kategori_id);
        //         refresh = false;
        //         if ($li.hasClass('list-group-item-primary')) {
        //             refresh = true;
        //         }


        //         $.post('/aplikasi/menuKategori/' + kategori_id, {
        //                 _method: "delete",
        //                 // '_token': `{{ csrf_token() }}`
        //             })
        //             .done((response) => {

        //                 // if (response.meta.message == "File Deleted") {
        //                     $li.fadeOut('fast', function() {
        //                         $li.remove();

        //                         $li.remove();
        //                         if (refresh) {
        //                             $('#list-kategori').find('li').eq(0).click();
        //                         }
        //                     });
        //                 // }

        //             })
        //             .fail((errors) => {
        //                 Swal.fire(
        //                     "Something went wrong.",
        //                     "You clicked the button!",
        //                     "error"
        //                 );
        //                 return;
        //             });
        //     });


        //     $('#form').on('keyup keypress', function(e) {
        //         var keyCode = e.keyCode || e.which;
        //         if (keyCode === 13) {
        //             e.preventDefault();
        //             return false;
        //         }
        //     });

        //     $('#list_menu').wdiMenuEditor({
        //         expandBtnHTML: '<button data-action="expand" class="fa fa-plus" type="button">Expand</button>',
        //         collapseBtnHTML: '<button data-action="collapse" class="fa fa-minus" type="button">Collapse</button>',
        //         editBtnCallback: function($list) {
        //             // console.log($list.data('id'))
        //             editFormMenu('Edit', $list.data('id'))
        //         },
        //         beforeRemove: function(item, plugin) {
        //             list_data = $('#list_menu').wdiMenuEditor('serialize');
        //             menu_tree = JSON.stringify(list_data);

        //             Swal.fire({
        //                 title: "Delete?",
        //                 text: "Apakah anda yakin?",
        //                 type: "warning",
        //                 showCancelButton: !0,
        //                 confirmButtonText: "Yes",
        //                 cancelButtonText: "No",
        //                 reverseButtons: !0,
        //             }).then(
        //                 function(e) {
        //                     if (e.value === true) {
        //                         $.post('/aplikasi/menu/' + item.attr('data-id'), {
        //                                 _method: "delete",
        //                                 // '_token': `{{ csrf_token() }}`
        //                             })
        //                             .done((response) => {
        //                                 // console.log(response);
        //                                 if (response.meta.message == "File Deleted") {
        //                                     plugin.deleteList(item);
        //                                     if ($('#list_menu').find('li').length == 0) {
        //                                         $('#list-kategori').find(
        //                                             '.list-group-item-primary').click();
        //                                     }
        //                                     // window.location.href = location.pathname;
        //                                 }
        //                                 // table.ajax.reload();
        //                             })
        //                             .fail((errors) => {
        //                                 Swal.fire(
        //                                     "Something went wrong.",
        //                                     "You clicked the button!",
        //                                     "error"
        //                                 );
        //                                 return;
        //                             });
        //                     } else {
        //                         e.dismiss;
        //                     }
        //                 },
        //                 function(dismiss) {
        //                     return false;
        //                 }
        //             );
        //         },
        //     });

        //     $('#parent_id').select2({
        //         theme: "bootstrap-5",
        //         placeholder: $(this).data('placeholder'),
        //         dropdownParent: $("#modalMenu"),
        //         // minimumInputLength: 2,
        //         allowClear: true,
        //     });




        //     $('#use_icon').on('change', function() {
        //         var use_icon = $(this).val();
        //         if (use_icon == 'Y') {
        //             $('#icon_class').removeClass('d-none');

        //             $('#icon_class').select2({
        //                 theme: "bootstrap-5",
        //                 placeholder: $(this).data('placeholder'),
        //                 containerCssClass: "use_icon",
        //                 dropdownParent: $("#modalMenu"),
        //                 ajax: {
        //                     url: "{{ asset('assets/css/fontawesome/icons.json') }}",
        //                     dataType: 'json',
        //                     delay: 250,
        //                     processResults: function(data) {
        //                         return {
        //                             results: $.map(data, function(index, item) {
        //                                 if (index.styles == 'solid') {
        //                                     var icon_label = 'fa';
        //                                 } else if (index.styles == 'brands') {
        //                                     var icon_label = 'fab';
        //                                 } else if (index.styles[1] == 'regular') {
        //                                     var icon_label = 'far';
        //                                 }

        //                                 return {
        //                                     id: item,
        //                                     text: '<i class="' + icon_label + ' fa-' +
        //                                         item +
        //                                         ' me-2"></i> ' + item

        //                                 }
        //                             })
        //                         };
        //                     },
        //                     cache: true
        //                 },
        //                 escapeMarkup: function(markup) {
        //                     return markup;
        //                 }
        //             });


        //         } else if (use_icon == 'N') {
        //             $('.use_icon').addClass('d-none');
        //         }
        //     });



        // });


        // function addFormKategori(url, title = 'Tambah Kategori') {
        //     $(modalKategori).modal('show');
        //     $(`${modalKategori} .modal-title`).text(title);
        //     $(`${modalKategori} form`).attr('action', url);
        //     $(`${modalKategori} [name=_method]`).val('post');

        //     // resetForm(`${modalKategori} form`);
        // }

        // function addFormMenu(url, title = 'Tambah Menu') {
        //     $(modalMenu).modal('show');
        //     $(`${modalMenu} .modal-title`).text(title);
        //     $(`${modalMenu} form`).attr('action', url);
        //     $(`${modalMenu} [name=_method]`).val('post');

        //     // resetForm(`${modalMenu} form`);
        // }

        // function editFormKategori(type, id = '') {
        //     console.log(id);

        //     var title = type == 'edit' ? 'Edit Menu' : 'Tambah Menu';

        //     $.get('/aplikasi/menu/' + id)
        //         .done(response => {
        //             $(modalKategori).modal('show');
        //             $(`${modalKategori} .modal-title`).text(title);
        //             $(`${modalKategori} form`).attr('action', url);
        //             $(`${modalKategori} [name=_method]`).val('put');

        //             // resetForm(`${modal} form`);
        //             loopForm(response.data);
        //         })
        //         .fail(errors => {
        //             alert('Tidak dapat menampilkan data');
        //             return;
        //         });
        // }

        // function editFormMenu(type = '', id = '') {
        //     var url = '{{ route('aplikasi.menu.update', ':id') }}';
        //     url = url.replace(':id', id);
        //     $.get('/aplikasi/menu/' + id + '/s-menu')
        //         .done(response => {
        //             $(modalMenu).modal('show');
        //             $(`${modalMenu} .modal-title`).text(type);
        //             $(`${modalMenu} form`).attr('action', url);
        //             $(`${modalMenu} [name=_method]`).val('put');

        //             var options = new Option(response.data.parent == null ? 'Tidak ada parent menu' : response.data[
        //                     'parent'].nama_menu,
        //                 response.data.parent == null ? 'Tidak ada parent menu' : response.data['parent'].id, true,
        //                 true);
        //             $('#parent_id').append(options).change();

        //             var option = new Option(response.data.class, response.data.class, true, true);
        //             $('#icon_class').append(option).change();

        //             var aktif = $("input[name=aktif");
        //             if (response.data.aktif == 'Y') {
        //                 aktif.attr("checked", 'checked');
        //             } else {
        //                 aktif.removeAttr('checked', 'checked');
        //             }

        //             resetForm(`${modalMenu} form`);
        //             loopForm(response.data);
        //         })
        //         .fail(errors => {
        //             alert('Tidak dapat menampilkan data');
        //             return;
        //         });
        // }

        // function submitForm(originalForm) {
        //     $.post({
        //             url: $(originalForm).attr('action'),
        //             data: new FormData(originalForm),
        //             // type: 'POST',
        //             dataType: 'json',
        //             contentType: false,
        //             cache: false,
        //             processData: false
        //         })
        //         .done(response => {
        //             // console.log(response);
        //             if (response.meta.code == 200) {
        //                 swal.fire({
        //                     text: response.meta.message,
        //                     type: "success",
        //                 }).then(function() {
        //                     $(modalKategori).modal('hide');
        //                     table.ajax.reload();
        //                 });
        //             } else if (response.meta.code == 500) {
        //                 swal.fire({
        //                     text: response.meta.message,
        //                     type: "error",
        //                 });
        //             }
        //         })
        //         .fail(errors => {
        //             if (errors.status == 422) {
        //                 // console.log("Error:", errors.responseJSON.errors);
        //                 loopErrors(errors.responseJSON.errors);
        //                 return;
        //             }
        //         });
        // }

        // function resetForm(selector) {
        //     $(selector)[0].reset();

        //     $('#aktif').removeAttr('checked', 'checked');

        //     $('#parent_id').val('').trigger('change');
        //     $('.form-control, .custom-select, [type=radio], [type=checkbox], [type=file], .select2, .note-edito')
        //         .removeClass('is-invalid');
        //     $('.invalid-feedback').remove();
        // }

        // function loopForm(originalForm) {
        //     for (field in originalForm) {
        //         if ($(`[name=${field}]`).attr('type') != 'file') {
        //             if ($(`[name=${field}]`).hasClass('summernote')) {
        //                 $(`[name=${field}]`).summernote('code', originalForm[field]);
        //             } else if ($(`[name=${field}]`).attr('type') == 'radio') {
        //                 $(`[name=${field}]`).filter(`[value="${originalForm[field]}"]`).prop('checked', true);
        //             } else {
        //                 $(`[name=${field}]`).val(originalForm[field]);
        //             }

        //             $('select').trigger('change');
        //         } else {
        //             $(`.preview-${field}`)
        //                 .attr('src', originalForm[field])
        //                 .show();
        //         }
        //     }
        // }

        // function loopErrors(errors) {
        //     $(".invalid-feedback").remove();

        //     if (errors == undefined) {
        //         return;
        //     }

        //     for (error in errors) {
        //         $(`[name=${error}]`).addClass("is-invalid");

        //         if ($(`[name=${error}]`).hasClass("form-select")) {
        //             if ($("span").hasClass("select2")) {
        //                 $(
        //                     `<span class="error invalid-feedback">${errors[error][0]}</span>`
        //                 ).insertAfter($(`[name=${error}]`).next());
        //             }
        //         } else {
        //             if ($(`[name=${error}]`).length == 0) {
        //                 $(`[name="${error}[]"]`).addClass("is-invalid");
        //                 $(
        //                     `<span class="error invalid-feedback">${errors[error][0]}</span>`
        //                 ).insertAfter($(`[name="${error}[]"]`).next());
        //             } else {
        //                 $(
        //                     `<span class="error invalid-feedback">${errors[error][0]}</span>`
        //                 ).insertAfter($(`[name=${error}]`));
        //             }
        //         }
        //     }
        // }
    </script>
@endpush

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="col-12 item-title">
                        <h6>Data Menu</h6>
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

                <div class="col-auto d-flex flex-row">
                    <div class="col-md-5 col-sm-4">
                        <a href="{{ route('aplikasi.menuKategori.create') }}" class="btn btn-primary" title="Add" id="add-kategori">
                        {{-- <a class="btn btn-primary d-flex align-items-center" title="Add" href="#"
                            onclick="addFormKategori('{{ route('aplikasi.menuKategori.store') }}')"> --}}
                            {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                            <i class="fa fa-plus me-2"></i>
                            Tambah Kategori
                        </a>
                        <div class="horizontal-line my-4"></div>
                        <div class="kategori-container">
                            <div class="list-kategori">
                                <ul class="list-group menu-kategori-container" id="list-kategori-container">
                                    @foreach ($menuKategori as $index => $item)
                                        <li class="list-group-item kategori-item {{ $index == 0 ? 'list-group-item-primary' : '' }}"
                                            data-kategori-id="{{ $item->id }}">
                                            {{ $item->nama_kategori }}
                                            <div class="toolbox">
                                                <a href="javascript:void(0)" class="btn-edit"><i
                                                        class="fas fa-pen mx-2 text-green "></i></a>
                                                <a href="javascript:void(0)" class="btn-remove"><i
                                                        class="fas fa-times mx-2 text-red"></i></a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-7 ps-4">
                        <button class="btn btn-primary btn-green d-flex align-items-center" title="Add" href="#"
                            onclick="addFormMenu('{{ route('aplikasi.menu.store') }}')">
                            {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                            <i class="fa fa-plus me-2"></i>
                            Tambah Menu
                        </button>
                        <div class="horizontal-line my-4"></div>
                        <div class="dd" id="list_menu">
                            {!! $menu !!}
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>
    {{-- @includeIf('page.setting.menu.form_kategori') --}}
    @includeIf('page.setting.menu.form_menu')
@endsection
