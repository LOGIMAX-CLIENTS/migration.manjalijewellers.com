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

            <li class="active">Closed Scheme Account</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Closed Scheme Account List</h3>  <span id="total_closed_accounts" class="badge bg-aqua"></span>       

                         

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



							
						   </div>

				

                   <div class="table-responsive">

                  <table id="closed_list" class="table table-bordered table-striped text-center grid" role="grid">

                    <thead>

                      <tr>

                        <th>ID</th>

                        <th>Customer</th>   

						<th>Mobile</th>	

                        <th>A/c Name</th>					

                        <th>Scheme A/c No</th>					

                        <th>Code</th>

                        <th>Start Date</th>                        

                        <th>Type</th>

                        <th>Payable (<?php echo $this->session->userdata('currency_symbol')?>)</th>

                        <th>Closing Balance</th>

                        <th>Closed Date</th>

                      

                      </tr>

                    </thead>
					
					<tbody>
					   <?php 
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
                        
                      	 <td><?php echo $account['id_scheme_account'];?></td>
                      	 <td><?php echo $account['name'];?></td>
                       	 <td><?php echo $account['mobile'];?></td>
                       	 <td><?php echo $account['account_name'];?></td>
                  		 <td><?php echo ($account['has_lucky_draw']==1 ? $account['scheme_group_code'] : $account['code']).' '.($account['scheme_acc_number']!=''?$account['scheme_acc_number']:"Not Allocated");?></td>
                  		 <td><?php echo $account['code'];?></td>
                  		 <td><?php echo $account['start_date'];?></td>
                  		  <td><?php echo $account['scheme_type'];?></td>
                  		 <td><?php echo $account['amount'];?></td>
                  		 <td><?php echo $account['closing_balance'];?></td>
                  		 <td><?php echo $account['closing_date'];?></td>
                       <!--  <td>
                       	 	<a href="<?php echo base_url('index.php/account/edit/'.$account['id_scheme_account']);?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>                       	 	
                       	 	    
                      	 </td> -->
                       	
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

                 <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      





<!-- modal -->      

<div class="modal fade" id="confirm-revert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Revert Closed Scheme</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to revert this scheme account?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Revert</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->   



<!-- modal -->      



<div class="modal fade" id="clsd_acc_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



  <div class="modal-dialog">



    <div class="modal-content">



      <div class="modal-header bg-yellow">



        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>



        <h4 class="modal-title" id="myModalLabel" align="center">Transaction Detail</h4>



      </div>



      <div class="modal-body">



         <div class="closed_acc_detail"></div>    



      </div>



    </div>

   </div>

</div>

<!-- / modal -->     

    

<style type="text/css">



.popover1{



    width:230px;



    height:330px;    



}



.trans tr{



	 width:50%;



    height:50%;



	font-size:15px;



}



</style>