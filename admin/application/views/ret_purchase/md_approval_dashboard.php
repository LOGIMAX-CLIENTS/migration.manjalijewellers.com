  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Purchase
            <small>MD Approval Dashboard</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Purchase</a></li>
            <li class="active">MD Approval Dashboard</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Purchase Orders - Pending MD Approval</h3>
                  <span id="pending_count" class="badge bg-orange"></span>

                  <!-- Bulk Action Bar (hidden by default) -->
                  <div class="pull-right" id="bulk_action_bar" style="display:none;">
                    <button type="button" id="btn_bulk_approve" class="btn btn-success btn-sm">
                      <i class="fa fa-check-circle"></i> Approve Selected
                    </button>
                  </div>
                </div>

                 <div class="box-body">

                  <div class="table-responsive">
	                 <table id="md_approval_table" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="3%;"><input type="checkbox" id="select_all_chk"></th>
	                        <th width="8%;">PO No</th>
	                        <th width="8%;">Order Date</th>
	                        <th width="10%;">Karigar</th>
	                        <th width="10%;">Product</th>
	                        <th width="5%;">Pcs</th>
	                        <th width="8%;">Approx Wt</th>
	                        <th width="10%;">Weight Range</th>
	                        <th width="8%;">Type</th>
	                        <th width="8%;">Created By</th>
	                        <th width="10%;">Action</th>
	                      </tr>
	                    </thead>
	                 </table>
                  </div>

                </div><!-- /.box-body -->

                <div class="overlay" id="loading_overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->


<!-- Reject Reason Modal -->
<div class="modal fade" id="reject_modal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#dd4b39; color:#fff;">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="rejectModalLabel"><i class="fa fa-times-circle"></i> Reject Purchase Order</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="reject_order_id" value="">
        <div class="form-group">
          <label for="reject_reason">Reason for Rejection <span class="text-red">*</span></label>
          <textarea class="form-control" id="reject_reason" rows="4" placeholder="Enter reason for rejecting this PO..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btn_confirm_reject"><i class="fa fa-times"></i> Reject</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<!-- / Reject Reason Modal -->



<script>
$(document).ready(function() {

    var base_url = '<?php echo base_url(); ?>';

    // Initialize DataTable
    var mdTable = $('#md_approval_table').DataTable({
        "ajax": {
            "url": base_url + "index.php/Admin_ret_purchase_approval/md_dashboard/ajax",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            {
                "data": "id_customerorder",
                "render": function(data) {
                    return '<input type="checkbox" class="row_chk" value="' + data + '">';
                },
                "orderable": false,
                "searchable": false
            },
            { "data": "pur_no" },
            { "data": "order_date" },
            { "data": "karigar_name" },
            { "data": "products" },
            { "data": "total_pcs" },
            { "data": "order_approx_wt" },
            { "data": "weight_range" },
            { "data": "order_type_label" },
            { "data": "created_by_name" },
            {
                "data": "id_customerorder",
                "render": function(data) {
                    return '<button class="btn btn-success btn-xs btn_approve" data-id="' + data + '" title="Approve">' +
                           '<i class="fa fa-check"></i></button> ' +
                           '<button class="btn btn-danger btn-xs btn_reject" data-id="' + data + '" title="Reject">' +
                           '<i class="fa fa-times"></i></button>';
                },
                "orderable": false,
                "searchable": false
            }
        ],
        "order": [[2, "desc"]],
        "pageLength": 25,
        "drawCallback": function(settings) {
            var count = this.api().data().length;
            $('#pending_count').text(count + ' Pending');
        }
    });

    // ===== Select All Checkbox =====
    $('#select_all_chk').on('change', function() {
        var checked = this.checked;
        $('.row_chk').prop('checked', checked);
        toggleBulkBar();
    });

    // Individual checkbox change
    $(document).on('change', '.row_chk', function() {
        var allChecked = $('.row_chk').length === $('.row_chk:checked').length;
        $('#select_all_chk').prop('checked', allChecked);
        toggleBulkBar();
    });

    function toggleBulkBar() {
        var checkedCount = $('.row_chk:checked').length;
        if(checkedCount > 0) {
            $('#bulk_action_bar').show();
        } else {
            $('#bulk_action_bar').hide();
        }
    }

    // ===== Single Approve =====
    $(document).on('click', '.btn_approve', function() {
        var id = $(this).data('id');
        if(!confirm('Are you sure you want to approve this Purchase Order?')) return;

        $('#loading_overlay').show();
        $.ajax({
            url: base_url + "index.php/Admin_ret_purchase_approval/md_dashboard/approve",
            type: "POST",
            data: { id_customerorder: id },
            dataType: "json",
            success: function(res) {
                $('#loading_overlay').hide();
                if(res.status) {
                    toastr.success(res.msg);
                    mdTable.ajax.reload(null, false);
                } else {
                    toastr.error(res.msg);
                }
            },
            error: function() {
                $('#loading_overlay').hide();
                toastr.error('Server error. Please try again.');
            }
        });
    });

    // ===== Single Reject - Open Modal =====
    $(document).on('click', '.btn_reject', function() {
        var id = $(this).data('id');
        $('#reject_order_id').val(id);
        $('#reject_reason').val('');
        $('#reject_modal').modal('show');
    });

    // Confirm Reject
    $('#btn_confirm_reject').on('click', function() {
        var id = $('#reject_order_id').val();
        var reason = $.trim($('#reject_reason').val());
        if(reason === '') {
            toastr.warning('Please enter a reason for rejection.');
            return;
        }

        $('#reject_modal').modal('hide');
        $('#loading_overlay').show();
        $.ajax({
            url: base_url + "index.php/Admin_ret_purchase_approval/md_dashboard/reject",
            type: "POST",
            data: { id_customerorder: id, reason: reason },
            dataType: "json",
            success: function(res) {
                $('#loading_overlay').hide();
                if(res.status) {
                    toastr.success(res.msg);
                    mdTable.ajax.reload(null, false);
                } else {
                    toastr.error(res.msg);
                }
            },
            error: function() {
                $('#loading_overlay').hide();
                toastr.error('Server error. Please try again.');
            }
        });
    });

    // ===== Bulk Approve =====
    $('#btn_bulk_approve').on('click', function() {
        var selected = [];
        $('.row_chk:checked').each(function() {
            selected.push($(this).val());
        });
        if(selected.length === 0) {
            toastr.warning('Please select at least one order.');
            return;
        }
        if(!confirm('Are you sure you want to approve ' + selected.length + ' Purchase Order(s)?')) return;

        $('#loading_overlay').show();
        $.ajax({
            url: base_url + "index.php/Admin_ret_purchase_approval/md_dashboard/bulk_approve",
            type: "POST",
            data: { order_ids: selected },
            dataType: "json",
            success: function(res) {
                $('#loading_overlay').hide();
                if(res.status) {
                    toastr.success(res.msg);
                    mdTable.ajax.reload(null, false);
                    $('#select_all_chk').prop('checked', false);
                    toggleBulkBar();
                } else {
                    toastr.error(res.msg);
                }
            },
            error: function() {
                $('#loading_overlay').hide();
                toastr.error('Server error. Please try again.');
            }
        });
    });

});
</script>
