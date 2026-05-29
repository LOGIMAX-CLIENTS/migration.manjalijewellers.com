<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            Stock Age Master
            <small>Manage inventory age ranges</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Stock Age Master</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Stock Age Master List</h3>
                        <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/admin_stock_age_master/form'); ?>">
                            <i class="fa fa-plus"></i> Add New
                        </a>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <!-- Alert -->
                        <?php if ($this->session->flashdata('success')) { ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-check"></i> Success!</h4>
                                <?php echo $this->session->flashdata('success'); ?>
                            </div>
                        <?php } ?>

                        <?php if ($this->session->flashdata('error')) { ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php } ?>

                        <div class="table-responsive">
                            <table id="age_master_table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Age From (Days)</th>
                                        <th>Age To (Days)</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </section><!-- /.content -->
<!-- Toast Container (top-right, non-blocking) -->
<div id="toast-container" style="position:fixed;top:15px;right:15px;z-index:9999;min-width:300px;max-width:400px;"></div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="confirmModalTitle">Confirm</h4>
            </div>
            <div class="modal-body">
                <p id="confirmModalBody">Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmModalYes">Yes, Proceed</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Toast styles for Bootstrap 3 */
    .sam-toast {
        padding: 12px 20px;
        margin-bottom: 10px;
        border-radius: 4px;
        color: #fff;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        opacity: 0;
        transform: translateX(50px);
        transition: all 0.35s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sam-toast.show {
        opacity: 1;
        transform: translateX(0);
    }
    .sam-toast.toast-success { background-color: #00a65a; }
    .sam-toast.toast-error   { background-color: #dd4b39; }
    .sam-toast.toast-info    { background-color: #00c0ef; }
    .sam-toast .toast-close {
        margin-left: 15px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        opacity: 0.7;
        background: none;
        border: none;
        color: #fff;
        line-height: 1;
    }
    .sam-toast .toast-close:hover { opacity: 1; }
    .sam-toast .toast-icon { margin-right: 10px; }
</style>

<script>
(function initStockAgeMaster() {
    if (typeof $ === 'undefined' || typeof $.fn === 'undefined' || typeof $.fn.DataTable === 'undefined') {
        return setTimeout(initStockAgeMaster, 100);
    }
    var base_url = '<?php echo base_url(); ?>';

    // ── Toast helper ──
    function showToast(message, type) {
        type = type || 'info';
        var icons = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle' };
        var icon = icons[type] || icons.info;
        var $toast = $(
            '<div class="sam-toast toast-' + type + '">' +
                '<span><i class="fa ' + icon + ' toast-icon"></i>' + message + '</span>' +
                '<button class="toast-close">&times;</button>' +
            '</div>'
        );
        $('#toast-container').append($toast);
        // Trigger reflow then show
        setTimeout(function() { $toast.addClass('show'); }, 30);
        // Auto-dismiss after 3 seconds
        var autoHide = setTimeout(function() { dismissToast($toast); }, 3000);
        $toast.find('.toast-close').on('click', function() {
            clearTimeout(autoHide);
            dismissToast($toast);
        });
    }
    function dismissToast($toast) {
        $toast.removeClass('show');
        setTimeout(function() { $toast.remove(); }, 350);
    }

    // ── Confirm modal helper ──
    function showConfirm(title, message, btnClass, callback) {
        $('#confirmModalTitle').text(title);
        $('#confirmModalBody').text(message);
        $('#confirmModalYes').removeClass('btn-primary btn-danger btn-warning btn-success').addClass(btnClass || 'btn-primary');
        $('#confirmModal').modal('show');
        // Unbind previous and bind new
        $('#confirmModalYes').off('click').on('click', function() {
            $('#confirmModal').modal('hide');
            callback();
        });
    }

    // ── Initialize DataTable ──
    var table = $('#age_master_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'index.php/admin_stock_age_master/ajax_get_list',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, orderable: false }
        ],
        pageLength: 25,
        columnDefs: [
            { className: "text-center", targets: [0, 4] }
        ]
    });

    // ── Delete record ──
    $(document).on('click', '.delete-record', function () {
        var id = $(this).data('id');
        showConfirm('Delete Record', 'Are you sure you want to delete this record?', 'btn-danger', function() {
            $.post(base_url + 'index.php/admin_stock_age_master/delete', { id: id }, function (data) {
                var response = JSON.parse(data);
                if (response.success) {
                    showToast(response.message, 'success');
                    table.draw();
                } else {
                    showToast(response.message, 'error');
                }
            });
        });
    });

    // ── Toggle status (Active <-> Inactive) ──
    $(document).on('click', '.toggle-status', function () {
        var id = $(this).data('id');
        var currentStatus = $(this).hasClass('btn-success') ? 'Active' : 'Inactive';
        var newStatus = (currentStatus === 'Active') ? 'Inactive' : 'Active';
        showConfirm(
            'Change Status',
            'Change status from ' + currentStatus + ' to ' + newStatus + '?',
            (newStatus === 'Active') ? 'btn-success' : 'btn-warning',
            function() {
                $.post(base_url + 'index.php/admin_stock_age_master/toggle_status', { id: id }, function (data) {
                    var response = JSON.parse(data);
                    if (response.success) {
                        showToast(response.message, 'success');
                        table.draw();
                    } else {
                        showToast(response.message, 'error');
                    }
                });
            }
        );
    });
})();
</script>

