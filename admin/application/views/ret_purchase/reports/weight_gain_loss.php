
  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Weight Gain / Loss Report
          </h1>
    
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-8">  
						   <div class="row">
                             <div class="col-md-3">
                                <div class="form-group">
                                   <div class="input-group">
                                          <button class="btn btn-default btn_date_range" id="rpt_payment_date">
                                                  <i class="fa fa-calendar"></i> Date range picker<i class="fa fa-caret-down"></i>
                                           </button>
            							                   	 <span style="display:none;" id="rpt_payments1"></span>
                                               <span style="display:none;" id="rpt_payments2"></span>
                                     </div>
                                 </div>
                             </div>
                             
                         
                                <div class="col-md-2"> 
                                    <div class="form-group">
                                    <button type="button" id="weight_gain_report_search" class="btn btn-info">Search</button>       
                                    </div>
                                </div>
							</div>
                   </div>
                   </div>
                    
                    <div class="box box-info stock_details">
    						<div class="box-header with-border">
    						  <h3 class="box-title">Summary</h3>
    						  <div class="box-tools pull-right">
    							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
    						  </div>
    						</div>
        					<div class="box-body collapse">
        							<div class="row">
        								<div class="box-body col-md-offset-2 col-md-8">
        									
        								<table id="stock_list_summary" class="table table-bordered table-striped text-center">
                                           <thead>
                                               <tr>
        											<th>#</th>
            										<th>Pcs</th>
            										<th>Gross Wt</th>
            										<th>Net Wt</th>
            										<th>Dia Wt</th>
        									   </tr>
        
        								   </thead>
                                           <tbody>
                                              <tr>
        										<td>Opening</td>
        										<td class="blc_pcs" style="text-align:right;"></td>
        										<td class="blc_gwt" style="text-align:right;"></td>
        										<td class="blc_nwt" style="text-align:right;"></td>
        										<td class="blc_diawt" style="text-align:right;"></td>
        									  </tr>
        									 
        									  <tr>
        										<td>Lot</td>
        										<td class="lot_pcs" style="text-align:right;"></td>
        										<td class="lot_gwt" style="text-align:right;"></td>
        										<td class="lot_nwt" style="text-align:right;"></td>
        										<td class="lot_diawt" style="text-align:right;"></td>
        									  </tr>
        									  <tr>
        										<td> Tag</td>
        										<td class="tag_pcs" style="text-align:right;"></td>
        										<td class="tag_gwt" style="text-align:right;"></td>
        										<td class="tag_nwt" style="text-align:right;"></td>
        										<td class="tag_diawt" style="text-align:right;"></td>
        									  </tr>
        									  
        									  <tr>
        										<td>Closing </td>
        										<td class="closing_pcs" style="text-align:right;"></td>
        										<td class="closing_gwt" style="text-align:right;"></td>
        										<td class="closing_nwt" style="text-align:right;"></td>
        										<td class="closing_diawt" style="text-align:right;"></td>
        									  </tr>
        
        								   </tbody>
        								   
        
        								</table>
        
        								</div>
        							</div>
        						</div>
					</div>
					
				   <div class="row">
						<div class="col-xs-12">
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
				   </div>
				   
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
								   <table id="weight_gain_loss_table" class="table table-bordered table-striped text-center">
                                       <thead>
                                           <tr class="tablerow">
												<th colspan="2">#</th>
												<th colspan="4">Opening</th>
												<th colspan="4">Lot</th>
												<th colspan="4">Tag</th>
												<th colspan="4">Gain/Loss</th>
											</tr>
                                           <tr>
        								       <th scope="col">Lot NO</th>
        								       <th scope="col">Lot Date</th>

        									   <th scope="col">O/p Pcs</th>
                                               <th scope="col">O/p Grs Wt</th>
                                               <th scope="col">O/p Net Wt</th>
                                               <th scope="col">O/p Dia Wt</th>
                                               
                                               <th scope="col">Lot Pcs</th>
                                               <th scope="col">Lot Grs Wt</th>
                                               <th scope="col">Lot Net Wt</th>
                                               <th scope="col">Lot Dia Wt</th>
                                               
        									   <th scope="col">Tag Pcs</th>
                                               <th scope="col">Tag Grs Wt</th>
                                               <th scope="col">Tag Net Wt</th>
                                               <th scope="col">Tag Dia Wt</th>
                                               
        									   <th scope="col">G/L Pcs</th>
                                               <th scope="col">G/L Grs Wt</th>
                                               <th scope="col">G/L Net Wt</th>
                                               <th scope="col">G/L Dia Wt</th>
                                           </tr>
                                                   </thead>
                                           <tbody></tbody>
                                           <tfoot>
                                              <tr style="font-weight:bold;">
                                                  <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                              </tr>
                                           </tfoot>
                                       </table>
								  </div>
								</div> 
							</div> 
						</div>
        				
					</div>
					<div class="overlay" style="display:none">
        				  <i class="fa fa-refresh fa-spin"></i>
        				</div>
                </div><!-- /.box-body -->
                
              </div>
              </div>
              <!-- </div>
              </div> -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <!-- <div class="modal fade" id="modal-feedback" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Enter Feedback</h4>
          </div>
        <div class="modal-body">
      <div class="row" >
            <div class="col-md-offset-1 col-md-10" id='error'></div>
              </div>
                <div class="row">
                <form id="feedback_form">
                  <div class="form-group" id='feedback_content'>
                    
                  </div>
                  </form>
                </div>  
              </div>
        <div class="modal-footer">
          <a href="#" id="add_feedback" class="btn btn-success" data-dismiss="modal" >Save</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" style="width:90%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                          <div id="order_images" style="margin-top: 2%;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        </br>
                        <button type="button" id="close_stone_details" class="btn btn-warning"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
  </div> -->