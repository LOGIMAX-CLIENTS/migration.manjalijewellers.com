      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Metal Rates
            <small>Update your daily metal rates</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('index.php/settings/drawee/list');?>">Metal Rates</a></li>
            
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo ( $rates['id_metalrates']!=NULL?'Edit' :'Add'); ?> Market Metal Rate</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open((  $rates['id_metalrates']!=NULL &&  $rates['id_metalrates']>0 ?'settings/rate/update/'.$rates['id_metalrates']:'settings/rate/save')) ?>

        <div class="row">
				     <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Gold 9KT</label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" id="goldrate_9ct" name="rates[goldrate_9ct]" value="<?php echo set_value('rates[goldrate_9ct]',$rates['goldrate_9ct']); ?>" placeholder="eg: 2822.00"  > 
                                <span class="input-group-addon">Per G</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
				 </div>
				<div class="row">
				     <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Gold 14KT</label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" id="goldrate_14ct" name="rates[goldrate_14ct]" value="<?php echo set_value('rates[goldrate_14ct]',$rates['goldrate_14ct']); ?>" placeholder="eg: 2822.00"  > 
                                <span class="input-group-addon">Per G</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
				 </div>

				<div class="row">
				     <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Gold 18KT</label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" id="market_gold_18ct" name="rates[market_gold_18ct]" value="<?php echo set_value('rates[market_gold_18ct]',$rates['market_gold_18ct']); ?>" placeholder="eg: 2822.00"  > 
                                <span class="input-group-addon">Per G</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
				 </div>
				 
				<div class="row">
                    <div class="col-md-2" align="right">
                      <label for="chargeseme_name" class="input_text">Gold 20KT</label>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <?php echo $this->session->userdata('currency_symbol') ?>
                          </span>
                          <input type="text" class="form-control input_currency" id="market_gold_20ct"
                            name="rates[market_gold_20ct]"
                            value="<?php echo set_value('rates[market_gold_20ct]', $rates['market_gold_20ct']); ?>"
                            placeholder="eg: 2822.00">
                          <span class="input-group-addon">Per G</span>
                          <p class="help-block"></p>
                        </div>
                      </div>
                    </div>
                  </div>
				 
				 <div class="row">
				     <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Gold 22KT <span class='error'>*</span></label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" id="mjdmagoldrate_22ct" name="rates[mjdmagoldrate_22ct]" value="<?php echo set_value('rates[mjdmagoldrate_22ct]',$rates['mjdmagoldrate_22ct']); ?>" placeholder="eg: 2822.00" required="true"> 
                                <span class="input-group-addon">Per G</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
                </div>
				<div class="row">
				    <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Gold 24KT</label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" name="rates[goldrate_24ct]" id="goldrate_24ct" value="<?php echo set_value('rates[goldrate_24ct]',$rates['goldrate_24ct']); ?>" placeholder="eg: 30180.00" />
                                <span class="input-group-addon">Per G</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
				 </div>				
				 <div class="row">
                    <div class="col-md-2" align="right">
                      <label for="chargeseme_name" class="input_text">Gold 99.5</label>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <?php echo $this->session->userdata('currency_symbol') ?>
                          </span>
                          <input type="text" class="form-control input_currency" id="market_gold_995"
                            name="rates[market_gold_995]"
                            value="<?php echo set_value('rates[market_gold_995]', $rates['market_gold_995']); ?>"
                            placeholder="eg: 2822.00">
                          <span class="input-group-addon">Per G</span>
                          <p class="help-block"></p>
                        </div>
                      </div>
                    </div>
                </div>

        <div class="row">
            <div class="col-md-2" align="right">
              <label for="chargeseme_name" class="input_text">Gold Coin</label>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                    <?php echo $this->session->userdata('currency_symbol') ?>
                  </span>
                  <input type="text" class="form-control input_currency" id="coin_gold_22ct"
                    name="rates[coin_gold_22ct]"
                    value="<?php echo set_value('rates[coin_gold_22ct]', $rates['coin_gold_22ct']); ?>"
                    placeholder="0.00">
                  <span class="input-group-addon">Per G</span>
                  <p class="help-block"></p>
                </div>
              </div>
            </div>
        </div>

				<div class="row">
				    <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Platinum</label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" name="rates[platinum_1g]" id="platinum_1g" value="<?php echo set_value('rates[platinum_1g]',$rates['platinum_1g']); ?>" placeholder="2491.00"/>
                                <span class="input-group-addon">Per G</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
				 </div>	


          
		  
		  
		  
		  <div class="row">
            <div class="col-md-2" align="right">
              <label for="chargeseme_name" class="input_text">Silver 99.9</label>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                    <?php echo $this->session->userdata('currency_symbol') ?>
                  </span>
                  <input type="text" class="form-control input_currency" name="rates[mjdmasilverrate_999]" id="mjdmasilverrate_999"
                    value="<?php echo set_value('rates[mjdmasilverrate_999]', $rates['mjdmasilverrate_999']); ?>"
                    placeholder="2491.00" />
                  <span class="input-group-addon">Per G</span>
                  <p class="help-block"></p>
                </div>
              </div>
            </div>
          </div>
				 <div class="row">
				     <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Silver</label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" name="rates[mjdmasilverrate_1gm]" id="mjdmasilverrate_1gm" value="<?php echo set_value('rates[mjdmasilverrate_1gm]',$rates['mjdmasilverrate_1gm']); ?>" placeholder="eg: 40.00"/>
                                <span class="input-group-addon">Per G</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
                    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                                <input type="text" class="form-control input_currency" name="rates[silverrate_1kg]" id="silverrate_1kg" value="<?php echo set_value('rates[silverrate_1kg]',$rates['silverrate_1kg']); ?>" placeholder="eg: 40465.00"/>
                                <span class="input-group-addon">Per KG</span>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
				 </div>
				 
				 <input id="is_branchwise_rate" name="rates[is_branchwise_rate]"  type="hidden" value="<?php echo set_value('rates[is_branchwise_rate]',$rates['is_branchwise_rate']); ?>"/>
				 <?php if($this->session->userdata('branch_settings')==1 && $rates['is_branchwise_rate'] == 1){?>
				 <div class="row">
				     <div class="col-md-2" align="right">
				         <label for="chargeseme_name" class="input_text">Select Branch</label>
				    </div>
				    <div class="col-md-3">
    				 	<div class="form-group">
                            <div class="input-group">
                  				<select id="branch_select"  multiple style="width:310px;"  ></select>
    							<div id="sel_bran" data-sel_bran='<?php echo  $id_branch;?>'></div> 
    							<input id="id_branch" name="branch[id_branch]"  type="hidden" value=""/>
                                <p class="help-block"></p>
                           </div>
                        </div>
                    </div>
				 </div>	
			<?php }?>
				 <p class="help-block"> NOTE : 
    				 <ul>
    				     <li>Enter market metal rate.</li>
    				     <li>Rate Notification will be sent to user if activated.</li>
    				 </ul>
				 </p> 	 
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
				   	  
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>      
				        	
               </form>              	              	
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->