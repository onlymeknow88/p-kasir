jQuery(document).ready(function () {
    list_barang_terpilih = {};
    $table = $("#list-produk");

    const $tableResult = $("#table-result");
    if ($tableResult.length) {
        const column = $.parseJSON($("#dataTables-column").html());
        const url = $("#dataTables-url").text();
        const addSetting = $.parseJSON($("#dataTables-setting").html());
        const settings = {
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: url,
            },
            columns: column,
            language: {
                paginate: {
                    next: ">", // or '→'
                    previous: "<", // or '←'
                },
            },

            initComplete: function (settings, json) {
                table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                    /* this
                .child(
                    $(
                        '<tr>'+
                            '<td>'+rowIdx+'.1</td>'+
                            '<td>'+rowIdx+'.2</td>'+
                            '<td>'+rowIdx+'.3</td>'+
                            '<td>'+rowIdx+'.4</td>'+
                        '</tr>'
                    )
                )
                .show(); */
                });
            },
            ...addSetting, // Merge additional settings
        };
        const table = $tableResult.DataTable(settings);
    }

    $(".flatpickr").flatpickr({
        enableTime: false,
        dateFormat: "d-m-Y",
        time_24hr: true,
    });

    $(".cari-invoice").click(function () {
        $this = $(this);
        if ($this.hasClass("disabled")) {
            return false;
        }

        // let gudang = $('#gudang').val();
        // let harga = $('#jenis-harga').val();
        var $modal = jwdmodal({
            title: "Pilih Invoice",
            url: "/pembelian-retur/getDataDTListInvoice",
            width: "850px",
            action: function () {
                $trs = $table.find("tbody").eq(0).find("tr");
                var list_barang =
                    '<span class="belum-ada mb-2">Silakan pilih invoice</span>';
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
            .undelegate(".pilih-invoice", "click")
            .delegate(".pilih-invoice", "click", function () {
                // Invoice Popup
                $tr = $(this).parents("tr").eq(0);
                pembelian = JSON.parse($tr.find(".pembelian").eq(0).text());
                barang = pembelian["detail"];
                console.log(pembelian);

                // List barang
                $tbody = $table.find("tbody").eq(0);
                $tr = $trs.eq(0).clone();
                $trs = $tbody.find("tr");

                num = $trs.length;
                if ($table.is(":hidden")) {
                    $trs.remove();
                    num = 0;
                }
                // console.log(pembelian);
                $("#no-invoice").val(pembelian.no_invoice);
                $("#nama-supplier").val(pembelian.nama_supplier);
                $("#id-gudang").val(pembelian.gudang_id);
                $("#id-pembelian").val(pembelian.id);
                Object.keys(barang).map(function (i, v) {
                    item = barang[v];
                    // console.log(item)
                    $new_tr = $tr.clone();
                    $new_tr.find(".id-pembelian-detail").val(item.id);
                    $new_tr
                        .find(".harga-total-beli")
                        .val(format_ribuan(item.harga_neto));
                    $td = $new_tr.find("td");
                    $td.eq(0).html(parseInt(i) + 1);
                    $new_tr.find(".nama-barang").html(item.nama_barang);
                    $td.eq(2).html(item.satuan);
                    $td.eq(3)
                        .find("input")
                        .val(format_ribuan(item.harga_satuan));
                    $td.eq(4).find("input").val(format_ribuan(item.qty));
                    // $td.eq(7).find('input').val(format_ribuan(item.harga_neto_retur));
                    $tbody.append($new_tr);
                });
                // console.log(item.nama_barang);

                $table.show();
                // $harga_satuan.trigger('keyup');

                /* $tr.find('.flatpickr').flatpickr({
				enableTime: false,
				dateFormat: "d-m-Y",
				time_24hr: true
			}); */

                $(".list-barang-terpilih").find(".belum-ada").remove();
                $(".list-barang-terpilih").append(
                    '<small  class="px-3 py-2 me-2 mb-2 text-light bg-success bg-opacity-10 border border-success border-opacity-10 rounded-2">' +
                        item.nama_barang +
                        "</small>"
                );
            });
    });

    $(".tanggal-invoice").change(function () {
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

    $("table").delegate(".qty-retur", "keyup", function () {
        let value = setInt(this.value);
        $tr = $(this).parents("tr").eq(0);
        let qty_beli = setInt($tr.find(".qty-beli").val());
        console.log(qty_beli);
        if (value > qty_beli) {
            console.log("ccc");
            this.value = format_ribuan(qty_beli);
        } else {
            console.log("ccc33");
            this.value = format_ribuan(value);
        }

        $(this).parents("tr").eq(0).find(".harga-satuan").trigger("keyup");
    });

    $("table").delegate(".harga-satuan", "keyup", function () {
        $tr = $(this).parents("tr").eq(0);
        harga_satuan = setInt(this.value);
        qty = setInt($tr.find(".qty-retur").val());
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
        $tr.find(".harga-total-retur").val(format_ribuan(harga_total));
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

    function update_penyesuaian() {
        operator = $("#operator-penyesuaian").val();
        penyesuaian = setInt($("#penyesuaian").val());
        if (operator == "-") {
            if (penyesuaian > 0) {
                penyesuaian = penyesuaian * -1;
            }
        } else {
            if (penyesuaian < 0) {
                penyesuaian = penyesuaian * -1;
            }
        }
        $("#penyesuaian").val(format_ribuan(penyesuaian));
        calculate_total();
    }

    $("form").delegate("#penyesuaian", "keyup", function () {
        update_penyesuaian();
    });

    $("form").delegate("#operator-penyesuaian", "change", function () {
        update_penyesuaian();
    });

    function calculate_total() {
        $input_harga = $("#list-produk").find(".harga-total-retur");

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

        /* operator = $('#operator-penyesuaian').val();
		penyesuaian = setInt( $('#penyesuaian').val());
		if (operator == '-') {
			neto = subtotal - penyesuaian;
		} else {
			neto = subtotal + penyesuaian;
		} */

        penyesuaian = setInt($("#penyesuaian").val());
        neto = subtotal + penyesuaian;

        $("#total").val(format_ribuan(neto));
        $(".item-bayar").eq(0).trigger("keyup");
        // $('.kurang-bayar').val(format_ribuan(neto));
    }
});

function submitForm(url) {
    var form = $("#form");
    var formdata = false;
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }

    // if ($("#gudang-asal").val() == $("#gudang-tujuan").val()) {
    //     bootbox.alert(
    //         '<div class="d-flex my-2"><span class="text-danger"><i class="fas fa-exclamation-triangle me-3" style="font-size:20px"></i></span>Gudang asal dan gudang tujuan tidak boleh sama</div>'
    //     );
    //     return false;
    // }
    // if ($table.is(":hidden")) {
    //     bootbox.alert(
    //         '<div class="d-flex my-2"><span class="text-danger"><i class="fas fa-exclamation-triangle me-3" style="font-size:20px"></i></span>Barang belum dipilih</div>'
    //     );
    //     return false;
    // }

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
                console.log(data);
                swal.fire({
                    text: data.meta.message,
                    type: "success",
                }).then(function () {
                    window.history.back();
                    // window.location.href = "/transfer-barang";
                    // Reload only the affected rows
                    var table = $tableResult.DataTable();
                    var indexes = table
                        .rows()
                        .eq(0)
                        .filter(function (rowIdx) {
                            return table.cell(rowIdx, 0).data() === data.id
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
            } else {
                swal.fire({
                    text: "An error occurred. Please try again later.",
                    type: "error",
                });
            }
        },
    });
}

function deleteData(url) {
    const confirmation = confirm("Apakah anda yakin?");
    if (confirmation) {
        $.post(url, {
            _method: "delete",
        })
            .done((response) => {
                console.log(response);
                if (response.meta.message == "Deleted") {
                    // window.location.href = location.pathname;
                    $('#table-result').DataTable().ajax.reload();
                }
            })
            .fail((errors) => {
                alert("Something went wrong.");
                return;
            });
    }
}

function loopErrors(errors) {
    const invalidFeedback = $(".invalid-feedback");
    invalidFeedback.remove();
    if (!errors) {
        return;
    }
    for (const [name, errorMessages] of Object.entries(errors)) {
        const input = $(`[name="${name}"]`);
        const isSelect = input.hasClass("form-select");
        input.addClass("is-invalid");
        if (isSelect && $("span.select2").length) {
            $("<span>", {
                class: "error invalid-feedback",
                text: errorMessages[0],
            }).insertAfter(input.next());
        } else {
            const isArray = input.length === 0;
            const nameAttr = isArray
                ? `[name="${name}[]"]`
                : `[name="${name}"]`;
            $(`<span>`, {
                class: "error invalid-feedback",
                text: errorMessages[0],
            }).insertAfter($(nameAttr).last());
            if (isArray) {
                $(nameAttr).addClass("is-invalid");
            }
        }
    }
}
