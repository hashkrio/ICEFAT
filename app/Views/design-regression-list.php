<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">DESIGN REGRESSION LISTS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-hover" id="ajax-design-regression-list-datatables">
                    <thead class="bg-dark">
                        <tr>
                            <th width="3%">&nbsp;</th>
                            <th width="7%">Sr No.</th>
                            <th width="15%">nAME</th>
                            <th width="20%">Type</th>
                            <th width="14%">m</th>
                            <th width="14%">b</th>
                            <th width="12%">x_var</th>
                            <th width="15%">notes</th>
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
<style <?= csp_style_nonce() ?>>
    div.DTE_Body div.DTE_Body_Content div.DTE_Field {
        width: 50%;
        padding: 5px 20px;
        box-sizing: border-box;
    }
    
    div.DTE_Body div.DTE_Form_Content {
        display:flex;
        flex-direction: row;
        flex-wrap: wrap;
    }
    </style>
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
            type: "hidden",
            width: "3%"
        },
        { 
            data: null, 
            className: 'text-center',
            title: 'Sr No.', 
            render: function(data, type, row, meta) {
                return `${(meta.row + meta.settings._iDisplayStart + 1)}`;
            },
            type: "hidden",
            width: "7%"
        },
        { data: 'name', title: 'Crate Design', disabled: true, width: "10%"},
        { data: 'x_var_text', title: 'Type', disabled: true, width: "10%"},
        { data: 'calculate_m', title: 'm', type: "text", width: "10%"},
        { data: 'calculate_b', title: 'b', type: "text", width: "10%"},
        { data: 'calculate_m_inches', title: 'm inches', disabled: true, width: "10%"},
        { data: 'calculate_b_inches', title: 'b inches', disabled: true, width: "10%"},
        { data: 'x_var', title: 'x_var', disabled: true, width: "10%"},
        { data: 'notes', title: 'notes', disabled: true, width: "20%"},
    ];

    document.addEventListener('DOMContentLoaded', function(ev){

        const materialQuantityListDataTable = initDataTable('ajax-design-regression-list-datatables', "<?php echo url_to('ajax.design.regression.list.data'); ?>", DataColumns, {
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
                    url: "<?php echo url_to('ajax.design.regression.update.data'); ?>",
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
            materialQuantityListDataTable.row(materialQuantityListDataTable.row(this).index()).select();
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