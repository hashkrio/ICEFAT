<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<form id="calculationForm" class="needs-validation" novalidate>
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
                <div class="table-responsive">
                    <?php
                        $first="10%";
                        $second="60%";
                        $third="30%";
                    ?>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <td width="<?=$first;?>" class="p-0">&nbsp;</td>
                                <td width="<?=$second;?>" class="p-0">&nbsp;</td>
                                <td width="<?=$third;?>" class="p-0">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Insert data in <span class="orange-input-color">orange</span> cells</td>
                                <td width="<?=$third;?>" class="p-0"><div class="orange-input-bg-color border-dark form-control p-0 rounded-0">&nbsp;</div></td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Results will calculate in <span class="blue-input-color">blue</span> cells</td>
                                <td width="<?=$third;?>" class="p-0"><div class="blue-input-bg-color border-dark form-control p-0 rounded-0">&nbsp;</div></td>
                            </tr>
                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="100%" colspan="3" class="border"><b>CALCULATOR INSTRUCTIONS</b></td>
                            </tr>
                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>
    
                            <tr>
                                <td width="<?=$first;?>"><b>Step 1: </b></td>
                                <td width="<?=$second;?>">Choose if you want to use metric or imperial units</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <select class="form-control custom-select orange-input-bg-color border-dark rounded-0" style="width: 100%;" id="measurement_unit_id">
                                        <option value="Metric">Metric</option>
                                        <option value="Imperial">Imperial</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Object height (cm)</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" value="100" id="height" />
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Object width (cm)</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" value="100" id="width" />
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Object depth (cm)</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" value="5" id="depth" />
                                </td>
                            </tr>
    
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">Choose Crate Design</td>
                                <td width="<?=$third;?>" class="p-0 border-0">
                                    <select class="form-control custom-select orange-input-bg-color border-dark rounded-0" id="crate_type_id">
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
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" id="label_name" />
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
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">The weight of your crate is:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="number" min="0" max="10000" class="form-control blue-input-bg-color border-dark rounded-0" id="weight_in_kg" readonly />
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">The carbon footprint of producing your crate is:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="number" min="0" max="10000" class="form-control blue-input-bg-color border-dark rounded-0" id="embodied_carbon_factor_in_kg" readonly />
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
                                    <input type="number" min="0" max="10000" class="form-control orange-input-bg-color border-dark rounded-0" id="number_of_oneway_trips" />
                                </td>
                            </tr>
                            <tr>
                                <td width="<?=$first;?>">&nbsp;</td>
                                <td width="<?=$second;?>">The carbon footprint of producing your crate per one-way trip is:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="number" min="0" max="10000" class="form-control blue-input-bg-color border-dark rounded-0" id="per_one_way_trip" readonly />
                                </td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="border-bottom p-0">&nbsp;</td>
                            </tr>

                            <tr>
                                <td width="100%" colspan="3" class="p-0">&nbsp;</td>
                            </tr>

                            <tr>
                                <td width="<?=$first;?>"><b>Results: </b></td>
                                <td width="<?=$second;?>">The total carbon footprint is:</td>
                                <td width="<?=$third;?>" class="p-0">
                                    <input type="number" min="0" max="10000" class="form-control blue-input-bg-color border-dark rounded-0" id="total_result" readonly />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="donut-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
</form>
<?= $this->include('layouts/datatable_script'); ?>
<?= $this->endSection(); ?>