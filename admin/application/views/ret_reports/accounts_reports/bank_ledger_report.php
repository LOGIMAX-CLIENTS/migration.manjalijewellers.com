<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <style>
        @media print {

            html,
            body {
                height: auto;
                width: 190vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
            }
        }

        /* Fixed column widths for ledger table */
        #ledger_table {
            table-layout: fixed;
            width: 100%;
        }

        #ledger_table th:nth-child(1),
        #ledger_table td:nth-child(1) {
            width: 12%;
        }

        #ledger_table th:nth-child(2),
        #ledger_table td:nth-child(2) {
            width: 12%;
        }

        #ledger_table th:nth-child(3),
        #ledger_table td:nth-child(3) {
            width: 36%;
        }

        #ledger_table th:nth-child(4),
        #ledger_table td:nth-child(4) {
            width: 20%;
            text-align: right;
        }

        #ledger_table th:nth-child(5),
        #ledger_table td:nth-child(5) {
            width: 20%;
            text-align: right;
        }
    </style>
    <section class="content-header">
        <h1>
            Reports
            <small>Bank Ledger</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Retail Reports</a></li>
            <li class="active">Bank Ledger Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <!-- <div class="col-md-2">
                            <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                                <div class="form-group">
                                    <select id="branch_select" class="form-control branch_filter" style="width:100%;"></select>
                                </div>
                            <?php } else { ?>
                                <input type="hidden" id="branch_filter"
                                    value="<?php echo $this->session->userdata('id_branch') ?>">
                                <input type="hidden" id="branch_name"
                                    value="<?php echo $this->session->userdata('branch_name') ?>">
                            <?php } ?>
                        </div> -->

                        <div class="col-md-2">
                            <div class="form-group">
                                <select id="id_ledger" class="form-control" style="width:100%;">
                                    <option value="">Select Ledger</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <button class="btn btn-default btn_date_range" id="rpt_date_picker">
                                        <i class="fa fa-calendar"></i> Date range picker
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    <span style="display:none;" id="rpt_from_date"></span>
                                    <span style="display:none;" id="rpt_to_date"></span>
                                </div>
                            </div><!-- /.form group -->
                        </div>

                        <div class="col-md-1">
                            <div class="form-group">
                                <button type="button" id="btn_search" class="btn btn-info">Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="ledger_table" class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Trans Date</th>
                                                <th>Trans No</th>
                                                <th>Particulars</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    var BASE_URL = "<?php echo base_url(); ?>";
</script>