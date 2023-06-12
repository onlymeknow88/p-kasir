// let modalKategori = "#modalKategori";
// let modalMenu = "#modalMenu";

$(document).ready(function () {
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
            console.log(data)
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
        // console.log($(this).parents("li").eq(0).attr("data-kategori-id"));
        if ($(this).hasClass("disabled")) return false;
        showFormKategori(
            "edit",
            $(this).parents("li").eq(0).attr("data-kategori-id")
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
            // editFormMenu("Edit", $list.data("id"));

            $bootbox = showFormMenu("edit", $list.data("id"));
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
                        })
                            .done((response) => {
                                // console.log(response);
                                if (response.meta.message == "Menu Deleted") {
                                    plugin.deleteList(item);
                                    if (
                                        $("#list_menu").find("li").length == 0
                                    ) {
                                        $("#list-kategori")
                                            .find(".list-group-item-primary")
                                            .click();
                                    }
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
        },

        // Drag end
        onChange: function (el) {
            list_data = $("#list_menu").wdiMenuEditor("serialize");
            data =
                JSON.stringify(list_data) +
                "&menu_kategori_id=" +
                $(".list-group-item-primary").attr("data-kategori-id");

            $.ajax({
                url: "/refrensi/kategori/u-barangkategori",
                type: "post",
                dataType: "json",
                data: "data=" + data,
                success: function (result) {
                    // console.log(result.meta.code)
                    if (result.meta.code != "200") {
                        alert("gagal");
                        // show_alert('Error !!!', data.message, 'error');
                    }
                },
                error: function (xhr) {
                    // show_alert('Error !!!', 'Ajax error, untuk detailnya bisa di cek di console browser', 'error');
                    console.log(xhr);
                },
            });
        },
    });

    $("#parent_id").select2({
        theme: "bootstrap-5",
        placeholder: $(this).data("placeholder"),
        dropdownParent: $("#modalMenu"),
        // minimumInputLength: 2,
        allowClear: true,
    });

    $(document).on("change", 'select[name="use_icon"]', function () {
        $this = $(this);
        if (this.value == 1) {
            $icon_preview = $(".icon-preview").show();
            $(".icon-preview").show().removeClass("d-none");

            var calass_name = $icon_preview.find("i").attr("class");
            $this.parent().find('[name="icon_class"]').val(calass_name);
        } else {
            $this.next().hide();
        }
    });

    $(document).on("click", ".icon-preview", function () {
        $bootbox.hide();
        $this = $(this);
        fapicker({
            iconUrl: "/assets/css/fontawesome/metadata/icons.yml",
            onSelect: function (elm) {
                $bootbox.show();
                var icon_class = $(elm).data("icon");
                // console.log(icon_class)
                $this.find("i").removeAttr("class").addClass(icon_class);
                $this.parent().find('[name="icon_class"]').val(icon_class);
            },
            onClose: function () {
                $bootbox.show();
            },
        });
    });

    $("#add-kategori").click(function (e) {
        e.preventDefault();
        $bootbox = showFormKategori();
    });

    $("#add-menu").click(function (e) {
        e.preventDefault();
        $bootbox = showFormMenu("add");
    });

    //-- Kategori

    dragKategori = dragula(
        [document.getElementById("list-kategori-container")],
        {
            accepts: function (el, target, source, sibling) {
                if (!sibling) return false;
                return true;
            },
            moves: function (el, container, handle) {
                return !el.classList.contains("uncategorized");
            },
        }
    );

    dragKategori.on("dragend", function (el) {
        urut = [];
        $("#list-kategori")
            .find("li")
            .each(function (i, el) {
                id_kategori = $(el).attr("data-kategori-id");
                if (id_kategori) {
                    urut.push(id_kategori);
                }
            });

        $.ajax({
            type: "POST",
            url: '/aplikasi/menuKategori/u-kategori',
            data: "id=" + JSON.stringify(urut),
            dataType: "json",
            success: function (data) {
                // if (data.status == "error") {
                //     show_alert("Error !!!", data.message, "error");
                // }
            },
            error: function (xhr) {
                // show_alert("Error !!!", xhr.responseText, "error");
                console.log(xhr.responseText);
            },
        });
    });
});

function showFormMenu(type = "add", id = "") {
    $bootbox = bootbox.dialog({
        title: type == "edit" ? "Edit Menu" : "Tambah Menu",
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

                    list_data = $("#list_menu").wdiMenuEditor("serialize");
                    menu_tree = JSON.stringify(list_data);

                    $.post({
                        url: $("#add-formMenu").attr("action"),
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
                                var nama_menu = $form_filled
                                    .find('input[name="nama_menu"]')
                                    .val();
                                var id = $form_filled
                                    .find('input[name="id"]')
                                    .val();
                                var use_icon = $form_filled
                                    .find('select[name="use_icon"]')
                                    .val();
                                var icon_class = $form_filled
                                    .find('input[name="icon_class"]')
                                    .val();
                                // console.log(id);

                                if (id) {
                                    $menu = $("#list-menu").find(
                                        '[data-id="' + id + '"]'
                                    );
                                    $menu
                                        .find(".menu-title:eq(0)")
                                        .text(nama_menu);
                                }

                                // add
                                else {
                                    $menu_container =
                                        $("#list_menu").children();
                                    $menu = $menu_container
                                        .children(":eq(0)")
                                        .clone();
                                    $menu.find("ol, ul").remove();
                                    $menu
                                        .find('[data-action="collapse"]')
                                        .remove();
                                    $menu
                                        .find('[data-action="expand"]')
                                        .remove();
                                    $menu.attr(
                                        "data-id",
                                        response.result.data.id
                                    );
                                    $menu.find(".menu-title").text(nama_menu);
                                }

                                $handler = $menu.find(".dd-handle:eq(0)");
                                $handler.find("i").remove();

                                if (use_icon == 1) {
                                    $handler.prepend(
                                        '<i class="' + icon_class + '"></i>'
                                    );
                                }

                                if (!id) {
                                    $menu_container.prepend($menu);
                                }

                                swal.fire({
                                    text: response.meta.message,
                                    type: "success",
                                }).then(function () {
                                    $bootbox.modal("hide");
                                    $(".menu-kategori-container")
                                        .find(".list-group-item-primary")
                                        .click();
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

    var url = "/aplikasi/menu/create?id=" + id;
    $.get(url, function (result) {
        // $button.prop('disabled', false);
        $bootbox.find(".modal-body").html(result);
        // $('#parent_id').val(result.).trigger('change');
        $("#parent_id").select2({
            theme: "bootstrap-5",
            dropdownParent: $(".bootbox"),
        });
    });
    return $bootbox;
}

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
                    var $form_filled = $bootbox.find("form");
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
                                nama_kategori = $form_filled
                                    .find('[name="nama_kategori"]')
                                    .val();

                                if (type == "edit") {
                                    $("#list-kategori")
                                        .find(
                                            'li[data-kategori-id="' + id + '"]'
                                        )
                                        .find(".text")
                                        .html(nama_kategori);
                                    // show_alert('Sukses !!!', data.message, 'success');
                                } else {
                                    $template = $(
                                        "#kategori-item-template"
                                    ).clone();
                                    $template.removeAttr("id");
                                    $template.attr(
                                        "data-kategori-id",
                                        response.result.data.id
                                    );
                                    $template.find(".text").html(nama_kategori);
                                    $template.insertBefore(".uncategorized");
                                    $template.fadeIn("fast");
                                }

                                swal.fire({
                                    text: response.meta.message,
                                    type: "success",
                                }).then(function () {
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

    var url = "/aplikasi/menuKategori/create?id=" + id;
    $.get(url, function (result) {
        // $button.prop('disabled', false);
        $bootbox.find(".modal-body").html(result);
    });
    return $bootbox;
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
