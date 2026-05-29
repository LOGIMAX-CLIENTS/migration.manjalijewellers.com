<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                <div class="box box-primary">
                    <div class="box-body">

                        <div class="box box-info stock_details">
                            <div class="box-header with-border">

                                <div class="col-md-2">
                                    <h3 class="box-title">Supplier Outstanding Ledger</h3>
                                </div>

                                <div class="col-md-2">
                                    <input type="checkbox" id="metal_wise_required" class="metal_wise_required" name="metal_wise_required" value="1">
                                    <label for="metal_wise_required">Metal column required</label>
                                </div>

                                <div class="col-md-2">
                                    <select id="metal" class="form-control" style="width:100%;" multiple></select>
                                </div>

                                <div class="col-md-2">
                                    <select id="karigar" class="form-control" style="width:100%;"></select>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <?php
                                            $fromdt = date("d/m/Y");
                                            $todt = date("d/m/Y");
                                        ?>
                                        <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date - To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="button" id="supplier_unified_ledger_search" class="btn btn-info">Search</button>
                                    </div>
                                </div>

                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="row">
                                    <div class="box-body">
                                        <div class="table-responsive">

                                            <!-- Ledger Table -->
                                            <div id="smith_ledgere_list_sip">
                                                <table id="smith_ledgere_list" class="table table-bordered table-striped text-center">
                                                    <thead>
                                                        <tr class="tablerow">
                                                            <th colspan="7"></th>
                                                            <th colspan="4">Receipt</th>
                                                            <th colspan="4">Issue</th>
                                                            <th colspan="2">Amount</th>
                                                            <th colspan="5">Balance</th>
                                                            <th colspan="1"></th>
                                                        </tr>
                                                        <tr>
                                                            <th>Particulars</th>
                                                            <th>PO No</th>
                                                            <th>PO Date</th>
                                                            <th>Tran No</th>
                                                            <th>Tran Date</th>
                                                            <th>Touch</th>
                                                            <th>Rate</th>
                                                            <th>Receipt Pcs</th>
                                                            <th>Receipt Gross Wt</th>
                                                            <th>Receipt Net Wt</th>
                                                            <th>Receipt Pure Wt</th>
                                                            <th>Issue Pcs</th>
                                                            <th>Issue Gross Wt</th>
                                                            <th>Issue Net Wt</th>
                                                            <th>Issue Pure Wt</th>
                                                            <th>Amount Debit</th>
                                                            <th>Amount Credit</th>
                                                            <th>Blc Pcs</th>
                                                            <th>Blc Gross Wt</th>
                                                            <th>Blc Net Wt</th>
                                                            <th>Blc Pure Wt</th>
                                                            <th>Blc Amt</th>
                                                            <th>Narration</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>

                                            <!-- Metal Summary Table -->
                                            <div id="smith_ledgere_metal_list_sip" style="display:none;">
                                                <table id="smith_ledgere_metal_list" class="table table-bordered table-striped text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Supplier</th>
                                                            <th>Gold Pure Wt</th>
                                                            <th>Silver Pure Wt</th>
                                                            <th>Platinum Pure Wt</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>

                                        </div> <!-- /.table-responsive -->
                                    </div> <!-- /.box-body -->
                                </div> <!-- /.row -->
                            </div> <!-- /.box-body -->
                        </div> <!-- /.stock_details -->

                    </div> <!-- /.box-body -->

                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div> <!-- /.box-primary -->

            </div> <!-- /.col -->
        </div> <!-- /.row -->
    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->