<!-- BS Stepper -->
<script src="<?php echo site_url('plugins/bs-stepper/js/bs-stepper.min.js'); ?>" <?= csp_script_nonce() ?>></script>
<?= $this->include('layouts/toast_script'); ?>
<?= $this->include('layouts/float_script'); ?>
<script type="text/javascript" <?= csp_script_nonce() ?>>
    var stepperFormEl = document.querySelector('#stepperForm')
    var stepperForm = new Stepper(stepperFormEl, {
        animation: true
    });
    var btnNextList = [].slice.call(document.querySelectorAll('.btn-next-form'))
    var btnPrevList = [].slice.call(document.querySelectorAll('.btn-prev-form'))
    var stepperPanList = [].slice.call(stepperFormEl.querySelectorAll('.bs-stepper-pane'))
    var inputMailForm = document.getElementById('inputMailForm')
    var inputPasswordForm = document.getElementById('inputPasswordForm')
    var form = stepperFormEl.querySelector('.bs-stepper-content form')

    const restartCalculation = () => {
        stepperForm.to(0)
    }

    jQuery(function ($) {

        var step0Form = $( "#step0Form" );
        var step1Form = $( "#step1Form" );
        var step2Form = $( "#step2Form" );
        var step3Form = $( "#step3Form" );
        var step4Form = $( "#step4Form" );

        btnNextList.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation()
                stepperForm.next()
            })
        })

        btnPrevList.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation()
                stepperForm.previous()
            })
        })

        stepperFormEl.addEventListener('show.bs-stepper', function (event) {
            form.classList.remove('was-validated')
            
            if(!step0Form.valid()) {
                event.preventDefault()
                form.classList.add('was-validated');
            } 
            if(!step1Form.valid()) {
                event.preventDefault()
                form.classList.add('was-validated');
            } 
            if(!step2Form.valid()) {
                event.preventDefault()
                form.classList.add('was-validated');
            } 
            if(!step3Form.valid()) {
                event.preventDefault()
                form.classList.add('was-validated');
            }
        });
        
        let nTrips = document.querySelector("input[name='number_of_oneway_trips']");

        const numberOfOneWayTripValue = () => {
            numberOfOnewayTrips  = $('#numberOfOneWayTrips').val();

            if(isNaN(numberOfOnewayTrips)) {
                return 1;
            }

            if(numberOfOnewayTrips <= 0) {
                return 1;
            }

            return numberOfOneWayTrips;
        }

        const embodiedCarbonFactorValue = () => {

            if(isNaN(embodiedCarbonFactorInKg)) {
                return 0;
            }
            
            if(embodiedCarbonFactorInKg <= 0) {
                return 0;
            }

            return embodiedCarbonFactorInKg;
        }

        const updatePerOneWayTrip = () => {
            perOneWayTrip = parseInt(($("#embodiedCarbonFactorInKg").val() || 0) / (nTrips.value || 1));
        }
        
        const getCrateWeightAndCarbonFactor = () => {
            
            let selectedModalOption = $('#selectModalOptions').val();
            let selectedCrateDesign = $('input[type="radio"]:checked').val();
            let requestUrl = 'filter/caret_weight' + '/' + (selectedModalOption || 0) + '/weight/' + (selectedCrateDesign || 0);

            $.ajax({
                url: requestUrl,
                type: 'POST',
                async: false,
                dataType: 'json',
                success: function(data, textStatus, JQueryXHR) {
                    $("#crateWeight").val(data?.weight_in_kg || 0);
                    $("#embodiedCarbonFactorInKg").val(parseInt(data?.embodied_carbon_factor_in_kg || 0));
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#crateWeight").val(0);
                    $("#embodiedCarbonFactorInKg").val(0);
                }
            });
        };
        
        const getcarbonFootPrintValue = () => {
            var inputShipment1 = document.querySelector('input[name="shipment[0]"]');
            var inputShipment2 = document.querySelector('input[name="shipment[1]"]');
            var inputShipment3 = document.querySelector('input[name="shipment[2]"]');
            var inputShipment4 = document.querySelector('input[name="shipment[3]"]');
            let air_passanger = inputShipment1.value || 0;
            let air_freight = inputShipment2.value || 0;
            let road_freight = inputShipment3.value || 0;
            let sea_freight = inputShipment4.value || 0;
            let requestUrl = 'filter/carbon_footprint/'+air_passanger+'/air_passanger/'+air_freight+'/air_freight/'+road_freight+'/road_freight/'+sea_freight+'/sea_freight/'+($("#crateWeight").val() || 0)+'';
            
            $.ajax({
                url: requestUrl,
                type: 'POST',
                async: false,
                dataType: 'json',
                success: function(data, textStatus, JQueryXHR) {
                    $("#carbonFooterPrint").val(parseInt(data?.carbon_foot_print_value || 0));
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#carbonFooterPrint").val(0);
                }
            });
        };

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
                }
            }
        };

        var interactive_plot = $.plot('#donut-chart', [{
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
        
        const updateChartData = () => {
            let perOneWayTrip = ($("#perOneWayTrip").val() || 0);
            let carbonFooterPrint = ($("#carbonFooterPrint").val() || 0);

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
            + Math.round(series.percent) + '%</div>'
            if(series == undefined) return label;
        }
        
        const updateCalculations = (updateAjaxValues = false) => {
            if(updateAjaxValues === true) {
                $.when(getCrateWeightAndCarbonFactor()).then((ajaxData) => {
                    $("#perOneWayTrip").val(parseInt(($("#embodiedCarbonFactorInKg").val() || 0) / (nTrips.value || 1)));
                    $.when(getcarbonFootPrintValue())
                    .then((r) => {
                        $("#totalCarbonFootPrint").val(parseInt(($("#carbonFooterPrint").val() || 0)) + parseInt((parseInt(($("#embodiedCarbonFactorInKg").val() || 0) / (nTrips.value || 1)))));
                        updateChartData()
                    })
                })
            } else {
                $("#perOneWayTrip").val(parseInt(($("#embodiedCarbonFactorInKg").val() || 0) / (nTrips.value || 1)));
                $.when(getcarbonFootPrintValue())
                .then((r) => {
                    $("#totalCarbonFootPrint").val(parseInt(($("#carbonFooterPrint").val() || 0)) + parseInt((parseInt(($("#embodiedCarbonFactorInKg").val() || 0) / (nTrips.value || 1)) || 0)));
                    updateChartData()
                })
            }
        }

        $.validator.setDefaults({
            submitHandler: function (form) {
                return false;
            }
        });

        $('#step0Form').validate({
            rules: {
                crate_type_id: {
                    required: true,
                },
            },
            messages: {},
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.btn-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                return false;
            }
        });

        $('#step1Form').validate({
            rules: {
                model_option_id: {
                    required: true,
                },
                crate_weight: {
                    required: true,
                },
                embodied_carbon_factor_in_kg: {
                    required: true,
                },
            },
            messages: {},
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                return false;
            }
        });
        
        $('#step2Form').validate({
            rules: {
                number_of_oneway_trips: {
                    required: true,
                    minlength: 1,
                    min:1,
                    max:100
                },
                per_oneway_trip: {
                    required: true,
                    minlength: 1,
                    min:1
                },
            },
            messages: {},
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                return false;
            }
        });
        
        $('#step3Form').validate({
            rules: {
                "shipment[]": {
                    required: true,
                    minlength: 1,
                    min:1,
                    max:100000
                },
            },
            messages: {},
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                return false;
            }
        });
        
        $('#step4Form').validate({
            rules: {
                total_carbon_foot_print: {
                    required: true,
                },
            },
            messages: {},
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                try {
                    if(step0Form.valid() && step1Form.valid() && step2Form.valid() && step3Form.valid()) {
                        let formData = { ...Object.fromEntries(new FormData(step0Form[0]).entries()), ...Object.fromEntries(new FormData(step1Form[0]).entries()), ...Object.fromEntries(new FormData(step2Form[0]).entries()), ...Object.fromEntries(new FormData(step3Form[0]).entries()), ...Object.fromEntries(new FormData(step4Form[0]).entries())}
                        
                        $.ajax({
                            url: '<?= url_to('ajax.carbon.footprint.save') ?>',
                            type: 'POST',
                            async: false,
                            dataType: 'json',
                            data: formData,
                            success: function(data, textStatus, JQueryXHR) {
                                toastr.info('Data saved Successfully');
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                toastr.warning('Failed to save data')
                            }
                        });
                    } else {
                        toastr.warning('Failed to save data')
                    }
                } catch(error) {
                    toastr.warning('Failed to save data')
                }
                return false;
            }
        });

        $.when(
            $('#selectModalOptions').select2({
                ajax: {
                    url: '<?= url_to('ajax.modal.options') ?>',
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    minimumInputLength: 0,
                    processResults: function(data){
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            })
        ).then(function(ModalOptions) {

            $(ModalOptions).on('change',function(e) {
                updateCalculations(true);
            })

            $('input[type="radio"]').on('change',function(e) {
                updateCalculations(true);
            })

            $("#perOneWayTrip").on('input', function(e){
                updateCalculations()
            });
            $("input.shipments").on('input', function(e) {
                updateCalculations();
            })
        })
    });
</script>