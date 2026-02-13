<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">TRANSPORT EMISSION FACTOR LISTS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-hover" id="ajax-transportation-emission-factor-list-datatables">
                    <thead class="bg-dark">
                        <tr>
                            <th width="3%">&nbsp;</th>
                            <th width="7%">Sr No.</th>
                            <th width="10%">Transportation</th>
                            <th width="10%">Shipment</th>
                            <th width="14%">Carbon emissions g CO2e/kg-km</th>
                            <th width="5%">Data source</th>
                            <th width="31%">[Site]</th>
                            <th width="20%">Notes</th>
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
<?= $this->include('layouts/toast_script'); ?>
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
            className: 'dt-center editor-edit editbutton',
            defaultContent: '<button class="btn btn-link"><i class="fas fa-edit"/></button>',
            orderable: false,
            targets: 0,
            type: "hidden",
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
            type: "hidden",
        },
        { data: 'transportation', title: 'Transportation', disabled: true},
        { data: 'shipment', title: 'Shipment', disabled: true},
        { data: 'carbon_emission', title: 'Carbon emissions g CO2e/kg-km', type: "text"},
        { data: 'data_source', title: 'Data source', type: "text"},
        { data: 'site_link', title: '[Site]', type: "text"},
        { data: 'notes', title: 'Notes', type: "text"},
    ];

    document.addEventListener('DOMContentLoaded', function(ev){

        const tranportationEmissionFactorListDataTable = initDataTable('ajax-transportation-emission-factor-list-datatables', "<?php echo url_to('ajax.transporation.emission.factor.list.data'); ?>", DataColumns, {
            altEditor: true,     // Enable altEditor
            buttons: [],         // no buttons, however this seems compulsory
            select: {
                style: 'single',
                toggleable: false,
                className: 'shown'
            },
            searching: true, 
            processing: true, 
            serverSide: true,
            onEditRow: function(datatable, rowdata, success, error) {
                $.ajax({
                    url: "<?php echo url_to('ajax.transporation.emission.factor.update.data'); ?>",
                    type: 'PUT',
                    data: rowdata,
                    success: function(response) {
                        $.when(success(response)).then(toastr.success('Record Saved.'))
                    },
                    error: function(response) {
                        $.when(error(response)).then(toastr.error('Record failed to save.'))
                    }
                });
            },
        })
        .on('click', "td.editbutton",  'tr', function (e) {
            var tableID = $(this).closest('table').attr('id');    // id of the table
            tranportationEmissionFactorListDataTable.row(tranportationEmissionFactorListDataTable.row(this).index()).select();
            var that = $( '#'+tableID )[0].altEditor;
            that._openEditModal();
            $('#altEditor-edit-form-' + that.random_id)
                        .off('submit')
                        .on('submit', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            that._editRowData();
                        });
        })
    })
    

</script>
<?= $this->endSection(); ?>