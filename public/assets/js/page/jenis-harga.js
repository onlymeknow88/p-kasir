var dataTable;

dataTable = $(".table").DataTable({
    ajax: {
        url: "/jenis-harga",
    },
    columns: [
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "nama_jenis_harga",
            name: "nama_jenis_harga",
        },
        {
            data: "deskripsi",
            name: "deskripsi",
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

$(document).ready(function() {

    $("body").delegate("form", "submit", function(e) {
        e.preventDefault();
        return false;
    });

    $("#add-jenis-harga").click(function(e) {
        e.preventDefault();
        $bootbox = showForm("add");
    });

    $("#form").on("keyup keypress", function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
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

                    list_data = $("#list_menu").wdiMenuEditor("serialize");
                    kategori_tree = JSON.stringify(list_data);

                    $.post({
                            url: $("#add-form").attr("action"),
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
                                    dataTable.ajax.reload();
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

    var url = "/jenis-harga/create?id=" + id;
    $.get(url, function(result) {
        // $button.prop('disabled', false);
        $bootbox.find(".modal-body").html(result);
    });
    return $bootbox;
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
