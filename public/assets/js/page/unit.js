var dataTable;

dataTable = $(".table").DataTable({
    ajax: {
        url: "/refrensi/unit",
    },
    columns: [
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
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

$(document).ready(function () {
    $("body").delegate("form", "submit", function (e) {
        e.preventDefault();
        return false;
    });

    $("#add-unit").click(function (e) {
        e.preventDefault();
        $bootbox = showForm("add");
    });
});

function showForm(type = "add", id = "") {
    $bootbox = bootbox.dialog({
        title: type == "edit" ? "Edit Unit" : "Tambah Unit",
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
                        url: $("#add-formUnit").attr("action"),
                        data: formdata ? formdata : form.serialize(),
                        type: "POST",
                        dataType: "json",
                        contentType: false,
                        cache: false,
                        processData: false,
                    })
                        .done((response) => {
                            if (response.meta.code == 200) {

                                swal.fire({
                                    text: response.meta.message,
                                    type: "success",
                                }).then(function () {
                                    $bootbox.modal("hide");
                                    dataTable.ajax.reload();
                                });
                            } else if (response.meta.code == 500) {
                                swal.fire({
                                    text: response.meta.message,
                                    type: "error",
                                });
                            }
                        })
                        .fail((xhr, status, error) => {
                            if (xhr.status == 422) {
                                // console.log("Error:", errors.responseJSON.errors);
                                loopErrors(xhr.responseJSON.errors);
                                return;
                            }
                        });
                    return false;
                },
            },
        },
    });

    var url = "/refrensi/unit/create?id=" + id;
    $.get(url, function (result) {
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
              if (response.meta.message == "Unit Deleted") {
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
