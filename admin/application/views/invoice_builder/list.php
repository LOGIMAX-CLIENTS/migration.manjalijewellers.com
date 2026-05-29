<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Invoice Builder V2
            <small>Pixel-Perfect Invoice Designer</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Invoice Builder</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Invoice Templates</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('index.php/invoice-builder/add'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Invoice Template</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="templateTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Size</th>
                                    <th>Default</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($templates as $t): ?>
                                <tr>
                                    <td><?php echo $t['id_template']; ?></td>
                                    <td><?php echo $t['template_name']; ?></td>
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
                                            <a href="<?php echo base_url('index.php/invoice-builder/set-default/'.$t['id_template']); ?>" class="btn btn-xs btn-default">Set Default</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo ($t['is_active']) ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo base_url('index.php/invoice-builder/designer/'.$t['id_template']); ?>" class="btn btn-warning btn-xs" title="Design"><i class="fa fa-paint-brush"></i> Design</a>
                                            <a href="<?php echo base_url('index.php/invoice-builder/preview/'.$t['id_template']); ?>" target="_blank" class="btn btn-info btn-xs" title="Preview"><i class="fa fa-eye"></i></a>
                                            <a href="#" data-href="<?php echo base_url('index.php/invoice-builder/delete/'.$t['id_template']); ?>" data-toggle="modal" data-target="#confirm-delete" class="btn btn-danger btn-xs btn-del" title="Delete"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
  $(function () {
    $('#templateTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
    
    $('#confirm-delete').on('show.bs.modal', function(e) {
        var link = $(e.relatedTarget).data('href');
        if(!link) { 
            link = $(e.relatedTarget).closest('a').data('href'); 
        }
        $(this).find('.btn-confirm').attr('href', link);
    });
  });
</script>

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
