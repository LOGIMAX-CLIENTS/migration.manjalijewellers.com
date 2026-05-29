<style type="text/css">
/* .add_rate {
	cursor: pointer;
	color: blue;
} */
.add_rate {
  margin-top: 5px;
}
.remove_rate
{
  margin-top: 5px;
}
.ad_rate
{
  margin-top: 4px;
}

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Selling Diamond Rate
            <small></small>
          </h1>
    
        </section>

	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<!-- <php echo form_open_multipart(($rate_list['id_cents_rate']!= NULL && $rate_list['id_cents_rate']>0 ?'admin_ret_catalog/rate/update/'.$rate_list['id_cents_rate']:'admin_ret_catalog/rate/save')); ?>
		Default box -->
		<form id="selling_diamond_rate_list">
		<div class="box">
	
			<div class="box-body">
	        <div class="row" >
			<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
			</div> 
            <div class="box-body">
                 <div class="row">
                 <div class="col-sm-4" id="quality code">
               <div class='form-group'>
               <label for="pan"> Quality code </label>
               <select class="form-control" id="quality_sel" name="rate[quality_code_id]" ></select>
               <input class="form-control" id="quality_code_id" type="hidden" value="<?php echo set_value('rate[quality_code_id]',$rate['quality_code_id']); ?>" />
               </div>
                </div> 
				<div class="col-md-7 pull-right">   
                 <div class="col-sm-7">    
				 <div class="form-group">
			     <label for="date_of_birth ">Effective Date</label>
				<input class="form-control datemask" data-date-format="dd-mm-yyyy" id="effective_date" name="rate[effective_date]" value="<?php echo set_value('rate[effective_date]',$rate['effective_date']); ?>" type="text">
				</div>
				</div>
                </div>  
                </div> 
				</div> 
				<div class="box-body">
			     <div class="row">   
			           <label for="scheme_code" class="col-md-3"><button type="button" id="add_selling_diamond_rate" class="btn btn-success ad_rate">Add Rate</button></label>
				       <!-- <div class="form-group"> -->
					   <div class="col-md-2"style="<?php echo $rate['add_diamond_rate'] == 1 || $rate['add_diamond_rate'] == 0 ? "" : "display: none;" ;?>" >
				       <!-- <div class="col-md-4"> -->
					   </div>
                         </div>
				         </div>
						 <div class="col-sm-12"> 
					    <div class="table-responsive"> 
                         <table id="diamond_selling_rate_table" width="50%" cellpadding="0" cellspacing="0"  name="rate_list[] "class="table table-bordered table-striped text-center" border="#729111 1px solid"required="true" >
                         <div class="row">
						              <thead>
                             <tr>  
                                <th>From Cent</th>
                                <th>To Cent</th>
                                <th>Rate</th>
                                <th>Action</th>	
                              </tr>
                         </thead>
                          <tbody>
                          <?php if($this->uri->segment(3) == 'edit'){
                            //echo"<pre>"; print_r($rate_list);exit;
                           foreach($rate_list as $key => $val){
                                      echo '<tr>
                                           <td><input type="number" class ="from_cent" name="sel_rate_list[from_cent][]" value='.$val['from_cent'].'></td>
                                           <td><input type="number" class ="to_cent"  name="sel_rate_list[to_cent][]" value='.$val['to_cent'].'></td>
                                           <td><input type="number" class ="rate" name="sel_rate_list[rate][]" value='.$val['rate'].'></td>
                                           <td><div><button class="add btn btn-success  name="add" "type="button"><i class="fa fa-plus"></i></button>
                                           <button class="delete btn btn-danger" type="button"><i class="fa fa-trash"></i></button></div></td>
                                           </tr>'; 
                                }
                        } ?>
                          </tbody>
 
                         </table> 
                    </div>
                 </div>
            </div>
                                 

		<div class="row">
		   <div class="box box-default"><br/>
			  <div class="col-xs-offset-5">
				<button type="button" id="selling_diamond_rate_submit" class="btn btn-primary">Save</button> 
				<button type="button" class="btn btn-default btn-cancel">Cancel</button>
			  </div> <br/>
			</div>
		  </div> 			
     </div>	
		<?php echo form_close();?> 
	</section><!-- /.content -->
</div>
</div>
</div> 
</div> <!-- /.content-wrapper -->
   <script type="text/javascript">
  </script>