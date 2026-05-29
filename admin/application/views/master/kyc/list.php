<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>KYC Master</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">KYC Master List</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">KYC Document List</h3>
                        <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/kyc/master_add'); ?>"><i class="fa fa-plus"></i> Add New</a> 
                    </div>
                    <div class="box-body">
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-check"></i> Success!</h4>
                                <?php echo $this->session->flashdata('success'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table id="kyc_master_list" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Document Name</th>
                                        <th>Short Code</th>
                                        <th>Type</th>
                                        <th>Mandatory?</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($kyc_masters)): foreach($kyc_masters as $key => $row): ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['short_code']; ?></td>
                                        <td>
                                            <?php 
                                            if($row['doc_type']==1) echo 'Identity Proof';
                                            else if($row['doc_type']==2) echo 'Address Proof';
                                            else if($row['doc_type']==3) echo 'ID & Address proof';
                                            else echo 'Transaction Proof';
                                            ?>
                                        </td>
                                        <td><?php echo $row['is_mandatory'] ? 'Yes' : 'No'; ?></td>
                                        <td><?php echo $row['status'] == 1 ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                        <td>
                                            <a href="<?php echo base_url('index.php/kyc/master_edit/'.$row['id_mas_kyc']); ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                            <a href="#" class="btn btn-danger btn-sm delete-kycmaster" data-id="<?php echo $row['id_mas_kyc']; ?>"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo base_url('assets/js/kyc.js?v='.time()); ?>"></script>
