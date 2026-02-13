<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-10 offset-1">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title"><b>ICEFAT Carbon Calculator Tool v0.6.</b></h3>
                <span class="float-right"><?=date('d-M-Y');?></span>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
            
                <div class="table-responsive" id="customTemplateForm">
                    <?php
                        $first="10%";
                        $second="60%";
                        $third="30%";
                    ?>
                    <table class="table table-sm table-borderless" id="customRow">
                        <tbody>
                            <tr>
                                <td width="100%" colspan="3" class="border-bottom"><b>CALCULATOR INSTRUCTIONS</b></td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>" class="p-0">&nbsp;</td>
                                <td width="<?=$second;?>" class="p-0">&nbsp;</td>
                                <td width="<?=$third;?>" class="p-0">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>" class="p-0">&nbsp;</td>
                                <td width="90%" colspan="2" class="p-0">Insert data in <span class="orange-input-color">orange</span> cells, results will calculate in <span class="blue-input-color">blue</span> cells</td>
                            </tr>
                            
                            <tr>
                                <td width="100%" colspan="3" class="p-0" style="border-bottom: 3px solid;">&nbsp;</td>
                            </tr>
                            
                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>
    
                            <tr>
                                <td width="<?=$first;?>"><b>Step 1: </b></td>
                                <td width="<?=$second;?>">Choose if you want to use metric or imperial units</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <select class="form-control custom-select orange-input-bg-color border-dark rounded-0" style="width: 100%;" data-editor-field="measurement_unit_id" id="measurement_unit_id">
                                        <?php foreach(\App\Enums\Unit::array() as $k => $v): ?>
                                            <option value="<?=$k?>"><?=$v?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>" class="unit_text">Object height (cm)</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" data-editor-field="height" value="100.000" id="height" />
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>" class="unit_text">Object width (cm)</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" data-editor-field="width" value="100.000" id="width" />
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>" class="unit_text">Object depth (cm)</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" data-editor-field="depth" value="5.000" id="depth" />
                                </td>
                            </tr>
    
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Choose Crate Design</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <select class="form-control custom-select orange-input-bg-color border-dark rounded-0" data-editor-field="crate_type_id" id="crate_type_id">
                                        <?php foreach($primaryCrateType as $crateType): ?>
                                            <?php
                                                $isPrimary = $crateType['is_primary'] == "1";
                                                $isActive = $isPrimary ? 'active' : '';
                                                $isSelected = $isPrimary ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo $crateType['id']; ?>" <?=$isSelected?>><?php echo $crateType['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
    
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Crate label</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <input type="text" class="form-control orange-input-bg-color border-dark rounded-0" data-editor-field="label_name" id="label_name" />
                                </td>
                            </tr>

                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">The weight of your crate is:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="hidden" id="weight_in_kg" data-editor-field="weight_in_kg" />
                                    <span class="form-control blue-input-bg-color border-dark rounded-0" id="weight_in_kg_label"></span>
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">The carbon footprint of producing your crate is:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="hidden" id="embodied_carbon_factor_in_kg" data-editor-field="embodied_carbon_factor_in_kg" />
                                    <span class="form-control blue-input-bg-color border-dark rounded-0" id="embodied_carbon_factor_in_kg_label"></span>
                                </td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="border-bottom p-0">&nbsp;</td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>

                            <tr>
                                <td width="<?=$first;?>"><b>Step 2: </b></td>
                                <td width="<?=$second;?>">Enter the number of one-way trips your crates will be used for over:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" data-editor-field="number_of_oneway_trips" value="1" id="number_of_oneway_trips" />
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">The carbon footprint of producing your crate per one-way trip is:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="hidden" data-editor-field="per_one_way_trip" id="per_one_way_trip"/>
                                    <span class="form-control blue-input-bg-color border-dark rounded-0" id="per_one_way_trip_label"></span>
                                </td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>

                            <tr>
                                <td width="50%" colspan="2">&nbsp;</td>
                                <td width="50%" class="text-center p-0" ><button type="button" class="btn-app rounded" id="save_data">Add Crate</button></td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>
                            
                            <tr>
                                <td width="<?=$first;?>"><b>Results: </b></td>
                                <td width="<?=$second;?>">The Total shipment carbon footprint is (List Below):</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <span class="form-control blue-input-bg-color border-dark rounded-0" id="total_result"></span>
                                </td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="border-bottom p-0">&nbsp;</td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive" id="customTemplate2">
                    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table table-striped" id="example">
                    </table>
                </div>
                <div id="chartDiv">
                    <div class="text-center text-black-50 mb-2">Carbon footprint (kg CO2e)</div>
                    <div id="donut-chart" style="height: 300px;"></div>
                </div>

                <div class="table-responsive" id="customTemplate">
                    <table class="table table-bordered table-hover table-responsive-sm" id="ajax-calculation-list-datatables">
                        <thead class="bg-dark">
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('layouts/datatable_script'); ?>
<?= $this->endSection(); ?>