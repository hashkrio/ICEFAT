<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/jquery-3.7.1.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/bootstrap/js/bootstrap.js'); ?>" <?= csp_script_nonce() ?>></script>

<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/dataTables.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/dataTables.responsive.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/responsive.dataTables.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/dataTables.buttons.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/buttons.dataTables.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/dataTables.select.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/select.dataTables.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/dataTables.dateTime.min.js'); ?>" <?= csp_script_nonce() ?>></script>
<!-- <script defer src="<?php //echo site_url('plugins/Editor-2.3.2/js/dataTables.editor.js'); ?>" <?= csp_script_nonce() ?>></script> -->
<!-- <script defer src="<?php //echo site_url('plugins/Editor-2.3.2/js/editor.dataTables.js'); ?>" <?= csp_script_nonce() ?>></script> -->
<script defer src="<?php echo site_url('dist/js/dataTables.altEditor.free.js'); ?>" <?= csp_script_nonce() ?>></script>

<script defer src="<?php echo site_url('plugins/datatables-fixedheader/js/fixedHeader.bootstrap4.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/datatables-scroller/js/scroller.bootstrap4.js'); ?>" <?= csp_script_nonce() ?>></script>

<script defer src="<?php echo site_url('plugins/sweetalert2/sweetalert2.js'); ?>" <?= csp_script_nonce() ?>></script>
<script defer src="<?php echo site_url('plugins/Editor-2.3.2/dist/js/html2canvas.min.js'); ?>"></script>

<script <?= csp_script_nonce() ?>>
    const initDataTable = (tableId, apiUrl, columns, options = {}) => {
        var defaultDataTableOptions = {
            bAutoWidth: false,
            serverSide: true,
            deferRender: true,
            destroy: true, // Clean up any previous instance
            ordering: false,
            sorting: false,
            searching:false,
            order: [],
            ajax: {
                url  : apiUrl,
                type: 'POST',
                data: function(d) {
                    d.searchVal = d.search.value; // Capture search term
                },
            },
            columns: columns,
            ...options
        };

        // Merge the default options with any additional ones passed to the function
        var mergeOptions = $.extend({}, defaultDataTableOptions, options);

        return $('#'+tableId+'').DataTable(mergeOptions);
    };
</script>