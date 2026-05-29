<style>

  	/* CSS for Drill-down */

  	.drill-collapsed {

	    display: none;

	}

	.drill-close {

	    display: none;

	}

	.drill-open {

	    display: block;

	}

	.drill-detail {

	    background:#fdfdfd

	}

	/* .CSS for Drill-down */

  </style>

     <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Rate Fixed Reports

          </h1>

          

        </section>

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               <div class="box box-primary">

			   

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-12">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

								

							    <div class="col-md-2"> 

								     <label></label>

    								 <div class="form-group">

                                          <button class="btn btn-default btn_date_range"  id="rpt_date_picker">

                                                    <i class="fa fa-calendar"></i> Date range picker

                                                    <i class="fa fa-caret-down"></i>

                                            </button>

                                                <span style="display:none;" id="rpt_from_date"></span>

                                                <span style="display:none;" id="rpt_to_date"></span>

                                         </div><!-- /.form group -->

                                </div>



								<div class="col-md-2"> 

									<label>Select Karigar</label>

									<select id="karigar" class="form-control" style="width:100%;"></select>

								</div>

								<div class="col-md-2"> 

									<label>Select Metal</label>

									<select id="metal" class="form-control" style="width:100%;"></select>

								</div>

								

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="rate_fixed_search" class="btn btn-info">Search</button>   

									</div>

								</div>

							</div>

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

				   	<div class="box box-info stock_details">

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive">

								   <table id="rate_fixed_list"class="table table-striped table-bordered">									

										 <thead>

										  	<tr>

												<th width="2%">GRN No</th>

												<th width="2%" style="text-align:right;">Purchase Amount</th>

												<th width="2%">PO No</th>

												<th width="2%">Apprv Ref No</th>

												<th width="2%">Bill Date</th>

												<th width="2%">Karigar</th>

												<th width="2%">Metal</th>

												<th width="2%">Fixed Date</th>

												<th width="2%" style="text-align:right;">Fixed Wgt</th>

												<th width="2%">Fixed Rate</th>

												<th width="2%" style="text-align:right;">Fixed Amount</th>

												<th width="2%" style="text-align:right;">Balance Amount</th>

										 	</tr>

					                    </thead>

										<tbody>



										</tbody>

					                   

									 </table>

								  </div>

								</div> 

							</div> 

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