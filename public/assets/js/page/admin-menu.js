let modalKategori = "#modalKategori";
let modalMenu = "#modalMenu";

$(document).ready(function(){
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

    $(document).on('change', 'select[name="use_icon"]', function(){
        $this = $(this);
        if (this.value == 1)
		{
            $icon_preview = $('.icon-preview').show();
			$('.icon-preview').show().removeClass('d-none');

			var calass_name = $icon_preview.find('i').attr('class');
			$this.parent().find('[name="icon_class"]').val(calass_name);
		} else {
			$this.next().hide();
		}

    });

    $(document).on('click', '.icon-preview', function() {
		$bootbox.hide();
		$this = $(this);
		fapicker({
			iconUrl: '/assets/css/fontawesome/metadata/icons.yml',
			onSelect: function (elm) {
				$bootbox.show();
				var icon_class = $(elm).data('icon');
                // console.log(icon_class)
				$this.find('i').removeAttr('class').addClass(icon_class);
				$this.parent().find('[name="icon_class"]').val(icon_class);
			},
			onClose: function() {
				$bootbox.show();
			}
		});
	});



    $("#add-kategori").click(function (e) {
        e.preventDefault();
        $bootbox = showFormKategori();
    });

    $("#add-menu").click(function (e) {
        e.preventDefault();
        $bootbox = showFormMenu('add');
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

                    list_data = $('#list_menu').wdiMenuEditor('serialize');
                    menu_tree = JSON.stringify(list_data);

                    console.log($form_filled)

                    // $.post({
                    //     url: $("#add-formMenu").attr("action"),
                    //     data: formdata ? formdata : form.serialize(),
                    //     type: "POST",
                    //     dataType: "json",
                    //     contentType: false,
                    //     cache: false,
                    //     processData: false,
                    // })
                    //     .done((response) => {
                    //         console.log(response);
                    //         if (response.meta.code == 200) {
                    //             swal.fire({
                    //                 text: response.meta.message,
                    //                 type: "success",
                    //             }).then(function () {
                    //                 $bootbox.modal("hide");
                    //             });
                    //         } else if (response.meta.code == 500) {
                    //             swal.fire({
                    //                 text: response.meta.message,
                    //                 type: "error",
                    //             });
                    //         }
                    //     })
                    //     .fail((errors) => {
                    //         if (errors.status == 422) {
                    //             // console.log("Error:", errors.responseJSON.errors);
                    //             loopErrors(errors.responseJSON.errors);
                    //             return;
                    //         }
                    //     });
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
        $('#parent_id').select2({theme: 'bootstrap-5', dropdownParent: $(".bootbox")});


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
                    var form = $bootbox.find("form");
                    var formdata = false;
                    if (window.FormData) {
                        formdata = new FormData(form[0]);
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
                            console.log(response);
                            if (response.meta.code == 200) {
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

// function addFormMenu(url, title = "Tambah Menu") {
//     $(modalMenu).modal("show");
//     $(`${modalMenu} .modal-title`).text(title);
//     $(`${modalMenu} form`).attr("action", url);
//     $(`${modalMenu} [name=_method]`).val("post");

//     // resetForm(`${modalMenu} form`);
// }

// functyion editFormMenu(type = "", id = "") {
//     var url = "/aplikasi/menu/" + id;
//     url = url.replace(":id", id);
//     $.get("/aplikasi/menu/" + id + "/s-menu")
//         .done((response) => {
//             console.log(response.data);
//             $(modalMenu).modal("show");
//             $(`${modalMenu} .modal-title`).text(type);
//             $(`${modalMenu} form`).attr("action", url);
//             $(`${modalMenu} [name=_method]`).val("put");

//             var options = new Option(
//                 response.data.parent == null
//                     ? "Tidak ada parent menu"
//                     : response.data["parent"].nama_menu,
//                 response.data.parent == null
//                     ? "Tidak ada parent menu"
//                     : response.data["parent"].id,
//                 true,
//                 true
//             );
//             $("#parent_id").append(options).change();

//             var option = new Option(
//                 response.data.class,
//                 response.data.class,
//                 true,
//                 true
//             );
//             $("#icon_class").append(option).change();

//             var aktif = $("input[name=aktif");
//             if (response.data.aktif == "Y") {
//                 aktif.attr("checked", "checked");
//             } else {
//                 aktif.removeAttr("checked", "checked");
//             }

//             $("#menu_status_id").val(response.data.menu_status_id).change();

//             resetForm(`${modalMenu} form`);
//             loopForm(response.data);
//         })
//         .fail((errors) => {
//             alert("Tidak dapat menampilkan data");
//             return;
//         });
// }

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
