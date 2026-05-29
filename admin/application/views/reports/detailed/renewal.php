  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Scheme Details
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Scheme Account</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Scheme Account List</h3>      
                           
                </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?>      
                 <div class="responsive">
                  <table id="sch_acc_list" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr>
                        <th style="text-align:left;">ID</th>
                        <th style="text-align:left;">Account.No</th>                        
                        <th style="text-align:left;">Customer</th> 
                         <th style="text-align:left;">Mobile</th>                       
                        <th style="text-align:left;">Scheme Code</th>
						<th style="text-align:left;">Type</th>
                        <th style="text-align:left;">Start Date</th>
                        <th style="text-align:right;">Total Installment</th> 
                        <th style="text-align:left;">Installment Payable</th>
                       
                        
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($accounts)) { 
                                		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
                         <td style="text-align:left;"><?php echo $account['id_scheme_account'];?></td>
                       	 <td style="text-align:left;"><?php echo $account['scheme_acc_number'];?></td>
                       	 <td style="text-align:left;"><?php echo $account['name'];?></td>
                       	 <td style="text-align:left;"><?php echo $account['mobile'];?></td>
                       	 <td style="text-align:left;"><?php echo $account['code'];?></td>                       	 
                         <td style="text-align:left;"><?php echo $account['scheme_type'];?></td>
						 <td style="text-align:left;"><?php echo date("d-m-Y",strtotime($account['start_date']));?></td>
						   <td style="text-align:right;"><?php echo $account['total_installments'];?></td>
                       	 <td style="text-align:left;"><?php echo ($account['scheme_type'] == 'Amount' ? "Rs. ".$account['amount']: "max ".$account['max_weight']." g/month");?></td>
                       
                       	 
                      
                       	
                       </tr>
                       <?php } } ?>
                    </tbody>
                 <!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
                  </table>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

