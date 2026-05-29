
<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Flexible scheme Type</h3>
        </div>
        <div class="box-body">
        	- Amount <br/>
			- Amount to Weight [Amt based]<br/>
			- Amount to Weight [Wgt based]<br/>
			- Amount [Partly Flexible]<br/>
			- Weight [Partly Flexible]<br/>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>

<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Payment Settings </h3> Fix first payment amount
        </div>
        <div class="box-body">
        	- Fix installment amount (Cus can choose amount from given limit & contn. like fixed scheme)<br/> 
        	- Get 1st ins amount in sch join [ YES / NO ]<br/> 
			- Fix 1st payment as  [ Payable / Max Amt ]<br/>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>
<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Payment Settings </h3> If amount
        </div>
        <div class="box-body">
        	//Min amount, Max amount<br/>
        	Denomination<br/>
        	Get amount in sch join [ YES / NO ]<br/>
        	[Below table Just for reference purpose]
        	<table id="scheme_setting_tbl" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
			<thead>
				<tr>
				<th>Installment</th>
				<th>Based on</th>
				<th>Min Value</th>
				<th>Max Value</th>
				<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<tr rowid="1"><td>From<input type="number" name="scheme_flexible[0][ins_from]" class="ins_from" value="1" style="width: 50px;">To<input type="number" name="scheme_flexible[0][ins_to]" class="ins_to" value="1" style="width: 50px;"></td><td><select  class="form-control" data-placeholder="Type" name="payable_limit_based_on"><option value="2" selected="">Plan Min Max Value</option><option value="0">Avg Amount</option><option value="1">1st ins amount</option><option value="3">3 times of 1st ins amount</option></select></td><td><input type="number" name="scheme_flexible[0][min_value]" value="" step="any" class="form-control min_value" style="width: 100px;"></td><td><input type="number" step="any" name="scheme_flexible[0][max_value]" value="" class="form-control max_value" style="width: 100px;"></td><td><div><button id="1" class="delete btn btn-danger" name="delete" type="button"><i class="fa fa-trash"></i> REMOVE</button></div></td></tr></tbody>
			</table>
        	
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>

<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Payment Settings </h3> If weight
        </div>
        <div class="box-body">
        	If weight [based on amount] => Min amount, Max amount<br/> 
        	If weight [based on weight] => Min wgt, Max wgt<br/>
        	Denomination<br/>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>

<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Payment Settings </h3> Payment Chances
        </div>
        <div class="box-body">
        	Payment allowed per Month : One time / Multiple time <br/> 
        	Apply payment attempt limit for : Daily transaction / Monthly transaction <br/> 
        	No. of attempts : Min, Max <br/> 
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>




