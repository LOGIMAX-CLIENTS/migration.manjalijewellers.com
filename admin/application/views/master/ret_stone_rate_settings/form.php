<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
        	Stone Rate Settings 
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>Master</a></li>
		<li class="active"> Add Stone Rate settings </li>
		</ol>
	</section>
     <!-- Default box -->
    <section class="content">
      <form id="stn_rate_settings">  
		<div class="box">
			<div class="box-header with-border">
              <h3 class="box-title"></h3>
                <div class="box-tools pull-right">
                 <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                 <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                </div>
            </div> 
			<div class="box-body">
				<div class="col-md-12">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_1" data-toggle="pill">Loose Stone</a></li>
						<li class="prod_stn"><a href="#tab_2" data-toggle="pill">Loose Products</a></li>
					</ul>
					<div class="tab-content col-md-12"><br/>
						<div class="tab-pane active" id="tab_1">
							<div class='row title-add-wastage'>
								<?php if($this->uri->segment(3) == 'add'){?>
									Add Stone ( <span class="add_stonerate_info"><i class="fa fa-plus"></i></span> )
								<?php } ?>
							</div><br>
							<div class="row">
								<div class="table-responsive">
									<table id="total_stone_rate_items" class="table table-bordered table-striped text-center">
										<input  type="hidden" value="0" id="i_increment" />	
										<thead>
											<tr style="width:100%;">
												<th width="10%;">Branch</th>
												<th width="10%;">Stone Type</th>
												<th width="10%;">Stone Name</th>
												<th width="10%;">Quality Code</th>
												<th width="10%;">UOM</th>
												<th width="10%;">Calc Type</th>
												<th width="10%;">From Cent</th>
												<th width="10%;">To Cent</th>
												<th width="10%;">Min Rate</th>
												<th width="10%;">Max rate</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="box box-default"><br/>
									<div class="col-xs-offset-5">
										<button type="button" id="add_stn_rate_settings"  class="btn btn-primary">save</button> 
										<button type="button" class="btn btn-default btn-cancel">Cancel</button>
									</div> <br/>
								</div>
		            		</div> 
						</div>
						<div class="tab-pane tab-products" id="tab_2">
							<div class='row title-add-wastage'>
								<?php if($this->uri->segment(3)=='add'){?> 
									Add Products ( <span class="add_stoneproduct_info"><i class="fa fa-plus"></i></span> )
								<?php }?>
								
							</div><br>
							<div class="row">
								<div class="table-responsive">
									<table id="total_stnproduct_rate_items" class="table table-bordered table-striped text-center">
										<input  type="hidden" value="0" id="i_increment" />	
										<thead>
											<tr style="width:100%;">
												<th width="10%;">Branch</th>
												<th width="10%;">Product</th>
												<th width="10%;">Design</th>
												<th width="10%;">Sub Design</th>
												<th width="10%;">Quality Code</th>
												<th width="10%;">UOM</th>
												<th width="10%;">Calc Type</th>
												<th width="10%;">From Cent</th>
												<th width="10%;">To Cent</th>
												<th width="10%;">Min Rate</th>
												<th width="10%;">Max rate</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="box box-default"><br/>
									<div class="col-xs-offset-5">
										<button type="button" id="add_stnproduct_rate_settings"  class="btn btn-primary">save</button> 
										<button type="button" class="btn btn-default btn-cancel">Cancel</button>
									</div> <br/>
								</div>
		            		</div> 
						</div>
					</div>
				</div>
			</div>
        </form>
    </section>
</div>