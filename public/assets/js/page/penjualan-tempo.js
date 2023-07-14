jQuery(document).ready(function () {

    const column = $.parseJSON($('#dataTables-column').html());
	let url = $('#dataTables-url').text();

	const settings = {
        "processing": true,
        "serverSide": true,
		"scrollX": true,
		"ajax": {
            "url": url,
            "type": "POST",
			"dataSrc": function ( json ) {
				// if (json.data.length) {
				// 	for ( let i = 0, len = json.data.length ; i < len ; i++ ) {
				// 		$('#total-nilai').html(json.data[i].total.total_neto);
				// 		$('#total-qty').html(json.data[i].total.total_qty);
				// 		break;
				// 	}
				// }
                console.log(json)

				if (json.recordsTotal > 0) {
					$('.btn-export').removeAttr('disabled');
				} else {
					$('.btn-export').attr('disabled', 'disabled');
				}

				return json.data;
			}
        },

        "columns": column
    }

	let $add_setting = $('#dataTables-setting');
	if ($add_setting.length > 0) {
		add_setting = $.parseJSON($('#dataTables-setting').html());
		for (k in add_setting) {
			settings[k] = add_setting[k];
		}
	}

	let dataTables =  $('#table-result').DataTable( settings );

    $('#daterange').daterangepicker({
		opens: 'right',
		ranges: {
             'Hari ini': [moment(), moment()],
			 'Bulan ini': [moment().startOf('month'), moment()],
             'Tahun ini': [moment().startOf('year'), moment()],
             '7 Hari Terakhir': [moment().subtract('days', 6), moment()],
             '30 Hari Terakhir': [moment().subtract('days', 29), moment()],

          },
		showDropdowns: true,
		   "linkedCalendars": false,
		locale: {
			customRangeLabel: 'Pilih Tanggal',
            format: 'DD-MM-YYYY',
			applyLabel: 'Pilih',
			separator: " s.d ",
				 "monthNames": [
				"Januari",
				"Februari",
				"Maret",
				"April",
				"Mei",
				"Juni",
				"Juli",
				"Agustus",
				"September",
				"Oktober",
				"November",
				"Desember"
			],
        }
	},	function(start, end, label)
	{
		start_date = start.format('YYYY-MM-DD');
		end_date = end.format('YYYY-MM-DD');
		$('#start-date').val(start_date);
		$('#end-date').val(end_date);
		update_data();

	})

    $('#jatuh-tempo').change(function() {
		update_data();
	})

    function update_data()
	{
		$spinner_neto = $('<div class="spinner-border text-secondary mt-2" style="width: 20px; height: 20px;"></div>');
		$spinner_bayar = $spinner_neto.clone();
		$spinner_piutang = $spinner_neto.clone();

		start_date = $('#start-date').val();
		end_date = $('#end-date').val();
		$('#total-neto').html($spinner_neto);
		$('#total-bayar').html($spinner_bayar);
		$('#total-piutang').html($spinner_piutang);

		jatuh_tempo = $('#jatuh-tempo').val();

		const searchParams = new URLSearchParams(window.location.search);
		searchParams.set("start_date", start_date);
		searchParams.set("end_date", end_date);
		searchParams.set("jatuh_tempo", jatuh_tempo);
		var newRelativePathQuery = window.location.pathname + '?' + searchParams.toString();
		history.pushState(null, '', newRelativePathQuery);

		$.ajax({
			url: 'penjualan-tempo/ajaxGetResumePenjualanTempo?start_date=' + start_date + '&end_date=' + end_date + '&jatuh_tempo=' + jatuh_tempo,
			dataType: 'JSON',
			success: function(data) {
				$('#total-neto').html( format_ribuan(data.total_neto) );
				$('#total-bayar').html( format_ribuan(data.total_bayar));
				$('#total-piutang').html( format_ribuan(data.total_piutang));
			}
		})

		settings.ajax.url = 'penjualan-tempo/getDataDTPenjualanTempo?start_date=' + start_date + '&end_date=' + end_date + '&jatuh_tempo=' + jatuh_tempo;
		dataTables.destroy();
		len = $('#table-result').find('thead').find('th').length;
		$('#table-result').find('tbody').html('<tr>' +
								'<td colspan="' + len + '" class="text-center">Loading data...</td>' +
							'</tr>');
		dataTables =  $('#table-result').DataTable( settings );
	}
});

