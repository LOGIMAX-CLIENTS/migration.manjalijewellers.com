
<div class="row">
    <div class="col-md-4">
        <h4>Plan Type <small>[ Amount / Weight ] <i>Note : Amount - payable will be in amount form. Weight - payable will be in wgt form</i></small></h4>
        <h4>Plan Payable Type <small><span class="badge bg-green">N</span>[ Fixed / Flexible ] </small></h4>
        <h4>Auto Debit [Cashfree subscription] <small>NA / Periodic / OnDemand</small></h4>
        <h4>Repeat Plan <small>Yes / No</small></h4>
        <h4>Repeat Plan Terms</h4>
    </div>
    <div class="col-md-4">
        <h4>Installment Type <small><span class="badge bg-green">N</span> [ Daily / Monthly / One time / Reserve Booking ]</small></h4>
        <h4>Total Installment</h4>
        <h4>Show installment <small>[ Paid installment or Paid/total installments ]</small></h4>
    </div>
    <div class="col-md-4">
        <h4>Allow Advance <small> [ YES / NO ]</small></h4>
        <h4>Allow Pending <small>[ YES / NO ]</small></h4>
        <h4>Mark unpaid as default <small>[ YES / NO ]</small></h4>
    </div>
    
</div>
    <div class="col-md-4">
        <h4> General Advance<small> [ YES / NO ]</small></h4>
    </div>
       
<p></p>

<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Weight conversion</h3>
        </div>
        <div class="box-body"> 
            <h4>Weight conversion <small> Yes / No</small></h4>
            <h4>Convert weight during <small> Payment / scheme closing / Anytime [OTP based]</small></h4>
            <h4>Rate selection <small> Current / Choose from History / Lowest </small></h4>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>    
</div>

<?php $this->load->view("master/crm_scheme/maturity"); ?>           


