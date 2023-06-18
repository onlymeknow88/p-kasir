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
        },
        {
            data: "nama_barang",
            name: "nama_barang",
        },
        {
            data: "deskripsi",
            name: "deskripsi",
        },
        {
            data: "barcode",
            name: "barcode",
        },
        {
            data: "stok",
            name: "stok",
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

    $('.barcode').keyup(function() {
		value = this.value.replace(/\D/g, '');
		this.value = value;
		if (value.length > 13) {
			this.value = value.substr(0, 13);
		}
		$('.jml-digit').text(this.value.length)

	});

	$('.generate-barcode').click(function()
	{
		$this = $(this).prop('disabled', true);
		$input = $this.prev().prop('disabled', true);
		$parent = $this.parent().parent();
		$spinner = $parent.find('.spinner').show();

		$.ajax({
			url: '/barang/GenerateBarcodeNumber',
			success: function(data) {
				console.log(data);
				$this.prop('disabled', false);
				$input.prop('disabled', false).val(data).trigger('keyup');
				$spinner.hide();
			}, error: function() {

			}
		})
	});

});

function showForm(type = "add", id = "") {
    $bootbox = bootbox.dialog({
        title: type == "edit" ? "Edit Kategori" : "Tambah Kategori",
        message: '<div class="text-center"><div class="spinner-border text-secondary" role="status"></div>',
        buttons: {
            cancel: {
                label: "Cancel",
                className: "btn-link",
            },
            success: {
                label: "Submit",
                className: "btn-primary submit",
                callback: function() {
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
                                }).then(function() {
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
    $.get(url, function(result) {
        // $button.prop('disabled', false);
        $bootbox.find(".modal-body").html(result);
        // $('#parent_id').val(result.).trigger('change');
    });
    return $bootbox;
}
