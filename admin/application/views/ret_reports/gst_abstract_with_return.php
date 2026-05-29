<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
     <!-- Content Header (Page header) -->
      <style>
        .dataTable tr td:nth-child(3), .dataTable tr td:nth-child(5), .dataTable tr td:nth-child(6), .dataTable tr td:nth-child(7), .dataTable tr td:nth-child(8), .dataTable tr td:nth-child(9), .dataTable tr td:nth-child(10), .dataTable tr td:nth-child(11), .dataTable tr td:nth-child(12) {
            text-align: right;
        }
     @media print {

         html,
         body {
             height: auto;
             width: 190vh;
             margin: 0 !important;
             padding: 0 !important;
             overflow: hidden;
         }
     }
     .sales_return_total {
        font-weight: bold;
     }
     </style>
     <section class="content-header">
         <h1>
             Reports
             <small>GST Bills</small>
         </h1>
         <ol class="breadcrumb">
             <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
             <li><a href="#">Retail Reports</a></li>
             <li class="active">GST Abstract(Return deducted)</li>
         </ol>
     </section>

     <!-- Main content -->
     <section class="content">
         <div class="row">
             <div class="col-xs-12">
                 <div class="box box-primary">
                     <div class="box-header with-border">
                         <h3 class="box-title">GST Abstract</h3> <span id="total_count" class="badge bg-green"></span>
                     </div>
                     <div class="box-body">
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="box box-default">
                                     <div class="box-body">
                                         <div class="row">
                                             <div class="col-md-2">
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

                                            <div class="col-md-2"> 
                                                <label>Select Metal</label>
                                                <select id="metal" class="form-control" style="width:100%;"></select>
                                            </div>

                                            <div class="col-md-2"> 
                                                <label>Select Category</label>
                                                <select id="category" class="form-control" style="width:100%;" multiple></select>
                                            </div>

                                            <div class="col-md-2"> 
                                                <label>Tax Group</label>
                                                <select id="tax_group" class="form-control" style="width:100%;" multiple></select>
                                            </div>

                                            <div class="col-md-2"> 
                                                <label>HSN Code</label>
                                                <select id="hsn_code" class="form-control" style="width:100%;" multiple></select>
                                            </div>
                                             
                                            <div class="col-md-2">
                                                 <div class="form-group">
                                                     <div class="input-group">
                                                         <label>Select Report Type</label>
                                                         <select class="form-control" style="width:100%;"
                                                             id="report_type">
                                                             <option value="0" selected>All</option>
                                                             <option value="1">B2C</option>
                                                             <option value="2">B2B</option>
                                                         </select>
                                                     </div>
                                                 </div><!-- /.form group -->
                                             </div>

                                             <div class="col-md-2">
                                                 <div class="form-group">
                                                     <div class="input-group">
                                                         <label>GST Filter</label>
                                                         <select class="form-control" style="width:100%;"
                                                             id="gst_filter">
                                                             <option value="0" selected>All</option>
                                                            <!-- <option value="1">Overseas Bills</option>-->
                                                             <option value="1">Inter State Bills</option>
                                                             <option value="2">Intra State Bills</option>
                                                         </select>
                                                     </div>
                                                 </div><!-- /.form group -->
                                             </div>

                                             <div class="col-md-2">
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

                                             <div class="col-md-2" style="display:none;">
                                                 <div class="form-group">
                                                     <div class="input-group">
                                                         <label>GST Filter</label>
                                                         <select class="form-control" style="width:100%;"
                                                             id="sale_ret_filter">
                                                             <option value="0" >With Sales Return</option>
                                                             <option value="1" selected>Without Sales Return</option>
                                                         </select>
                                                     </div>
                                                 </div><!-- /.form group -->
                                             </div>

                                             


                                             <div class="col-md-2">
                                                 <label></label>
                                                 <div class="form-group">
                                                     <button type="button" id="gst_abstract_search_with_return"
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
                                     <table id="gst_abstract_list"
                                         class="table table-bordered table-striped text-center">
                                         <thead>
                                             <tr>
                                                 <th>Category</th>
                                                 <th>Starting/Ending<br>Bill No</th>
                                                 <th>Pcs</th>
                                                 <th>HSN Code</th>
                                                 <th>Gwt(Grams)</th>
                                                 <!-- <th>NWT(Grams)</th> -->
                                                 <th>Taxable Amount(Rs)</th>
                                                 <th>SGST</th>
                                                 <th>CGST</th>
                                                 <th>IGST</th>
                                                 <th>GST</th>
                                                 <th>Round Off</th>
                                                 <th>Total Amount(Rs)</th>
                                             </tr>
                                         </thead>
                                         <tbody></tbody>
                                         <tfoot>
                                             <tr>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <td></td>
                                                 <!-- <td></td> -->
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