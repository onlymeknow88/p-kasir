var dataTable;
 dataTable = $(".table").DataTable({
    ajax: {
        url: "/barang",
    },
    columns: [
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
            width: "5%",
        },
        {
            data: "nama_barang",
            name: "nama_barang",
            width: "20%",
        },
        {
            data: "deskripsi",
            name: "deskripsi",
            width: "30%",
        },
        {
            data: "barcode",
            name: "barcode",
            width: "10%",
        },
        {
            data: "stok",
            name: "stok",
            width: "10%",
        },
        {
            data: "aksi",
            searchable: false,
            sortable: false,
            width: "25%",
        },
    ],
    pagingType: "simple_numbers",
    lengthMenu: [10, 25, 50],
    pageLength: 10,
    language: {
        paginate: {
            next: ">", // or '→'
            previous: "<", // or '←'
        },
    },
});

jQuery(document).ready(function () {
    $(".barcode").keyup(function () {
        value = this.value.replace(/\D/g, "");
        this.value = value;
        if (value.length > 13) {
            this.value = value.substr(0, 13);
        }
        $(".jml-digit").text(this.value.length);
    });

    $(".generate-barcode").click(function () {
        $this = $(this).prop("disabled", true);
        $input = $this.prev().prop("disabled", true);
        $parent = $this.parent().parent();
        $spinner = $parent.find(".spinner").show();

        $.ajax({
            url: "/barang/GenerateBarcodeNumber",
            success: function (data) {
                console.log(data);
                $this.prop("disabled", false);
                $input.prop("disabled", false).val(data).trigger("keyup");
                $spinner.hide();
            },
            error: function () {},
        });
    });

    $(".increment").click(function () {
        value = setInt($(this).prev().val());
        console.log(value);
        $(this)
            .prev()
            .val(value + 1)
            .trigger("keyup");
    });

    $(".decrement").click(function () {
        value = setInt($(this).next().val());
        if (value > 0) {
            $(this)
                .next()
                .val(value - 1)
                .trigger("keyup");
        }
    });

    $(".stok").keyup(function () {
        adjusment = this.value;
        if (!adjusment) {
            adjusment = "0";
        }
        adjusment = setInt(adjusment);

        $parent = $(this).parents(".stok-number").eq(0);
        operator = $parent.find(".operator").val();
        stok_awal = setInt($parent.find(".stok-awal").text());
        stok_akhir =
            operator == "plus" ? stok_awal + adjusment : stok_awal - adjusment;

        sign = operator == "plus" ? "+" : "-";
        $parent.find(".stok-adjusment").text(sign + adjusment.toString());
        $parent.find(".stok-akhir").text(stok_akhir);
        this.value = format_ribuan(adjusment);
        $parent
            .find('input[name="adjusment[]"]')
            .val(sign + adjusment.toString());
    });


    $(".operator").change(function () {
        $parent = $(this).parents(".stok-number").eq(0);
        $parent.find(".stok").trigger("keyup");
    });

    $(".number").keyup(function () {
        number = this.value;
        if (!number) {
            number = "0";
        }
        number = setInt(number);
        this.value = format_ribuan(number);
    });

    $(".harga-pokok").keyup(function () {
        $parent = $(this).parent();
        harga_pokok_awal = setInt($parent.find(".harga-pokok-awal").text());
        harga_pokok_akhir = setInt(this.value);
        adjusment = harga_pokok_akhir - harga_pokok_awal;
        $parent.find(".adjusment-harga-pokok").text(format_ribuan(adjusment));
        $parent.find('input[name="adjusment_harga_pokok"]').val(adjusment);
    });

    $(".harga-jual").keyup(function () {
        $parent = $(this).parent().parent();
        harga_jual_awal = setInt($parent.find(".harga-jual-awal").text());
        harga_jual_akhir = setInt(this.value);
        adjusment = harga_jual_akhir - harga_jual_awal;
        console.log(adjusment);
        $parent.find(".adjusment-harga-jual").text(format_ribuan(adjusment));
    });

    $("#list-kategori").css("width", "100%");

    $("#btn-excel").click(function () {
        $this = $(this);
        $this.prop("disabled", true);
        $spinner = $(
            '<div class="spinner-border spinner-border-sm me-2"></div>'
        );
        $spinner.prependTo($this);

        url = "/barang/exportExcel";
        $.ajax({
            url: url,
            method: "GET",
            success: function (response) {
                // Redirect to the file download URL
                $this.prop("disabled", false);
                $spinner.remove();
                window.location.href = url;
            },
            error: function (xhr) {
                $this.prop("disabled", false);
                $spinner.remove();
                console.log(xhr.responseText);
            },
        });
    });
});

function showForm(type = "add", id = "") {
    $bootbox = bootbox.dialog({
        title: type == "edit" ? "Edit Kategori" : "Tambah Kategori",
        message:
            '<div class="text-center"><div class="spinner-border text-secondary" role="status"></div>',
        buttons: {
            cancel: {
                label: "Cancel",
                className: "btn-link",
            },
            success: {
                label: "Submit",
                className: "btn-primary submit",
                callback: function () {
                    $form_filled = $bootbox.find("form");
                    var formdata = false;
                    if (window.FormData) {
                        formdata = new FormData($form_filled[0]);
                    }

                    $.post({
                        url: $("#add-formKategori").attr("action"),
                        data: formdata ? formdata : form.serialize(),
                        type: "POST",
                        dataType: "json",
                        contentType: false,
                        cache: false,
                        processData: false,
                    })
                        .done((response) => {
                            // console.log(response);

                            if (response.meta.code == 200) {
                                swal.fire({
                                    text: response.meta.message,
                                    type: "success",
                                }).then(function () {
                                    $bootbox.modal("hide");
                                });
                            } else if (response.meta.code == 500) {
                                swal.fire({
                                    text: response.meta.message,
                                    type: "error",
                                });
                            }
                        })
                        .fail((errors) => {
                            if (errors.status == 422) {
                                // console.log("Error:", errors.responseJSON.errors);
                                loopErrors(errors.responseJSON.errors);
                                return;
                            }
                        });
                    return false;
                },
            },
        },
    });

    var url = "/barang/create?id=" + id;
    $.get(url, function (result) {
        // $button.prop('disabled', false);
        $bootbox.find(".modal-body").html(result);
        // $('#parent_id').val(result.).trigger('change');
    });
    return $bootbox;
}

function submitForm(url) {
    if ($("#id").val()) {
        $("input[name='_method']")
            .removeAttr("value", "post")
            .attr("value", "patch");
    }

    var form = $("#form");
    var formdata = false;
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }

    $.ajax({
        data: formdata || form.serialize(),
        url: url,
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.meta.code == 200) {
                // console.log(data.meta.message.substr(0, 12));
                swal.fire({
                    text: data.meta.message,
                    type: "success",
                }).then(function () {
                    history.back();
                    var table = $(".table").DataTable();
                    var indexes = table
                        .rows()
                        .eq(0)
                        .filter(function (rowIdx) {
                            return table.cell(rowIdx, 0).data() === data.result.data.id
                                ? true
                                : false;
                        });
                    table.rows(indexes).invalidate().draw(false);
                });
            } else if (data.meta.code == 500) {
                swal.fire({
                    text: data.meta.message,
                    type: "error",
                });
            }
        },
        error: function (xhr, status, error) {
            console.log("Error:", xhr);
            if (xhr.status == 422) {
                loopErrors(xhr.responseJSON.errors);
                return;
            }
        },
    });
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
                    // '_token': `{{ csrf_token() }}`
                })
                    .done((response) => {
                        console.log(response);
                        if (response.meta.message == "Deleted") {
                            // window.location.href = location.pathname;
                            dataTable.ajax.reload();
                        }
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

function loopErrors(errors) {
    $(".invalid-feedback").remove();

    if (errors == undefined) {
        return;
    }

    for (error in errors) {
        $(`[name=${error}]`).addClass("is-invalid");

        if (
            $(`[name=${error}]`).hasClass("barcode") ||
            $(`[name=${error}]`).hasClass("number")
        ) {
            $(
                `<span class="error invalid-feedback">${errors[error][0]}</span>`
            ).insertAfter($(`[name=${error}]`).next());
        } else if ($(`[name=${error}]`).hasClass("form-select")) {
            if ($("span").hasClass("select2")) {
                $(
                    `<span class="error invalid-feedback">${errors[error][0]}</span>`
                ).insertAfter($(`[name=${error}]`).next());
            }
        } else {
            if ($(`[name=${error}]`).length == 0) {
                $(`[name="${error}[]"]`).addClass("is-invalid");
                $(
                    `<span class="error invalid-feedback">${errors[error][0]}</span>`
                ).insertAfter($(`[name="${error}[]"]`).next());
            } else {
                $(
                    `<span class="error invalid-feedback">${errors[error][0]}</span>`
                ).insertAfter($(`[name=${error}]`));
            }
        }
    }
}
