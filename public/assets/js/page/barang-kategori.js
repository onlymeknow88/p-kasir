$(document).ready(function() {

    $("body").delegate("form", "submit", function(e) {
        e.preventDefault();
        return false;
    });

    $("#add-kategori").click(function(e) {
        e.preventDefault();
        $bootbox = showForm("add");
    });

    $("#list_menu").wdiMenuEditor({
        expandBtnHTML: '<button data-action="expand" class="fa fa-plus" type="button">Expand</button>',
        collapseBtnHTML: '<button data-action="collapse" class="fa fa-minus" type="button">Collapse</button>',
        editBtnCallback: function($list) {
            $bootbox = showForm('edit', $list.data('id'));
        },
        beforeRemove: function(item, plugin) {
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
                function(e) {
                    if (e.value === true) {
                        $.post("/refrensi/kategori/" + item.attr("data-id"), {
                                _method: "delete",
                            })
                            .done((response) => {
                                // console.log(response);
                                if (response.meta.message == "Kategori Deleted") {
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
                function(dismiss) {
                    return false;
                }
            );
        },

        onChange: function(el) {
            list_data = $("#list_menu").wdiMenuEditor("serialize");
            data = JSON.stringify(list_data) +
                "&barang_kategori_id=" +
                $(".list-group-item-primary").attr("data-kategori-id");;

            // console.log(data);
            $.ajax({
                url: "/refrensi/kategori/u-kategori",
                type: "post",
                dataType: "json",
                data: "data=" + data,
                success: function(result) {
                    // console.log(result.meta.code)
                    if (result.meta.code != "200") {
                        alert("gagal");
                        // show_alert('Error !!!', data.message, 'error');
                    }
                },
                error: function(xhr) {
                    // show_alert('Error !!!', 'Ajax error, untuk detailnya bisa di cek di console browser', 'error');
                    console.log(xhr);
                },
            });
        },
    });

    $("#parent_id").select2({
        theme: "bootstrap-5",
        placeholder: $(this).data("placeholder"),
        dropdownParent: $("#modalKategori"),
        // minimumInputLength: 2,
        allowClear: true,
    });

    $("#form").on("keyup keypress", function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $(document).on("change", 'select[name="use_icon"]', function() {
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

    $(document).on("click", ".icon-preview", function() {
        $bootbox.hide();
        $this = $(this);
        fapicker({
            iconUrl: "/assets/css/fontawesome/metadata/icons.yml",
            onSelect: function(elm) {
                $bootbox.show();
                var icon_class = $(elm).data("icon");
                // console.log(icon_class)
                $this.find("i").removeAttr("class").addClass(icon_class);
                $this.parent().find('[name="icon_class"]').val(icon_class);
            },
            onClose: function() {
                $bootbox.show();
            },
        });
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
                                var nama_kategori = $form_filled.find('input[name="nama_kategori"]')
                                    .val();
                                var id = $form_filled.find('input[name="id"]').val();
                                var use_icon = $form_filled.find('select[name="use_icon"]').val();
                                var icon_class = $form_filled.find('input[name="icon_class"]')
                                    .val();

                                // edit
                                if (id) {
                                    $list_kategori = $('#list_menu').find('[data-id="' + id + '"]');
                                    $list_kategori.find('.menu-title:eq(0)').text(nama_kategori);
                                    $handler = $list_kategori.find('.dd-handle:eq(0)');
                                }
                                // add
                                else {
                                    $menu_container =
                                        $("#list_menu").children();
                                    $new_kategori = $menu_container
                                        .children(":eq(0)")
                                        .clone();
                                    $new_kategori.find("ol, ul").remove();
                                    $new_kategori
                                        .find('[data-action="collapse"]')
                                        .remove();
                                    $new_kategori
                                        .find('[data-action="expand"]')
                                        .remove();
                                    $new_kategori.attr(
                                        "data-id",
                                        response.result.data.id
                                    );
                                    $new_kategori.find(".menu-title").text(nama_kategori);
                                    $handler = $new_kategori.find('.dd-handle:eq(0)');
                                }

                                // console.log($new_kategori);

                                $handler.find('i').remove();

                                if (use_icon == 1) {
                                    $handler.prepend('<i class="' + icon_class + '"></i>');
                                }

                                if (!id) {
                                    $menu_container.prepend($new_kategori);
                                }

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

    var url = "/refrensi/kategori/create?id=" + id;
    $.get(url, function(result) {
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
