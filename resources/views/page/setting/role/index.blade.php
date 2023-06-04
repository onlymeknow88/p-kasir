@extends('layouts.app')

@section('breadcumb')
    <div class="content-header" id="content-header">
        <div class="sub-items-left">
            <div class="item-breadcumb active">
                <a href="#">Role</a>
            </div>
        </div>
        <div class="sub-items-right">
            <div class="item-button">
                {{-- <a href="{{ route('aplikasi.role.create') }}"
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

    <script>
        var table = $('.table').DataTable({
            ajax: {
                url: '{{ url()->current() }}',
            },
            columns: [{
                    data: 'nama_role',
                    name: 'nama_role'
                },
                {
                    data: 'judul_role',
                    name: 'judul_role'
                },
                {
                    data: 'menu_id',
                    name: 'menu_id'
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'aksi',
                    searchable: false,
                    sortable: false
                },
            ],
            // responsive: true,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            language: {
                paginate: {
                    next: '>', // or '→'
                    previous: '<' // or '←'
                }
            },
        });

        function deleteData(url) {
            Swal.fire({
                title: "Delete?",
                text: "Apakah anda yakin?",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                reverseButtons: !0
            }).then(function(e) {

                if (e.value === true) {
                    $.post(url, {
                            '_method': 'delete',
                            // '_token': `{{ csrf_token() }}`
                        })
                        .done(response => {
                            console.log(response);
                            if (response.meta.message == 'File Deleted') {
                                window.location.href = (location.pathname);
                            }
                            table.ajax.reload();
                        })
                        .fail(errors => {
                            Swal.fire("Something went wrong.", "You clicked the button!", "error");
                            return;
                        });
                } else {
                    e.dismiss;
                }

            }, function(dismiss) {
                return false;
            })
        }
    </script>
    {{-- <script src="{{ asset('assets/js/script.js') }}"></script> --}}
@endpush

@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <h6>All Role</h6>
                </div>
                <div class="horizontal-line my-3"></div>
                <a class="btn btn-primary" title="Add" href="{{ route('aplikasi.role.create') }}">
                    {{-- <img src="{{ asset('assets/icon/plus.svg') }}" alt="" class="me-2"> --}}
                    <i class="fa fa-plus me-2"></i>
                    Tambah Kategori
                </a>

                <div class="horizontal-line my-3"></div>
                <div class="table-responsive">

                    <table class="table table-hover table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Nama Role</th>
                                <th>Judul Role</th>
                                <th>Default Menu</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
