<!-- BS Stepper -->
<?= $this->include('layouts/toast_script'); ?>
<?= $this->include('layouts/float_script'); ?>
<script type="text/javascript" <?= csp_script_nonce() ?>>

    var id = 1;

    
    /*
    * DONUT CHART
    * -----------
    */

    /*
    * Custom Label formatter
    * ----------------------
    */
    const labelFormatter = (label, series) => {
        if(series.data[0][1] == null || series.data[0][1] == "1") {
            return 'No Data';
        }
        return '<div style="font-size:13px; text-align:center; padding:2px; color: #000; font-weight: 600;">'
        + '(' + series.data[0][1] + ')'
        + '<br>'
        + label
        + '<br>'
        + Math.round(series.percent) + '%</div>'
        if(series == undefined) return label;
    }
    
    const combileTitle = {
        pie: {
            show: true,
            radius     : 3/4,
            innerRadius: 0.4,
            label      : {
                show     : true,
                radius   : 1,
                formatter: labelFormatter,
            }
        }
    };

    var interactive_plot;
    const defaultChartDataSet = [{
        label: "No Data",
        data: 1,  // To show a neutral circle (not a complete donut)
        color: '#ccc'
    }];
    
    document.addEventListener('DOMContentLoaded', function(ev) {
        interactive_plot = $.plot('#donut-chart', 
            defaultChartDataSet, 
            {
                series: {
                    ...combileTitle
                },
                legend: {
                    show: false
                },
                grid: {
                    hoverable: true,  // Disable hover effect since we don't need it
                    clickable: false   // Allow the chart to be clickable
                },
            });
    });    

    /*
    * END DONUT CHART
    */
    const kgPrefix = 'kg';
    const kgCo2Prefix = 'kg CO2e';

    const setPrefixSpan = (ele, value, prefix = kgCo2Prefix) => {
        $(`#${ele}`).html(`<b>${(value)} ${prefix}</b>`);
    }

    let nTrips = document.getElementById("number_of_oneway_trips");
        
    const getCrateWeightAndCarbonFactor = () => {
        
        $.ajax({
            url: "<?php echo url_to('ajax.crate.values.by.create'); ?>",
            type: 'POST',
            async: false,
            data: {
                crate_type_id: function() {
                    return ($("#crate_type_id").val() || 0);
                },
                measurement_unit_id: function() {
                    return ($("#measurement_unit_id").val() || 0);
                },
                height: function() {
                    return ($("#height").val() || 0);
                },
                width: function() {
                    return ($("#width").val() || 0);
                },
                depth: function() {
                    return ($("#depth").val() || 0);
                },
            },
            dataType: 'json',
            success: function(data, textStatus, JQueryXHR) {
                let weightInKg = (data?.weight_in_kg || 0);
                let embodiedCarbonFactorInKg = parseInt(data?.embodied_carbon_factor_in_kg || 0);
                $("#weight_in_kg").val(weightInKg);
                setPrefixSpan("weight_in_kg_label",weightInKg, kgPrefix);
                $("#embodied_carbon_factor_in_kg").val(embodiedCarbonFactorInKg);
                setPrefixSpan("embodied_carbon_factor_in_kg_label",embodiedCarbonFactorInKg);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#weight_in_kg").val(0);
                setPrefixSpan("weight_in_kg_label",0);
                $("#embodied_carbon_factor_in_kg").val(0);
                setPrefixSpan("embodied_carbon_factor_in_kg_label",0);
            }
        });
    };
    
    const updateCalculations = (updateAjaxValues = false) => {
        if(updateAjaxValues === true) {
            $.when(getCrateWeightAndCarbonFactor()).then((ajaxData) => {
                let perOneWayTrip = parseInt(($("#embodied_carbon_factor_in_kg").val() || 0) / (nTrips.value || 1));
                $("#per_one_way_trip").val(perOneWayTrip);
                setPrefixSpan("per_one_way_trip_label",perOneWayTrip);
            })
        } else {
            let perOneWayTrip = parseInt(($("#embodied_carbon_factor_in_kg").val() || 0) / (nTrips.value || 1));
            $("#per_one_way_trip").val(perOneWayTrip);
            setPrefixSpan("per_one_way_trip_label",perOneWayTrip);
        }
    }

    /* const getPlotChartImage = () => {
        var canvas = interactive_plot.getCanvas();

        var context = canvas.getContext("2d");

        // Save the current canvas state
        context.save();

        // Draw a white background rectangle
        context.globalCompositeOperation = "destination-over"; // Draw the rectangle behind existing content
        context.fillStyle = "#FFFFFF"; // Set the background color to white
        context.fillRect(0, 0, canvas.width, canvas.height);

        // Convert to PNG image
        var imgData = canvas.toDataURL("image/png");

        // Restore the original canvas state
        context.restore();

        return imgData;
    } */

    function getPlotChartImage() {
        var canvas = interactive_plot.getCanvas(); // Get the canvas element
        var context = canvas.getContext("2d");

        // Save the current canvas state
        context.save();

        // Draw a white background rectangle
        context.globalCompositeOperation = "destination-over"; // Draw the rectangle behind existing content
        context.fillStyle = "#FFFFFF"; // Set the background color to white
        context.fillRect(0, 0, canvas.width, canvas.height);

        // Restore the original canvas state
        context.restore();

        // Convert to PNG image
        var imgData = canvas.toDataURL("image/png");
        return imgData;
    }

    const exportExcelDataTable = (dataTableList, exportType, fileName, exportClassName) => {

        $('.'+exportClassName).prop('disabled', true);

        const requestData = dataTableList.ajax.params(); // Get current request parameters

        html2canvas(document.getElementById("chartDiv")).then(canvas => {
        // Convert canvas to a PNG image
            exportFiles(dataTableList.ajax.url(),{...requestData, is_export: exportType, image: canvas.toDataURL("image/png")},undefined,fileName, exportClassName)
        });
    }

    function exportFiles(export_url,formData,errorMsg='Something went wrong, please contact Administrator.',exportFileName,exportClassName){
        $.ajax({
            url: export_url,
            type: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob' // Ensure the response type is 'blob' for file download
            },
            success: function(response, status, xhr) {
                try {
                    if(xhr.responseJSON) {
                        throw Error((response.message || 'An error occurred while generating the file.'));
                    } else {    
                        console.log(xhr.getResponseHeader('content-type'));
                        if(xhr.getResponseHeader('content-type') == 'application/pdf' || xhr.getResponseHeader('content-type') == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                        
                            // Create a link element to initiate the download
                            var link = document.createElement('a');
            
                            link.href = window.URL.createObjectURL(response);
                            link.download = exportFileName; // Set the file name
                            document.body.appendChild(link);
                            link.click();
                            window.URL.revokeObjectURL(link.href);
                            document.body.removeChild(link);
                            // Swal.fire('success', "File download successfully", "success");
                        } else {
                            throw Error('Invalid file.');
                        }
                    }
                } catch (error) {                    
                    Swal.fire('warning', error.message, "warning");
                    throw error;
                }
            },
            error: function(xhr, status, error) {
                if(xhr.getResponseHeader('content-type') == 'application/json') {
                    Swal.fire('warning', errorMsg, "warning");
                } else {
                    Swal.fire('warning', errorMsg, "warning");
                }
            }, complete:function(jqXHR, textStatus) {
                $('.'+exportClassName+'').prop('disabled', false);
            }
        });
    }
    
    jQuery(function ($) {

        var singleDeleteEditor = new DataTable.Editor({
            ajax: {
                remove: {
                    type: 'PUT',
                    url: "<?php echo url_to('ajax.calculation.all.data.list'); ?>"
                },
            },
            table: '#ajax-calculation-list-datatables'
        });
        
        var allRowDeleteEditor = new DataTable.Editor({
            ajax: {
                remove: {
                    type: 'PUT',
                    url: "<?php echo url_to('ajax.calculation.all.data.list'); ?>"
                },
            },
            table: '#ajax-calculation-list-datatables'
        });

        const dataTableOptions = {
            pageLength: 50,
            scrollY: 300,
            scroller: {
                loadingIndicator: true
            },
            select: {
                style: 'multi',
                selector: 'td:first-child',
                headerCheckbox: 'select-page'
            },
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'remove', editor: singleDeleteEditor, text: 'Remove Crate', className: 'border-0 rounded bg-primary' },
                        { 
                            text: 'Clear List',
                            className: 'border-0 rounded bg-primary',
                            action: function (e, dt, node, config, cb) {
                                allRowDeleteEditor.remove(dt.rows().nodes(), {
                                    title: 'Delete Records',
                                    message: 'Are you sure you want to delete all records?',
                                    buttons: 'Delete'
                                });
                            }
                        }
                    ]
                },
                topEnd: {
                    buttons: [
                        { 
                            text: 'Export',
                            className: 'btn_excel border-0 rounded bg-primary',
                            action: function (e, dt, node, config, cb) {
                                exportExcelDataTable(dt, 1, 'export.xlsx', 'btn_excel');
                            }
                        },
                        { 
                            text: 'Print',
                            className: 'btn_pdf border-0 rounded bg-primary',
                            action: function (e, dt, node, config, cb) {
                                exportExcelDataTable(dt, 2, 'export.pdf', 'btn_pdf');
                            }
                        }
                    ]
                }
            },
            footerCallback: function (row, data, start, end, display) {
                let api = this.api();
                
                // Remove the formatting to get integer data for summation
                let intVal = function (i) {
                    return typeof i === 'string'
                    ? i.replace(/[\$,]/g, '') * 1
                    : typeof i === 'number'
                    ? i
                    : 0;
                };

                setPrefixSpan("total_result",(api?.ajax?.json()?.totalSum || 0));

                let economyChartValue = (api?.ajax?.json()["ECONOMY"] || 0)
                let midRangeChartValue = (api?.ajax?.json()["MID-RANGE"] || 0)
                let meseumChartValue = (api?.ajax?.json()["MUSEUM"] || 0)

                let dataSet = defaultChartDataSet;
                
                if(economyChartValue > 0 || midRangeChartValue > 0 || meseumChartValue > 0) {
                    dataSet = [{
                        label: "ECONOMY",
                        data : (api?.ajax?.json()["ECONOMY"] || 0),
                        color: 'rgb(68,114,196)'
                    },
                    {
                        label: "MID-RANGE",
                        data : (api?.ajax?.json()["MID-RANGE"] || 0),
                        color: 'rgb(237,125,49)'
                    },
                    {
                        label: "MUSEUM",
                        data : (api?.ajax?.json()["MUSEUM"] || 0),
                        color: 'rgb(204,204,204)'
                    }]
                }

                interactive_plot.setData(dataSet);
        
                // Since the axes don't change, we don't need to call plot.setupGrid()
                interactive_plot.draw()
                // Total over all pages
                // total = $('#ajax-calculation-list-datatables').DataTable().ajax.json().totalSum;
                // console.log(total)
                // Total over this page
                // pageTotal = api
                //     .column(13, { page: 'current' })
                //     .data()
                //     .reduce((a, b) => intVal(a) + intVal(b), 0);
                
                // Update footer
                // api.column(1).footer().innerHTML = 'Page total: ' + pageTotal + ' € ('+ total + ' € total over all pages)';
            }
        }

        const DataColumns = [
        {
            // data:'DTDT_RowId',
            defaultContent: '',
            orderable: false,
            className: 'select-checkbox',
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
            data: 'label_name',
            title: 'Label'
        },
        {
            data:'height',
            title:'Height'
        },
        {
            data:'width',
            title:'Width'
        },
        {
            data:'depth',
            title:'Depth'
        },
        {
            data:'measurement_unit_name',
            title:'Units'
        },
        {
            data:'weight_in_kg',
            title:'Weight (kg)'
        },
        {
            data:'carbon_footprint',
            title:'Footprint (kg CO2e)'
        }];

        const calculationListDataTable = initDataTable('ajax-calculation-list-datatables', "<?php echo url_to('ajax.calculation.all.data.list'); ?>", DataColumns, dataTableOptions)
        .on('click', 'td.editor-remove button', function (e) {
            e.preventDefault();
            e.stopPropagation();
            editor.edit($(this).closest('tr'), {
                title: 'Edit record',
                buttons: ([
                {
                    text: 'Save',
                    className: 'btn btn-default btn-sm float-right text-red',
                    action: function () {
                        this.submit();
                    }
                }])
            });
        });

        $("#crate_type_id, #measurement_unit_id").on('change',function(e) {
            updateCalculations(true);
        });

        $("#height, #width, #depth").on('blur',function(e) {
            updateCalculations(true);
        });

        $(nTrips).on('blur', function(e){
            updateCalculations()
        });

        updateCalculations(true);

        const primaryCrateTypeD = JSON.parse('<?= json_encode(esc($primaryCrateType)); ?>');

        const unitArrs = JSON.parse('<?= json_encode(esc($unitArr)); ?>');

        const editor = new DataTable.Editor({
            ajax: '<?= url_to('ajax.carbon.footprint.new.save') ?>',
            fields: [
                {
                    label: 'Units:',
                    name: 'measurement_unit_id',
                    type: 'select',
                    options: unitArrs
                },
                {
                    label: 'Height',
                    name: 'height',
                    type: 'text'
                },
                {
                    label: 'Width',
                    name: 'width',
                    type: 'text'
                },
                {
                    label: 'Depth',
                    name: 'depth',
                    type: 'text'
                },
                {
                    label: 'Status:',
                    name: 'crate_type_id',
                    type: 'select',
                    options: primaryCrateTypeD
                },
                {
                    label: 'Crate Label',
                    name: 'label_name',
                    type: 'text'
                },
                {
                    label: 'Weight (kg)',
                    name: 'weight_in_kg',
                    type: 'text'
                },
                {
                    label: 'Footprint',
                    name: 'embodied_carbon_factor_in_kg',
                    type: 'text'
                },
                {
                    label: 'Number of one way trips',
                    name: 'number_of_oneway_trips',
                    type: 'text'
                },
                {
                    label: 'Per one way trip',
                    name: 'per_one_way_trip',
                    type: 'text'
                }
            ]
        });

        const fieldArr = [
            'measurement_unit_id',
            'height',
            'width',
            'depth',
            'crate_type_id',
            'label_name',
            'weight_in_kg',
            'embodied_carbon_factor_in_kg',
            'number_of_oneway_trips',
            'per_one_way_trip',
        ];

        var ErrorData = {};

        const setError = (type) => {
            
            document.querySelectorAll(`[data-editor-field]`).forEach(x => x.classList.remove('bg-danger'))

            if(Object.keys(ErrorData).length > 0) {
                for(const [key, value] of Object.entries(ErrorData)) {
                    if(document.querySelector(`[data-editor-field=${value.name}]`)) {
                        document.querySelector(`[data-editor-field=${value.name}]`).classList.add('bg-danger')
                    }
                }
            }
        }

        // Custom function to reset editor after successful submission
        const resetEditorForFurtherEditing = () => {
            // Manually reset the form fields if necessary            
            editor.edit(id, false);  // Optionally trigger re-edit for a row after submit
        }

        editor
        .create(false)
        .edit( id, false )           // no editing id, and don't show immediately      
        .set('measurement_unit_id','height','width','depth','crate_type_id','label_name','weight_in_kg','embodied_carbon_factor_in_kg','number_of_oneway_trips','per_one_way_trip')      
        // .template("#customTemplateForm")
        .on('initSubmit', function ( e, type) {
            try{
                fieldArr.map(x => this.field(x).set(document.getElementById(x).value));
                return true;   
            } catch (err) {
                setError(type)
                return false;   
            }
        })
        .on('postSubmit', function (e, json, data, action, xhr) {
            // Do error processing on json.fieldErrors
            if((json.fieldErrors || []).length > 0) {
                ErrorData = Object.assign({}, json.fieldErrors);
                // Wipe out the errors before Editor can see them
                json.fieldErrors.length = 0;
            } else {
                ErrorData = {};
            }
        })
        .on('submitSuccess', function ( e, type ) {
            setError(type)
            calculationListDataTable.ajax.reload();

            // Ensure the editor is available for further editing
            resetEditorForFurtherEditing();  // Custom function to reset editor after success
        })
        .on('submitUnsuccessful', function ( e, type ) {
            setError(type)
        });

        document.querySelector('#save_data').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            editor.submit().clear();
        });
    });
</script>