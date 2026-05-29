<div class="content-wrapper">
    <section class="content-header">
        <h1>
            New Print Template
            <small>Create Template</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Template Details</h3>
                    </div>
                    
                    <form role="form" action="<?php echo base_url('index.php/print-templates/save'); ?>" method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="template_name">Template Name</label>
                                <input type="text" class="form-control" name="template_name" id="template_name" placeholder="E.g. Standard Billing Invoice" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="template_code">Template Code (Unique)</label>
                                <input type="text" class="form-control" name="template_code" id="template_code" placeholder="E.g. BILL_STD_A4" required>
                            </div>

                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" name="template_category">
                                    <?php foreach($categories as $id => $name): ?>
                                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Paper Size</label>
                                        <select class="form-control" name="paper_size">
                                            <?php foreach($paper_sizes as $size): ?>
                                                <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Orientation</label>
                                        <select class="form-control" name="page_orientation">
                                            <option value="portrait">Portrait</option>
                                            <option value="landscape">Landscape</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                             <div class="form-group">
                                <label>Branch (Optional)</label>
                                <select class="form-control" name="id_branch">
                                    <option value="">All Branches</option>
                                    <!-- Populate if branches available -->
                                </select>
                            </div>

                        </div>

                        <div class="box-footer">
                            <a href="<?php echo base_url('print-templates'); ?>" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-primary pull-right">Create & Open Designer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
