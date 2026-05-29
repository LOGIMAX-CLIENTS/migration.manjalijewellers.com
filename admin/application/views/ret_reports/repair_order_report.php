<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
     <!-- Content Header (Page header) -->
      <style>
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
     </style>
     <section class="content-header">
         <h1>
             Reports
             <small> Issue Receipt Report</small>
         </h1>
         <ol class="breadcrumb">
             <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
             <li><a href="#">Retail Reports</a></li>
             <li class="active">Issue Receipt Report</li>
         </ol>
     </section>

     <!-- Main content -->
     <section class="content">
         <div class="row">
             <div class="col-xs-12">
                 <div class="box box-primary">
                     <div class="box-header with-border">


                                             <div class="col-md-2">
                                                <label for=""></label>
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

                                             <div class="col-md-2">
                                                 <label for=""> Select Branch</label>
                                                 <div class="form-group">
                                                     <select id="branch_select" class="form-control" style="width:100%;" ></select>
                                                 </div>

                                             </div>

                                             <div class="col-md-2">
                                                 <label for=""> Select Employee</label>
                                                 <div class="form-group">
                                                     <select id="emp_select" class="form-control" style="width:100%;" ></select>
                                                 </div>

                                             </div>
                                            <div class="col-md-2">
                                                 <label for=""> Select Karigar</label>
                                                 <div class="form-group">
                                                     <select id="karigar" class="form-control" style="width:100%;" ></select>
                                                 </div>

                                             </div>



                                             <div class="col-md-2">
                                                 <label for=""> Order Type</label>
                                                 <div class="form-group">
                                                     <select id="cus_order_type" class="form-control" style="width:100%;" >
                                                        <option value="1">CUSTOM</option>
                                                        <option value="2">STOCK</option>
                                                    </select>
                                                 </div>

                                             </div>

                                             <div class="col-md-2">
                                                 <label for=""> Work At</label>
                                                 <div class="form-group">
                                                     <select id="order_type" class="form-control" style="width:100%;" >
                                                     <option value="0">ALL</option>
                                                        <option value="1">INHOUSE</option>
                                                        <option value="2">OUTSOURCE</option>
                                                    </select>
                                                 </div>

                                             </div>

                                             <div class="col-md-2">
                                                 <label for=""> Report Type</label>
                                                 <div class="form-group">
                                                     <select id="report_type" class="form-control" style="width:100%;" >
                                                        <option value="1">Details</option>
                                                        <option value="2">Summary</option>
                                                    </select>
                                                 </div>

                                             </div>



                                             <div class="col-md-1">
                                             <label for=""></label>
                                                 <div class="form-group">
                                                     <button type="button" id="repair_search"
                                                         class="btn btn-info">Search</button>
                                                 </div>
                                             </div>
                     </div>
                     <div class="box-body">
                         <div class="row stock_order" style = "display:none">
                             <div class="col-md-12">
                                 <div class="table-responsive">
                                     <table id="repair_order"
                                         class="table table-bordered table-striped text-center">
                                         <thead>
                                             <tr>
                                                <td>Karigar</td>
                                                <td>Work At</td>
                                                <td>Issue No</td>
                                                <td>Issue Date</td>
                                                <td>Order No</td>
                                                <td>Branch</td>
                                                <td>Employee</td>
                                                <td>Product</td>
                                                <td>Design</td>
                                                <td>Subdesign</td>
                                                <td>Issue wt</td>
                                                <td>Received  Wt</td>
                                                <td>Balance Weight</td>
                                                <td>Received  Date</td>
                                                <td>Received  By</td>
                                                <td>Details</td>
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
                         <div class="row custom_order" >
                             <div class="col-md-12">
                                 <div class="table-responsive">
                                     <table id="repair_order_cus" class="table table-bordered table-striped text-center">
                                         <thead>
                                             <tr>
                                                <td>Order No</td>
                                                <td>Order Date</td>
                                                <td>Karigar</td>
                                                <td>Branch</td>
                                                <td>Employee</td>
                                                <td>Work at</td>
                                                <td>Product</td>
                                                <td>Design</td>
                                                <td>Subdesign</td>
                                                <td>Issue wt</td>
                                                <td>Received  Wt</td>
                                                <td>Balance Weight</td>


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