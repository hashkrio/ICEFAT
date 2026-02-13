<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-10 col-md-8 col-sm-10 col-xl-10 col-xs-12 offset-lg-1 offset-md-2 offset-sm-1 offset-xl-1">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">CALCULATION LISTS</h3>
                <a href="<?php echo url_to('add.calculations'); ?>" class="btn btn-default btn-sm float-right text-red">Add</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-hover table-responsive-sm" id="ajax-calculation-list-datatables">
                    <thead class="bg-dark">
                        <tr>
                            <th width="3%">&nbsp;</th>
                            <th width="10%">Sr No.</th>
                            <th width="55%">Crate Design</th>
                            <th width="30%">Total carbon footprint</th>
                            <th width="5%">View</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Calculation Analysis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div id="donut-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->

<!-- BS Stepper -->
<script src="<?php echo site_url('plugins/bs-stepper/js/bs-stepper.min.js'); ?>" <?= csp_script_nonce() ?>></script>
<?= $this->include('layouts/datatable_script'); ?>
<?= $this->include('layouts/float_script'); ?>
<script type="text/javascript" <?= csp_script_nonce() ?>>
    function getData (event) {
        
        if (event.stopPropagation) {
            event.stopPropagation();
        }
        else if (window.event) {
            window.event.cancelBubble = true;
        }

        var row = $(event.currentTarget).closest('tr');  // Find the closest table row
        var rowData = $('#ajax-calculation-list-datatables').DataTable().row(row).data();  // Get data for that row
        $.when(updateChartData(rowData))
        .then(function(res){
            $("#exampleModalCenter").modal('show');
        })
    };

    var interactive_plot;

    const updateChartData = (row) => {
        let perOneWayTrip = (row.per_oneway_trip || 0);
        let carbonFooterPrint = (row.carbon_footer_print || 0);
        
        if(perOneWayTrip > 0 || carbonFooterPrint > 0) {
            interactive_plot.setData([{
                    label: 'Crate',
                    data : perOneWayTrip,
                    color: 'rgb(68,114,196)'
                },
                {
                    label: 'Transport',
                    data : carbonFooterPrint,
                    color: 'rgb(237,125,49)'
                }
            ]);
    
            // Since the axes don't change, we don't need to call plot.setupGrid()
            interactive_plot.draw()
        }
    }

    document.addEventListener('DOMContentLoaded', function(ev) {

        /*
        * DONUT CHART
        * -----------
        */
        const combileTitle = {
            pie: {
                show: true,
                radius     : 3/4,
                innerRadius: 0.4,
                label      : {
                    show     : true,
                    radius   : 1,
                    formatter: labelFormatter,
                    // threshold: 0.1, Hidden value if Pie is less than 10%
                }
            }
        };

        interactive_plot = $.plot('#donut-chart', [{
                label: "No Data",
                data: 1,  // To show a neutral circle (not a complete donut)
                color: '#ccc'
            }], {
            series: {
                ...combileTitle
            },
            legend: {
                show: false
            },
            grid: {
                hoverable: false,  // Disable hover effect since we don't need it
                clickable: false   // Allow the chart to be clickable
            }
        })
        /*
        * END DONUT CHART
        */
       

        /*
        * Custom Label formatter
        * ----------------------
        */
        function labelFormatter(label, series) {
            
            if(series.data[0][1] == null || series.data[0][1] == "1") {
                return 'No Data';
            }
            return '<div style="font-size:13px; text-align:center; padding:2px; color: #000; font-weight: 600;">'
            + '(' + series.data[0][1] + ')'
            + '<br>'
            + label
            + '<br>'
            + Math.round(series.percent) + '%</div>';
        }
            
        const DataColumns = [{
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: ''
            },
            {
                data: null,
                className: 'text-center',
                title: 'Sr No.',
                render: function(data, type, row, meta) {
                    return `${(meta.row + meta.settings._iDisplayStart + 1)}`;
                }
            },
            {
                data: 'crate_type_name',
                title: 'Crate Design'
            },
            {
                data: 'total_carbon_foot_print',
                title: 'Total Carbon Footprint'
            },
            {
                data: null,
                render: function(data, type, row, meta) {
                    let datas = (JSON.stringify(row));
                    return "<button class=\"btn btn-sm btn-outline-dark\"><i class=\"fas fa-chart-pie\" onclick=\"getData(event)\" ></i></button>";
                }
            },
        ];

        const dataTableOptions = {
            searching: true,
            processing: true,
            serverSide: true
        }

        function format ( rowData ) {
            var div = $('<div/>')
                .addClass( 'loading' )
                .text( 'Loading...' );
        
            $.ajax({
                url: "<?= site_url('filter/calculation_list_data/details') ?>/" + rowData.id,
                method: "POST",
                dataType: 'json',
                success: function(row) {
                    // Populate the child row with user details
                    div.html(`<div class="bg-gray-light m-1 rounded">
                        <fieldset class="bg-gray-light border p-2 rounded shadow" id="test-vl-1">
                            <legend  class="w-auto"><span class="badge badge-primary">Step - 1</span></legend>
                            <div class="form-group">
                                <label for="selectModalOptions">Choose how you would like to model your crate and fill out fields in corresponding worksheet tab:</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.crate_type_name}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xl-8">    
                                        <span class="form-control">${row.model_option_name}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="crateWeight">Based on your specifications, the weight of your crate is:</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.crate_weight}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="embodiedCarbonFactorInKg">Based on your specifications, the carbon footprint of producting your crate is:</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.embodied_carbon_factor_in_kg}</span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="bg-gray-light border p-2 rounded shadow" id="test-vl-2">
                            <legend  class="w-auto"><span class="badge badge-primary">Step - 2</span></legend>
                            <div class="form-group">
                                <label for="numberOfOneWayTrips">Enter the number of one-way trips your crate will be used for over its lifetime:</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.number_of_oneway_trips}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="perOneWayTrip">Based on the number of trips, the carbon footprint of producing your crate per one-way trip is:</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.per_oneway_trip}</span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="bg-gray-light border p-2 rounded shadow" id="test-vl-3">
                            <legend  class="w-auto"><span class="badge badge-primary">Step - 3</span></legend>
                            <h6>Enter in your transportation information per one-way trip (in km):</h6>
                            <div class="form-group">
                                <label for="shipment_air_passenger">Air Passenger</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.shipment_air_passenger}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="shipment_air_freight">Air Freight</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.shipment_air_freight}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="shipment_road_freight">Road Freight</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.shipment_road_freight}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="shipment_sea_freight">Sea Freight</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.shipment_sea_freight}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="carbonFooterPrint">Based on these distances and the weight of your crate, the carbon footprint of transporting your crate per one-way trip is:</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.carbon_footer_print}</span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="bg-gray-light border p-2 rounded shadow" id="test-vl-4">
                            <legend  class="w-auto"><span class="badge badge-primary">Step - 4</span></legend>
                            <div class="form-group">
                                <label for="totalCarbonFootPrint">The total carbon footprint of your crate per one-way trip is:</label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <span class="form-control">${row.total_carbon_foot_print}</span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>`)
                    .removeClass( 'loading' );
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    div.html('No data').removeClass( 'loading' );
                }
            });
        
            return div;
        }

        const calculationListDataTable = initDataTable('ajax-calculation-list-datatables', "<?php echo url_to('ajax.calculation.list.data'); ?>", DataColumns, dataTableOptions)
        .on('click', 'td.dt-control', function (e) {
            let tr = e.target.closest('tr');
            let row = calculationListDataTable.row(tr);
        
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.classList.remove('shown');
            }
            else {
                // Open this row                
                row.child(format(row.data())).show();
                tr.classList.add('shown')
            }
        })
    })
</script>
<?= $this->endSection(); ?>