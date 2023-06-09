var table = $('.table').DataTable({
    ajax: {
        url: '/role',
    },
    columns: [{
            data: 'nama_role',
            name: 'nama_role',
            width: '20%'
        },
        {
            data: 'judul_role',
            name: 'judul_role',
            width: '20%'
        },
        {
            data: 'menu_id',
            name: 'menu_id',
            width: '20%'
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
    responsive: true,
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
