<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">USER LISTS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-hover" id="ajax-user-list-datatables">
                    <thead class="bg-dark">
                        <tr>
                            <th width="3%">&nbsp;</th>
                            <th width="8%">Sr No.</th>
                            <th width="32%">Full Name.</th>
                            <th width="32%">Username</th>
                            <th width="25%">Email</th>
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
<link rel="stylesheet" href="<?php echo site_url('plugins/select2/css/select2.css'); ?>" />
<script defer src="<?php echo site_url('plugins/select2/js/select2.js'); ?>" ></script>
<script type="text/javascript" <?= csp_script_nonce() ?>>
    const DataColumns = [
        {
            id: "DT_RowId",
            data: "DT_RowId",
            type: "hidden",
            visible: false
        },
        { 
            data: null, 
            className: 'text-center',
            title: 'Sr No.', 
            orderable: false,
            render: function (data, type, row, meta) {
                return (meta.row + meta.settings._iDisplayStart + 1);
            },
            targets: 0,
            type:"hidden"
        },
        { 
            data: "first_name", 
            className: 'text-center',
            title: 'Full Name', 
            orderable: false,
            render: function (data, type, row, meta) {
                return [(row.users.first_name || ''),(row.users.last_name || '')].join(' ');
            },
            type:"hidden"
        },
        { data: 'users.first_name', title: 'First Name', visible: false, type:"text"},
        { data: 'users.last_name', title: 'Last Name', visible: false, type:"text"},
        { data: 'users.username', title: 'Username',type:"text"},
        { data: 'users.email', title: 'Email',type:"text"},
        { data: 'users.password', title: 'Password', visible: false, type:"password"},
        { data: 'users.created_at', title: 'Date', className: 'text-center', type:"hidden", visible: false, disabled:true},
    ];

    document.addEventListener('DOMContentLoaded', function(ev){

        const userListDataTable = initDataTable('ajax-user-list-datatables', "<?php echo url_to('ajax.users.lists'); ?>", DataColumns, {
            searching: true, 
            processing: true, 
            serverSide: true, 
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: 'Add',
                            name: 'add'        // do not change name
                        },
                        {
                            extend: 'selected', // Bind to Selected row
                            text: 'Edit',
                            name: 'edit'        // do not change name
                        },
                        {
                            extend: 'selected', // Bind to Selected row
                            text: 'Delete',
                            name: 'delete'      // do not change name
                        },
                        {
                            text: 'Refresh',
                            name: 'refresh'      // do not change name
                        }
                    ],
                }
            },
            altEditor: true,     // Enable altEditor
            select: {
                style: 'single',
                toggleable: false,
                className: 'shown'
            },
            onAddRow: function(datatable, rowdata, success, error) {
                $.ajax({
                    url: "<?php echo url_to('ajax.update.create.user'); ?>",
                    type: 'PATCH',
                    data: rowdata,
                    success: success,
                    error: error
                });
            },
            onDeleteRow: function(datatable, rowdata, success, error) {
                $.ajax({
                    url: "filter/delete_user/" + rowdata[0].DT_RowId,
                    type: 'DELETE',
                    data: rowdata,
                    success: success,
                    error: error
                });
            },
            onEditRow: function(datatable, rowdata, success, error) {
                $.ajax({
                    url: "<?php echo url_to('ajax.update.create.user'); ?>",
                    type: 'PUT',
                    data: rowdata,
                    success: success,
                    error: error
                });
            }
        });
    })
    

</script>
<?= $this->endSection(); ?>