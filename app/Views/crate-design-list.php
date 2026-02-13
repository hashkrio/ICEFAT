<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">CRATE DESIGN LISTS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-hover" id="ajax-crate-design-list-datatables">
                    <thead class="bg-dark">
                        <tr>
                            <th width="7%">Sr No.</th>
                            <th width="10%">Crate Design</th>
                            <th width="28%">Type</th>
                            <th width="16%">Material Type</th>
                            <th width="12%">Weight</th>
                            <th width="12%">Unit embodied carbon factor (kg CO2e/kg)</th>
                            <th width="15%">Embodied carbon (kg CO2e)</th>
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
<!-- /.row -->
<?= $this->include('layouts/datatable_script'); ?>
<script type="text/javascript" <?= csp_script_nonce() ?>>
const DataColumns = [
    { 
            data: null, 
            className: 'text-center',
            title: 'Sr No.', 
            render: function (data, type, row, meta) {
                return (meta.row + meta.settings._iDisplayStart + 1);
            }
        },
    { data: 'name', title: 'Crate Design'},
    { data: 'x_var_text', title: 'Type'},
    { data: 'material_type', title: 'Material Type'},
    { data: 'weight_in_kg', title: 'Weight'},
    { data: 'embodied_carbon_factor_unit_per_kg', title: 'Unit embodied carbon factor (kg CO2e/kg)'},
    { data: 'embodied_carbon_factor_in_kg', title: 'Embodied carbon (kg CO2e)'},
];

document.addEventListener('DOMContentLoaded', function(ev){
    const crateDesignListDataTable = initDataTable('ajax-crate-design-list-datatables', "<?php echo url_to('ajax.crate.design.list.data'); ?>", DataColumns, {searching: true});
});
</script>
<?= $this->endSection(); ?>