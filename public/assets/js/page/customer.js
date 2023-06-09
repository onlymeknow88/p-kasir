var dataTable;

dataTable = $(".table").DataTable({
    ajax: {
        url: "/customer",
    },
    columns: [
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "nama_customer",
            name: "nama_customer",
        },
        {
            data: "alamat_customer",
            name: "alamat_customer",
        },
        {
            data: "email",
            name: "email",
        },
        {
            data: "no_telp",
            name: "no_telp",
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
        data: formdata ? formdata : form.serialize(),
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
                    // window.history.back();
                    window.location.href = '/customer';
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
