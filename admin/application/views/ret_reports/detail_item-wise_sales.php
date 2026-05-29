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

	.tagged-options {

		padding-top: 20px;

	}

	/* .CSS for Drill-down */


    /* ── COLUMN MANAGER DRAWER — right-side slide-in panel ── */

    /* Backdrop: covers full screen, click outside to close */
    #colManagerModal {
        display: none !important;
        position: fixed !important;
        inset: 0 !important;
        background: rgba(0,0,0,.40) !important;
        z-index: 10000 !important;
        justify-content: flex-end !important;
        align-items: stretch !important;
        font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
    }
    #colManagerModal.show {
        display: flex !important;
    }

    /* Drawer panel — anchored to right edge, full height */
    #colManagerModal .modal-box {
        background: #fff;
        width: 400px;
        max-width: 95vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
        box-shadow: -6px 0 30px rgba(0,0,0,.22);
        transform: translateX(100%);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
        border-radius: 0;
        border: none;
        border-left: 3px solid #2a6496;
        overflow: hidden;
    }
    #colManagerModal.show .modal-box {
        transform: translateX(0);
    }

    /* Header */
    #colManagerModal .modal-header {
        background: linear-gradient(135deg, #3c8dbc 0%, #2a6496 100%);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        border-bottom: none;
    }
    #colManagerModal .modal-header h2 {
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        flex: 1;
        margin: 0;
        letter-spacing: .3px;
    }
    #colManagerModal .col-vis-badge {
        background: rgba(255,255,255,.25);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 10px;
        white-space: nowrap;
    }
    #colManagerModal .close-btn {
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.3);
        color: #fff;
        cursor: pointer;
        font-size: 16px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
        flex-shrink: 0;
        line-height: 1;
        padding: 0;
    }
    #colManagerModal .close-btn:hover { background: rgba(255,255,255,.30); }

    /* Preset buttons row */
    #colManagerModal .preset-row {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        padding: 12px 16px;
        background: #f0f4f8;
        border-bottom: 1px solid #dce3ea;
        flex-shrink: 0;
    }
    #colManagerModal .preset-btn {
        padding: 4px 12px;
        border-radius: 12px;
        border: 1px solid #c5cdd6;
        background: #fff;
        color: #555;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        transition: all .15s;
        white-space: nowrap;
    }
    #colManagerModal .preset-btn:hover  { background: #ebf5fb; border-color: #3c8dbc; color: #3c8dbc; }
    #colManagerModal .preset-btn.active { background: #3c8dbc; border-color: #367fa9; color: #fff; }

    /* Custom view delete × */
    #colManagerModal .preset-del {
        display: inline-flex; align-items: center; justify-content: center;
        margin-left: 5px; width: 14px; height: 14px;
        border-radius: 50%; background: rgba(255,255,255,.35);
        font-size: 11px; line-height: 1; cursor: pointer;
        transition: background .15s;
    }
    #colManagerModal .preset-btn.active .preset-del { background: rgba(255,255,255,.3); }
    #colManagerModal .preset-del:hover { background: #e74c3c; color: #fff; }

    /* + Save View pill */
    #colManagerModal .preset-add-btn {
        border-style: dashed !important;
        color: #3c8dbc !important;
        background: transparent !important;
    }
    #colManagerModal .preset-add-btn:hover { background: #ebf5fb !important; }

    /* Hint strip */
    #colManagerModal .feature-hint {
        background: #eaf4fb;
        border: none;
        border-bottom: 1px solid #d0e8f5;
        border-radius: 0;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        margin: 0;
    }
    #colManagerModal .feature-hint .icon { font-size: 15px; }
    #colManagerModal .feature-hint p    { font-size: 11px; color: #31708f; line-height: 1.5; margin: 0; }
    #colManagerModal .feature-hint strong { color: #245269; }

    /* Scrollable body */
    #colManagerModal .modal-body {
        padding: 12px 14px;
        overflow-y: auto;
        flex: 1;
        background: #f7f9fb;
    }

    /* Column list section label */
    #colManagerModal .col-list-label {
        font-size: 10px;
        font-weight: 700;
        color: #9aabb8;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 8px;
        padding-bottom: 5px;
        border-bottom: 1px solid #e2eaf0;
    }

    /* Column rows */
    #col-sort-list { list-style: none; display: flex; flex-direction: column; gap: 4px; padding: 0; margin: 0; }
    #col-sort-list li {
        background: #fff;
        border: 1px solid #dce3ea;
        border-radius: 5px;
        padding: 8px 11px;
        display: flex;
        align-items: center;
        gap: 9px;
        cursor: grab;
        transition: border-color .15s, background .15s, box-shadow .15s;
        user-select: none;
    }
    #col-sort-list li:hover {
        border-color: #3c8dbc;
        background: #f0f7fc;
        box-shadow: 0 1px 4px rgba(60,141,188,.12);
    }
    #col-sort-list li.dragging  { opacity: .45; border-color: #3c8dbc; }
    #col-sort-list li.drag-over { border-color: #3c8dbc; background: #e8f4fb; }

    .drag-handle       { color: #c5cdd6; font-size: 15px; cursor: grab; line-height: 1; flex-shrink: 0; }
    .drag-handle:hover { color: #3c8dbc; }
    .col-badge {
        background: #3c8dbc; color: #fff;
        font-size: 10px; font-weight: 700;
        min-width: 20px; height: 20px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .col-name { flex: 1; font-size: 12.5px; color: #333; }
    .visibility-toggle { cursor: pointer; color: #c5cdd6; font-size: 15px; background: none; border: none; padding: 0; transition: color .15s; flex-shrink: 0; }
    .visibility-toggle:hover      { color: #3c8dbc; }
    .visibility-toggle.hidden-col { color: #e74c3c; }
    li.hidden-col-row             { opacity: .55; }
    li.hidden-col-row .col-name   { text-decoration: line-through; color: #aaa; }

    /* Footer */
    #colManagerModal .modal-footer {
        padding: 12px 16px;
        border-top: 1px solid #dce3ea;
        background: #fff;
        display: flex;
        gap: 8px;
        align-items: center;
        flex-shrink: 0;
    }
    #colManagerModal .modal-footer .btn-outline {
        background: #fff; border: 1px solid #ccc; color: #555;
        border-radius: 3px; font-size: 12px; padding: 6px 14px; cursor: pointer;
        transition: background .15s;
    }
    #colManagerModal .modal-footer .btn-outline:hover { background: #f0f0f0; }
    #colManagerModal .modal-footer .btn-primary {
        background: #3c8dbc; border: 1px solid #367fa9; color: #fff;
        border-radius: 3px; font-size: 12px; padding: 6px 16px; cursor: pointer;
        margin-left: auto;
        transition: background .15s;
        font-weight: 600;
    }
    #colManagerModal .modal-footer .btn-primary:hover { background: #367fa9; }



  </style>

     <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Product-wise Sales</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Product-wise Sales report</li>

          </ol>

        </section>

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Product-wise Sales</h3>  <span id="total_count" class="badge bg-green"></span>  

                </div>

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-12">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-2"> 

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_select" class="form-control branch_filter"></select>

									</div> 

								</div> 

								<?php }else{?>

									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

								<?php }?> 

								<div class="col-md-2"> 

									 <div class="form-group">

            		                    <div class="input-group">

            		                        <br>

            		                       <button class="btn btn-default btn_date_range" id="rpt_date_picker">

            							    

            		                        <i class="fa fa-calendar"></i> Date range picker

            		                        <i class="fa fa-caret-down"></i>

            		                      </button>

										  <span  style="display:none;" id="rpt_from_date"></span>

            							<span  style="display:none;" id="rpt_to_date"></span>

										  <span id="reportrange"></span>

            		                    </div>

            		                 </div><!-- /.form group -->

								</div>


								<div class="col-md-2"> 

									<label>Select Metal</label>

									<select id="metal" class="form-control" style="width:100%;"></select>

								</div>

								

								<div class="col-md-2"> 

									<label>Select category</label>

									<select id="category" style="width: 100%;"></select>

								</div>

								<div class="col-md-2"> 

                                		<label>Select Section</label>

                                		<select id="section_select" class="form-control" style="width:100%;"></select>

                                </div>

								<div class="col-md-2"> 

									<label>Select Product</label>

									<select id="prod_select" class="form-control" style="width:100%;"></select>

								</div>

								

							</div>

							<div class="row">
							<div class="col-md-2"> 

								<label>Design</label>

								<select id="des_select" class="form-control" style="width:100%;"></select>

								</div>
									<div class="col-md-2"> 

									<label>Sub Design</label>

											 <select id="sub_des_select" class="form-control" style="width:100%;"></select>

									</div>
								
                                 <div class="col-md-2">  
                                <div class="form-group">
									<label>HUID</label>
                                <input type="text" id="tag_huid" class="form-control" placeholder="HUID">
                                </div>
                                </div>
								<div class="col-md-2"> 

									<label>Report Type</label>

									<select id="sales_report_type" class="form-control" style="width:100%;">

									    <option value="1" >Summary</option>

									    <option value="2" selected>Detailed</option>

									</select>

								</div>

								<div class="col-md-2"> 

									<label>Select Employee</label>

									<select id="emp_sel" class="form-control" style="width:100%;"></select>

								</div>

								

								<div class="col-md-3 tagged-options"> 


							    	<!-- <input type="radio" id="all" value="0" name="tag_or_nontag" checked/> <label for="tagged">All</label> &nbsp; -->

								
									<input type="radio" id="tagged" value="1" name="tag_or_nontag" checked/> <label for="tagged">Tagged</label> &nbsp;

									

									<input type="radio" id="non_tagged" value="2" name="tag_or_nontag" /> <label for="non_tagged">Non Tagged</label> &nbsp;

									

									<!--<input type="radio" id="all_bills" value="3" name="tag_or_nontag" checked/> <label for="all_bills">All</label>-->

									

								</div>

							    <div class="col-md-3"> 

									<label></label>

									<div class="form-group" style="display: flex; gap: 5px;">

										<button type="button" id="item_sale_detail_search" class="btn btn-info">Search</button>   
                                        <button type="button" id="openColManager" class="btn btn-primary"><i class="fa fa-cog"></i> Customize Columns</button>

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

								   <div class="table-responsive detail_report" style="">

									  <table id="itemwise-sales_detail" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">

										 <thead><!-- built dynamically by COL_MAP in ret_reports.js --></thead><tbody></tbody>

									 </table>

									</div>

									 <div class="table-responsive summary_report" style="display:none;">

									     <table id="sales_summary_report" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">

    										 <thead><!-- built dynamically by SUMMARY_COL_MAP in ret_reports.js --></thead><tbody></tbody>

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
      
      <!-- Column Manager Modal -->
      <div class="modal-overlay" id="colManagerModal">
        <div class="modal-box">
          <div class="modal-header">
            <i class="fa fa-columns" style="color:rgba(255,255,255,.85);font-size:16px;"></i>
            <h2>Customize Columns</h2>
            <span class="col-vis-badge" id="colVisBadge">0 / 0 visible</span>
            <button class="close-btn" id="closeModal">✕</button>
          </div>
          <div class="modal-body">
            <div class="feature-hint" style="margin-bottom:18px">
              <span class="icon">ℹ️</span>
              <p><strong>Drag rows</strong> to reorder columns &nbsp;|&nbsp; Click <strong>👁 / 🚫</strong> to show/hide &nbsp;|&nbsp; Use <strong>Presets</strong> for quick views &nbsp;|&nbsp; Preferences saved in browser.</p>
            </div>
            <div class="preset-row">
              <button class="preset-btn active" data-preset="default">Default</button>
              <button class="preset-btn" data-preset="customer">Customer View</button>
              <button class="preset-btn" data-preset="weight">Weight View</button>
              <button class="preset-btn" data-preset="finance">Finance View</button>
            </div>
            <div class="col-list-label">Column Order &amp; Visibility (drag to reorder)</div>
            <ul id="col-sort-list"></ul>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" id="resetCols">↺ Reset Default</button>
            <button class="btn btn-primary" id="applyColOrder">✓ Apply &amp; Save</button>
          </div>
        </div>
      </div>
      <!-- /.Column Manager Modal -->

      

     <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

            <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

          </div>

          <div class="modal-body">

            <img src="" id="imagepreview" style="width: 300px; height: 264px;" >

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-default danger" data-dismiss="modal">Close</button>

          </div>

        </div>

      </div>

    </div>
