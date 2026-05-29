
<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Gift</h3>
        </div>
        <div class="box-body"> 
		Gift - Yes / No<br/>
		Issue type - Single/Multiple<br/>
		Gift deduction on preclose - yes/no
		
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>
<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Voucher</h3>
        </div>
        <div class="box-body"> 
		Voucher - Yes / No<br/>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>
<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Interest</h3>
        </div>
        <div class="box-body"> 
		Interest - Yes / No<br/>
		Calculation Type - Installment Wise / Maturity Wise / Term Wise<br/> 
        Grace days<small>[ no of days given additionally to give interest ]</small><br/>
		<button type="button" id="interest_sett" class="btn btn-success">ADD+</button>
			<table id="flexible" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
    			<thead> 	 
    			    
    				<tr>
    				<th>Interest Calc On</th> 
    				<th>Interest By</th> 
    				<th>From</th>
    				<th>To</th>
    				<th>Type</th>
    				<th>Value</th>
    				<th>Installment No</th>	
    				<th>Action</th>
    				</tr>
    			</thead>
    			<tbody>
    			<tr rowid="1">
    			    
    			    <td>
    			        <select  class="form-control" data-placeholder="Calc By" name="min_formula">
    			            <option value="1" selected="">By Installment</option>
    			            <option value="2" >By Value</option>
    			        </select>
    			    </td>
					
					<td>
    			        <select  class="form-control" data-placeholder="Interest By" name="max_formula">
    			            <option value="1" selected="">Installments</option>
    			            <option value="2" >Days</option>
    			            <option value="2" >Term</option>
    			        </select>
    			    </td>
					
					<td><input type="number" name="scheme_flexible[0][ins_from]" class="ins_from" value="1" style="width: 50px;"></td>
    			    <td><input type="number" name="scheme_flexible[0][ins_to]" class="ins_to" value="1" style="width: 50px;"></td>
    			    
    			   
    			    <td>
    			        <select  class="form-control" data-placeholder="Type" name="interest_type">
    			            <option value="1" selected="">N/A</option>
    			            <option value="2" >%</option>
    			            <option value="3" >Amount</option>
    			        </select>
    			    </td>
    			    <td><input type="number" step="any" name="scheme_flexible[0][interest_value]" value="" class="form-control disc_value" style="width: 100px;"></td>
					
    			    <td><input type="number" step="any" name="scheme_flexible[0][ins_no]" value="" class="form-control denom_value" style="width: 100px;"></td>
    			    <td><div><button id="1" class="delete btn btn-danger" name="delete" type="button"><i class="fa fa-trash"></i></button></div></td></tr></tbody>
			</table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>

<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Free Payment settings</h3>Fixed amt, Weight - [fixed weight], amt to wgt
        </div>
        <div class="box-body">
          	Allow Free Payment - [ Yes / No]<br/>	
			Allow Second Pay [Allow 2nd ins payment after 1st free payment]<br/>	
			Approval required for Free Payment<br/>	
			Select free installment <br/>	
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>








