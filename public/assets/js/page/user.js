// Use a server-side processing script to fetch data and handle pagination, sorting, and filtering on the server side
var dataTable = $(".table").DataTable({
    serverSide: true,
    ajax: {
        url: "/user",
    },
    columns: [
        {
            data: "avatar",
            name: "avatar",
            searchable: false,
            sortable: false,
            width: "50px",
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
            width: "150px",
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
    error: function (xhr, error, thrown) {
        // Handle errors or exceptions that may occur during the AJAX request or table rendering process
        console.log("Error:", error);
    },
});

$("#email").on("blur", function () {
    var email = $(this).val();
    var loading = $(".spin-e");
    var emailInput = $("#email");
    var feedback = $("#cek-email");
    loading.removeClass("d-none");
    $.ajax({
        url: "/check-email",
        type: "POST",
        dataType: "json",
        data: { email: email },
        success: function (data) {
            if (data.success == true) {
                emailInput.removeClass("is-invalid").addClass("is-valid");
                feedback
                    .removeClass("invalid-feedback")
                    .addClass("valid-feedback")
                    .text(data.messages);
            } else {
                console.log(data.messages);
                emailInput.removeClass("is-valid").addClass("is-invalid");
                feedback
                    .removeClass("valid-feedback")
                    .addClass("error invalid-feedback")
                    .text(data.messages);
            }
        },
        error: function (data) {
            console.error(data);
            feedback
                .removeClass("valid-feedback")
                .addClass("error invalid-feedback")
                .text(
                    "An error occurred while checking the email. Please try again later."
                );
        },
        complete: function () {
            loading.addClass("d-none");
        },
    });
});

var usernameInput = $("#username");
var loadingSpinner = $(".spin-u");
var feedbackElement = $("#cek-usr");
usernameInput.on("blur", function () {
    var username = usernameInput.val();
    loadingSpinner.removeClass("d-none");
    usernameInput.removeClass("is-invalid is-valid");
    feedbackElement.removeClass("invalid-feedback valid-feedback").text("");
    $.ajax({
        url: "/check-username",
        method: "POST",
        data: { username: username },
        success: function (data) {
            if (data.success) {
                usernameInput.addClass("is-valid");
                feedbackElement.addClass("valid-feedback").text(data.messages);
            } else {
                console.log(data.messages);
                usernameInput.addClass("is-invalid");
                feedbackElement
                    .addClass("error invalid-feedback")
                    .text(data.messages);
            }
        },
        error: function (data) {
            console.error(data);
        },
        complete: function () {
            loadingSpinner.addClass("d-none");
        },
    });
});
// });

function submitForm(url) {
    var $id = $("#id");
    var id = $id.val();
    if (id) {
        $("input[name='_method']").attr("value", "patch");
    }
    var $file = $(".file");
    var file = $file[0].files[0];
    var _URL = window.URL || window.webkitURL;
    var img = new Image();
    var imgwidth = 0;
    var imgheight = 0;
    var maxwidth = 150;
    var maxheight = 150;
    var $form = $("#form");
    var form = $form[0];
    var formdata = new FormData(form);
    img.src = _URL.createObjectURL(file);
    img.onload = function () {
        imgwidth = this.width;
        imgheight = this.height;
        if (imgwidth <= maxwidth && imgheight <= maxheight) {
            $.ajax({
                data: formdata,
                url: url,
                type: "POST",
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.meta.code == 200) {
                        swal.fire({
                            text: data.meta.message,
                            type: "success",
                        }).then(function () {
                            window.history.back();
                            var $table = $(".table");
                            var table = $table.DataTable();
                            var indexes = table
                                .rows()
                                .eq(0)
                                .filter(function (rowIdx) {
                                    return table.cell(rowIdx, 0).data() ===
                                        data.id
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
                    }
                },
            });
        }else {
            Swal.fire(
                "Failed to process image.",
                "Refresh & reupload!",
                "error"
            );
        }
    };
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
