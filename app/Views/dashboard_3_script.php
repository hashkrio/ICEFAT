<!-- BS Stepper -->
<?= $this->include('layouts/toast_script'); ?>
<?= $this->include('layouts/float_script'); ?>
<script type="text/javascript" <?= csp_script_nonce() ?>>
    var id = 1;
    var totalSum = 0;
    var unitLBS = '<?php echo App\Enums\Unit::IMPERIAL->value; ?>';
    
    /*
     * DONUT CHART
     * -----------
     */

    const combileTitle = {
        pie: {
            show: true,
            radius: 1,
            startAngle: 2 * (Math.PI / 2), // Rotate 270 degrees
            innerRadius: 0,
            label: {
                show: true,
                radius: 1, // Moves labels further away
                formatter: function(label, series) {

                    if (series.data[0][1] == null || series.data[0][1] == "1") {
                        return 'No Data';
                    }

                    return `<div style="font-size:10px; text-align:center; color:#000000;font-weight:700" class="flot-custom-label">(${label}) : ${Number.parseFloat(series.percent).toFixed(2)}% (${series.data[0][1]})</div>`;
                },
            },
        }
    };

    var interactive_plot;
    const defaultChartDataSet = [{
        label: "No Data",
        data: 1, // To show a neutral circle (not a complete donut)
        color: '#ccc'
    }];

    document.addEventListener('DOMContentLoaded', function(ev) {
        interactive_plot = $.plot('#donut-chart',
            defaultChartDataSet, {
                series: {
                    ...combileTitle
                },
                legend: {
                    show: false
                },
                grid: {
                    hoverable: true, // Disable hover effect since we don't need it
                    clickable: false // Allow the chart to be clickable
                },
            });
    });

    /*
     * END DONUT CHART
     */
    const kgPrefix = 'kg';
    const lbsPrefix = 'lbs';
    const kgCo2Prefix = 'kg CO2e';
    const lbsCo2Prefix = 'lbs CO2e';

    const weightPrefix = () => {
        return (($("#measurement_unit_id").val() || 0) == (unitLBS || 0) ? lbsPrefix : kgPrefix)
    }

    const resultWeightPrefix = () => {
        return kgCo2Prefix;
    }

    const setPrefixSpan = (ele, value, prefix = kgCo2Prefix) => {

        $(`#${ele}`).html(`<b>${(value)} ${prefix}</b>`);
    }

    let nTrips = document.getElementById("number_of_oneway_trips");
    let elementHeight = document.getElementById('height');
    let elementWidth = document.getElementById('width');
    let elementDepth = document.getElementById('depth');

    const returnUnitTypeWeight = (data) => {
        if(($("#measurement_unit_id").val() || 0) == (unitLBS || 0)) {
            return parseFloat((data?.weight_in_kg || 0) * 2.2046).toFixed(2);
        }

        return (data?.weight_in_kg || 0);
    }

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
                let weightInKg = returnUnitTypeWeight(data);
                let embodiedCarbonFactorInKg = parseInt(data?.embodied_carbon_factor_in_kg || 0);
                $("#weight_in_kg").val(weightInKg);
                setPrefixSpan("weight_in_kg_label", weightInKg, weightPrefix());
                $("#embodied_carbon_factor_in_kg").val(embodiedCarbonFactorInKg);
                setPrefixSpan("embodied_carbon_factor_in_kg_label", embodiedCarbonFactorInKg, resultWeightPrefix());
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#weight_in_kg").val(0);
                setPrefixSpan("weight_in_kg_label", 0, weightPrefix());
                $("#embodied_carbon_factor_in_kg").val(0);
                setPrefixSpan("embodied_carbon_factor_in_kg_label", 0, resultWeightPrefix());
            }
        });
    };

    const updateCalculations = (updateAjaxValues = false) => {
        if (updateAjaxValues === true) {
            $.when(getCrateWeightAndCarbonFactor())
                .then((ajaxData) => {
                    let perOneWayTrip = Math.round(($("#embodied_carbon_factor_in_kg").val() || 0) / (nTrips.value || 1));
                    $("#per_one_way_trip").val(perOneWayTrip);
                    setPrefixSpan("per_one_way_trip_label", perOneWayTrip, resultWeightPrefix());
                    setPrefixSpan("total_result", (totalSum || 0), resultWeightPrefix());
                })
        } else {
            let perOneWayTrip = Math.round(($("#embodied_carbon_factor_in_kg").val() || 0) / (nTrips.value || 1));
            $("#per_one_way_trip").val(perOneWayTrip);
            setPrefixSpan("per_one_way_trip_label", perOneWayTrip, resultWeightPrefix());
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

        $('.' + exportClassName).prop('disabled', true);

        const requestData = dataTableList.ajax.params(); // Get current request parameters

        html2canvas(document.getElementById("chartDiv")).then(canvas => {
            // Convert canvas to a PNG image
            exportFiles(dataTableList.ajax.url(), {
                ...requestData,
                is_export: exportType,
                image: canvas.toDataURL("image/png")
            }, undefined, fileName, exportClassName)
        });
    }

    function exportFiles(export_url, formData, errorMsg = 'Something went wrong, please contact Administrator.', exportFileName, exportClassName) {
        $.ajax({
            url: export_url,
            type: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob' // Ensure the response type is 'blob' for file download
            },
            success: function(response, status, xhr) {
                try {
                    if (xhr.responseJSON) {
                        throw Error((response.message || 'An error occurred while generating the file.'));
                    } else {
                        if (xhr.getResponseHeader('content-type') == 'application/pdf' || xhr.getResponseHeader('content-type') == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {

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
                if (xhr.getResponseHeader('content-type') == 'application/json') {
                    Swal.fire('warning', errorMsg, "warning");
                } else {
                    Swal.fire('warning', errorMsg, "warning");
                }
            },
            complete: function(jqXHR, textStatus) {
                $('.' + exportClassName + '').prop('disabled', false);
            }
        });
    }


    jQuery(function($) {

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
            altEditor: true, // Enable altEditor
            layout: {
                /* topStart: {
                    buttons: [{
                            extend: 'remove',
                            editor: singleDeleteEditor,
                            text: 'Remove Crate',
                            className: 'border-0 rounded bg-primary'
                        },
                        {
                            text: 'Clear List',
                            className: 'border-0 rounded bg-primary',
                            action: function(e, dt, node, config, cb) {
                                allRowDeleteEditor.remove(dt.rows().nodes(), {
                                    title: 'Delete Records',
                                    message: 'Are you sure you want to delete all records?',
                                    buttons: 'Delete'
                                });
                            }
                        }
                    ]
                }, */
                topStart: {
                    buttons: [{
                            extend: 'selected', // Bind to Selected row
                            text: 'Delete',
                            name: 'delete', // do not change name
                            text: 'Remove Crate',
                            className: 'border-0 rounded bg-primary'
                        },
                        {
                            text: 'Delete',
                            name: 'delete-1', // do not change name
                            text: 'Clear List',
                            className: 'border-0 rounded bg-primary',
                            action: function(e, dt, node, config, cb) {
                                Swal.fire({
                                    title: "Do you want to clear all data?",
                                    showCancelButton: true,
                                    cancelButtonText: "No, cancel!",
                                    confirmButtonText: "Yes",
                                    showLoaderOnConfirm: true,
                                    preConfirm: async (login) => {
                                        try {
                                            const response = await fetch("<?php echo url_to('ajax.calculation.all.data.delete.all'); ?>", {
                                                method: 'PUT'
                                            });
                                            if (!response.ok) {
                                                return Swal.showValidationMessage(`
                                                ${JSON.stringify(await response.json())}
                                                `);
                                            }
                                            return response.json();
                                        } catch (error) {
                                            Swal.showValidationMessage(`
                                                Request failed: ${error}
                                            `);
                                        }
                                    },
                                    allowOutsideClick: () => !Swal.isLoading()
                                }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                        Swal.fire("All data cleared successfully!", "", "success");
                                        dt.ajax.reload();
                                    } else {
                                        Swal.fire("Request cancelled", "", "info");
                                        dt.ajax.reload();
                                    }
                                });

                            }
                        }
                    ]
                },
                topEnd: {
                    buttons: [{
                            text: 'Export',
                            className: 'btn_excel border-0 rounded bg-primary',
                            action: function(e, dt, node, config, cb) {
                                exportExcelDataTable(dt, 1, 'export.xlsx', 'btn_excel');
                            }
                        },
                        {
                            text: 'Print',
                            className: 'btn_pdf border-0 rounded bg-primary',
                            action: function(e, dt, node, config, cb) {
                                exportExcelDataTable(dt, 2, 'export.pdf', 'btn_pdf');
                            }
                        }
                    ]
                }
            },
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();

                // Remove the formatting to get integer data for summation
                let intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i :
                        0;
                };

                totalSum = (api?.ajax?.json()?.totalSum || 0);
                setPrefixSpan("total_result", (totalSum || 0), resultWeightPrefix());

                let economyChartValue = (api?.ajax?.json()["ECONOMY"] || 0)
                let midRangeChartValue = (api?.ajax?.json()["MID-RANGE"] || 0)
                let meseumChartValue = (api?.ajax?.json()["MUSEUM"] || 0)

                let dataSet = defaultChartDataSet;

                if (economyChartValue > 0 || midRangeChartValue > 0 || meseumChartValue > 0) {
                    dataSet = [{
                            label: "ECONOMY",
                            data: (api?.ajax?.json()["ECONOMY"] || 0),
                            color: 'rgb(68,114,196)'
                        },
                        {
                            label: "MID-RANGE",
                            data: (api?.ajax?.json()["MID-RANGE"] || 0),
                            color: 'rgb(237,125,49)'
                        },
                        {
                            label: "MUSEUM",
                            data: (api?.ajax?.json()["MUSEUM"] || 0),
                            color: 'rgb(204,204,204)'
                        }
                    ]
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
            },
            onDeleteRow: function(datatable, rowdata, success, error) {
                $.ajax({
                    url: "<?php echo url_to('ajax.calculation.all.data.delete.list'); ?>",
                    type: 'DELETE',
                    data: {
                        deleteIds: rowdata.map(t => t.DT_RowId)
                    },
                    success: success,
                    error: error
                });
            },
        }

        const DataColumns = [{
                id: "DT_RowId",
                data: "DT_RowId",
                type: "hidden",
                visible: false
            },
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
                data: 'height',
                title: 'Height'
            },
            {
                data: 'width',
                title: 'Width'
            },
            {
                data: 'depth',
                title: 'Depth'
            },
            {
                className: 'text-center',
                data: 'measurement_unit_name',
                title: 'Units'
            },
            {
                className: 'text-right',
                data: 'weight_in_kg',
                title: 'Weight (kg/lbs)'
            },
            {
                className: 'text-right',
                data: 'carbon_footprint_with_unit',
                title: 'Footprint (kg CO2e)'
            }
        ];



        const calculationListDataTable = initDataTable('ajax-calculation-list-datatables', "<?php echo url_to('ajax.calculation.all.data.list'); ?>", DataColumns, dataTableOptions)
        /* .on('click', 'td.editor-remove button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            editor.edit($(this).closest('tr'), {
                title: 'Edit record',
                buttons: ([{
                    text: 'Save',
                    className: 'btn btn-default btn-sm float-right text-red',
                    action: function() {
                        this.submit();
                    }
                }])
            });
        }); */
        const centimeterToInch = (value) => {
            return Number.parseFloat(value / 2.54).toFixed(3);
        }

        const InchTocentimeter = (value) => {
            return Number.parseFloat(value * 2.54).toFixed(3);
        }

        const changeValueByUnit = (cmToInch = false) => {
            let oldHeightValue = (elementHeight.value || 0);
            let oldWidthValue = (elementWidth.value || 0);
            let oldDepthValue = (elementDepth.value || 0);
            if (oldHeightValue != null && oldHeightValue != '' && oldHeightValue > 0) {
                elementHeight.value = (cmToInch) ? centimeterToInch(oldHeightValue) : InchTocentimeter(oldHeightValue);
            }
            if (oldWidthValue != null && oldWidthValue != '' && oldWidthValue > 0) {
                elementWidth.value = (cmToInch) ? centimeterToInch(oldWidthValue) : InchTocentimeter(oldWidthValue);
            }
            if (oldDepthValue != null && oldDepthValue != '' && oldDepthValue > 0) {
                elementDepth.value = (cmToInch) ? centimeterToInch(oldDepthValue) : InchTocentimeter(oldDepthValue);
            }
        }

        const updateInputValues = (e) => {
            if (e.target.value == 1) {
                changeValueByUnit()
                setTimeout(() => {
                    updateCalculations(true)
                }, 100);
            } else {
                changeValueByUnit(true)
                setTimeout(() => {
                    updateCalculations(true)
                }, 100);
            }
        }

        const changeVolumnUnitText = (e) => {
            document.querySelectorAll(".unit_text").forEach(function(ele) {
                if (e.target.value == 1) {
                    ele.innerHTML = ele.innerHTML.replaceAll('(in)', '(cm)')
                } else {
                    ele.innerHTML = ele.innerHTML.replaceAll('(cm)', '(in)')
                }
            });
        }

        $("#measurement_unit_id").on('change', function(e) {
            $.when(changeVolumnUnitText(e), updateInputValues(e)).
            then(() => console.log('update'));
        });

        $("#crate_type_id").on('change', function(e) {
            updateCalculations(true);
        });

        $("#height, #width, #depth").on('blur', function(e) {
            updateCalculations(true);
        });

        $(nTrips).on('blur', function(e) {
            updateCalculations()
        });

        updateCalculations(true);

        const primaryCrateTypeD = JSON.parse('<?= json_encode(esc($primaryCrateType)); ?>');

        const unitArrs = JSON.parse('<?= json_encode(esc($unitArr)); ?>');

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

        const setError = () => {

            document.querySelectorAll(`[data-editor-field]`).forEach(x => x.classList.remove('bg-danger'))

            if (Object.keys(ErrorData).length > 0) {
                for (const [key, value] of Object.entries(ErrorData)) {
                    if (document.querySelector(`[data-editor-field=${value.name}]`)) {
                        document.querySelector(`[data-editor-field=${value.name}]`).classList.add('bg-danger')
                    }
                }
            }
        }

        document.querySelector('#save_data').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            let data = [{
                'measurement_unit_id': null,
                'height': null,
                'width': null,
                'depth': null,
                'crate_type_id': null,
                'label_name': null,
                'weight_in_kg': null,
                'embodied_carbon_factor_in_kg': null,
                'number_of_oneway_trips': null,
                'per_one_way_trip': null,
            }];
            data[0]['measurement_unit_id'] = function() {
                return ($("#measurement_unit_id").val() || 0);
            };
            data[0]['height'] = function() {
                return ($("#height").val() || '');
            };
            data[0]['width'] = function() {
                return ($("#width").val() || '');
            };
            data[0]['depth'] = function() {
                return ($("#depth").val() || '');
            };
            data[0]['crate_type_id'] = function() {
                return ($("#crate_type_id").val() || '');
            };
            data[0]['label_name'] = function() {
                return ($("#label_name").val() || '');
            };
            data[0]['weight_in_kg'] = function() {
                return ($("#weight_in_kg").val() || '');
            };
            data[0]['embodied_carbon_factor_in_kg'] = function() {
                return ($("#embodied_carbon_factor_in_kg").val() || '');
            };
            data[0]['number_of_oneway_trips'] = function() {
                return ($("#number_of_oneway_trips").val() || '');
            };
            data[0]['per_one_way_trip'] = function() {
                return ($("#per_one_way_trip").val() || '');
            };
            $.ajax({
                url: '<?= url_to('ajax.carbon.footprint.new.save') ?>',
                type: 'POST',
                async: false,
                data: {
                    data
                },
                dataType: 'json',
                success: function(data, textStatus, JQueryXHR) {

                    if ((data.fieldErrors || []).length > 0) {
                        ErrorData = Object.assign({}, data.fieldErrors);
                        // Wipe out the errors before Editor can see them
                        data.fieldErrors.length = 0;
                        setError()
                    } else {
                        ErrorData = {};
                        setError()
                    }
                    calculationListDataTable.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    setError()
                }
            });
        });
    });
</script>