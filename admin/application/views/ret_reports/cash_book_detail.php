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

                     <div class="box-body">
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="table-responsive">
                                     <table id="cash_book_list"
                                         class="table table-bordered table-striped text-center">
                                         <thead>
                                             <tr>
                                                <th style="text-align: left;">Bill Date</th>
                                                <th style="text-align: left;">Bill Number</th>
                                                <th style="text-align: left;">Customer / Bank Details</th>
                                                <th style="text-align: right;">Amount</th>
                                                
                                             </tr>
                                         </thead>
                                         <tbody></tbody>
                                         <tfoot>
                                             <tr>
                                                 <th style="text-align: left;">Total</th>
                                                 <th></th>
                                                 <th></th>
                                                 <th style="text-align: right;"></th>
                                                 
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