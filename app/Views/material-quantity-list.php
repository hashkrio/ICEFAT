<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">MATERIAL QUANTITY LISTS</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="ajax-material-quantity-list-datatables">
                        <thead class="bg-dark">
                            <tr>
                                <th width="3%">&nbsp;</th>
                                <th width="3%">&nbsp;</th>
                                <th width="7%">Sr No.</th>
                                <th width="15%">Type</th>
                                <th width="33%">Material Type</th>
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
            className: 'dt-control',
            orderable: false,
            defaultContent: '',
            type: "hidden",
        },
        {
            data: null,
            title: "Actions",
            name: "Actions",
            className: 'dt-center editor-edit editbutton',
            defaultContent: '',
            defaultContent: '<button type="button" class="btn btn-link"><i class="fas fa-edit"/></button>',
            type: "hidden",
            orderable: false,
        },
        { 
            data: null, 
            className: 'text-center',
            title: 'Sr No.', 
            render: function(data, type, row, meta) {
                return `${(meta.row + meta.settings._iDisplayStart + 1)}`;
            },
            type: "hidden",
        },
        { 
            id: 'main_type', 
            data: 'main_type', 
            title: 'Type',
            disabled: true,
        },
        { 
            id: 'material_type', 
            data: 'material_type', 
            title: 'Material Type',
            disabled: true,
        },
        { 
            id: 'weight_in_kg', 
            data: 'weight_in_kg', 
            title: 'Weight', 
            type: "text",
        },
        { 
            id: 'embodied_carbon_factor_unit_per_kg', 
            data: 'embodied_carbon_factor_unit_per_kg', 
            title: 'Unit embodied carbon factor (kg CO2e/kg)',
            type: "text",
        },
        { 
            id: 'embodied_carbon_factor_in_kg', 
            data: 'embodied_carbon_factor_in_kg', 
            title: 'Embodied carbon (kg CO2e)',
            type: "readonly"
        },
        {
            id: 'data_source',
            data: 'data_source',
            title: 'Data Source',
            type: "text",
            visible: false
        },
        {
            id: 'site_link',
            data: 'site_link',
            title: 'Site Link',
            type: "text",
            visible: false
        },
        {
            id: 'notes',
            data: 'notes',
            title: 'Notes',
            type: "text",
            visible: false
        }
    ];

    
    document.addEventListener('DOMContentLoaded', function(ev){
    
        const dataTableOptions = {
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
                    url: "<?php echo url_to('ajax.material.quantity.update.data'); ?>",
                    type: 'PUT',
                    data: rowdata,
                    success: function(response) {
                        $.when(success(response)).then(toastr.success('Record Saved.'))
                    },
                    error: function(response) {
                        $.when(error(response)).then(toastr.error('Record Saved.'))
                    }
                });
            },
        }

        function format ( rowData ) {
            var div = $('<div/>')
                .addClass( 'loading' )
                .text( 'Loading...' );
        
            $.ajax({
                url: "<?= site_url('filter/material_quantity_list_data/details') ?>/" + rowData.DT_RowId,
                method: "POST",
                dataType: 'json',
                success: function(data) {
                    // Populate the child row with user details
                    div.html(`<div class="bg-gray-light m-1 rounded">
                        <fieldset class="bg-gray-light border p-2 rounded shadow" id="${data.id}">
                            <legend class="w-auto"><button data-id="${data.id}" type="button" class="btn btn-sm btn-primary">Other Data</button></legend>
                            <div class="form-group">
                                <label>Data Source: </label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <input type="text" id="data_source_${data.id}" class="form-control" value="${data.data_source || ''}" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Site Link: </label>
                                <div class="row">
                                    <div class="col-xl-8">    
                                        <input type="text" id="site_link_${data.id}" class="form-control" value="${data.site_link || ''}" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Notes: </label>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <input type="text" id="notes_${data.id}" class="form-control" value="${data.notes || ''}" disabled />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>`)
                    .removeClass( 'loading' );;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    div.html('No data').removeClass( 'loading' );
                }
            });
        
            return div;
        }
        
        const materialQuantityListDataTable = initDataTable('ajax-material-quantity-list-datatables', "<?php echo url_to('ajax.material.quantity.list.data'); ?>", DataColumns, dataTableOptions)
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
        }).on('click', 'td.dt-control', function (e) {
            let tr = e.target.closest('tr');
            let row = materialQuantityListDataTable.row(tr);
        
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.classList.remove('shown');
            }
            else {
                // Open this row                
                row.child(format(row.data())).show();
                tr.classList.add('shown')
            }
        });
    })
    

</script>
<?= $this->endSection(); ?>