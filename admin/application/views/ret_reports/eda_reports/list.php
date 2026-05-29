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
             <small>EDA Item Details</small>
         </h1>
         <ol class="breadcrumb">
             <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
             <li><a href="#">Retail Reports</a></li>
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
            									<select id="metal" class="form-control" style="width:100%;"></select>
            								</div>
                                             


                                             <div class="col-md-1">
                                                 <div class="form-group">
                                                     <button type="button" id="eda_sales_search"
                                                         class="btn btn-info">Search</button>
                                                 </div>
                                             </div>
                     </div>
                     <div class="box-body">
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="table-responsive">
                                     <table id="eda_sales_list"
                                         class="table table-bordered table-striped text-center" >
                                         <thead style="text-transform: uppercase;">
                                             <tr>
                                                 <th>Est No</th>
                                                 <th>Customer</th>
                                                 <th>Mobile</th>
                                                 <th>Tag Code</th>
                                                 <th>Metal</th>
                                                 <th>Category</th>
                                                 <th>Product</th>
                                                 <th>Design</th>
                                                 <th>Sub Design</th>
                                                 <th>Piece</th>
                                                 <th>GWT(Grams)</th>
                                                 <th>LWT(Grams)</th>
                                                 <th>NWT(Grams)</th>
                                                 <th>Discount</th>
                                                 <th>Item Cost(Rs)</th>
                                                 
                                             </tr>
                                         </thead>
                                         <tbody></tbody>
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