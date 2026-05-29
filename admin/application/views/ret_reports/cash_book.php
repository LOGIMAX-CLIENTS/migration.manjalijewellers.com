<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
     <!-- Content Header (Page header) -->
      <style>
     @media print {

         html,
         body {
             height: auto;
             width: 100%;
             margin: 0 !important;
             padding: 0 !important;
             overflow: hidden;
             table-layout: fixed;
         }
     }
     </style>
     <section class="content-header">
         <h1>
             Reports
             <small>Cash Book</small>
         </h1>
         <ol class="breadcrumb">
             <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
             <li><a href="#">Retail Reports</a></li>
             <li class="active">Cash Book Report</li>
         </ol>
     </section>

     <!-- Main content -->
     <section class="content">
         <div class="row">
             <div class="col-xs-12">
                 <div class="box box-primary">
                     <div class="box-header with-border">
                        <div class="col-md-2">
                                                 <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
                                                 <div class="form-group">
                                                     <select id="branch_select" class="form-control branch_filter" style="width:100%;" ></select>
                                                 </div>
                                                 <?php }else{?>
                                                 <input type="hidden" id="branch_filter"
                                                     value="<?php echo $this->session->userdata('id_branch') ?>">
                                                 <input type="hidden" id="branch_name"
                                                     value="<?php echo $this->session->userdata('branch_name') ?>">
                                                 <?php }?>
                                             </div>



                                             <div class="col-md-2">
                                                 <div class="form-group">
                                                     <div class="input-group">
                                                         <button class="btn btn-default btn_date_range"
                                                             id="rpt_date_picker">

                                                             <i class="fa fa-calendar"></i> Date range picker
                                                             <i class="fa fa-caret-down"></i>
                                                         </button>
                                                         <span style="display:none;" id="rpt_from_date"></span>
                                                         <span style="display:none;" id="rpt_to_date"></span>
                                                     </div>
                                                 </div><!-- /.form group -->
                                             </div>
                                            <!-- <div class="col-md-2">
            									<select id="category" class="form-control" style="width:100%;"></select>
            								</div> -->





                                             <div class="col-md-1">
                                                 <div class="form-group">
                                                     <button type="button" id="cash_book_search"
                                                         class="btn btn-info">Search</button>
                                                 </div>
                                             </div>
                     </div>
                     <div class="box-body">
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="table-responsive">
                                     <table id="cash_book_list"
                                         class="table table-bordered table-striped text-center" style="width: 100%;table-layout:fixed;">
                                         <thead>
                                             <tr>
                                                <td style="width:20% !important;">TRANSDATE</td>
                                                <td style="width:15% !important;">TRANNO</td>
                                                <td style="width:25% !important;">PARTICULAR</td>
                                                <td style="width:15% !important;">DEBIT</td>
                                                <td style="width:15% !important;">CREDIT</td>
                                                <td style="width:20% !important;">REMARKS</td>
                                             </tr>
                                         </thead>
                                         <tbody></tbody>
                                         <tfoot>
                                             <tr>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                             </tr>
                                         </tfoot>
                                     </table>
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