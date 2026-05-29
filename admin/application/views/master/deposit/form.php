<style type="text/css">
  .curr_bal_row {
    margin-bottom: 20px;
  }

  .curr_bal {
    font-size: 20px;
  }

  .curr_bal_chit {
    display: none;
  }
  
  .curr_balance {
    font-weight: bold;
    font-size: 14px;
  }
</style>
<?php 
function moneyFormatIndia($num) {
	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
}
?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Bank Deposit Master
      <small>Add Bank Deposits</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('index.php/settings/drawee/list');?>">Bank Deposits</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Default box -->
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title"><?php echo ( $deposit['dep_id']!=NULL?'Edit' :'Add'); ?> Deposit</h3>
        <div class="box-tools pull-right">
          <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
          <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body">
          <?php 
              if($this->session->flashdata('chit_alert')) {
                
                $message = $this->session->flashdata('chit_alert');  ?>

                <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                  <?php echo $message['message']; ?>
                </div>

          <?php } ?> 
        <div class="">
          <?php 
          $attributes = array('id' => 'depositForm');
          echo form_open((  $deposit['dep_id']!=NULL &&  $deposit['dep_id']>0 ?'deposit/update/'.$deposit['dep_id']:'deposit/save'), $attributes)
          ?>

<div class="row">
            <div class="col-md-2" align="right">
              <label class="input_text"> Branch <span class="error"> *</span></label>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                  <div>
                    <select type="text" class="form-control" id="dep_branch" name="dep_branch" >

                    </select>
                    <input type="hidden" id="id_branch" value="<?php echo $deposit['dep_branch'] ?>" />
                    <p class="help-block">Choose branch</p> 
                  </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-2" align="right">
              <label class="input_text"> Type <span class="error"> *</span></label>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                  <div>
                      <select type="text" class="form-control" id="cash_type" name="type" >
                        <option value="0" <?php if($deposit['type'] == 0 ) { ?> Selected <?php } ?>>All</option>
                        <option value="1" <?php if($deposit['type'] == 1 ) { ?> Selected <?php } ?>>Retail</option>
                        <option value="2" <?php if($deposit['type'] == 2 ) { ?> Selected <?php } ?>>CRM</option>
                      </select>
                      <p class="help-block">Choose All, Retail or CRM</p> 
                  </div>
              </div>
            </div>

            <div class="col-md-2" align="right">
              <label for="dep_amount" class="input_text"> Cash In Hand </label>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                  <div>
                      <div class="curr_balance">
                         0.00
                      </div>
                  </div>
              </div>
            </div>
          </div>


          <div class="row">
              <div class="col-md-2" align="right">
                <label for="dep_amount" class="input_text"> Amount(<?php echo $this->session->userdata('currency_symbol')?>) <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                          <input type="number" class="form-control" step="any" id="dep_amount" name="dep_amount" value="<?php echo set_value('dep_amount',$deposit['dep_amount']); ?>" required min="1" >
                          <p class="help-block">Deposited amount in bank</p>
                    </div>
                </div>
              </div>

              <div class="col-md-2" align="right">
                <label for="dep_cur_balance" class="input_text"> Deposit Type <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <select type="text" class="form-control" id="dep_type" name="dep_type" disabled value="<?php echo set_value('dep_type',$deposit['dep_type']); ?>" >
                        <option value="1" <?php if($deposit['dep_type'] == 1 ) { ?> Selected <?php } ?>>Credit</option>
                        <option value="0" <?php if($deposit['dep_type'] == 0 ) { ?> Selected <?php } ?>>Debit</option>
                      </select>
                      <p class="help-block">Choose credit or debit</p>                      
                    </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-2" align="right">
                  <label for="dep_bank" class="input_text"> Bank Name <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <select type="text" class="form-control" id="dep_bank" name="dep_bank" required>
                          <option value="">--Select--</option>
                          <?php foreach($bank_name as $bnk) { ?>
                              <option value="<?php echo $bnk['id_bank'] ?>" <?php if($bnk['id_bank'] == $deposit['dep_bank']) { ?> Selected <?php } ?>><?php echo $bnk['bank_name'] ?></option>
                          <?php } ?>
                        </select>
                        <p class="help-block">Bank name</p>
                    </div>
                </div>
              </div>

              <div class="col-md-2" align="right">
                  <label for="dep_ref_id" class="input_text">Referece Id <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <input type="text" class="form-control" id="dep_ref_id" name="dep_ref_id" value="<?php echo set_value('dep_ref_id',$deposit['dep_ref_id']); ?>" required>
                        <p class="help-block">Reference id from bank</p>                     
                    </div>
                </div>
              </div>
            </div>
            
            <input type="hidden" id="current_dep_id" value="<?php echo ($deposit['dep_id'] != NULL && $deposit['dep_id'] > 0) ? $deposit['dep_id'] : 0; ?>" />  
            <div class="row">
              <div class="col-md-2" align="right">
                  <label for="dep_bank" class="input_text"> Deposit Date <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <input type="text" readonly class="form-control" id="dep_date" name="dep_date" value="<?php echo set_value('dep_date',$deposit['deposited_date']); ?>" data-date-format="dd-mm-yyyy hh:mm:ss" required>
                        <p class="help-block">Deposit Date</p>
                    </div>
                </div>
              </div>

              <div class="col-md-2" align="right">
                  <label for="dep_bank" class="input_text">  Cash in Hand Date <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <input type="text" readonly class="form-control" id="cash_date" name="cash_date" value="<?php echo set_value('cash_date',$deposit['cash_in_hand_date']); ?>" data-date-format="dd-mm-yyyy hh:mm:ss" required>
                        <p class="help-block">Cash in Hand Date</p>
                    </div>
                </div>
              </div>

              
            </div>

            <div class="row">

              <div class="col-md-2" align="right">
                  <label for="dep_mode" class="input_text">Deposit Mode <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <select type="text" class="form-control" id="dep_mode" name="dep_mode" disabled value="<?php echo set_value('dep_mode',$deposit['dep_mode']); ?>" >
                            <option value="">--Select--</option>
                            <?php foreach($payModes as $pm) { ?>
                                <option value="<?php echo $pm['id_mode'] ?>"  <?php if($pm['id_mode'] == $deposit['dep_mode']) { ?> Selected <?php } ?>><?php echo $pm['mode_name'] ?></option>
                            <?php } ?>
                        </select>
                        <p class="help-block">Payment Modes</p>               
                    </div>
                </div>
              </div>
            
              <div class="col-md-2" align="right">
                  <label for="dep_bank" class="input_text"> Deposited By <span class="error"> *</span></label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <select type="text" class="form-control" id="dep_by" name="dep_by" value="<?php echo set_value('dep_by',$deposit['dep_by']); ?>" required>
                          <option value="">--Select--</option>
                          <?php foreach($employee as $emp) { ?>
                              <option value="<?php echo $emp['id_employee'] ?>"  <?php if($emp['id_employee'] == $deposit['dep_by']) { ?> Selected <?php } ?> ><?php echo $emp['emp_name'] ?></option>
                          <?php } ?>
                        </select>
                        <p class="help-block">Deposited by</p>
                    </div>
                </div>
              </div>
            </div>


            <div class="row">

              <div class="col-md-2" align="right">
                <label for="dep_mode" class="input_text">Narration</label>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                    <div>
                        <textarea rows="5" class="form-control" id="dep_narration" name="dep_narration"><?php echo $deposit['dep_narration'] ?></textarea>
                        <p class="help-block">Narration</p>                    
                    </div>
                </div>
              </div>
            </div>

            <div class="row col-xs-12">
              <div class="box box-default"><br/>
                
                <div class="col-xs-offset-5">
                  <button type="submit" class="btn btn-primary btn_submit">Save</button> 
                  <button type="button" class="btn btn-default btn-cancel">Cancel</button>
                </div> <br/>
                
              </div>
            </div>      
                    
          </form>              	              	
        </div>
      </div><!-- /.box-body -->
      <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
      <div class="box-footer">
        
      </div><!-- /.box-footer-->
    </div><!-- /.box -->

  </section><!-- /.content -->
</div><!-- /.content-wrapper -->