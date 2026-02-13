<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-6 offset-3">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">MODEL OPTION LISTS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-hover" id="ajax-model-option-list-datatables">
                    <thead class="bg-dark">
                        <tr>
                            <th width="10%">Sr No.</th>
                            <th width="90%">Name</th>
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
        { data: 'name', title: 'Name'},
    ];

    document.addEventListener('DOMContentLoaded', function(ev){
        const modelOptionListDataTable = initDataTable('ajax-model-option-list-datatables', "<?php echo url_to('ajax.model.option.list.data'); ?>", DataColumns, {searching:true});
    })
    

</script>
<?= $this->endSection(); ?>