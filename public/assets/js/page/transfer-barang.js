var dataTable;

    dataTable = $("#table-result").DataTable({
        ajax: {
            url: "/transfer-barang",
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            {
                data: "no_nota_transfer",
                name: "no_nota_transfer",
            },
            {
                data: "tgl_nota_transfer",
                name: "tgl_nota_transfer",
            },
            {
                data: "nama_gudang_asal",
                name: "nama_gudang_asal",
            },
            {
                data: "nama_gudang_tujuan",
                name: "nama_gudang_tujuan",
            },
            {
                data: "total_qty_transfer",
                name: "total_qty_transfer",
            },
            {
                data: "aksi",
                searchable: false,
                sortable: false,
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

jQuery(document).ready(function () {
    list_barang_terpilih = {};
    $table = $("#list-produk");



    // Edit
    if (!$("#list-produk").is(":hidden")) {
        list = $("#list-barang-terpilih").text();
        if (list) {
            list_barang_terpilih = JSON.parse(list);
        }
    }

    $(".flatpickr").flatpickr({
        enableTime: false,
        dateFormat: "d-m-Y",
        time_24hr: true,
    });

    $(".tanggal-nota-transfer").change(function () {
        // alert();
        split = this.value.split("-");
        let date = new Date(
            split[2] + "-" + split[1] + "-" + split[0] + " 00:00:00"
        );
        date.setDate(date.getDate() + 21);
        d = "0" + date.getDate();
        m = "0" + (date.getMonth() + 1);
        y = date.getFullYear();
        $(".tanggal-jatuh-tempo").val(
            d.substr(-2) + "-" + m.substr(-2) + "-" + y
        );
    });

    $("#jenis-harga").change(function () {
        if (Object.keys(list_barang_terpilih).length == 0) return;
        // console.log(list_barang_terpilih);
        id_jenis_harga = this.value;
        $(".id-barang").each(function (i, el) {
            id_barang = $(el).val();
            $tr = $(el).parents("tr").eq(0);
            new_harga =
                list_barang_terpilih[id_barang]["list_harga"][id_jenis_harga];
            $tr.find(".harga-satuan").val(new_harga).trigger("keyup");
        });
    });

    $("form").delegate(".harga", "keyup", function () {
        calculate_total();
    });

    $("table").delegate(".qty", "keyup", function () {
        let value = setInt(this.value);
        $tr = $(this).parents("tr").eq(0);
        let jml_stok = setInt($tr.find(".jml-stok").text());
        if (value > jml_stok) {
            this.value = format_ribuan(jml_stok);
        } else {
            this.value = format_ribuan(value);
        }

        $(this).parents("tr").eq(0).find(".harga-satuan").trigger("keyup");
    });

    $("table").delegate(".harga-satuan", "keyup", function () {
        $tr = $(this).parents("tr").eq(0);
        harga_satuan = setInt(this.value);
        qty = setInt($tr.find(".qty").val());
        harga_total = qty * harga_satuan;
        diskon_jenis = $tr.find(".diskon-barang-jenis").val();
        diskon = setInt($tr.find(".diskon-barang").val());
        if (diskon) {
            if (diskon_jenis == "%") {
                jumlah_diskon = Math.round((harga_total * diskon) / 100);
            } else {
                jumlah_diskon = diskon;
            }
            harga_total = harga_total - jumlah_diskon;
        }
        $tr.find(".harga-total").val(format_ribuan(harga_total));
        this.value = format_ribuan(harga_satuan);
        calculate_total();
    });

    $("#diskon-total").keyup(function () {
        let diskon_value = setInt(this.value);
        let diskon_total_jenis = $("#diskon-total-jenis").val();
        if (diskon_value) {
            if (diskon_total_jenis == "%") {
                if (diskon_value > 100) {
                    diskon_value = 100;
                }
            }
        }
        this.value = format_ribuan(diskon_value);
        calculate_total();
    });

    $("#diskon-total-jenis").change(function () {
        calculate_total();
    });

    $("table").delegate(".diskon-barang", "keyup", function () {
        let diskon_value = setInt(this.value);
        $tr = $(this).parents("tr").eq(0);
        diskon_jenis = $tr.find(".diskon-barang-jenis").val();
        diskon = setInt($tr.find(".diskon-barang").val());
        if (diskon) {
            if (diskon_jenis == "%") {
                if (diskon_value > 100) {
                    diskon_value = 100;
                }
            }
        }
        this.value = format_ribuan(diskon_value);
        $(this).parents("tr").eq(0).find(".harga-satuan").trigger("keyup");
    });

    $("table").delegate(".diskon-barang-jenis", "change", function () {
        $(this).parents("tr").eq(0).find(".diskon-barang").trigger("keyup");
    });

    $("table").delegate(".del-row", "click", function () {
        $this = $(this);
        $table = $this.parents("table");
        $tbody = $table.find("tbody").eq(0);
        $trs = $tbody.find("tr");
        id = $table.attr("id");

        if ($trs.length == 1) {
            $trs.find("input").val("");
            $tbody.parent().hide();
            if (id == "list-pembayaran") {
                $("#using-pembayaran").val(0);
            } else if (id == "list-barang") {
                $("#using-list-barang").val(0);
            }
        } else {
            $this.parents("tr").eq(0).remove();
            $new_trs = $tbody.find("tr");
            $new_trs.each(function (i, elm) {
                $(elm)
                    .find("td")
                    .eq(0)
                    .html(i + 1);
            });
        }

        if (id == "list-pembayaran") {
            $tbody.find(".item-bayar").eq(0).trigger("keyup");
        } else if (id == "list-barang") {
            $tbody.find(".harga-satuan").eq(0).trigger("keyup");
        }

        calculate_total();
    });

    // Total

    $("form").delegate("#penyesuaian", "keyup", function () {
        this.value = format_ribuan(this.value);
        calculate_total();
    });

    $("form").delegate("#operator-penyesuaian", "change", function () {
        calculate_total();
    });

    function calculate_total() {
        $input_harga = $("#list-produk").find(".harga-total");

        subtotal = 0;
        $input_harga.each(function (i, elm) {
            value = $(elm).val();
            subtotal += setInt(value);
        });
        $("#subtotal").val(format_ribuan(subtotal));

        // Diskon
        let diskon_total_jenis = $("#diskon-total-jenis").val();
        let diskon_total = setInt($("#diskon-total").val());
        if (diskon_total) {
            if (diskon_total_jenis == "%") {
                jumlah_diskon = Math.round((subtotal * diskon_total) / 100);
            } else {
                jumlah_diskon = diskon_total;
            }
            subtotal = subtotal - jumlah_diskon;
        }

        operator = $("#operator-penyesuaian").val();
        penyesuaian = setInt($("#penyesuaian").val());
        if (operator == "-") {
            neto = subtotal - penyesuaian;
        } else {
            neto = subtotal + penyesuaian;
        }

        $("#total").val(format_ribuan(neto));
        $(".item-bayar").eq(0).trigger("keyup");
        // $('.kurang-bayar').val(format_ribuan(neto));
    }

    $(".barcode").keypress(function (e) {
        $(".input-group").next(".barcode").css("display", "none");
        if (e.which == 13) {
            return false;
        }
    });

    $(".barcode").keyup(function (e) {
        $this = $(this);
        value = $this.val().replace(/\D/g, "");
        this.value = value.substr(0, 13);
        // console.log(this.value)
        // // console.log(value.length);
        if (value.length >= 13) {
            let gudang = $("#gudang-asal").val();
            value = value.substr(0, 13);
            $spinner = $(
                '<div class="spinner-border text-secondary spinner" style="height: 18px; width:18px; position:absolute; left:315px; top:7px" role="status"><span class="visually-hidden">Loading...</span></div>'
            );
            $parent = $this.parent().parent();
            $parent.find(".spinner").remove();
            $spinner.appendTo($parent);
            $this.attr("disabled", "disabled");
            $(".add-barang").attr("disabled", "disabled").addClass("disabled");
            $.ajax({
                url:
                    "/transfer-barang/ajaxGetBarangByBarcode?code=" +
                    value +
                    "&gudang_id=" +
                    gudang,
                success: function (data) {
                    $parent.find(".spinner").remove();
                    $this.removeAttr("disabled");
                    $(".add-barang")
                        .removeAttr("disabled")
                        .removeClass("disabled");

                    data = JSON.parse(data);
                    // console.log(data.data)
                    if (data.status == "ok") {
                        addBarang(data.data);
                        $this.val("").focus();
                    } else {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: "color-red text-white p-2 mb-2",
                            },
                            didOpen: (toast) => {
                                toast.addEventListener(
                                    "mouseenter",
                                    Swal.stopTimer
                                );
                                toast.addEventListener(
                                    "mouseleave",
                                    Swal.resumeTimer
                                );
                            },
                        });

                        Toast.fire({
                            html: '<div class="toast-content"><i class="far fa-check-circle me-2"></i> Data tidak ditemukan</div>',
                        });
                    }
                },
                error: function () {},
            });
        }
    });

    function addBarang(barang) {
        list_barang_terpilih[barang.id] = barang;

        // List barang
        $tbody = $table.find("tbody").eq(0);

        $trs = $tbody.find("tr");
        $tr = $trs.eq(0).clone();
        num = $trs.length;
        if ($table.is(":hidden")) {
            $trs.remove();
            num = 0;
        }

        $td = $tr.find("td");
        $td.eq(0).text(num + 1);
        $td.eq(1).text(barang.nama_barang);
        $td.eq(2).html('<span class="jml-stok">' + barang.stok + "</span>");
        $td.eq(3).html(barang.satuan);

        $tr.find(".qty").val(1);
        $tr.find(".diskon-barang").val("");
        harga_jual = barang.harga_pokok || 0;
        $harga_satuan = $tr.find(".harga-satuan").val(harga_jual);

        $barang_id = $tr.find(".id-barang");
        $parent = $barang_id.parent();
        // console.log($barang_id)
        $barang_id.remove();
        $parent.append(
            '<input type="hidden" class="id-barang" name="barang_id[]" value="' +
                barang.id +
                '"/>'
        );

        $table.show();
        $tbody.append($tr);

        $harga_satuan.trigger("keyup");

        $tr.find(".flatpickr").flatpickr({
            enableTime: false,
            dateFormat: "d-m-Y",
            time_24hr: true,
        });

        $(".list-barang-terpilih").find(".belum-ada").remove();
        $(".list-barang-terpilih").append(
            '<small  class="px-3 py-2 me-2 mb-2 text-light bg-success bg-opacity-10 border border-success border-opacity-10 rounded-2">' +
                barang.nama_barang +
                "</small>"
        );
    }

    $(".add-barang").click(function () {
        $this = $(this);
        if ($this.hasClass("disabled")) {
            return false;
        }

        let gudang = $("#gudang-asal").val();
        let harga = $("#jenis-harga").val();
        var $modal = jwdmodal({
            title: "Pilih Barang",
            url:
                "/transfer-barang/getDataDTListBarang?gudang_id=" +
                gudang +
                "&jenis_harga_id=" +
                harga,
            width: "850px",
            action: function () {
                $trs = $table.find("tbody").eq(0).find("tr");
                var list_barang =
                    '<span class="belum-ada mb-2">Silakan pilih barang</span>';
                if ($table.is(":visible")) {
                    var list_barang = "";
                    $trs.each(function (i, elm) {
                        $td = $(elm).find("td");
                        list_barang +=
                            '<small  class="px-3 py-2 me-2 mb-2 text-light bg-success bg-opacity-10 border border-success border-opacity-10 rounded-2">' +
                            $td.eq(1).html() +
                            "</small>";
                    });
                }
                $(".jwd-modal-header-panel").prepend(
                    '<div class="list-barang-terpilih">' +
                        list_barang +
                        "</div>"
                );
            },
        });

        $(document)
            .undelegate(".pilih-barang", "click")
            .delegate(".pilih-barang", "click", function () {
                // Barang Popup
                $tr = $(this).parents("tr").eq(0);
                barang = JSON.parse($tr.find(".detail-barang").text());
                addBarang(barang);
            });
    });
});

function submitForm(url) {

    var form = $("#form");
    var formdata = false;
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }

    if ($("#gudang-asal").val() == $("#gudang-tujuan").val()) {
        bootbox.alert(
            '<div class="d-flex my-2"><span class="text-danger"><i class="fas fa-exclamation-triangle me-3" style="font-size:20px"></i></span>Gudang asal dan gudang tujuan tidak boleh sama</div>'
        );
        return false;
    }
    if ($table.is(":hidden")) {
        bootbox.alert(
            '<div class="d-flex my-2"><span class="text-danger"><i class="fas fa-exclamation-triangle me-3" style="font-size:20px"></i></span>Barang belum dipilih</div>'
        );
        return false;
    }

    $.ajax({
        data: formdata ? formdata : form.serialize(),
        url: url,
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.meta.code == 200) {
                console.log(data);
                swal.fire({
                    text: data.meta.message,
                    type: "success",
                }).then(function () {
                    // window.history.back();
                    window.location.href = "/trasnfer-barang";
                    dataTable.ajax.reload(null, false);
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

        if ($(`[name=${error}]`).hasClass("form-select")) {
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
