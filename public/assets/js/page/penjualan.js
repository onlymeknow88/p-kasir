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
                    // console.log(this.node());
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

    $('.barcode').keypress(function(e) {
		if (e.which == 13) {
			return false;
		}
	})

	$('.barcode').keyup(function(e) {

		$this = $(this);
		value = $this.val().replace(/\D/g,'');
		this.value = value.substr(0,13);
		// console.log(value.length);
		if (value.length >= 13)
		{
			value = value.substr(0,13);
			$spinner = $('<div class="spinner-border text-secondary spinner" style="height: 18px; width:18px; position:absolute; left:315px; top:7px" role="status"></div>');
			$parent = $this.parent().parent();
			$parent.find('.spinner').remove();
			$spinner.appendTo($parent);

			$this.attr('disabled', 'disabled');
			$('.add-barang').attr('disabled', 'disabled').addClass('disabled');
			$.ajax({
				url :'/penjualan/ajaxGetBarangByBarcode?code=' + value + '&gudang_id=' + $('#gudang').val() + '&jenis_harga_id=' + $('#jenis-harga').val()
				, success : function (data) {
					console.log(data);

					$parent.find('.spinner').remove();
					$this.removeAttr('disabled');
					$('.add-barang').removeAttr('disabled').removeClass('disabled');

					// data = JSON.parse(data);
					if (data.status == 'ok') {
						addBarang(data.data);
						$this.val('').focus();

					} else {
						const Toast = Swal.mixin({
							toast: true,
							position: 'bottom-end',
							showConfirmButton: false,
							timer: 2500,
							timerProgressBar: true,
							iconColor: 'white',
							customClass: {
								popup: 'bg-danger text-light toast p-2 mb-3'
							},
							didOpen: (toast) => {
								toast.addEventListener('mouseenter', Swal.stopTimer)
								toast.addEventListener('mouseleave', Swal.resumeTimer)
							}
						})
						Toast.fire({
							html: '<div class="toast-content"><i class="far fa-check-circle me-2"></i> Data tidak ditemukan</div>'
						})
					}
				}, error: function() {

				}
			})
		}

	})

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

    $("#gudang").change(function () {
        id_gudang = this.value;
        $(".id-barang").each(function (i, el) {
            id_barang = $(el).val();
            $tr = $(el).parents("tr").eq(0);
            new_stok = list_barang_terpilih[id_barang]["list_stok"][id_gudang];
            $tr.find(".jml-stok").val(new_stok);
            $tr.find(".jml-stok-text").text(new_stok);
            $tr.find(".qty").trigger("keyup");
        });
    });

    $("#jenis-harga").change(function () {
        if (Object.keys(list_barang_terpilih).length == 0) return;

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
        let jml_stok = setInt($tr.find(".jml-stok-text").text());
        if (value > jml_stok) {
            this.value = format_ribuan(jml_stok);
        } else {
            this.value = format_ribuan(value);
        }
        if (value == 0) {
            this.value = 1;
        }
        $(this).parents("tr").eq(0).find(".harga-satuan").trigger("keyup");
    });

    $("table").delegate(".qty-plus", "click", function () {
        new_value = setInt($(this).prev().val());
        $(this)
            .prev()
            .val(new_value + 1)
            .trigger("keyup");
    });

    $("table").delegate(".qty-min", "click", function () {
        new_value = setInt($(this).next().val());
        $(this)
            .next()
            .val(new_value - 1)
            .trigger("keyup");
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

    $("#pajak-nilai").keyup(function () {
        let pajak_nilai = setInt(this.value);
        if (pajak_nilai > 100) {
            pajak_nilai = 100;
        } else if (pajak_nilai <= 0) {
            pajak_nilai = 0;
        }
        $("#pajak-nilai").val(pajak_nilai);

        calculate_total();
    });

    $("form").delegate("#pajak-plus", "click", function () {
        let pajak_nilai = setInt($("#pajak-nilai").val());
        $("#pajak-nilai")
            .val(pajak_nilai + 1)
            .trigger("keyup");
    });

    $("form").delegate("#pajak-min", "click", function () {
        let pajak_nilai = setInt($("#pajak-nilai").val());
        $("#pajak-nilai")
            .val(pajak_nilai - 1)
            .trigger("keyup");
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

        /* operator = $('#operator-penyesuaian').val();
		penyesuaian = setInt( $('#penyesuaian').val());
		if (operator == '-') {
			neto = subtotal - penyesuaian;
		} else {
			neto = subtotal + penyesuaian;
		} */

        penyesuaian = setInt($("#penyesuaian").val());
        neto = subtotal + penyesuaian;

        // Pajak
        tarif_pajak = $("#pajak-nilai").val();
        if (tarif_pajak) {
            pajak = Math.round((neto * parseInt(tarif_pajak)) / 100);
            neto = neto + pajak;
        }

        $("#total").val(format_ribuan(neto));
        $(".item-bayar").eq(0).trigger("keyup");
        // $('.kurang-bayar').val(format_ribuan(neto));
    }

    $("table").delegate(".item-bayar", "keyup", function () {
        let bayar = 0;
        $("table")
            .find(".item-bayar")
            .each(function (i, el) {
                number = setInt($(el).val());
                if (!number) number = 0;
                bayar += number;
            });

        total = setInt($("#total").val());
        sisa = total - bayar;

        this.value = format_ribuan(this.value);
        if (sisa == 0) {
            $(".sisa").text("Lunas");
            $(".kurang-bayar").removeClass("text-danger");
        } else {
            if (sisa < 0) {
                sisa = sisa * -1;
                $(".sisa").text("Kembali");
                $(".kurang-bayar").addClass("text-danger");
            } else {
                $(".sisa").text("Kurang");
                $(".kurang-bayar").addClass("text-danger");
            }
        }
        $(".kurang-bayar").val(format_ribuan(sisa));
    });

    $('table').delegate('.del-row', 'click', function()
	{
		$this = $(this);
		$table = $this.parents('table');
		$tbody = $table.find('tbody').eq(0);
		$trs = $tbody.find('tr');
		id = $table.attr('id');

		if ($trs.length == 1) {
			$trs.find('input').val('');
			$tbody.parent().hide();
			if (id == 'list-pembayaran') {
				$('#using-pembayaran').val(0);
			} else if (id == 'list-barang') {
				$('#using-list-barang').val(0);
			}
		} else {
			$this.parents('tr').eq(0).remove();
			$new_trs = $tbody.find('tr');
			$new_trs.each(function(i, elm) {
				$(elm).find('td').eq(0).html(i + 1);
			});
		}

		if (id == 'list-pembayaran') {
			$tbody.find('.item-bayar').eq(0).trigger('keyup');
		} else if (id == 'list-barang') {
			$tbody.find('.harga-satuan').eq(0).trigger('keyup');
		}

		calculate_total()
	});

    function addBarang(barang) {
        // var base_url = window.location.origin;
        // suara = new Audio(base_url + "/assets/files/audio/beep.wav");
        // suara.play();

        // Check if the browser supports SpeechSynthesis
        // if ("speechSynthesis" in window) {
        //     // Create a new SpeechSynthesisUtterance object
        //     var msg = new SpeechSynthesisUtterance();

        //     // Set the text to be spoken
        //     msg.text = barang.nama_barang;

        //     // Set the language for the speech
        //     msg.lang = "id-ID"; // Specify the language code, e.g., "en-US" for English (United States)

        //     // Call the speech synthesis API to speak the text
        //     window.speechSynthesis.speak(msg);
        // }

        $("#using-list-barang").val(1);
        // List barang
        $tbody = $table.find("tbody").eq(0);

        if (list_barang_terpilih[barang.id] != undefined) {
            let $id_barang = $tbody.find(
                '.id-barang[value="' + barang.id + '"]'
            );
            let $qty = $id_barang.parents("tr").eq(0).find(".qty");
            let qty = parseInt($qty.val());
            $qty.val(qty + 1);
            $qty.trigger("keyup");
            return;
        }

        list_barang_terpilih[barang.id] = barang;
        $trs = $tbody.find("tr");
        $tr = $trs.eq(0).clone();
        num = $trs.length;
        if ($table.is(":hidden")) {
            $trs.remove();
            num = 0;
        }

        $td = $tr.find("td");
        $td.eq(0).text(num + 1);
        $td.eq(1).html(
            barang.nama_barang +
                '<div class="list-barang-detail"><small class="rounded badge-clear-success">Stok: <span class="jml-stok-text">' +
                barang.stok +
                "</small><span> " +
                barang.satuan +
                "</div>"
        );
        // $td.eq(2).html('<span class="jml-stok">' + barang.stok + '</span> ' + barang.satuan);

        $tr.find(".qty").val(1);
        $tr.find(".diskon-barang").val("");
        harga_jual = barang.harga_jual || 0;
        $harga_satuan = $tr.find(".harga-satuan").val(harga_jual);

        harga_pokok = barang.harga_pokok || 0;
        $harga_pokok = $tr.find(".harga-pokok").val(harga_pokok);

        $id_barang = $tr.find(".id-barang");
        $parent = $id_barang.parent();
        $id_barang.remove();
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

    // Modal Barang
    $(".add-barang").click(function () {
        $this = $(this);
        if ($this.hasClass("disabled")) {
            return false;
        }

        let gudang = $("#gudang").val();
        let harga = $("#jenis-harga").val();
        var $modal = jwdmodal({
            title: "Pilih Barang",
            url:
                "/penjualan/getDataDTListBarang?gudang_id=" +
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
                // $(document);
            });
    });

    $(".cari-customer").click(function () {
        $this = $(this);
        var $modal = jwdmodal({
            title: "Pilih Customer",
            url: "/penjualan/getListCustomer",
            width: "950px",
            action: function () {},
        });

        $(document)
            .undelegate(".pilih-customer", "click")
            .delegate(".pilih-customer", "click", function () {
                // Customer popup
                $this = $(this);
                $this.attr("disabled", "disabled");
                customer = JSON.parse($(this).next().text());
                $("#id-customer").val(customer.id);
                $("#nama-customer").val(customer.nama_customer);
                $("#del-customer").show();
                $modal.remove();
            });
    });

    $('.add-pembayaran').click(function() {
		$tr = $(this).parents('tr').eq(0);
		$tfoot = $tr.parent();
		$new_tr = $tr.clone();
		$new_tr.find('input').val('');
		$new_tr.find('td').eq(0).text( $tfoot.children('tr.row-bayar').length + 1);
		$new_tr.find('td').last().html( '<button type="button" class="btn text-red del-pembayaran"><i class="fas fa-times"></i></button>' );
		$new_tr.insertBefore($tfoot.find('tr').last());
		$('.flatpickr').flatpickr({
			enableTime: false,
			dateFormat: "d-m-Y",
			time_24hr: true
		});
	});

    $('table').delegate('.del-pembayaran', 'click', function() {
		$tr = $(this).parents('tr').eq(0);
		$tfoot = $tr.parent();
		$tr.remove();
		$list_tr = $tfoot.find('tr.row-bayar');
		$list_tr.each(function(i, el) {
			$(el).find('td').eq(0).text( i + 1 );
		});
		$table.find('.item-bayar').eq(0).trigger('keyup');
	});
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
