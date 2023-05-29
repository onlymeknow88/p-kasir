let table;
let modal = "#modal-form";

$(function () {
    table = $(".table").DataTable({
        ajax: {
            url: "/member/data",
        },
        columns: [
            { data: "select_all", searchable: false, sortable: false },
            { data: "DT_RowIndex", searchable: false, sortable: false },
            { data: "kode_member" },
            { data: "nama" },
            { data: "telepon" },
            { data: "alamat" },
            { data: "aksi", searchable: false, sortable: false },
        ],
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        ordering: false,
        language: {
            paginate: {
                next: ">", // or '→'
                previous: "<", // or '←'
            },
        },
    });

    $("#form").on("keyup keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $("[name=select_all]").on("click", function () {
        $(":checkbox").prop("checked", this.checked);
    });
});

function addForm(url, title = "Tambah") {
    $(modal).modal("show");
    $(`${modal} .modal-title`).text(title);
    $(`${modal} form`).attr("action", url);
    $(`${modal} [name=_method]`).val("post");

    resetForm(`${modal} form`);
}

function submitForm(originalForm) {
    $.post({
        url: $(originalForm).attr("action"),
        data: new FormData(originalForm),
        // type: 'POST',
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
                }).then(function () {
                    $(modal).modal("hide");
                    table.ajax.reload();
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
}

function resetForm(selector) {
    $(selector)[0].reset();

    $(".select2").trigger("change");
    $(
        ".form-control, .custom-select, [type=radio], [type=checkbox], [type=file], .select2, .note-edito"
    ).removeClass("is-invalid");
    $(".invalid-feedback").remove();
}

function loopForm(originalForm) {
    for (field in originalForm) {
        if ($(`[name=${field}]`).attr("type") != "file") {
            if ($(`[name=${field}]`).hasClass("summernote")) {
                $(`[name=${field}]`).summernote("code", originalForm[field]);
            } else if ($(`[name=${field}]`).attr("type") == "radio") {
                $(`[name=${field}]`)
                    .filter(`[value="${originalForm[field]}"]`)
                    .prop("checked", true);
            } else {
                $(`[name=${field}]`).val(originalForm[field]);
            }

            $("select").trigger("change");
        } else {
            $(`.preview-${field}`).attr("src", originalForm[field]).show();
        }
    }
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

function cetakMember(url) {
    var allVals = [];
    $("input:checked").each(function () {
        allVals.push($(this).attr("id"));
    });
    // console.log(allVals);
    if ($("input:checked").length < 1) {
        alert("Pilih data yang akan dicetak");
        return;
    } else {
        var join_selected_values = allVals.join(",");
        $.post({
            url: url,
            data: "id=" + join_selected_values,
        }).done((response) => {});
    }
}
