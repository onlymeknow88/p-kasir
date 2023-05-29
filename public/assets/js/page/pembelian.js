let table,table1;

$(function () {
    table = $(".table-pembelian").DataTable({
        ajax: {
            url: "/pembelian/data",
        },
        columns: [
            {
                data: "DT_RowIndex",
                searchable: false,
                sortable: false,
            },
            { data: "tanggal" },
            { data: "supplier" },
            { data: "total_item" },
            { data: "total_harga" },
            { data: "diskon" },
            { data: "bayar" },
            { data: "aksi", searchable: false, sortable: false },
        ],
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        ordering: false,
        language: {
            paginate: {
                next: '>', // or '→'
                previous: '<' // or '←'
            }
        },
    });

    $('.table-supplier').DataTable();

    table1 = $('.table-detail').DataTable({
        processing: true,
        bSort: false,
        dom: 'Brt',
        columns: [
            {data: 'DT_RowIndex', searchable: false, sortable: false},
            {data: 'kode_product'},
            {data: 'nama'},
            {data: 'harga_beli'},
            {data: 'jumlah'},
            {data: 'subtotal'},
        ]
    })

    $("#form").on("keyup keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
});

function addForm() {
    $('#modal-supplier').modal("show");

}

function showDetail(url) {
    $('#modal-detail').modal("show");

    table1.ajax.url(url);
    table1.ajax.reload();
}

function deleteData(url) {
    Swal.fire({
        title: "Delete?",
        text: "Apakah anda yakin?",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        reverseButtons: !0,
    }).then(
        function (e) {
            if (e.value === true) {
                $.post(url, {
                    _method: "delete",
                })
                    .done((response) => {
                        console.log(response);
                        if (response.meta.message == "File Deleted") {
                            window.location.href = location.pathname;
                        }
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        Swal.fire(
                            "Something went wrong.",
                            "You clicked the button!",
                            "error"
                        );
                        return;
                    });
            } else {
                e.dismiss;
            }
        },
        function (dismiss) {
            return false;
        }
    );
}



