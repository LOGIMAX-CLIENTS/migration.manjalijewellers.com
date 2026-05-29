<div class="content-wrapper">
    <section class="content">
        <div class="overlay" style="display:none"><i class="fa fa-spinner fa-spin" style="font-size:60px;color:black;margin:30% 50%;"></i></div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body">
                        <!-- FILTER ROW 1 -->
                        <div class="row" style="margin-bottom:5px;">
                            <div class="col-md-2">
                                <select class="form-control" id="branch_select" multiple="multiple" style="width:100%;">
                                    <?php if(isset($branch_list)) foreach($branch_list as $b) { ?>
                                        <option value="<?=$b['id_branch']?>"><?=$b['name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div id="reportrange_gst_v3" class="form-control" style="cursor:pointer;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span id="rpt_from_date"></span> - <span id="rpt_to_date"></span>
                                    <b class="caret"></b>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="category" style="width:100%;">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="metal" style="width:100%;">
                                    <option value="">All Metals</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="report_type_v3">
                                    <option value="0">All (B2B + B2C)</option>
                                    <option value="1">B2B (Registered)</option>
                                    <option value="2">B2C (Unregistered)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-block" id="gst_abstract_search_v3">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <!-- FILTER ROW 2 -->
                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-2">
                                <select class="form-control" id="state_type_v3">
                                    <option value="0">All State Types</option>
                                    <option value="1">Intra-State (SGST+CGST)</option>
                                    <option value="2">Inter-State (IGST)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="view_type_v3">
                                    <option value="detail">Detail (Item-wise)</option>
                                    <option value="summary">Summary (Bill-wise)</option>
                                </select>
                            </div>
                            <!-- <div class="col-md-2">
                                <button class="btn btn-default" id="col_visibility_toggle_v3">Column visibility</button>
                            </div> -->
                        </div>

                        <!-- TABLE -->
                        <div class="table-responsive">
                            <table id="gst_abstract_v3_list" class="table table-bordered table-striped table-condensed" style="width:100%;font-size:11px;">
                                <thead>
                                    <tr>
                                        <th class="text-left">Category</th>
                                        <th class="text-center">HSN Code</th>
                                        <th class="text-left">Metal</th>
                                        <th class="text-center">Bill No</th>
                                        <th class="text-center">Bill Date</th>
                                        <th class="text-left">Customer</th>
                                        <th class="text-left">State</th>
                                        <th class="text-right">Pcs</th>
                                        <th class="text-right">Gwt(g)</th>
                                        <th class="text-right">NWT(g)</th>
                                        <th class="text-right">Metal Value</th>
                                        <th class="text-right">Tag VA</th>
                                        <th class="text-right">Sale VA</th>
                                        <th class="text-right">Tag MC</th>
                                        <th class="text-right">Sale MC</th>
                                        <th class="text-right">Stone Wt</th>
                                        <th class="text-right">Stone Amt</th>
                                        <th class="text-right">DIA WT(CT)</th>
                                        <th class="text-right">DIA Amt</th>
                                        <th class="text-right">Taxable Amt</th>
                                        <th class="text-right">Store Disc</th>
                                        <th class="text-right">Scheme Disc</th>
                                        <th class="text-right">Tax%</th>
                                        <th class="text-right">SGST</th>
                                        <th class="text-right">CGST</th>
                                        <th class="text-right">IGST</th>
                                        <th class="text-right">GST</th>
                                        <th class="text-right">Round Off</th>
                                        <th class="text-right">Total Amt</th>
                                        <th class="text-center">GST No</th>
                                        <th class="text-center">PAN</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr style="font-weight:bold;background:#f0f0f0;">
                                        <th class="text-right">Totals</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right">0</th>
                                        <th class="text-right">0.000</th>
                                        <th class="text-right">0.000</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.000</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.0000</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right"></th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right">0.00</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>