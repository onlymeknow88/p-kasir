jQuery(document).ready(function () {
    // Select the buttons with ids "print", "export-pdf", and "export-word"
    const button = $("#print, #export-pdf, #export-word");
    // Select the container with id "barcode-print-container"
    let $container = $("#barcode-print-container");
    // Define the conversion rate from millimeters to pixels
    const pixel = 3.7795275591; // 1 mm => pixel
    // Define the conversion rate from pixels to millimeters
    const milimeter = 0.2645833333; // 1 px => mm

    // Listen for keyup events on paper size width and height inputs
    $("#paper-size-width, #paper-size-height").keyup(function () {
        // Convert input value to integer
        this.value = setInt(this.value);
        // Limit input value to maximum of 300
        if (this.value > 300) {
            this.value = 300;
        }
        // Calculate width and height based on input values and pixel size
        var pixel = 10;
        var w = parseInt($("#paper-size-width").val()) * pixel;
        var h = parseInt($("#paper-size-height").val()) * pixel;
        // Set container width to calculated width
        var $container = $("#barcode-print-container");
        $container.css("width", w);
        // If container has a canvas element, set minimum height to calculated height
        if ($container.find("canvas").eq(0).length) {
            $container.css({ minHeight: h });
        }
    });

    // This code listens for the blur event on two input fields with ids "paper-size-width" and "paper-size-height"
    $("#paper-size-width, #paper-size-height").blur(function () {
        // If the value of the input field is less than 100, set it to 100
        if (this.value < 100) {
            this.value = 100;
        }
    });

    // This function is called when the paper size changes
    $("#paper-size").change(function () {
        // Initialize variables
        let w = 0;
        let h = 0;
        // Disable paper width and height inputs
        const $paper_width = $("#paper-size-width").attr(
            "disabled",
            "disabled"
        );
        const $paper_height = $("#paper-size-height").attr(
            "disabled",
            "disabled"
        );
        // Set paper width and height based on selected value
        if (this.value == "a4") {
            w = 210;
            h = 297;
        } else if (this.value == "f4") {
            w = 215;
            h = 330;
        } else {
            w = 210;
            h = 297;
            // Enable paper width and height inputs
            $paper_width.removeAttr("disabled");
            $paper_height.removeAttr("disabled");
        }
        // Set paper width and height inputs to selected value
        paper_width = $paper_width.val(w);
        paper_height = $paper_height.val(h);
        // Convert width and height to pixels and set container width
        w = w * pixel;
        h = h * pixel;
        const $container = $("#barcode-print-container");
        $container.css("width", w);
        // Set container minimum height if canvas element exists
        if ($container.find("canvas").eq(0).length) {
            $container.css({ minHeight: h });
        }
        // Call generateBarcode function
        // generateBarcode();
    });

    $(".barcode").keypress(function (e) {
        if (e.which == 13) {
            return false;
        }
    });

    // Delegate click event to table rows with class "del-row"
    $("table").delegate(".del-row", "click", function () {
        // Set $this variable to the clicked element
        $this = $(this);
        // Set $table variable to the parent table element
        $table = $this.parents("table");
        // Set $tbody variable to the first tbody element within the table
        $tbody = $table.find("tbody").eq(0);
        // Set $trs variable to all tr elements within the tbody
        $trs = $tbody.find("tr");
        // Set id variable to the id attribute of the table
        id = $table.attr("id");
        // If there is only one row in the table
        if ($trs.length == 1) {
            // Clear input values within the row
            $trs.find("input").val("");
            // Hide the parent element of the tbody
            $tbody.parent().hide();
            // If the table is the "list-pembayaran" table
            if (id == "list-pembayaran") {
                // Set the value of the "using-pembayaran" input to 0
                $("#using-pembayaran").val(0);
                // If the table is the "list-barang" table
            } else if (id == "list-barang") {
                // Set the value of the "using-list-barang" input to 0
                $("#using-list-barang").val(0);
            }
            // If there is more than one row in the table
        } else {
            // Remove the parent tr element of the clicked element
            $this.parents("tr").eq(0).remove();
            // Set $new_trs variable to all tr elements within the tbody
            $new_trs = $tbody.find("tr");
            // For each tr element within $new_trs
            $new_trs.each(function (i, elm) {
                // Set the text of the first td element to the row number
                $(elm)
                    .find("td")
                    .eq(0)
                    .html(i + 1);
            });
        }
        // If the table is the "list-pembayaran" table
        if (id == "list-pembayaran") {
            // Trigger the "keyup" event on the first element with class "item-bayar" within the tbody
            $tbody.find(".item-bayar").eq(0).trigger("keyup");
            // If the table is the "list-barang" table
        } else if (id == "list-barang") {
            // Trigger the "keyup" event on the first element with class "harga-satuan" within the tbody
            $tbody.find(".harga-satuan").eq(0).trigger("keyup");
        }
        // Call the generateBarcode function
        generateBarcode();
    });

    // Click event for add-barang button
    $(".add-barang").click(function () {
        // Store the clicked element in a variable
        $this = $(this);
        // If the element has a disabled class, return false and do nothing
        if ($this.hasClass("disabled")) {
            return false;
        }
        // Create a modal with specific properties
        var $modal = jwdmodal({
            title: "Pilih Barang",
            url: "/barcode-cetak/getDataDTListBarang",
            width: "650px",
            // Action to be performed when the modal is closed
            action: function () {
                // Find the table containing the list of selected items
                $table = $("#list-barang");
                // Find all the rows in the table
                $trs = $table.find("tbody").eq(0).find("tr");
                // Create a variable to store the selected items
                var list_barang =
                    '<span class="belum-ada mb-2">Silakan pilih barang</span>';
                // If the table is visible, create a list of selected items
                if ($table.is(":visible")) {
                    var list_barang = "";
                    $trs.each(function (i, elm) {
                        $td = $(elm).find("td");
                        list_barang +=
                            '<small  class="px-3 py-2 me-2 mb-2 text-light bg-success bg-opacity-10 border border-success border-opacity-10 rounded-2">' +
                            $td.eq(1).html() +
                            "</small>";
                    });
                }
                // Add the list of selected items to the modal header
                $(".jwd-modal-header-panel").prepend(
                    '<div class="list-barang-terpilih">' +
                        list_barang +
                        "</div>"
                );
            },
        });
        // Add a click event to all pilih-barang buttons
        $(document)
            .undelegate(".pilih-barang", "click")
            .delegate(".pilih-barang", "click", function () {
                // Set a value to a specific input field
                $("#using-list-barang").val(1);
                // Find the table containing the list of selected items
                $table = $("#list-barang");
                // Get the selected item's details
                $tr = $(this).parents("tr").eq(0);
                $td = $tr.find("td");
                barang = JSON.parse($tr.find(".detail-barang").text());
                // Add the selected item to the list of selected items
                $tbody = $table.find("tbody").eq(0);
                $trs = $tbody.find("tr");
                $tr = $trs.eq(0).clone();
                num = $trs.length;
                if ($table.is(":hidden")) {
                    $trs.remove();
                    num = 0;
                }
                $td = $tr.find("td");
                $td.eq(0).html(num + 1);
                $td.eq(1).html(barang.nama_barang);
                $td.eq(2).html(barang.barcode);
                $tr.find(".jml-cetak").val(10);
                $table.show();
                $tbody.append($tr);
                // Add the selected item to the list of selected items in the modal header
                $(".list-barang-terpilih").find(".belum-ada").remove();
                $(".list-barang-terpilih").append(
                    '<small  class="px-3 py-2 me-2 mb-2 text-light bg-success bg-opacity-10 border border-success border-opacity-10 rounded-2">' +
                        barang.nama_barang +
                        "</small>"
                );
                // Enable the preview button
                $("#preview").removeAttr("disabled");
                // Generate barcodes for the selected items
                generateBarcode();
                // $(document);
            });
    });

    // Handle keyup event on barcode input field
    $(".barcode").keyup(function (e) {
        // Get the current input element and its value
        let $this = $(this);
        let value = $this.val().replace(/\D/g, "");
        this.value = value.substr(0, 13);
        // If the value has at least 13 characters
        if (value.length >= 13) {
            value = value.substr(0, 13);
            // Create a spinner element
            let $spinner = $(
                '<div class="spinner-border text-secondary spinner" style="height: 18px; width:18px; position:absolute; right:15px; top:7px" role="status"><span class="visually-hidden">Loading...</span></div>'
            );
            // Add spinner to the parent element and disable input
            let $parent = $this.parent();
            $parent.find(".spinner").remove();
            $spinner.appendTo($parent);
            $this.attr("disabled", "disabled");
            $(".add-barang").attr("disabled", "disabled").addClass("disabled");
            // Make an AJAX request to get data based on the barcode value
            $.ajax({
                url: "/barcode-cetak/ajaxGetBarangByBarcode?code=" + value,
                success: function (data) {
                    // Remove the spinner and enable input
                    $parent.find(".spinner").remove();
                    $this.removeAttr("disabled");
                    $(".add-barang")
                        .removeAttr("disabled")
                        .removeClass("disabled");
                    // If data is found, display it in the table
                    if (data.status == "ok") {
                        let $table = $("#list-barang");
                        let barang = data.data;
                        // List barang
                        let $tbody = $table.find("tbody").eq(0);
                        let $trs = $tbody.find("tr");
                        let $tr = $trs.eq(0).clone();
                        let num = $trs.length;
                        if ($table.is(":hidden")) {
                            $trs.remove();
                            num = 0;
                        }
                        let $td = $tr.find("td");
                        $td.eq(0).html(num + 1);
                        $td.eq(1).html(barang.nama_barang);
                        $td.eq(2).html(barang.barcode);
                        $tr.find(".jml-cetak").val(10);
                        $table.show();
                        $tbody.append($tr);
                        generateBarcode();
                    } else {
                        // Show a toast with an error message
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: "color-red text-white p-2 mb-2",
                            },
                            didOpen: (toast) => {
                                toast.addEventListener(
                                    "mouseenter",
                                    Swal.stopTimer
                                );
                                toast.addEventListener(
                                    "mouseleave",
                                    Swal.resumeTimer
                                );
                            },
                        });
                        Toast.fire({
                            html: '<div class="toast-content"><i class="far fa-check-circle me-2"></i> Data tidak ditemukan</div>',
                        });
                    }
                },
                error: function () {},
            });
        }
    });

    // delegate click event to table element with class 'del-row'
    $("table").delegate(".del-row", "click", function () {
        // assign 'this' to variable '$this'
        $this = $(this);
        // find parent table of '$this' and assign it to variable '$table'
        $table = $this.parents("table");
        // find first tbody element in '$table' and assign it to variable '$tbody'
        $tbody = $table.find("tbody").eq(0);
        // find all tr elements in '$tbody' and assign them to variable '$trs'
        $trs = $tbody.find("tr");
        // get value of 'id' attribute of '$table' and assign it to variable 'id'
        id = $table.attr("id");
        // if there is only one row in the table
        if ($trs.length == 1) {
            // clear input value of the row
            $trs.find("input").val("");
            // hide parent element of '$tbody'
            $tbody.parent().hide();
        } else {
            // remove parent tr element of '$this'
            $this.parents("tr").eq(0).remove();
            // find all tr elements in '$tbody' and assign them to variable '$new_trs'
            $new_trs = $tbody.find("tr");
            // loop through each tr element in '$new_trs'
            $new_trs.each(function (i, elm) {
                // set the html content of first td element in the tr to i+1
                $(elm)
                    .find("td")
                    .eq(0)
                    .html(i + 1);
            });
        }
        // call generateBarcode function
        generateBarcode();
    });

    $("#display-value").change(function () {
        generateBarcode();
    });

    $("#barcode-height").on("input", function () {
        generateBarcode();
    });

    $("#barcode-width").on("input", function () {
        generateBarcode();
    });

    $("table").delegate(".jml-cetak", "keyup", function () {
        generateBarcode();
    });

    // This function sets an empty barcode
    function setEmptyBarcode() {
        // Select the container element and empty its contents
        const $container = $("#barcode-print-container").empty();
        // Set the container's height to auto and its text alignment to center
        $container.css({
            height: "auto",
            "text-align": "center",
        });
        // Set the container's HTML to "PREVIEW"
        $container.html("PREVIEW");
        // Disable the button
        button.attr("disabled", "disabled");
    }

    // This function generates barcodes based on the user inputs
    function generateBarcode() {
        // Check if the list of items is hidden or not
        if ($("#list-barang").is(":hidden")) {
            // If hidden, set the barcode to empty and return false
            setEmptyBarcode();
            return false;
        } else {
            // If not hidden, count the number of barcodes to be generated
            let jml_cetak = 0;
            $barcode_barang = $(".barcode-barang");
            $barcode_barang.each(function (i, elm) {
                $elm = $(elm);
                $tr = $elm.parents("tr").eq(0);
                jml_cetak += setInt($tr.find(".jml-cetak").val());
            });
            // If number of barcodes is 0, set the barcode to empty and return false
            if (jml_cetak == 0) {
                setEmptyBarcode();
                return false;
            }
        }
        // Enable the button
        button.removeAttr("disabled");
        // Set the height of the paper for the barcode
        h = $("#paper-size-height").val() * pixel;
        $container.empty();
        $container.css({ minHeight: h });
        $container.css("text-align", "left");
        // Generate barcodes for each item
        $(".barcode-barang").each(function (i, elm) {
            $elm = $(elm);
            $tr = $elm.parents("tr").eq(0);
            jml_cetak = setInt($tr.find(".jml-cetak").val());
            for (let i = 1; i <= jml_cetak; i++) {
                // Set the ID for the barcode
                id = "barcode-" + i + "-" + $elm.text();
                $canvas = $("<canvas/>");
                $canvas.attr({ id: id });
                $canvas.css("padding-right", 5 * pixel + "px");
                $canvas.appendTo($container);
                // Generate the barcode using JsBarcode library
                JsBarcode("#" + id, $elm.text(), {
                    format: "ean13",
                    width: $("#barcode-width").val(),
                    height: $("#barcode-height").val(),
                    displayValue:
                        $("#display-value").val() == "Y" ? true : false,
                });
            }
        });
        // Show the container with the generated barcodes
        $container.show();
    }

    $("#print").click(function () {
        // Define margin values in millimeters
        const margin_left = 10;
        const margin_top = 10;
        // Initialize variables for row width, column index, and barcode margins
        let row_width = 0;
        let index_col = 0;
        let barcode_margin_right = 0;
        let barcode_margin_bottom = 0;
        // Select the container element for the barcodes to be printed
        const $container = $("#barcode-print-container");
        // Create a new table element
        const $table = $('<table id="table-print">');
        const $tbody = $("<tbody>");
        let $tr = $("<tr>");
        // Loop through each canvas element in the container
        $container.find("canvas").each(function (i, elm) {
            // Clone the canvas element and get its image data URL
            const $elm = $(elm);
            const $elm_new = $elm.clone();
            const image_string = $(elm)[0].toDataURL();
            // Get the width and height of the canvas element in millimeters
            const width = parseFloat($elm.width()) * milimeter;
            const height = parseFloat($elm.height()) * milimeter;
            // Calculate the width of the current row
            row_width =
                margin_left +
                index_col * width +
                index_col * barcode_margin_right;
            index_col++;
            // Check if the row width exceeds the maximum width of 210 mm
            const cek_width = row_width + width * milimeter;
            if (cek_width > 210) {
                // If so, start a new row
                index_col = 1;
                row_width = 0;
                $tbody.append($tr);
                $tr = $("<tr>");
            }
            // Create a new table cell for the current barcode
            const $td = $("<td>");
            // Set the HTML content of the table cell to the barcode image
            $td.html(
                '<img src="' +
                    image_string +
                    '" style="width:' +
                    $elm.width() +
                    "px; max-width:" +
                    $elm.width() +
                    "px; height: " +
                    $elm.height() +
                    'px"/>'
            );
            // Add the table cell to the current row
            $tr.append($td);
        });
        // Add the last row to the table body
        if ($tr.children("td").length) {
            $tbody.append($tr);
        }
        // Remove any existing print container and create a new one
        $("#print-container").remove();
        const $print_container = $(
            '<div id="print-container" style="padding:10px">'
        );
        // Add the table to the print container and append it to the card body
        $table.append($tbody);
        $print_container.append($table);
        $print_container.appendTo($(".card-body"));
        // Print the contents of the print container using printJS
        printJS({
            printable: "print-container",
            type: "html",
            css: url_css,
        });
        // Remove the print container after printing
        $("#print-container").remove();
    });

    function mm(value) {
        point = value * 2.83465; // 1mm to point
        dxa = 20;
        return point * dxa;
    }

    $("#export-pdf").click(function () {
        paper_width = setInt($("#paper-size-width").val());
        paper_height = setInt($("#paper-size-height").val());

        const orientation =
            paper_width > paper_height ? "lanscape" : "portrait";

        window.jsPDF = window.jspdf.jsPDF;
        const pdf = new jsPDF({
            orientation: orientation,
            unit: "mm",
            format: [paper_height, paper_width],
        });

        margin_left = 5;
        margin_top = 5;

        row_width = 0;
        index_col = 0;
        index_row = 0;

        barcode_margin_right = 5;
        barcode_margin_bottom = 5;

        x = 0;
        y = 10;

        $container = $("#barcode-print-container");
        $container.find("canvas").each(function (i, elm) {
            const $elm = $(elm);
            const image_string = $(elm)[0].toDataURL();
            width = parseFloat($elm.width()) * milimeter;
            height = parseFloat($elm.height()) * milimeter;

            x =
                margin_left +
                index_col * width +
                index_col * barcode_margin_right;
            row_width = x + width + barcode_margin_right;

            if (row_width > paper_width) {
                index_col = 0;
                row_width = 0;
                index_row++;
                x =
                    margin_left +
                    index_col * width +
                    index_col * barcode_margin_right;
            }

            index_col++;
            y =
                margin_top +
                index_row * height +
                index_row * barcode_margin_bottom;

            if (y + height + barcode_margin_bottom > paper_height) {
                pdf.addPage([paper_height, paper_width], orientation);
                index_col = 1;
                row_width = 0;
                index_row = 0;
                y =
                    margin_top +
                    index_row * height +
                    index_row * barcode_margin_bottom;
            }
            pdf.addImage(image_string, "PNG", x, y, width, height);
        });
        pdf.save("Barcode-cetak.pdf");
    });

    $("#export-word").click(function () {
        paper_width = setInt($("#paper-size-width").val());
        paper_height = setInt($("#paper-size-height").val());

        margin_left = 10; //mm
        margin_top = 10; //mm

        row_width = 0;
        index_col = 0;

        barcode_margin_right = 0;
        barcode_margin_bottom = 0;

        table_row = [];
        table_col = [];
        $container = $("#barcode-print-container");
        $container.find("canvas").each(function (i, elm) {
            const $elm = $(elm);
            const image_string = $(elm)[0].toDataURL();
            width = parseFloat($elm.width());
            height = parseFloat($elm.height());

            row_width =
                margin_left +
                index_col * width * milimeter +
                index_col * barcode_margin_right;
            index_col++;

            cek_width = row_width + width * milimeter;
            if (cek_width > paper_width) {
                index_col = 1;
                row_width = 0;

                table_row.push(
                    new docx.TableRow({
                        children: table_col,
                    })
                );

                table_col = [];
            }

            table_col.push(
                new docx.TableCell({
                    children: [
                        new docx.Paragraph({
                            children: [
                                new docx.ImageRun({
                                    data: image_string,
                                    transformation: {
                                        width: width,
                                        height: height,
                                    },
                                }),
                            ],
                        }),
                    ],
                })
            );
        });

        if (table_col) {
            table_row.push(
                new docx.TableRow({
                    children: table_col,
                })
            );
        }

        const orientation =
            paper_width > paper_height
                ? docx.PageOrientation.LANSCAPE
                : docx.PageOrientation.PORTRAIT;
        const doc = new docx.Document({
            creator: "Jagowebdev",
            description: "Barcode",
            title: "Barcode",
            sections: [
                {
                    properties: {
                        page: {
                            margin: {
                                top: mm(margin_top),
                                right: mm(10),
                                bottom: mm(10),
                                left: mm(margin_left),
                            },
                            size: {
                                orientation: orientation,
                                height: docx.convertMillimetersToTwip(
                                    paper_height
                                ),
                                width: docx.convertMillimetersToTwip(
                                    paper_width
                                ),
                            },
                        },
                    },
                    children: [
                        new docx.Table({
                            rows: table_row,
                        }),
                    ],
                },
            ],
        });

        docx.Packer.toBlob(doc).then((blob) => {
            saveAs(blob, "Barcode-cetak.docx");
        });
    });
});
