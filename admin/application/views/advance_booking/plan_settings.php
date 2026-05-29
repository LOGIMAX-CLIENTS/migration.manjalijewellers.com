<style type="text/css">

.ui-autocomplete { 

max-height: 200px; 

overflow-y: scroll; 

overflow-x: hidden;

}



#myCheck:checked + #area {

  display: block !important;

}



.col-sm-3 {

    width: 33.333333%;

}



</style>

  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         
        Advance Bookings

     </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Advance Booking</a></li>
        <li class="active">Plan Settings</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Plan Settings</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
        <form id="adv_plan_form">
            <div class="">
                <div class="col-md-11 col-md-offset-1">
                    <div class="row">
                        <div class="col-md-5">
                            <div>
                                <input type="hidden" id="form_type" name="form_type" value="<?php echo ($plan['id_plan']!=NULL && $plan['id_plan']>0 ?'update':'save'); ?>" >
                                <input type="hidden" id="id_plan" name="id_plan" value="<?php echo ($plan['id_plan']!=NULL && $plan['id_plan']>0 ?$plan['id_plan']:'0'); ?>" >
                                <label>Visible to Customer</label>
                                <input type="radio" id="" name="is_visible" value="0" class="minimal" <?php if($plan['is_visible']==0){ ?> checked <?php } ?>/>  Restrict to Join</label>
                                <input type="radio" id="" name="is_visible" value="1" class="minimal" <?php if($plan['is_visible']==1){ ?> checked <?php } ?>/>  Show to All</label>
                                <input type="radio" id="" name="is_visible" value="2" class="minimal" <?php if($plan['is_visible']==2){ ?> checked <?php } ?>/>  Show in Admin</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div>
                                <label>Active to Customer</label>
                                <input type="checkbox" id="active" data-on-text="YES"
                                    data-off-text="NO" name="is_active" value="1" <?php if($plan['is_active']==1) { ?> checked="true" <?php } ?>/>
                            </div>
                        </div>
                    </div>
                        <legend> <a  data-toggle="tooltip" title="Enter Your Plan Details">Plan Detail</a></legend>
                    
                    <div class="col-md-8">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plan Name</label>
                                    <input type="text" class="form-control"  required="true" id="plan_name" name="plan_name" value="" placeholder="Your Plan name" required="true"> 
                                    <p class="help-block"></p>
                            </div>
                        </div>  
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="plan_code" class="">Plan Code</label>
                                <input type="text" class="form-control" id="plan_code"  required="true"  name="plan_code" value="" placeholder="Your Plan code"> 
                                <p class="help-block"></p>
                            </div> 	
		                </div> 
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sync_plan_code" class="">Sync Plan Code</label>
                                <input type="text" class="form-control" id="sync_plan_code"  required="true"  name="sync_plan_code" value="" placeholder="Sync Plan code"> 
                                <p class="help-block"></p>
                            </div> 	
		                </div>  

                        <!--<div class="col-md-6">                                                             
                            <div class="form-group" style="height:60px">
                                <label for=""><a data-toggle="tooltip" title="" data-original-title="Select branch to create advance plan Account"> Select Branch Permissions </a> <span class="error">*</span></label>
                                <select multiple="" id="login_branch_select" required="true" class="form-control select2 cls select2-hidden-accessible" data-placeholder="Select Your Baranchs" tabindex="-1" aria-hidden="true"><option value="0">All</option><option value="1">HEAD OFFICE</option><option value="2">KAMADHENU JEWELLERS</option></select><span class="select2 select2-container select2-container--default select2-container--below" dir="ltr" style="width: 275px;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false" tabindex="0"><ul class="select2-selection__rendered"><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" placeholder="Select Your Baranchs" style="width: 273px;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                <input id="login_branch" name="accessible_branches" type="hidden" value="">
                                <div id="sel_br" data-sel_br=""></div> 
                            </div>	
                        </div>-->
                         <div class="col-md-6">                                                             
                            <div class="form-group">
                               
                                   
                                <label for=""><a data-toggle="tooltip" title="" data-original-title="Select branch to create advance plan Account"> Select Branch Permissions </a> <span class="error">*</span></label>
                                <select multiple="multiple" id="login_branch_select" required="true" class="form-control select2 cls" data-placeholder="Select Your Branches"></select> 
                                <input id="login_branch" name="accessible_branches" type="hidden" value="">
                                <div id="sel_br" data-sel_br=""></div> 
                            </div>	
                        </div>
                       
                        <div class="col-md-6">
						 	<div class="form-group">
		                       <label for="metal">Commodity </label>
		                       <input type="hidden" id="metal_val" name="id_metal" value="0">
		                       	 <select id="metal_select" name="id_metal" class="form-control" required="true">
		                       	 </select>
		                  		 <p class="help-block"></p>                       	
		                    </div>
		                </div>
                        <div class="col-md-6">
						 	<div class="form-group">
		                       <label for="metal">Purity </label>
		                       <input type="hidden" id="purity_val" name="purity" value="">
		                       	 <select id="purity" name="purity" class="form-control">
                                 </select>
		                  		 <p class="help-block"></p>                       	
		                    </div>
		                </div>
                   
                        
                        <div class="col-md-6">
                            <label>Payable By</label>
                            <div class="row">
                                <div class="col-md-12">	
                                    <label>
                                        <input type="radio" id="opt_amount" name="payable_by" value="0" class="minimal" checked="">  Amount</label>
                                </div>
                                <div class="col-md-12">	
                                        <label>									  		
                                        <input type="radio" id="opt_weight" name="payable_by" value="1" class="minimal"> Weight</label>
                                </div>		
                                <p class="help-block"></p>                       	
                            </div>
                        </div>
                        <div class="col-md-6">

						 	<div class="form-group">

			                   <label>Maturity Type</label>

							   	 <select id="maturity_type" class="form-control" data-placeholder="Type" name='maturity_type'>

							   	     <option value=1 >Months</option>

							   	     <option value=2 >Days</option>

							   	 </select> 
								<p class="help-block"></p>

			                </div> 	
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">    					   					  								   
    						   <label for="units">Maturity Value</label>
    						   <input type="text" class="form-control input_amount" id="maturity_value" name="maturity_value" value="" placeholder="maturity value">
    						</div>
                            <p class="help-block"></p> 
                        </div>
                    </div>
                    <div class="col-md-4"> 
                        <div class="form-group">
                        <label for="chargeseme_name">Upload Plan image</label> 
                            <input id="edit_plan_img" name="edit_plan_img" accept="image/*" type="file" >
                                <img src="<?php echo(isset($plan['logo'])? base_url().'assets/img/plan_image/'.$plan['logo']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="edit_plan_img_preview" style="width:304px;height:100%;" alt="advance plan image"> 
                            <p class="help-block"></p>  
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <br><br>
                        <div class="col-md-4">
                                <div class="form-group">    					   					  								   
                                <label for="units">Minimum</label>
                                <input type="text" class="form-control input_amount" id="minimum_val" name="minimum_val" value="">
                                </div>
                                <p class="help-block"></p> 
                            </div>
                            <div class="col-md-4">	
                                <div class="form-group">						
                                    <label for="units">Maximum</label>
                                    <input type="text" class="form-control input_amount" id="maximum_val" name="maximum_val" value="">		
                                </div>  
                                <p class="help-block"></p> 
                            </div>
                            <div class="col-md-4">	
                                <div class="form-group">
                                    <label for="units">Denomination</label>
                                    <input type="text" class="form-control input_amount" id="flx_denomintion" name="flx_denomintion" value="">
                                </div>
                            </div>
                        </div>                  
                        <br><br>
                        <div class="row">                           
                            <legend> <a  data-toggle="tooltip" title="Enter Your advance plan Details">Advance Limit</a></legend>
                            <div class="col-md-4" style="display: block;">
					            <div class="form-group"> 
                                    <label>Advance Limit Available</label> 
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="radio" id="is_adv_limit_available" class="minimal" name="is_adv_limit_available" value="1"> Yes
                                        </div> 
                                        <div class="col-md-6">
                                            <input type="radio" id="is_adv_limit_available" class="minimal" name="is_adv_limit_available"  value="0">No 
                                        </div> 
                                    </div> 
                                    <p class="help-block"></p> 
                                </div>
                            </div>
                        </div>                    
                    <div class="col-md-12" id="advance_limit_div" style="pointer-events: none;opacity: 0.4;">
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label>Advance Limit Type</label> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="radio" id="adv_limit_type" class="minimal" name="adv_limit_type" value="1"> Percent type
                                    </div> 
                                    <div class="col-md-6">
                                        <input type="radio" id="adv_limit_type" class="minimal" name="adv_limit_type"  value="0">Amount type 
                                    </div> 
                                </div> 
                                <p class="help-block"></p> 
                            </div>
                        </div>
                        <div class="col-md-4">	
                            <div class="form-group">						
                                <label for="units">Total Advance Limit Value</label>
                                <input type="text" class="form-control input_amount" id="total_adv_limit_value" name="total_adv_limit_value" value="">		
                            </div>  
                            <p class="help-block"></p> 
                        </div>                               
                        <div class="col-md-4">	
                            <div class="form-group">						
                               <label for="units">Advance Limit Value Online (Only Amount INR)</label>
                                <input type="text" class="form-control input_amount" id="adv_limit_value_online" name="adv_limit_value_online" value="">		
                            </div>  
                            <p class="help-block"></p> 
                        </div>
                    </div>
                        <legend><a data-toggle="tooltip" title="" data-original-title="Plan Description"> Description</a></legend>           
                        <div class="row">			
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="user_lastname"></label>
                                    <textarea rows="4" cols="170" id="description" name="plan_description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-default"><br/>
                                    <div class="col-xs-offset-5">
                                        <button type="button" id="submit" class="btn btn-primary">Save</button> 
                                        <button type="button" class="btn btn-default btn-cancel">Cancel</button>
                                    </div> <br/>
                                </div> 
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </form>    
        </div>
    </div>
</div>
