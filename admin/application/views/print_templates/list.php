<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Print Templates
            <small>Manage Print Formats</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Print Templates</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Template List</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('index.php/print-templates/add'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Template</a>
                        </div>
                    </div>
                    <!-- Bulk Action Toolbar — ALWAYS visible -->
                    <div id="bulkActionBar" style="background:#f5f5f5; border-bottom:2px solid #ddd; padding:10px 15px;">
                        <button type="button" class="btn btn-default btn-sm" id="btnSelectAll" onclick="doSelectAll()">
                            <i class="fa fa-check-square-o"></i> Select All
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="btnBulkDelete" onclick="confirmBulkDelete()" style="margin-left:8px;">
                            <i class="fa fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-default btn-sm" id="btnDeselectAll" onclick="doDeselectAll()" style="margin-left:8px;">
                            <i class="fa fa-times"></i> Clear Selection
                        </button>
                        <span class="text-muted" style="margin-left:15px; font-size:12px;">
                            <i class="fa fa-info-circle"></i> Max 100 records per bulk delete | Active templates will be skipped
                        </span>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="templateTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:30px; text-align:center;">
                                        <input type="checkbox" id="selectAllCb" title="Select / Deselect All on this page">
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Template Code</th>
                                    <th>Category</th>
                                    <th>Size</th>
                                    <th>Default</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($templates as $t): ?>
                                <tr data-is-active="<?php echo $t['is_active'] ? '1' : '0'; ?>">
                                    <td style="text-align:center;">
                                        <?php if (!$t['is_active']): ?>
                                            <input type="checkbox" class="row-check" value="<?php echo $t['id_template']; ?>">
                                        <?php else: ?>
                                            <span class="text-muted" title="Active templates cannot be bulk deleted"><i class="fa fa-lock" style="color:#ccc;"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $t['id_template']; ?></td>
                                    <td><?php echo $t['template_name']; ?></td>
                                    <td><?php echo isset($t['code']) ? $t['code'] : '-'; ?></td>
                                    <td><?php echo $t['template_code']; ?></td>
                                    <td>
                                        <?php 
                                            // Ensure categories is set, even if empty
                                            $cat_map = isset($categories) ? $categories : [];
                                            $cat_id = $t['template_category'];
                                            $cat_name = isset($cat_map[$cat_id]) ? $cat_map[$cat_id] : ucfirst(str_replace('_', ' ', $cat_id));
                                        ?>
                                        <span class="label label-info"><?php echo $cat_name; ?></span>
                                    </td>
                                    <td><?php echo $t['paper_size']; ?></td>
                                    <td>
                                        <?php if($t['is_default']): ?>
                                            <span class="label label-success"><i class="fa fa-check"></i> Default</span>
                                        <?php else: ?>
                                            <a href="<?php echo base_url('index.php/print-templates/set-default/'.$t['id_template']); ?>" class="btn btn-xs btn-default">Set Default</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($t['is_active']): ?>
                                            <span class="label label-success">Active</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Inactive</span>
                                            <a href="javascript:void(0)" class="btn btn-xs btn-success" onclick="confirmActivate(<?php echo $t['id_template']; ?>, '<?php echo addslashes($t['template_name']); ?>', '<?php echo isset($t['code']) ? addslashes($t['code']) : ''; ?>')" style="margin-left:5px;">Activate</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo base_url('index.php/print-templates/designer/'.$t['id_template']); ?>" class="btn btn-primary btn-xs" title="V1 Block Designer">
                                                <i class="fa fa-paint-brush"></i> V1
                                            </a>
                                            <a href="<?php echo base_url('index.php/print-templates/designer-v2/'.$t['id_template']); ?>" target="_blank" class="btn btn-success btn-xs" title="V2 Canvas Designer (Konva.js)">
                                                <i class="fa fa-pencil-square-o"></i> V2
                                            </a>
                                            <a href="<?php echo base_url('index.php/print-templates/preview/'.$t['id_template']); ?>" target="_blank" class="btn btn-info btn-xs" title="Preview">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="#" data-href="<?php echo base_url('index.php/print-templates/delete/'.$t['id_template']); ?>" data-toggle="modal" data-target="#confirm-delete" class="btn btn-danger btn-xs btn-del" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
</div>

<script>
  var activateId = null;
  var activateUrl = '<?php echo base_url('index.php/print-templates/toggle-active/'); ?>';
  var bulkDeleteUrl = '<?php echo base_url('index.php/admin_print_template/bulk_delete'); ?>';
  var BULK_DELETE_MAX = 100;

  // ── Activate Functions ──
  function confirmActivate(id, name, code) {
      activateId = id;
      var msg = 'Are you sure you want to activate template <strong>"' + name + '"</strong> (ID: ' + id + ')?';
      if (code) {
          msg += '<br><br><small class="text-muted">All other versions with code <strong>"' + code + '"</strong> will be deactivated.</small>';
      }
      $('#confirm-activate .activate-msg').html(msg);
      $('#confirm-activate').modal('show');
  }

  function doActivate() {
      if (!activateId) return;
      var $btn = $('#btn-confirm-activate');
      $btn.prop('disabled', true).text('Activating...');
      $.ajax({
          url: activateUrl + '/' + activateId,
          type: 'GET',
          dataType: 'json',
          success: function(res) {
              if (res && res.success) {
                  $('#confirm-activate').modal('hide');
                  location.reload();
              } else {
                  alert('Failed to activate template.');
                  $btn.prop('disabled', false).text('Yes, Activate');
              }
          },
          error: function() {
              alert('Failed to activate template.');
              $btn.prop('disabled', false).text('Yes, Activate');
          }
      });
  }

  // ── Bulk Delete Functions ──
  function updateSelectedCount() {
      var count = $('.row-check:checked').length;
      $('#selectedCount').text(count);
  }

  /**
   * Select All — selects only non-active rows, up to 100 max.
   * Active rows don't have checkboxes so they're automatically skipped.
   */
  function doSelectAll() {
      // First deselect everything
      $('.row-check').prop('checked', false);

      var selected = 0;
      // Go through ALL rows in DataTable (not just visible page)
      var table = $('#templateTable').DataTable();
      var allRows = table.rows({ search: 'applied' }).nodes();

      $(allRows).each(function() {
          if (selected >= BULK_DELETE_MAX) return false; // stop at 100
          var $cb = $(this).find('.row-check');
          if ($cb.length) {
              $cb.prop('checked', true);
              selected++;
          }
      });

      $('#selectAllCb').prop('checked', selected > 0);
      updateSelectedCount();

      if (selected >= BULK_DELETE_MAX) {
          alert('Selected ' + selected + ' records (maximum limit reached).\nActive templates were automatically skipped.');
      } else {
          alert('Selected ' + selected + ' inactive template(s).\nActive templates were automatically skipped.');
      }
  }

  function doDeselectAll() {
      $('.row-check').prop('checked', false);
      $('#selectAllCb').prop('checked', false);
      updateSelectedCount();
  }

  function confirmBulkDelete() {
      var count = $('.row-check:checked').length;
      if (count === 0) {
          alert('No templates selected.\n\nPlease select at least one inactive template to delete.');
          return;
      }
      if (count > BULK_DELETE_MAX) {
          alert('You can only delete up to ' + BULK_DELETE_MAX + ' records at a time.\nCurrently selected: ' + count + '\n\nPlease deselect some records.');
          return;
      }
      $('#bulkDeleteCount').text(count);
      $('#confirm-bulk-delete').modal('show');
  }

  function doBulkDelete() {
      var ids = [];
      $('.row-check:checked').each(function() {
          ids.push($(this).val());
      });
      if (ids.length === 0) return;
      if (ids.length > BULK_DELETE_MAX) {
          alert('Maximum ' + BULK_DELETE_MAX + ' records allowed per bulk delete.');
          return;
      }

      var $btn = $('#btn-confirm-bulk-delete');
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

      $.ajax({
          url: bulkDeleteUrl,
          type: 'POST',
          data: { ids: ids },
          dataType: 'json',
          success: function(res) {
              if (res && res.success) {
                  $('#confirm-bulk-delete').modal('hide');
                  alert('Successfully deleted ' + res.deleted_count + ' template(s).\nBackup saved to /template/ folder.');
                  location.reload();
              } else {
                  alert('Error: ' + (res.message || 'Failed to delete templates.'));
                  $btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Delete All');
              }
          },
          error: function() {
              alert('Server error while deleting templates.');
              $btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Delete All');
          }
      });
  }

  $(function () {
    $('#templateTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "columnDefs": [
          { "orderable": false, "targets": 0 }
      ]
    });

    // Bind delete modal confirm button
    $('#confirm-delete').on('show.bs.modal', function(e) {
        var link = $(e.relatedTarget).data('href');
        if(!link) { link = $(e.relatedTarget).closest('a').data('href'); }
        $(this).find('.btn-confirm').attr('href', link);
    });

    // Reset activate modal on close
    $('#confirm-activate').on('hidden.bs.modal', function() {
        $('#btn-confirm-activate').prop('disabled', false).text('Yes, Activate');
        activateId = null;
    });

    // Reset bulk delete modal on close
    $('#confirm-bulk-delete').on('hidden.bs.modal', function() {
        $('#btn-confirm-bulk-delete').prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Delete All');
    });

    // Header "Select All" checkbox — current page only, skip active rows
    $('#selectAllCb').on('change', function() {
        var isChecked = this.checked;
        var rows = $('#templateTable').DataTable().rows({ search: 'applied', page: 'current' }).nodes();
        var selected = 0;
        var totalChecked = isChecked ? 0 : $('.row-check:checked').length;

        $(rows).each(function() {
            var $cb = $(this).find('.row-check');
            if ($cb.length) {
                if (isChecked) {
                    if (totalChecked + selected < BULK_DELETE_MAX) {
                        $cb.prop('checked', true);
                        selected++;
                    }
                } else {
                    $cb.prop('checked', false);
                }
            }
        });

        updateSelectedCount();

        if (isChecked && selected === 0) {
            alert('No inactive templates on this page to select.');
        }
    });

    // Individual row checkbox change — update count
    $('#templateTable tbody').on('change', '.row-check', function() {
        var totalChecked = $('.row-check:checked').length;
        if (totalChecked > BULK_DELETE_MAX) {
            $(this).prop('checked', false);
            alert('Cannot select more than ' + BULK_DELETE_MAX + ' records.\nPlease deselect some before selecting more.');
            totalChecked--;
        }
        if (!this.checked) {
            $('#selectAllCb').prop('checked', false);
        }
        updateSelectedCount();
    });
  });
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Template</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this template?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm">Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Activate Confirmation Modal -->
<div class="modal fade" id="confirm-activate" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #00a65a; color: #fff;">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-check-circle"></i> Activate Template</h4>
      </div>
      <div class="modal-body">
        <div class="activate-msg"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btn-confirm-activate" onclick="doActivate()">Yes, Activate</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="confirm-bulk-delete" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #dd4b39; color: #fff;">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Bulk Delete Templates</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong><span id="bulkDeleteCount">0</span></strong> selected template(s)?</p>
        <p class="text-muted"><small><i class="fa fa-info-circle"></i> The template data (gjs_data) will be backed up to files before deletion.</small></p>
        <p class="text-danger"><strong>This action cannot be undone!</strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btn-confirm-bulk-delete" onclick="doBulkDelete()"><i class="fa fa-trash"></i> Yes, Delete All</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
