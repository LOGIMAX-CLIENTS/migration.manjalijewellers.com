<!-- Content Wrapper. Contains page content -->
<style type="text/css">

    .del_img {
        cursor: pointer;
        text-decoration: underline;
        color: blue;
    }
    .images_container {
        display: inline-block;
    }
    .order_images_new{
    position: absolute;
    z-index: 1000;
    opacity: 0;
    cursor: pointer;
    right: 0;
    top: 0;
    height: 100%;
    font-size: 24px;
    width: 100%;
  }

  .ord_img
  {
    padding:5px 10px;
    background:#605CA8;
    border:1px solid #605CA8;
    position:relative;
    color:#fff;
    border-radius:2px;
    text-align:center;
    float:left;
    cursor:pointer;
  }
  .multiselect-native-select {
    display: block;
  }

  .uploadImg {

    margin-bottom: 10px;
    font-size: 22px;

}

.img img {
    height: 100px;
    width: 100px;
}

.disp_images {
    cursor: pointer;
    text-decoration: underline;
    color: blue;
}

.disp_img_tag {
    width: 100%;
}

</style>
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Supplier Catalogue
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
		<li class="active">Supplier Catalogue</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- form -->
	    <?php //echo form_open_multipart(($supp['id_supp_catalogue']!=NULL && $supp['id_supp_catalogue']>0 ?'admin_ret_supp_catalog/supplier_catalog/update/'.$supp['id_supp_catalogue']:'admin_ret_supp_catalog/supplier_catalog/save')); ?>

     
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
			<h3 class="box-title">Create Supplier Catalogue</h3>
			</div>
			<div class="box-body">
                <div class="row" >
                        <div class="col-md-offset-1 col-md-10" id='error-msg'></div>
                </div>
				<div class="row" id="add_catalog">  
                    <input type="hidden" id="id_supp_catalogue" name="id_supp_catalogue" value="<?php echo $id_supp_catalogue; ?>"/>
					<div class="tab-content col-md-12">
                        <div class='row suppRows'>							       
                             
                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="short_code">Product<span class="error"> *</span></label>
                                    <select id="ret_product" class="form-control"></select>
                                    <input type='hidden' id='product_id' name='product_id' class='id_product' required='true' value='' /><input type='hidden' id='cat_id' name='cat_id' class='cat_id' required='true' value='' />	
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                <label for="short_code">Design<span class="error"> *</span></label>	
                                 <input type='hidden' id='design_id' name='design_id' class='id_design' required='true' value='' />				
                                    <select id="ret_design" class="form-control"></select>
                                </div> 
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="short_code">SubDesign<span class="error"> *</span></label>
                                      <input type='hidden' id='subdesign_id' name='subdesign_id' class='id_subdesign' required='true' value='' />	
                                    <select id="ret_sub_design" class="form-control"></select>
                                </div> 
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="short_code"> Design Code</label>

                                    <input type='text' class='form-control design_code' placeholder='Design Code' name='design_code' required id='design_code' autocomplete='off' value='' readonly />
                                </div> 
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="size">Size</label>
                                    <input id="size" type="text" step="any" class="form-control size" name="size" autocomplete="off" value="" >
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="purity">Select Purity<span class="error"> *</span></label>
                                    <select id="purity" class='form-control purity' name='purity' required multiple /><input type='hidden' id="id_purity" name='id_purity' class='id_purity' value='' required/>
                                </div>
                            </div>

                        </div>

                        <div class='row suppRows'>					       

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="weight">Weight<span class="error"> *</span></label>
                                    <input id="weight" type="number" step="any" class="form-control weight" name="weight" autocomplete="off" value="" required value=''>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="from_weight">From Weight<span class="error"> *</span></label>
                                    <input id="from_weight" type="number" step="any" class="form-control from_weight" name="from_weight" autocomplete="off" value="" required="" value=''>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="to_weight">To Weight<span class="error"> *</span></label>
                                    <input id="to_weight" type="number" step="any" class="form-control to_weight" name="to_weight" autocomplete="off" value="" required="" value=''>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="display_mc_yes">Show MC<span class="error"> *</span></label>
                                    <div>
                                        <input type="radio" id="display_mc_yes" class="display_mc_yes" name="display_mc" value="1" > <label for="display_mc_yes">Yes</label> 
                                        <input type="radio" id="display_mc_no" class="display_mc_no" name="display_mc" value="0"  checked > <label for="display_mc_no">No</label> 
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="mc_type">MC Type<span class="error"> *</span></label>
                                    <select  id="mc_type" class="form-control mc_type"  name="mc_type" required="">
                                        <option value="1" <?php if($supp['mc_type'] == 1) { ?> selected <?php } ?> >Piece</option>
                                        <option value="2" <?php if($supp['mc_type'] == 2) { ?> selected <?php } ?> >Gram</option>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="mc_value">MC Value<span class="error"> *</span></label>
                                    <input  id="mc_value" type="number" step="any" class="form-control mc_value" name="mc_value" autocomplete="off" value="" required="">
                                </div>
                            </div>


                        </div>

                        <div class='row suppRows'>	
                            
                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="display_va_yes">Show VA<span class="error"> *</span></label>
                                    <div>
                                        <input type="radio" id="display_va_yes" class="display_va_yes" name="display_va" value="1"> <label for="display_va_yes">Yes</label>  
                                        <input type="radio" id="display_va_no" class="display_va_no" name="display_va" value="0" checked> <label for="display_va_no">No</label>  
                                    </div>
                                </div>
                            </div>
                             
                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="wastage">VA (%)<span class="error"> *</span></label>
                                    <input id="wastage" type="number" step="any" class="form-control wastage" name="wastage" autocomplete="off" value="" required="">
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="display_duration_yes">Show Delivery Duration<span class="error"> *</span></label>
                                    <div>
                                        <input type="radio" id="display_duration_yes" class="display_duration_yes" name="display_duration" value="1"  > <label for="display_duration_yes">Yes</label> 
                                        <input type="radio" id="display_duration_no" class="display_duration_no" name="display_duration" value="0" checked> <label for="display_duration_no">No</label> 
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="delivery_duration">Delivery Duration<span class="error"> *</span></label>
                                    <input  id="delivery_duration" type="number" step="any" class="form-control delivery_duration" name="delivery_duration" autocomplete="off" value="" required="">
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label for="karigar">Karigar<span class="error"> *</span></label>
                                    <select id="karigar" class='form-control select karigar' name='karigar' required multiple /><input type='hidden' id="id_karigar" name='id_karigar' class='id_karigar' value='' required/>
                                </div>
                            </div>
                       
                            <div class='col-sm-2'>
                                <div class='form-group'>
                                    <label>Status<span class="error"> *</span></label>
                                    <div>
                                        <input type="radio" value="1" name="status" id="status_active" checked /> <label for="status_active">Active</label>
                                        <input type="radio" value="0" name="status" id="status_inactive" /> <label for="status_inactive">InActive</label>
                                    </div>
                                </div>
                            </div>
                        

                        <div class="row" >
                            <div class="col-sm-12" style="margin: 15px;">
                                <div class="box box-default">
                                    <div class="uploadImg">
                                        Upload Image
                                    </div>

                                    <input id="s_img" name="supp_cat_image[]" accept="image/*" type="file" onchange="validate_image(this)" >

                                    <div class="images_preview">

                                    </div>

                                </div>
                            </div>
                        </div>
			        <!-- End of Dynamic image upload-->
					</div>
				</div>  
			</div>  <!-- /Tab content --> 
        </div><!-- /.box-body -->
        <div class="overlay" style="display:none">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default"><br/>
                    <div class="col-xs-offset-5">
                        <button type="buttom" class="btn btn-primary" id="save_supp">Save</button> 
                        <button type="button" class="btn btn-default btn-cancel" >Cancel</button>
                    </div> <br/>
                </div> 
            </div>
        </div>
		<?php //echo form_close(); ?>
      <!-- </form> -->

    <div class='row'>	
        <div class="box-body">
            <div class="table-responsive">
                <table id="supp_details" class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Catalog ID</th>
                            <th>Product</th>
                            <th>Design</th>
                            <th>Sub Design</th>
                            <th>Design Code</th>
                            <th>Weight</th>
                            <th>From Wgt</th>
                            <th>To Wgt</th>
                            <th>Purity</th>
                            <th>Size</th>
                            <th>Show MC</th>
                            <th>MC Type</th>
                            <th>MC Value</th>
                            <th>Show VA</th>
                            <th>VA(%)</th>
                            <th>Show Delivery Duration</th>
                            <th>Delivery Duration</th>
                            <th>Karigar</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead> 
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div> 
    </div> 

    <div class="modal fade" id="imageModal_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Images</h4>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12 box-items no-paddingwidth" style="max-height: 300px;overflow: auto;">
                                <div class="col-md-12 col-xs-12 recent_bills no-paddingwidth blog-box">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-md-12 col-xs-12 no-paddingwidth container-table">
                                            <table class="table table-bordered" id="design_img_preview">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Is Default</th>
                                                    <th>Image</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </div>
        </div>
    </div>


    <div class="row">
        <p class="help-block"></p>
    </div>

    </div><!-- /.box -->
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script type="text/javascript">
   var  weightRange = '<?php echo json_encode($supp['weightRange']) ?>';
</script>