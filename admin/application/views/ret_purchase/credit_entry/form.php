<style>
    .remove-btn {

  margin-top: -168px;

  margin-left: -38px;

  background-color: #e51712 !important;

  border: none;

  color: white !important;

}



.sm {

  font-weight: normal;

}

/* From Uiverse.io by andrew-demchenk0 */
.switch {
  --input-focus: #2d8cf0;
  --bg-color: #fff;
  --bg-color-alt: #666;
  --main-color: #323232;
  --input-out-of-focus: #ccc;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 30px;
  width: 50px;
  height: 25px;
  transform: translateX(calc(50% - 10px));
}

.toggle {
  opacity: 0;
}

.slider {
  box-sizing: border-box;
  border-radius: 100px;
  border: 2px solid var(--main-color);
  /* box-shadow: 4px 4px var(--main-color); */
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: var(--input-out-of-focus);
  transition: 0.3s;
}

.slider:before {
  content: "";
  box-sizing: border-box;
  height: 20px;
  width: 20px;
  position: absolute;
  left: 2px;
  bottom: 1px;
  border: 2px solid var(--main-color);
  border-radius: 100px;
  background-color: var(--bg-color);
  color: var(--main-color);
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  line-height: 15px;
  transition: 0.3s;
}

.toggle:checked+.slider {
  background-color: #605ca8;
  transform: translateX(0px);
}

.toggle:checked+.slider:before {
  content: "";
  transform: translateX(25px);
}

.content-header{
    padding: 0 15px 0 15px !important;
}
</style>
<!-- Content Wrapper. Contains page content -->
"<?php $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
    . "://" . $_SERVER['HTTP_HOST'];
    echo $current_url; ?>
      <div class="content-wrapper">

          <!-- Content Header (Page header) -->

          <section class="content-header">

              <h1>

                  Credit/Debit Entry

              </h1>

              <ol class="breadcrumb">

                  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

                  <li><a href="#">purchase</a></li>

                  <li class="active">Credit/Debit Entry</li>

              </ol>

          </section>



          <!-- Main content -->

          <section class="content order">



              <!-- Default box -->

              <div class="box box-primary" id="creditDebitModule">

                  <div class="box-body">

                      <?php

                	if($this->session->flashdata('chit_alert'))

                	 {

                		$message = $this->session->flashdata('chit_alert');

                ?>

                      <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                          <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

                          <?php echo $message['message']; ?>

                      </div>



                      <?php } ?>

    

                      <input id="crdrid" name="credit[crdrid]" type="hidden" value="<?php echo set_value('credit[crdrid]', $credit['crdrid']); ?>" />

                        <div class="row">
                            <label for="" class="col-md-2 col-md-offset-3">Transaction In<span class="error"> *</span></label>
                            <div class="row" style="display: flex;">
                                <div>
                                    <label class="switch">
                                        <input checked="" type="checkbox" class="toggle toggle-transaction" id="toggle_transaction">
                                        <span class="slider"></span>
                                        <span class="card-side"></span>
                                    </label>
                                </div>
                                <div style="margin-left: 5rem;">
                                    <label for="" id="transaction-in">Amount</label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="credit[transaction_in]" id="transaction_in" value="1">
                        <br>
                      <div class="row">

                          <label for="" class="col-md-2 col-md-offset-3">Supplier<span class="error"> *</span></label>

						  <div class="col-md-2">

						  <div class="input-group">

                          <select id="select_karigar" name="credit[karigar]" class="form-control select_karigar" required></select>

                        </div>

						</div>

                      </div>


                      <br>

                      <div class="row">

                          <label for="" class="col-md-2 col-md-offset-3"> Type</label>

						  <div class="col-md-3">

                         <div class="input-group account_type">

                         <input type="radio"  name="credit[accountto]" id="accountto1" value="1" checked> Supplier &nbsp;&nbsp;



                          <input type="radio" name="credit[accountto]" id="accountto2" value="2" > Smith  &nbsp;



                           <input type="radio" name="credit[accountto]" id="accountto3" value="3"> Approvals  &nbsp;&nbsp;                              </div>

						</div>

                      </div>

                       <br>

                       <div class="row" id="supplier_bills_row" style="display:block; margin-bottom:20px;">

                          <label for="" class="col-md-2 col-md-offset-3">Supplier Bills</label>

						    <div class="col-md-2">

                                <div class="input-group">

                                    <select id="select_karigar_bills" name="credit[karigar_bills]" class="form-control select_karigar_bills"></select>

                                </div>

						    </div>

                        </div>
                      <div class="row" id="ledger_row_wrapper">

                          <label for="id_cr_dr_ledger" class="col-md-2 col-md-offset-3">Ledger Type</label>

						  <div class="col-md-3">

                             <div class="input-group">

                                 <select id="id_cr_dr_ledger" name="credit[id_cr_dr_ledger]" class="form-control">

                                    <option value="">Select Ledger</option>

                                    <?php 

                                      if(isset($cr_dr_ledger)){

                                        foreach($cr_dr_ledger as $ledger){
                                            $selected = '';
                                            if(isset($credit['id_cr_dr_ledger']) && $credit['id_cr_dr_ledger'] == $ledger['id_crdr_ledger']){
                                                $selected = 'selected';
                                            }

                                            echo '<option value="'.$ledger['id_crdr_ledger'].'" '.$selected.'>'.$ledger['ledger_name'].'</option>';

                                        }

                                      }

                                    ?>

                                 </select>

                              </div>

						  </div>

                      </div>

					  <br>

                    <div class="row" id="amount_row">

                          <label for="tire_minimum_required" class="col-md-2 col-md-offset-3">Amount<span

                                  class="error">*</span> </label>



                          <div class="col-md-3">

                              <div class="input-group">

                                     <div class="input-group" >

                                     <input class="form-control" id="trans_amount" name="credit[transamount]" type="number" style="text-align:right;" placeholder="Enter Amount" value=""/>

						 			    <span class="input-group-btn">

						 			        <select class="form-control" name ="credit[transtype]" id="transtype" style="width:100px;">

                                             <option value="1">Credit</option>

                                             <option value="2">Debit</option>

						 			        </select>

						 			    </span>

									</div>

                              </div>

                          </div>

                      </div>


                        <div class="row" id="weight_row" style="display:none;">

                          <label for="tire_minimum_required" class="col-md-2 col-md-offset-3">Weight<span

                                  class="error">*</span> </label>



                          <div class="col-md-3">

                              <div class="input-group">

                                     <div class="input-group" >

                                     <input class="form-control" id="trans_weight" name="credit[transweight]" type="number" style="text-align:right;" placeholder="Enter Weight" value=""/>

						 			    <span class="input-group-btn">

						 			        <select class="form-control" name ="credit[wtranstype]" id="wtranstype" style="width:100px;">

                                             <option value="1">Credit</option>

                                             <option value="2">Debit</option>

						 			        </select>

						 			    </span>

									</div>

                              </div>

                          </div>

                      </div>

                      <br>

                      <div class="row">

                          <label for="" class="col-md-2 col-md-offset-3">Narration</label>

						  <div class="col-md-2">

                         <div class="input-group">

                             <textarea name="credit[naration]" id="naration" class="form-control" rows="2" cols="400" required> </textarea>

                              </div>

							  </div>

                      </div>

					  <br>

					




                  <div class="row">

                          <div class="col-xs-offset-5">

                              <button class="btn btn-primary" id="save_credit_entry">Save</button>

                              <button type="button" class="btn btn-default btn-cancel" id="cancel_bill_edit">Cancel</button>

                          </div> <br />



                  </div>

              </div> <!-- box-body-->

              <div class="overlay" style="display:none">

                  <i class="fa fa-refresh fa-spin"></i>

              </div>

      </div> <!-- Default box-->

      <?php echo form_close();?>



      <!-- /form -->

      </section>

      </div>

      </div>
