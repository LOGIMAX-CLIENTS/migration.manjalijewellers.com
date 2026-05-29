  <!-- Content Wrapper. Contains page content -->
  <style>
  	.custom-label{
		font-weight: 400;
	}  
  </style>
  <style type="text/css"media="print">
  	@media print
    {
        body {
            
        }
        #BTtagData{
				   
		}
		table{
			text-align: center; 
			border: 1px solid #f4f4f4;
			width: 100%;
		    max-width: 100%;
		    margin-bottom: 20px;
		    border-spacing: 0;
    		border-collapse: collapse;	   
		}
    } 
  </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Tagging
            <small>Manage your tag(s)</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Tagging</a></li>
            <li class="active">Tag</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row ">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Created Tags</h3>  <span id="total_tagging" class="badge bg-green"></span>  
                  <div class="pull-right"> 
                  	<a class="btn btn-success"  href="<?php echo base_url('index.php/admin_ret_tagging/tagging/add');?>" >Add Tag</a>
				  </div>
                </div> 
                 <div class="box-body" id="BTtagData"> 
                 	<?php if(isset($list[0]['from_branch'])){?>
                 	<div class="row ">  
		             	<div class="col-md-3">
		             		<p class="lead"><small>Date : </small><?php echo $list[0]['tag_date'];?> </p>
		             	</div>
		             	<div class="col-md-3">
		             		<p class="lead"><small>From Branch : </small> <?php echo $list[0]['from_branch'];?> </p>
		             	</div>
		             	<div class="col-md-3">
		             		<p class="lead"><small>To Branch : </small> <?php echo $list[0]['to_branch'];?> </p>
		             	</div>   
                 	</div> 
                 	<?php }?> 
                  <!-- Table row -->
				  <div class="row">
				    <div class="col-xs-12 table-responsive">
	                 <table id="createdTgList" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="10%">Trans Code</th>
	                        <th width="5%"> Product</th> 
							<th width="10%">Gross Wgt</th>
	                        <th width="10%">Net Wgt</th>
	                        <th width="5%">Pieces</th> 
	                        <th width="10%">Tag Code</th>
							<th width="10%">Lot No.</th>
	                      </tr>
	                    </thead> 
	                    <?php if(isset($list)){?> 
	                    <tbody>
	                    	<?php foreach($list as $r){?> 
							<tr>
								<td><?php echo $trans_code ;?></td> 
								<td><?php echo $r['product_name'] ;?></td> 
								<td><?php echo $r['gross_wt'] ;?></td> 
								<td><?php echo $r['net_wt'] ;?></td> 
								<td><?php echo $r['piece'] ;?></td> 
								<td><?php echo $r['tag_code'] ;?></td> 
								<td><?php echo $r['tag_lot_id'] ;?></td> 
							</tr>
							<?php }?>
	                    </tbody>
	                    <?php }?>
	                 </table>
                  </div>
                </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            </div> 
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper --> 