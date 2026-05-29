<!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
      <style>
     @media print {

         html,
         body {
             height: auto;
             width: 390mm;
             margin: 0 !important;
             padding: 0 !important;
             overflow: hidden;
         }
     }
     </style>
     <section class="content-header">
         <h1>
             Repair GST Abstract
         </h1>
     </section>

     <!-- Main content -->
     <section class="content">
         <div class="row">
             <div class="col-xs-12">
                 <div class="box box-primary">
                     <div class="box-body">
                         <div class="row">
                             <div class="col-md-offset-2 col-md-8">
                                 <div class="box box-default">
                                     <div class="box-body">
                                         <div class="row">
                                             <div class="col-md-3">
                                                 <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
                                                 <div class="form-group">
                                                     <label>Select Branch</label>
                                                     <select id="branch_select" class="form-control branch_filter" style="width:100%;" ></select>
                                                 </div>
                                                 <?php }else{?>
                                                 <input type="hidden" id="branch_filter"
                                                     value="<?php echo $this->session->userdata('id_branch') ?>">
                                                 <input type="hidden" id="branch_name"
                                                     value="<?php echo $this->session->userdata('branch_name') ?>">
                                                 <?php }?>
                                             </div>
                                             
                                             <div class="col-md-3">
                                                 <div class="form-group">
                                                     <div class="input-group">
                                                         <br>
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

                                             


                                             <div class="col-md-2">
                                                 <label></label>
                                                 <div class="form-group">
                                                     <button type="button" id="repair_gst_abstract_search"
                                                         class="btn btn-info">Search</button>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>


                         <div class="row">
                             <div class="col-md-12">
                                 <div class="table-responsive">
                                     <table id="repair_gst_abstract"
                                         class="table table-bordered table-striped text-center">
                                         <thead>
                                             <tr>
                                                 <th>Bill No</th>
                                                 <th>Bill Date</th>
                                                 <th>Customer</th>
                                                 <th>Mobile</th>
                                                 <th>Type</th>
                                                 <th>Taxable Amount</th>
                                                 <th>SGST(9%)</th>
                                                 <th>CGST(9%)</th>
                                                 <th>GST(18%)</th>
                                                 <th>Total Amount(Rs)</th>
                                             </tr>
                                         </thead>
                                         <tbody></tbody>
                                         <tfoot>
                                             <tr style="text-align:right;">
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
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