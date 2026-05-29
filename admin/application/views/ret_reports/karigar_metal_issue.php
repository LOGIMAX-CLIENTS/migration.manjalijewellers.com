<style>

@media print 

{    

     table tr td.sales

     { 

       font-weight:bold;

     }

     a[href]:after {
		content: "";
		}

 }

 </style> 

<!-- Content Wrapper. Contains page content -->



   <div class="content-wrapper">

     <!-- Content Header (Page header) -->

     <section class="content-header">

       <h1>

       Karigar Metal Issue / Receipt Report

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

                                <label>Select Karigar</label>

                                <select id="karigar" class="form-control" style="width:100%;"multiple></select>

                                </div>
                                

                                <div class="col-md-2"> 

                                  <label>Select Metal</label>

                                  <select id="metal" class="form-control" style="width:100%;" multiple></select>

                                  </div>



                                <div class="col-md-2"> 

                                      <label>Select Category</label>

                                      <select id="category" class="form-control" style="width:100%;"multiple></select>

                                      </div>

                                      
                                <div class="col-md-2"> 

                                    <label>Select Product</label>

                                    <select id="prod_select" class="form-control" style="width:100%;"multiple></select>

                                    </div>

                            

                                    <div class="col-md-2"> 

                                  <label>Report type</label>

                                  <select id="report_type" class="form-control" style="width:100%;">

                                  <option value="1" selected="">Detailed</option>

                                  <option value="2">Summary</option>

                                  </select>

                                  </div>

                             <div class="col-md-2">

                                <div class="form-group">

                                     <div class="input-group">

                                        <br>

                                            <button class="btn btn-default btn_date_range" id="rpt_date_picker">

                                                <i class="fa fa-calendar"></i> Date range picker

                                                     <i class="fa fa-caret-down"></i>

                                                   </button>

                                                <span style="display:none;" id="rpt_from_date"></span>

                                                        <span style="display:none;" id="rpt_to_date"></span>

                                               </div>

                                    </div><!-- /.form group -->

                               </div>

                             

                             

                             
                               </div>
                               
                               <div class="row"> 

                                <div class="col-md-2"> 
                                  <label>Status Type</label>
                                  <select id="bill_type" class="form-control" style="width:100%;">
                                      <option value="1">Success</option>
                                      <option value="2">Cancelled</option>
                                  </select>
                                </div>

                               <div class="col-md-2"> 

                                 <label></label>

                                 <div class="form-group">

                                     <button type="button" id="karigar_metal_issue" class="btn btn-info">Search</button>   

                                 </div>

                             </div>

                         </div>

                      </div>

                    </div> 

                   </div> 

                </div> 

             

               <div id="cash_abstract">

                    <div class="box box-info sales_details" >

                     <div class="box-header with-border">

                       <div class="box-tools pull-right">

                         <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                       </div>

                     </div>

                     <div class="box-body">

                         <div class="row">

                             <div class="box-body">

                                 <div class="table-responsive old_metal" style="">

                                     <table id="karigar_metal_issue_list" class="table table-bordered table-striped text-center sales_list" style="width:100%;">

                                         <thead style="text-transform:uppercase;">

                                            <tr>

                                                <th>S.No </th>
                                                 
                                                 <th>Po No</th>

                                                 <th>Po Date</th>

                                                 <th>Issue Date</th>

                                                 <th>Ref No</th>

                                                 <th>BT No</th>

                                                 <th>Karigar</th>

                                                 <th>Metal</th>

                                                 <th>category</th>

                                                 <th>Proudct</th>

                                                 <th>Issue Gross (Wt)</th>

                                                 <th>Issue Net (Wt)</th>

                                                 <th>Issue Dia (Wt)</th>

                                                 <th>Pure Weight</th>

                                                 <th>UOM</th>

                                                 <th>Employee</th>

                                                 <th>Metal Issue Type</th>

                                                 <th>Remark</th>


                                             </tr>

                                         </thead>

                                         <tbody ></tbody>

                                     </table>

                                 </div>

                              </div> 

                         </div> 

                     </div>

                 </div>

                 </div>





                    <div class="box box-info purchase_details" style="display: none;">

                     <div class="box-header with-border">

                       <h3 class="box-title">Purchase Details</h3>

                       <div class="box-tools pull-right">

                         <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                       </div>

                     </div>

                     <div class="box-body">

                         <div class="row">

                             <div class="box-body">

                                <div class="table-responsive">

                                   <table id="purchase_list" class="table table-bordered table-striped text-center">

                                         <thead>

                                           <tr>

                                             <th width="15%">Metal</th>                          

                                             <th width="15%">Metal</th>                                

                                             <th width="15%">Gross Wt</th>                               

                                             <th width="10%">Net Wt</th>

                                             <th width="10%">Amount</th>

                                             <th width="10%">Avg Rate</th>

                                           </tr>

                                         </thead>

                                         <tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>

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

   



<!-- CHIT DEPOSIT -->

<div class="modal fade" id="stone_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog modal-lg">

 <div class="modal-content">

     <div class="modal-header">

         <h4 class="modal-title" id="myModalLabel">Stone Details</h4>

     </div>

     <div class="modal-body">

         <div>

         <table id="stone_details" class="table table-bordered table-striped text-center">

             <thead>

             <tr>

             <th>Stone Name</th>

             <th>Stone Pcs</th>

             <th>Weight</th>

             <th>Rate</th>

             <th>Amount</th>

             </tr>

             </thead> 

             <tbody>

             </tbody>										

         </table>

         </div>

     </div>

     <div class="modal-footer">

         <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

     </div>

 </div>

</div>

</div>

<!-- CHIT DEPOSIT -->