var dataTable;

dataTable = $(".table").DataTable({
        ajax: {
            url: "/refrensi/unit",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {
                data: "nama_satuan",
                name: "nama_satuan",
            },
            {
                data: "satuan",
                name: "satuan",
            },
            {
                data: "aksi",
                searchable: false,
                sortable: false,
                width: "15%",
            },
        ],
        responsive: true,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        language: {
            paginate: {
                next: ">", // or '→'
                previous: "<", // or '←'
            },
        },
    });
