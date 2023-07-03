jQuery(document).ready(function () {
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
console.log(this.node());
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

    $(".select2").select2({ theme: "bootstrap-5" });

    $(".barcode").keypress(function (e) {
        if (e.which == 13) {
            return false;
        }
    });

    $(".barcode").keyup(function (e) {
        const $this = $(this);
        let value = $this.val().replace(/\D/g, "");
        this.value = value.substr(0, 13);
        if (value.length >= 13) {
            const gudang = $("#gudang-asal").val();
            value = value.substr(0, 13);
            const $spinner = $(
                '<div class="spinner-border text-secondary spinner" style="height: 18px; width:18px; position:absolute; left:315px; top:7px" role="status"><span class="visually-hidden">Loading...</span></div>'
            );
            const $parent = $this.parent().parent();
            $parent.find(".spinner").remove();
            $spinner.appendTo($parent);
            $this.attr("disabled", "disabled");
            $(".add-barang").attr("disabled", "disabled").addClass("disabled");
            $.ajax({
                url: `/daftar-pembelian/ajaxGetBarangByBarcode?code=${value}&gudang_id=${gudang}`,
                success: function (data) {
                    $parent.find(".spinner").remove();
                    $this.removeAttr("disabled");
                    $(".add-barang")
                        .removeAttr("disabled")
                        .removeClass("disabled");
                    // data = JSON.parse(data);
                    if (data.status == "ok") {
                        console.log(data);
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

    $('.add-barang').click(function() {
		$this = $(this);
		if ($this.hasClass('disabled')) {
			return false;
		}
		var $modal = jwdmodal({
			title: 'Pilih Barang',
			url: '/daftar-pembelian/getDataDTListBarang?gudang_id=' + $('#id-gudang').val(),
			width: '850px',
			action :function ()
			{
				$table = $('#list-barang');
				$trs = $table.find('tbody').eq(0).find('tr');
				var list_barang = '<span class="belum-ada mb-2">Silakan pilih barang</span>';
				if ($table.is(':visible')) {
					var list_barang = '';
					$trs.each (function (i, elm) {
						$td = $(elm).find('td');
						list_barang += '<small  class="px-3 py-2 me-2 mb-2 text-light bg-success bg-opacity-10 border border-success border-opacity-10 rounded-2">' + $td.eq(1).html() + '</small>';
					});
				}
				$('.jwd-modal-header-panel').prepend('<div class="list-barang-terpilih">' + list_barang + '</div>');
			}

		});

		$(document)
		.undelegate('.pilih-barang', 'click')
		.delegate('.pilih-barang', 'click', function() {

			$('#using-list-barang').val(1);
			$table = $('#list-barang');

			// Barang Popup
			$tr = $(this).parents('tr').eq(0);
			$td = $tr.find('td');
			nama_barang = $td.eq(1).find('.nama-barang').text();
			item = $td.eq(1).find('.detail-barang').text();
			stok = $td.eq(2).html();
			satuan = $td.eq(3).html();
			harga_modal = $td.eq(5).html();
			harga = $td.eq(6).html();
			$this.attr('disabled', 'disabled');

			// List barang
			$tbody = $table.find('tbody').eq(0);

			$trs = $tbody.find('tr');
			$tr = $trs.eq(0).clone();
			num = $trs.length;
			if ($table.is(':hidden')) {
				$trs.remove();
				num = 0;
			}

			$td = $tr.find('td');
			$td.eq(0).html(num + 1);
			$td.eq(1).html(nama_barang);

			$td.eq(1).find('input[name="barang_id[]"]').remove();
			$td.eq(1).find('input').val("");
			$td.eq(1).append('<input type="hidden"class name="barang_id[]" value="'+ $(this).attr('data-id-barang') +'"/>');

			$td.eq(2).find('input').val('');

			$td.eq(3).find('input').val("");
			$td.eq(4).find('input').val("");
			$td.eq(5).find('input').val('');
			$td.eq(5).find('.satuan').text(satuan);

			$table.show();
			$tbody.append($tr);

			$tr.find('.flatpickr').flatpickr({
				enableTime: false,
				dateFormat: "d-m-Y",
				time_24hr: true
			});

			$('.list-barang-terpilih').find('.belum-ada').remove();
			$('.list-barang-terpilih').append('<small  class="px-3 py-2 me-2 mb-2 text-light bg-success bg-opacity-10 border border-success border-opacity-10 rounded-2">' + nama_barang + '</small>');

			// $(document);
		});
	});

    function addBarang(item) {
        $table = $("#list-barang");

        // List barang
        $tbody = $table.find("tbody").eq(0);

        // exists
        $id_barang = $tbody.find('input[value="' + item.id_barang + '"');
        if ($id_barang.length) {
            return;
        }

        // Add New

        $trs = $tbody.find("tr");
        $tr = $trs.eq(0).clone();
        num = $trs.length;
        if ($table.is(":hidden")) {
            $trs.remove();
            num = 0;
        }

        $td = $tr.find("td");
        $td.eq(0).html(num + 1);
        $td.eq(1).html(item.nama_barang);

        $td.eq(1).find('input[name="id_barang[]"]').remove();
        $td.eq(1).find("input").val("");
        $td.eq(1).append(
            '<input type="hidden"class name="id_barang[]" value="' +
                item.id_barang +
                '"/>'
        );

        $td.eq(2).find("input").val("");

        $td.eq(3).find("input").val("");
        $td.eq(4).find("input").val("");
        $td.eq(5).find("input").val("");
        $td.eq(5).find(".satuan").text(item.satuan);

        $table.show();
        $tbody.append($tr);

        $tr.find(".flatpickr").flatpickr({
            enableTime: false,
            dateFormat: "d-m-Y",
            time_24hr: true,
        });
    }

    $("table").delegate(".format-ribuan", "keyup", function () {
        this.value = this.value.replace(/\D/g, "");
        if (this.value == "") this.value = 0;

        this.value = parseInt(this.value, 10);
        this.value = format_ribuan(this.value);
    });

    $("table").delegate(".item-bayar", "keyup", function () {
        $this = $(this);
        $table = $this.parents("table");
        $tbody = $table.find("tbody").eq(0);
        $item_bayar = $tbody.find(".item-bayar");

        total_bayar = 0;
        $item_bayar.each(function (i, elm) {
            total_bayar += parseInt($(elm).val().replace(/\D/g, ""));
        });

        total_tagihan = parseInt(
            $(".total-tagihan").val().replace(/\D/g, ""),
            10
        );
        kurang_bayar = total_tagihan - total_bayar;
        if (kurang_bayar < 0) {
            kurang_bayar = 0;
        }
        $table.find(".total-bayar").val(total_bayar).trigger("keyup");
        $table.find(".kurang-bayar").val(kurang_bayar).trigger("keyup");
    });

    $('.add-pembayaran').click(function() {

		$('#using-pembayaran').val(1);
		$table = $('#list-pembayaran');

		// List barang
		$tbody = $table.find('tbody').eq(0);

		$trs = $tbody.find('tr');
		$tr = $trs.eq(0).clone();
		num = $trs.length;
		if ($table.is(':hidden')) {
			$table.show();
			$trs.remove();
			num = 0;
		}

		$td = $tr.find('td');

		$td.eq(0).html(num + 1);
		$tr.find('input').val('');

		//
		$select = $tr.find('select');
		value = $select.find('option').eq(0).attr('value');
		$select.val(value);

		$table.append($tr);

		$tr.find('.flatpickr').flatpickr({
			enableTime: false,
			dateFormat: "d-m-Y",
			time_24hr: true
		});
	})

    $("#list-barang").delegate(
        ".harga-satuan, .kuantitas",
        "keyup",
        function () {
            $this = $(this);
            $table = $this.parents("table");
            $tr = $this.parents("tr").eq(0);
            $tbody = $table.find("tbody").eq(0);

            harga_satuan = setInt($tr.find(".harga-satuan").val());
            kuantitas = setInt($tr.find(".kuantitas").val());
            $tr.find(".harga-total").val(
                format_ribuan(harga_satuan * kuantitas)
            );

            sub_total = 0;
            $list_harga_satuan = $tbody.find(".harga-total");
            $list_harga_satuan.each(function (i, elm) {
                elm_val = $(elm).val();
                if (elm_val == "") {
                    elm_val = "0";
                }
                sub_total += parseInt(elm_val.replace(/\D/g, ""), 10);
                // console.log(sub_total);
            });

            diskon = $table.find(".diskon").val().replace(/\D/g, "");
            total = sub_total - diskon;
            if (total < 0) {
                total = 0;
            }
            $table.find(".sub-total").val(sub_total).trigger("keyup");
            $table.find(".total").val(total).trigger("keyup");

            $("#list-pembayaran")
                .find(".total-tagihan")
                .val(total)
                .trigger("keyup");
            $("#list-pembayaran").find(".item-bayar").eq(0).trigger("keyup");
        }
    );

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
    });

    $('.terima-barang-option').change(function() {
        // console.log(this.value);
		if (this.value == 'Y') {
			$('.terima-barang-container').show();
		} else if (this.value) {
			$('.terima-barang-container').hide();
		}

	});

    $('.diskon').keyup(function() {
		total = setInt( $('#list-barang').find('.sub-total').val() ) - setInt(this.value);
		$('.total, .total-tagihan').val(format_ribuan(total));
		$('.item-bayar').eq(0).trigger('keyup');


		/* sub_total = $('.sub-total').val().replace(/\D/g, '');
		diskon = $('.diskon').val().replace(/\D/g, '');
		neto = sub_total - diskon;
		if (neto < 0) {
			neto = 0;
		}
		$('.total').val(neto);
		$('.total').trigger('keyup'); */
	})

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
                    var table = $("#table-result").DataTable();
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
                    dataTable.ajax.reload();
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
          text: errorMessages[0]
        }).insertAfter(input.next());
      } else {
        const isArray = input.length === 0;
        const nameAttr = isArray ? `[name="${name}[]"]` : `[name="${name}"]`;
         $(`<span>`, {
          class: "error invalid-feedback",
          text: errorMessages[0]
        }).insertAfter($(nameAttr).last());
         if (isArray) {
          $(nameAttr).addClass("is-invalid");
        }
      }
    }
  }
