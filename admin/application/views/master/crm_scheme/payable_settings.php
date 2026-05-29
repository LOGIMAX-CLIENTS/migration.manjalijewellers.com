<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Get Payable </h3> 
        </div>
        <div class="box-body">
        	- Get amount in sch join [ YES / NO ]<br/>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>
<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Payable Settings </h3>
        </div>
        <div class="box-body">
            <h4>Limit by <span class="badge bg-green">N</span> <small>Amount / Weight</small></h4>
            
			<h4 class="text-red">Template :</h4>
			<button type="button" id="add_flexible_pay_sett" class="btn btn-success">ADD+</button>
			<table id="flexible" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
    			<thead> 	 
    			    <tr class="bg-green">
        				<th colspan='2'>Installment</th>  
        				<th colspan='2'>Minimum</th>  
        				<th colspan='2'>Maximum</th>  
        				<th colspan='2'>Denomination</th>  
        				<th colspan='2'>Discount</th>  
        				<th></th>
    				</tr>
    				<tr>
    				<th>From</th> 
    				<th>To</th> 
    				<th>Formula</th>
    				<th>Parameter</th>
    				<th>Formula</th>
    				<th>Parameter</th>
    				<th>Type</th>
    				<th>Value</th>
    				<th>Type</th>
    				<th>Value</th>
    				<th>Action</th>
    				</tr>
    			</thead>
    			<tbody>
    			<tr rowid="1">
    			    <td><input type="number" name="scheme_flexible[0][ins_from]" class="ins_from" value="1" style="width: 50px;"></td>
    			    <td><input type="number" name="scheme_flexible[0][ins_to]" class="ins_to" value="1" style="width: 50px;"></td>
    			    <td>
    			        <select  class="form-control" data-placeholder="Type" name="min_formula">
    			            <option value="1" selected="">Any</option>
    			            <option value="2" >X1 times of X2 ins</option>
    			            <option value="3" >Avg of X1 to X2</option>
    			        </select>
    			    </td>
    			    <td><input type="number" name="scheme_flexible[0][min_value]" value="" step="any" class="form-control min_value" style="width: 100px;"></td>
    			    <td>
    			        <select  class="form-control" data-placeholder="Type" name="max_formula">
    			            <option value="1" selected="">Any</option>
    			            <option value="2" >X1 times of X2 ins</option>
    			            <option value="3" >Avg of X1 to X2</option>
    			        </select>
    			    </td>
    			    <td><input type="number" step="any" name="scheme_flexible[0][max_value]" value="" class="form-control max_value" style="width: 100px;"></td>
    			    <td>
    			        <select  class="form-control" data-placeholder="Type" name="denomination">
    			            <option value="1" selected="">N/A</option>
    			            <option value="2" >Multiples</option>
    			            <option value="3" >Master</option>
    			        </select>
    			    </td>
    			    <td><input type="number" step="any" name="scheme_flexible[0][denom_value]" value="" class="form-control denom_value" style="width: 100px;"></td>
    			    <td>
    			        <select  class="form-control" data-placeholder="Type" name="discount">
    			            <option value="1" selected="">N/A</option>
    			            <option value="2" >%</option>
    			            <option value="3" >Amount</option>
    			        </select>
    			    </td>
    			    <td><input type="number" step="any" name="scheme_flexible[0][disc_value]" value="" class="form-control disc_value" style="width: 100px;"></td>
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
          <h3 class="box-title">Tax</h3>
        </div>
        <div class="box-body">
          <div class="row">
                <div class="col-md-2">Tax Group <span class="badge bg-green">N</span></div>
                <div class="col-md-4">
                    <select  class="form-control" data-placeholder="Type" name="min_formula">
    		            <option value="1" selected="">3% incl of GST </option>
    		            <option value="2" >3% excl of GST</option>
    		        </select>
		        </div>
            </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>    
</div>

<div class="row">
	<div class="col-md-12">
      <div class="box box-default box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Payment Charges</h3>
        </div>
        <div class="box-body">
          Label for Charge<br/>
          Charge Type - Percent/Amount<br/>
          Value <br/>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>    
</div>

