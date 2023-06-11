// $(document).ready(function () {

var dataTable;

dataTable = $(".table").DataTable({
        ajax: {
            url: "/user",
        },
        columns: [
            {
                data: "avatar",
                name: "avatar",
                searchable: false,
                sortable: false,
                width: "8%",
            },
            {
                data: "username",
                name: "username",
            },
            {
                data: "email",
                name: "email",
            },
            {
                data: "name",
                name: "name",
                width: "40%",
            },
            {
                data: "role_id",
                name: "role_id",
            },
            {
                data: "verified",
                name: "verified",
            },
            {
                data: "aksi",
                searchable: false,
                sortable: false,
                width: "15%",
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

    $("#email").on("blur", function () {
        var email = $(this).val();
        var loading = $(".spin-e");

        loading.removeClass("d-none");

        $("#email").removeClass("is-invalid").removeClass("is-valid");
        $("#cek-email")
            .removeClass("invalid-feedback")
            .removeClass("valid-feedback")
            .text("");

        $.ajax({
            url: "/check-email",
            type: "POST",
            dataType: "json",
            data: { email: email },
            success: function (data) {
                if (data.success == true) {
                    setTimeout(function () {
                        $("#email").addClass("is-valid");
                        $("#cek-email")
                            .addClass("valid-feedback")
                            .text(data.messages);
                        loading.addClass("d-none");
                    }, 500);
                } else {
                    console.log(data.messages);
                    setTimeout(function () {
                        $("#email").addClass("is-invalid");
                        $("#cek-email")
                            .addClass("error invalid-feedback")
                            .text(data.messages);
                        loading.addClass("d-none");
                    }, 500);
                }
            },
            error: function (data) {
                loading.addClass("d-none");
            },
        });
    });

    $("#username").on("blur", function () {
        var username = $(this).val();
        var loading = $(".spin-u");

        loading.removeClass("d-none");

        $("#username").removeClass("is-invalid").removeClass("is-valid");
        $("#cek-usr")
            .removeClass("invalid-feedback")
            .removeClass("valid-feedback")
            .text("");

        $.ajax({
            url: "/check-username",
            method: "POST",
            data: { username: username },
            success: function (data) {
                if (data.success == true) {
                    setTimeout(function () {
                        $("#username").addClass("is-valid");
                        $("#cek-usr")
                            .addClass("valid-feedback")
                            .text(data.messages);
                        loading.addClass("d-none");
                    }, 500);
                } else {
                    console.log(data.messages);
                    setTimeout(function () {
                        $("#username").addClass("is-invalid");
                        $("#cek-usr")
                            .addClass("error invalid-feedback")
                            .text(data.messages);
                        loading.addClass("d-none");
                    }, 500);
                }
            },
            error: function (data) {
                loading.addClass("d-none");
            },
        });
    });
// });

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
                    window.history.back();
                    window.location.href = '/user';
                    $('.table').DataTable.ajax.reload(null, false);
                });
            } else if (data.meta.code == 500) {
                swal.fire({
                    text: data.meta.message,
                    type: "error",
                });
            }
        },
        // error: function (xhr, status, error) {
        //     if (xhr.status === 422) {
        //         // Validation failed, handle error response
        //         var errors = xhr.responseJSON.errors;
        //         if (errors.hasOwnProperty("username")) {
        //             $("#username").prop("readonly", true);
        //         }

        //         if (errors.hasOwnProperty("email")) {
        //             $("#email").prop("readonly", true);
        //         }
        //         loopErrors(errors);
        //         return;
        //     }
        // },
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
              if (response.meta.message == "File Deleted") {
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

