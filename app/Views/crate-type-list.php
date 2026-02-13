<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">CRATE TYPE LISTS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-hover" id="ajax-crate-type-list-datatables">
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
<?= $this->include('layouts/toast_script'); ?>
<?= $this->include('layouts/datatable_script'); ?>
<script type="text/javascript" <?= csp_script_nonce() ?>>

    function getData (event) {
        
        if (event.stopPropagation) {
            event.stopPropagation();
        }
        else if (window.event) {
            window.event.cancelBubble = true;
        }
        console.log($(event.currentTarget).closest('tr'))
        var row = $(event.currentTarget).closest('tr');  // Find the closest table row
        var rowData = $('#ajax-crate-type-list-datatables').DataTable().row(row).data();  // Get data for that row
        
        $.ajax({
            url: '<?= url_to('ajax.crate.type.primary.save') ?>',
            type: 'POST',
            async: false,
            dataType: 'json',
            data: {crate_type_id : rowData.id},
            success: function(data, textStatus, JQueryXHR) {
                $('#ajax-crate-type-list-datatables').DataTable().ajax.reload()
                // toastr.info('Data saved Successfully');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // toastr.warning('Failed to save data')
            }
        });
    };
    
    const DataColumns = [
        { 
            data: null, 
            title: 'Sr No.', 
            className: 'text-center',
            render: function (data, type, row, meta) {
                let checked = row.is_primary == "1" ? 'checked' : '';
                return "<input type=\"radio\" id=\"set_primary_"+row.id+"\" name=\"set_primary\" onclick=\"getData(event)\" "+checked+" />";
            }
        },
        { 
            data: 'name', 
            title: 'Name',
            render: function (data, type, row, meta) {
                if(row.is_primary == "1") {
                    return `<div class="row">
                        <div class="col-sm-10">
                            ${row.name}
                        </div>
                        <div class="col-sm-2">
                            <span class="badge badge-primary">Primary</span>
                        </div>
                    </div>`;
                }

                return row.name;
            }
        },
    ];

    document.addEventListener('DOMContentLoaded', function(ev){
        const materialQuantityListDataTable = initDataTable('ajax-crate-type-list-datatables', "<?php echo url_to('ajax.crate.type.list.data'); ?>", DataColumns, {searching: true});
    })
    

</script>
<?= $this->endSection(); ?>