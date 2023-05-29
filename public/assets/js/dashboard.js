$(document).ready(function () {


    $('.assets').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/dashboard/location-asset',
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'assets_count', name: 'assets_count' },
            { data: 'assigned_assets_count', name: 'assigned_assets_count' },
            { data: 'users_count', name: 'users_count' },
        ],
        responsive: true,
        scrollY: '225px',
        scrollCollapse: true,
        paging: false,
        bPaginate: false,
        bInfo : false,
        bFilter: false,
        pageLength: 20,
    });

    $('.models').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/dashboard/assets-model',
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'models_count', name: 'models_count' },
        ],
        responsive: true,
        scrollY: '225px',
        scrollCollapse: true,
        paging: false,
        bPaginate: false,
        bInfo : false,
        bFilter: false,
        pageLength: 20,
    });

   $('.categories').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/dashboard/category-asset',
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'category_type', name: 'category_type' },
            { data: 'assets_count', name: 'assets_count' },
            { data: 'accessories_count', name: 'accessories_count' },
            { data: 'licenses_count', name: 'licenses_count' },
            { data: 'components_count', name: 'components_count' },
        ],
        responsive: true,
        scrollY: '225px',
        scrollCollapse: true,
        paging: false,
        bPaginate: false,
        bInfo : false,
        bFilter: false,
        pageLength: 20,
    });

    $('.action_log').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/dashboard/action-log',
        },
        columns: [
            // { data: 'id', name: 'id', width: '5%'},
            { data: 'created_at', name: 'created_at' },
            { data: 'user_id', name: 'user_id' },
            { data: 'action_type', name: 'action_type' },
            { data: 'item', name: 'item.name' },
            { data: 'target', name: 'target.name' },
        ],
        // responsive: true,
        scrollY: '225px',
        scrollCollapse: true,
        paging: false,
        bPaginate: false,
        bInfo : false,
        bFilter: false,
        pageLength: 20,
    });

    var archived = $('#archived').val();
    var rtd = $('#rtd').val();
    var pending = $('#pending').val();

    var chart = Highcharts.chart('donut-chart', {
        colors: ['#1438AE','#FFD600', '#00A650'],
        chart: {
            type: 'pie',
            margin: [0, 0, 0, 0],
            spacingTop: 10,
            spacingBottom: 0,
            spacingLeft: 0,
            spacingRight: 0,
            // width: 250,
            height: 275,

        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        exporting: {
            enabled: false
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.0f}%</b>'
        },
        plotOptions: {
            pie: {
                size: '70%',
                align: 'top',
                dataLabels: {
                    enabled: false
                },
                showInLegend: false
                }
        },
        series: [{
            name: 'Asset',
            colorByPoint: true,
            innerSize: '65%',
            data: [{
                name: 'Archived',
                y: parseInt(archived)
            }, {
                name: 'Pending',
                y: parseInt(pending)
            }, {
                name: 'Ready to Deploy',
                y: parseInt(rtd)
            }]
        }]
    });

});
