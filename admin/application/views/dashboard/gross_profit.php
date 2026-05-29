
<style>
 .alignright{
    text-align: right;
 }

 .alignleft{
    text-align:left;
 }

</style>
<!-- Content Wrapper. Contains page content -->
<div class="row contract">
	<div class="col-md-12 col-xs-12 container-row">
       
		<div class="col-md-12 col-xs-12 box-items no-paddingwidth">
			<div class="col-md-12 col-xs-12 sales_wise no-paddingwidth">
				<div class="col-md-12 col-xs-12 item-heading">
              
              
				</div>

                <div class="box-header">

                    <div class="col-md-12">

                        <div class="col-md-4"> 

                            GROSS PROFIT DETAILS

                        </div>

                        <div class="col-md-5"> 
                        
                        </div>

                        <div class="col-md-3 "> 
                            <!-- <label>Select Metal</label> -->
                            <select id="metal_select" class="form-control" style="width:100%;"></select>

                        </div>


                    </div>

                </div>

			    <div class="col-md-12 col-xs-12">

                   <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items" style="width: auto;">
                    
                   </div>
                    
                  
                 
                    <div class="col-md-12 col-xs-12">
                        
                    <div class="table-responsive">
									  <table id="other_issue_list" class="table table-bordered table-striped text-center">
										<thead>
										  <tr style="text-transform:uppercase;"> 
										    <th style="font-weight: bold; text-align:left;" width="10%">Description</th> 
										    <th style="font-weight: bold; " width="10%">Sale Weight</th>
										    <th style="font-weight: bold; " width="10%">Purity</th>
										    <th style="font-weight: bold; " width="10%">Rate</th>
										    <th style="font-weight: bold; " width="10%">Amount</th> 
										  </tr>
					                    </thead> 
					                    <tbody>
                                            <tr>
                                                <td class ="alignleft"  style="font-weight: bold; text-align:left;"> SALES </td>
                                                <td><input class ="form-control alignright " type="text"  id="sale_wt" readonly > <input type="hidden" id="sale_wt_h" ></td>
                                                <td></td>
                                                <td><input class ="form-control alignright " type="text" id="sale_rate" readonly><input type="hidden" id="sale_rate_h"  ></td>
                                                <td><input class ="form-control alignright" type="text" id="sale_amount" readonly><input type="hidden" id="sale_amount_h" ></td>
                                            </tr>

                                            <tr>
                                                <td class ="alignleft" style="font-weight: bold; text-align:left;" > PURCHASE </td>
                                                <td><input class ="form-control alignright" type="text" id="purchase_wt" readonly> <input type="hidden" id="purchase_wt_h"  > </td>
                                                <td><input class ="form-control alignright" type="number" id="purchase_purity" ></td>
                                                <td><input  class ="form-control alignright" type="number" id="purchase_rate" ></td>
                                                <td><input class ="form-control alignright" type="text" id="purchase_amount_gross" readonly> <input type="hidden" id="purchase_amount_h"></td>
                                            </tr>

                                            <tr>
                                                <td class ="alignleft" style="font-weight: bold; text-align:left;" > DISCOUNT </td>
                                                <td><input class ="form-control alignright" type="text" id="discount_wt" readonly><input type="hidden" id="discount_wt_h" ></td>
                                                <td></td>
                                                <td><input class ="form-control alignright" type="text" id="discount_rate"readonly > <input type="hidden" id="discount_rate_h"></td>
                                                <td><input class ="form-control alignright" type="text" id="discount_amount" readonly><input type="hidden" id="discount_amount_h"></td>
                                            </tr>


                                            <tr>
                                                <td class ="alignleft"  style="font-weight: bold; text-align:left;"> NET  </td>
                                                <td></td>
                                                <td></td>
                                                <td><input  class ="form-control alignright" type="text" id="net_rate" readonly><input type="hidden" id="net_rate_h" ></td>
                                                <td><input class ="form-control alignright" type="text" id="net_amount" readonly><input type="hidden" id="net_amount_h" ></td>
                                            </tr>

                                          

                                            <tr class ="alignleft" style ="display:none;">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><input class ="form-control alignright" type="text" id="purchase_net" readonly><input type="hidden" id="purchase_net_h" ></td>
                                                <td></td>
                                            </tr>

											  <tr>
                                                <td class ="alignleft" style="font-weight: bold; text-align:left;"> GROSS PROFIT </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><input class ="form-control alignright" type="text" id="gp_ratio" style="font-weight: bold;" readonly></td>
                                            </tr> 

                                          
                                            <tr>
                                                <td class ="alignleft" style="font-weight: bold; text-align:left;" > GROSS DISCOUNT </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><input class ="form-control alignright" type="text" id="gp_ratio_per" style="font-weight: bold;" readonly></td>
                                            </tr>
                                            <tr>
                                                <td class ="alignleft" style="font-weight: bold; text-align:left; font-size:17px;" > GROSS PROFIT PERCENTAGE </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><input class ="form-control alignright" type="text" id="total_gross_profit" style="font-weight: bold;font-size: 28px;" readonly></td>
                                            </tr>

                                        </tbody>
									 </table>
								  </div>
                     
                    </div>
              
					
				</div>
			</div>
		</div>
        
		
	</div>
</div>
	