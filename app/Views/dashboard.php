<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<style type="text/css" {csp_style_nonce}>
    .btn-dark:not(:disabled):not(.disabled).active, .btn-dark:not(:disabled):not(.disabled):active, .show>.btn-dark.dropdown-toggle {
        color: #fff !important;
        background-color: #007bff !important;
        border-color: #171a1d5c !important;
    }
</style>
<div class="row">
    <div class="col-12">
        <!-- jquery validation -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">CALCULATOR INSTRUCTIONS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="bg-white shadow-sm">
                            <form id="step0Form" class="needs-validation" novalidate>
                                <div class="btn-group btn-group-toggle btn-toolbar" data-toggle="buttons">
                                    <?php foreach($primaryCrateType as $crateType): ?>
                                    <?php
                                        $isPrimary = $crateType['is_primary'] == "1";
                                        $isActive = $isPrimary ? 'active' : '';
                                        $isChecked = $isPrimary ? 'checked' : '';
                                    ?>
                                    <label class="btn btn-dark <?= $isActive; ?>" >
                                        <input type="radio" name="crate_type_id" id="selectCrateDesigns_<?= $crateType['id']; ?>" autocomplete="off" value="<?= $crateType['id']; ?>" <?= $isChecked; ?>> <?php echo $crateType['name']; ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </form>
                            <div id="stepperForm" class="bs-stepper">
                                <div class="bs-stepper-header" role="tablist">
                                    <div class="step" data-target="#test-form-1">
                                        <button type="button" class="step-trigger" role="tab" id="stepperFormTrigger1" aria-controls="test-form-1">
                                            <span class="bs-stepper-label">Step</span>
                                            <span class="bs-stepper-circle">1</span>
                                        </button>
                                    </div>
                                    <div class="bs-stepper-line"></div>
                                    <div class="step" data-target="#test-form-2">
                                        <button type="button" class="step-trigger" role="tab" id="stepperFormTrigger2" aria-controls="test-form-2">
                                            <span class="bs-stepper-label">Step</span>
                                            <span class="bs-stepper-circle">2</span>
                                        </button>
                                    </div>
                                    <div class="bs-stepper-line"></div>
                                    <div class="step" data-target="#test-form-3">
                                        <button type="button" class="step-trigger" role="tab" id="stepperFormTrigger3" aria-controls="test-form-3">
                                            <span class="bs-stepper-label">Step</span>
                                            <span class="bs-stepper-circle">3</span>
                                        </button>
                                    </div>
                                    <div class="bs-stepper-line"></div>
                                    <div class="step" data-target="#test-form-4">
                                        <button type="button" class="step-trigger" role="tab" id="stepperFormTrigger4" aria-controls="test-form-4">
                                            <span class="bs-stepper-label">Step</span>
                                            <span class="bs-stepper-circle">4</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="bs-stepper-content">
                                    <div id="test-form-1" role="tabpanel" class="bs-stepper-pane fade" aria-labelledby="stepperFormTrigger1">
                                        <form id="step1Form" class="needs-validation" novalidate>
                                            <div class="form-group">
                                                <select id="selectModalOptions" name="model_option_id" class="form-control custom-select" style="width:100%;">
                                                    <option value="">Select Modal Option</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="crateWeight">Based on your specifications, the weight of your crate is:</label>
                                                <input type="number" name="crate_weight" id="crateWeight" class="form-control" value="0" placeholder="Enter crate weight in kg" readonly />
                                            </div>
                                            <div class="form-group">
                                                <label for="embodiedCarbonFactorInKg">Based on your specifications, the carbon footprint of producting your crate is:</label>
                                                <input type="number" name="embodied_carbon_factor_in_kg" class="form-control" value="0" id="embodiedCarbonFactorInKg" readonly />
                                            </div>
                                            <button type="button" class="btn btn-primary btn-next-form">Next</button>
                                        </form>
                                    </div>
                                    <div id="test-form-2" role="tabpanel" class="bs-stepper-pane fade" aria-labelledby="stepperFormTrigger2">
                                        <form id="step2Form" class="needs-validation" novalidate>
                                            <div class="form-group">
                                                <label for="numberOfOneWayTrips">Enter the number of one-way trips your crate will be used for over its lifetime:</label>
                                                <input type="number" min="1" max="100" name="number_of_oneway_trips" value="1" class="form-control" id="numberOfOneWayTrips">
                                            </div>
                                            <div class="form-group">
                                                <label for="perOneWayTrip">Based on the number of trips, the carbon footprint of producing your crate per one-way trip is:</label>
                                                <input type="number" name="per_oneway_trip" class="form-control" value="0" id="perOneWayTrip" readonly />
                                            </div>
                                            <button type="button" class="btn btn-primary btn-prev-form">Previous</button>
                                            <button type="button" class="btn btn-primary btn-next-form">Next</button>
                                        </form>
                                    </div>
                                    <div id="test-form-3" role="tabpanel" class="bs-stepper-pane fade" aria-labelledby="stepperFormTrigger3">
                                        <form id="step3Form" class="needs-validation" novalidate>
                                            <h6>Enter in your transportation information per one-way trip (in km):</h6>
                                            <?php foreach ($transport_emissions as $key => $emission): ?>
                                                <div class="form-group">
                                                    <label for="shipment_<?php echo $emission['id']; ?>"><?php echo $emission['shipment']; ?></label>
                                                    <input type="number" name="shipment[<?php echo $key; ?>]" value="0" class="form-control shipments" id="shipment_<?php echo $emission['id']; ?>">
                                                </div>
                                            <?php endforeach; ?>
                                            <div class="form-group">
                                                <label for="carbonFooterPrint">Based on these distances and the weight of your crate, the carbon footprint of transporting your crate per one-way trip is:</label>
                                                <input type="number" name="carbon_footer_print" class="form-control" id="carbonFooterPrint" value="0" readonly />
                                            </div>
                                            <button type="button" class="btn btn-primary btn-prev-form">Previous</button>
                                            <button type="button" class="btn btn-primary btn-next-form">Next</button>
                                        </form>
                                    </div>
                                    <div id="test-form-4" role="tabpanel" class="bs-stepper-pane fade" aria-labelledby="stepperFormTrigger4">
                                        <form id="step4Form" class="needs-validation" novalidate>
                                            <div class="form-group">
                                                <label for="totalCarbonFootPrint">The total carbon footprint of your crate per one-way trip is:</label>
                                                <input type="number" name="total_carbon_foot_print" class="form-control" value="0" id="totalCarbonFootPrint" readonly />
                                            </div>
                                            <button type="button" class="btn btn-primary btn-prev-form">Previous</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-primary float-right" onclick="restartCalculation()"><i class="fas fa-caret-right"></i> Start</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div id="donut-chart" style="height: 300px;"></div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>

</div>
<!-- /.row -->
<?= $this->endSection(); ?>