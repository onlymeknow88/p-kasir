let table, table2;
let id_pembelian = $("#id_pembelian").val();

console.log(id_pembelian);

$(function () {
    $(".sidebar").addClass("show-menu");
    $(".main-wrapper").addClass("show-menu");

    table = $(".table-pembelian")
        .DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "/pembelian_detail/" + id_pembelian + "/data",
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    searchable: false,
                    sortable: false,
                },
                {
                    data: "kode_product",
                },
                {
                    data: "nama",
                },
                {
                    data: "harga_beli",
                },
                {
                    data: "jumlah",
                },
                {
                    data: "subtotal",
                },
                {
                    data: "aksi",
                    searchable: false,
                    sortable: false,
                },
            ],
            dom: "Brt",
            bSort: false,
            paginate: false,
        })
        .on("draw.dt", function () {
            loadForm($("#diskon").val());
        });
    table2 = $(".table-produk").DataTable();

    $(document).on("input", ".qty", function () {
        let id = $(this).data("id");
        let jumlah = parseInt($(this).val());

        if (jumlah < 1) {
            $(this).val(1);
            alert("Jumlah tidak boleh kurang dari 1");
            return;
        }
        if (jumlah > 10000) {
            $(this).val(10000);
            alert("Jumlah tidak boleh lebih dari 10000");
            return;
        }

        // $.post('/pembelian_detail/'+id, {
        //         '_token': $('[name=csrf-token]').attr('content'),
        //         '_method': 'put',
        //         'jumlah': jumlah
        //     })
        //     .done(response => {
        //         $(this).on('mouseout', function() {
        //             table.ajax.reload(() => loadForm($('#diskon').val()));
        //         });
        //     })
        //     .fail(errors => {
        //         return;
        //     });
    });

    $(document).on("input", "#diskon", function () {
        if ($(this).val() == "") {
            $(this).val(0).select();
        }

        loadForm($(this).val());
    });
});

function tampilProduk() {
    $("#modal-produk").modal("show");
}

function hideProduk() {
    $("#modal-produk").modal("hide");
}

function pilihProduk(id, kode) {
    $("#kode_product").val(kode);
    $("#id_produk").val(id);
    hideProduk();
}

function tambahProduk() {
    var form = $("#form-produk");
    var formdata = false;
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }
    $.post({
        url: "/pembelian_detail",
        data: formdata ? formdata : form.serialize(),
        type: "POST",
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
    })
        .done((response) => {
            $("#kode_product").val("");
            table.ajax.reload(() => loadForm($("#diskon").val()));
            $("#qty").val("");
        })
        .fail((errors) => {
            alert("Tidak dapat menyimpan data");
            return;
        });
}

function deleteData(url) {
    if (confirm("Yakin ingin menghapus data terpilih?")) {
        $.post(url, {
            _token: $("[name=csrf-token]").attr("content"),
            _method: "delete",
        })
            .done((response) => {
                table.ajax.reload(() => loadForm($("#diskon").val()));
            })
            .fail((errors) => {
                alert("Tidak dapat menghapus data");
                return;
            });
    }
}

function simpanPembelian() {
    var form = $("#form-pembelian");
    var formdata = false;
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }
    $.ajax({
        url: "/pembelian",
        data: formdata ? formdata : form.serialize(),
        type: "POST",
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.meta.code == 200) {
                window.location.href = '/pembelian';
            }
        },
        error: function (data) {
            console.log("Error:", data);
            if (data.status == 422) {
                return;
            }
        },
    });
}

function loadForm(diskon = 0) {
    $("#total").val($(".total").text());
    $("#total_item").val($(".total_item").text());

    $.get("/pembelian_detail/loadform/" + diskon + "/" + $(".total").text())
        .done((response) => {
            $("#totalrp").val("Rp. " + response.totalrp);
            $("#bayarrp").val("Rp. " + response.bayarrp);
            $("#bayar").val(response.bayar);
            $(".tampil-bayar").text("Rp. " + response.bayarrp);
            $(".tampil-terbilang").text(response.terbilang);
        })
        .fail((errors) => {
            alert("Tidak dapat menampilkan data");
            return;
        });
}
