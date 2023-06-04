let modalKategori = "#modalKategori";
let modalMenu = "#modalMenu";

$(function () {
    $("body").delegate("form", "submit", function (e) {
        e.preventDefault();
        return false;
    });

    $(".kategori-container").delegate(".kategori-item", "click", function () {
        var $this = $(this);
        // if ($this.hasClass('processing'));
        // return false;

        var id_kategori = $this.attr("data-kategori-id");

        var $list_menu = $("#list_menu");
        var $group_container = $(".menu-kategori-container");

        $group_container.find("li").removeClass("list-group-item-primary");
        $this.addClass("list-group-item-primary");

        $list_menu.empty();
        $loader = $(
            '<div class="text-center"><div class="spinner-border text-secondary"></div></div>'
        ).appendTo($list_menu);

        $.get("/aplikasi/menu/kategori/" + id_kategori, function (data) {
            $loader.remove();
            if (data) {
                $("#list_menu").html(data);
            } else {
                $("#list_menu").html(
                    '<div class="alert alert-danger">Data tidak ditemukan</div>'
                );
            }

            $("#list_menu").wdiMenuEditor("customInit");
        });
    });

    $(".kategori-container").delegate(".btn-edit", "click", function (e) {
        e.stopPropagation();
        if ($(this).hasClass("disabled")) return false;
        showFormKategori(
            "edit",
            $(this).parents("li").attr("data-kategori-id")
        );
        return false;
    });

    $(".kategori-container").delegate(".btn-remove", "click", function (e) {
        e.stopPropagation();
        $this = $(this);

        $li = $this.parents("li");
        $li.addClass("list-group-item-secondary");
        $li.find("a").prop("disabled", true);
        $li.find("a").addClass("disabled");
        $li.prepend('<div class="custom-loader"></div>');

        var kategori_id = $li.attr("data-kategori-id");
        // console.log(kategori_id);
        refresh = false;
        if ($li.hasClass("list-group-item-primary")) {
            refresh = true;
        }

        $.post("/aplikasi/menuKategori/" + kategori_id, {
            _method: "delete",
            // '_token': `{{ csrf_token() }}`
        })
            .done((response) => {
                // if (response.meta.message == "File Deleted") {
                $li.fadeOut("fast", function () {
                    $li.remove();

                    $li.remove();
                    if (refresh) {
                        $("#list-kategori").find("li").eq(0).click();
                    }
                });
                // }
            })
            .fail((errors) => {
                Swal.fire(
                    "Something went wrong.",
                    "You clicked the button!",
                    "error"
                );
                return;
            });
    });

    $("#form").on("keyup keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $("#list_menu").wdiMenuEditor({
        expandBtnHTML:
            '<button data-action="expand" class="fa fa-plus" type="button">Expand</button>',
        collapseBtnHTML:
            '<button data-action="collapse" class="fa fa-minus" type="button">Collapse</button>',
        editBtnCallback: function ($list) {
            // console.log($list.data('id'))
            editFormMenu("Edit", $list.data("id"));
        },
        beforeRemove: function (item, plugin) {
            list_data = $("#list_menu").wdiMenuEditor("serialize");
            menu_tree = JSON.stringify(list_data);

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
                        $.post("/aplikasi/menu/" + item.attr("data-id"), {
                            _method: "delete",
                            // '_token': `{{ csrf_token() }}`
                        })
                            .done((response) => {
                                // console.log(response);
                                if (response.meta.message == "File Deleted") {
                                    plugin.deleteList(item);
                                    if (
                                        $("#list_menu").find("li").length == 0
                                    ) {
                                        $("#list-kategori")
                                            .find(".list-group-item-primary")
                                            .click();
                                    }
                                    // window.location.href = location.pathname;
                                }
                                // table.ajax.reload();
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
        },
    });

    $("#parent_id").select2({
        theme: "bootstrap-5",
        placeholder: $(this).data("placeholder"),
        dropdownParent: $("#modalMenu"),
        // minimumInputLength: 2,
        allowClear: true,
    });

    $("#use_icon").on("change", function () {
        var use_icon = $(this).val();
        if (use_icon == "Y") {
            $("#icon_class").removeClass("d-none");

            $("#icon_class").select2({
                theme: "bootstrap-5",
                placeholder: $(this).data("placeholder"),
                containerCssClass: "use_icon",
                dropdownParent: $("#modalMenu"),
                ajax: {
                    url: "{{ asset('assets/css/fontawesome/icons.json') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (index, item) {
                                if (index.styles == "solid") {
                                    var icon_label = "fa";
                                } else if (index.styles == "brands") {
                                    var icon_label = "fab";
                                } else if (index.styles[1] == "regular") {
                                    var icon_label = "far";
                                }

                                return {
                                    id: item,
                                    text:
                                        '<i class="' +
                                        icon_label +
                                        " fa-" +
                                        item +
                                        ' me-2"></i> ' +
                                        item,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        } else if (use_icon == "N") {
            $(".use_icon").addClass("d-none");
        }
    });
});

// function addFormKategori(url, title = 'Tambah Kategori') {
// $(modalKategori).modal('show');
// $(`${modalKategori} .modal-title`).text(title);
// $(`${modalKategori} form`).attr('action', url);
// $(`${modalKategori} [name=_method]`).val('post');

// resetForm(`${modalKategori} form`);
// }

$("#add-kategori").click(function (e) {
    e.preventDefault();
    $bootbox = showFormKategori();
});

function showFormKategori(type = "add", id = "") {
    var $button = "";
    var $bootbox = "";
    var $button_submit = "";

    $bootbox = bootbox.dialog({
        title: type == "edit" ? "Edit Kategori" : "Tambah Kategori",
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
                    var form = $bootbox.find("form");
                    var formdata = false;
                    if (window.FormData) {
                        formdata = new FormData(form[0]);
                    }

                    $.post({
                        url: $('#add-form').attr("action"),
                        data: formdata ? formdata : form.serialize(),
                        type: 'POST',
                        dataType: "json",
                        contentType: false,
                        cache: false,
                        processData: false,
                    })
                        .done((response) => {
                            console.log(response);
                            if (response.meta.code == 200) {
                                swal.fire({
                                    text: response.meta.message,
                                    type: "success",
                                }).then(function () {
                                    $bootbox.modal('hide');
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
    if(id) {
        var url = $("#add-kategori").attr("href");
    } else {
        var url = '/aplikasi/menukategori/'+id+'/edit';
    }
    $.get(url, function (result) {
        // console.log()
        // $button.prop('disabled', false);
        $bootbox.find(".modal-body").html(result);
    });
    return $bootbox;
}

function addFormMenu(url, title = "Tambah Menu") {
    $(modalMenu).modal("show");
    $(`${modalMenu} .modal-title`).text(title);
    $(`${modalMenu} form`).attr("action", url);
    $(`${modalMenu} [name=_method]`).val("post");

    // resetForm(`${modalMenu} form`);
}

function editFormKategori(type, id = "") {
    console.log(id);

    var title = type == "edit" ? "Edit Menu" : "Tambah Menu";

    $.get("/aplikasi/menu/" + id)
        .done((response) => {
            $(modalKategori).modal("show");
            $(`${modalKategori} .modal-title`).text(title);
            $(`${modalKategori} form`).attr("action", url);
            $(`${modalKategori} [name=_method]`).val("put");

            // resetForm(`${modal} form`);
            loopForm(response.data);
        })
        .fail((errors) => {
            alert("Tidak dapat menampilkan data");
            return;
        });
}

function editFormMenu(type = "", id = "") {
    var url = "/aplikasi/menu/" + id;
    url = url.replace(":id", id);
    $.get("/aplikasi/menu/" + id + "/s-menu")
        .done((response) => {
            $(modalMenu).modal("show");
            $(`${modalMenu} .modal-title`).text(type);
            $(`${modalMenu} form`).attr("action", url);
            $(`${modalMenu} [name=_method]`).val("put");

            var options = new Option(
                response.data.parent == null
                    ? "Tidak ada parent menu"
                    : response.data["parent"].nama_menu,
                response.data.parent == null
                    ? "Tidak ada parent menu"
                    : response.data["parent"].id,
                true,
                true
            );
            $("#parent_id").append(options).change();

            var option = new Option(
                response.data.class,
                response.data.class,
                true,
                true
            );
            $("#icon_class").append(option).change();

            var aktif = $("input[name=aktif");
            if (response.data.aktif == "Y") {
                aktif.attr("checked", "checked");
            } else {
                aktif.removeAttr("checked", "checked");
            }

            resetForm(`${modalMenu} form`);
            loopForm(response.data);
        })
        .fail((errors) => {
            alert("Tidak dapat menampilkan data");
            return;
        });
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
                    $(modalKategori).modal("hide");
                    $(modalMenu).modal("hide");
                    // table.ajax.reload();
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

    $("#aktif").removeAttr("checked", "checked");

    $("#parent_id").val("").trigger("change");
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
