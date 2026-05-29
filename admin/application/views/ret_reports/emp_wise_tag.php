<div class="content-wrapper">

    <section class="content-header">

        <h1>

            Employee Wise Tag

        </h1>

        <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Employee Wise Tag Report</a></li>

            <li class="active">Employee wise Tag</li>

        </ol>

    </section>

    <section class="content">

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-header with-border">

                    </div>

                    <div class="box-body">

                        <?php

                        if ($this->session->flashdata('chit_alert')) {

                            $message = $this->session->flashdata('chit_alert');

                        ?>

                            <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                                <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

                                <?php echo $message['message']; ?>

                            </div>

                        <?php } ?>

                        <div class="row">

                            <div class="col-md-12">

                                <div class="box box-default">

                                    <div class="box-body">

                                        <div class="row">



                                            <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>

                                                <div class="col-md-2">

                                                    <label>Branch</label>

                                                    <select class="form-control" id="branch_select" style="width:100%;"></select>

                                                </div>

                                            <?php } else { ?>

                                                <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">

                                            <?php } ?>
                                            
                                            
                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label>Date</label>

                                                    <?php

                                                    $fromdt = date("d/m/Y");

                                                    $todt = date("d/m/Y");

                                                    ?>

                                                    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt . ' - ' . $todt ?>" readonly="">

                                                </div>

                                            </div>

                                            <div class="col-md-2" style="display:none;">

                                                <div class="form-group">

                                                    <label>Select Employee<span class="error"> *</span></label>

                                                    <select id="emp_select" class="form-control" style="width:100%;" required></select>

                                                    <input type="hidden" name="estimation[created_by]" id="emp_name" value="">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label>Select Product<span class="error"> *</span></label>

                                                    <select id="prod_select" class="form-control" style="width:100%;" required></select>

                                                    <input type="hidden" name="estimation[created_by]" id="id_product" value="">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label>Select Design<span class="error"> *</span></label>

                                                    <select id="des_select" class="form-control" style="width:100%;" required></select>

                                                    <input type="hidden" name="estimation[created_by]" id="id_design" value="">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label>Select Sub Design<span class="error"> *</span></label>

                                                    <select id="sub_des_select" class="form-control" style="width:100%;" required></select>

                                                    <input type="hidden" name="estimation[created_by]" id="id_sub_design" value="">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label>Select Employee<span class="error"> *</span></label>

                                                    <select id="employee_select" class="form-control" style="width:100%;" required></select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label>Lot No</label>

                                                    <input id="lotno" class="form-control" style="width:100%;" type="number"/>


                                                </div>

                                            </div>

                                            

                                            <div class="col-md-2">

                                                <label></label>

                                                <div class="form-group">

                                                    <button type="button" id="emp_wise_tag_search" class="btn btn-info">Search</button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table id="emp_wise_tag_list" class="table table-bordered table-striped text-center">

                                <thead>

                                    <tr>

                                        <th width="5%">S.No</th>

                                        <th width="9%">Name</th>

                                        <th width="9%">Date</th>

                                        <th width="9%">Old Tag id</th>

                                        <th width="9%">Tag Code</th>

                                        <th width="9%">Lot No</th>

                                        <!-- <th width="9%">Lot Wt</th> -->

                                        <th width="9%">Image</th>

                                        <th width="9%">Size</th>

                                        <th width="9%">Product</th>

                                        <th width="9%">Design</th>

                                        <th width="9%">Sub Design</th>

                                        <th width="9%">Gross Wt</th>

                                        <th width="9%">Less Wt</th>

                                        <th width="9%">Net Wt</th>

                                        <th width="9%">V.A(%)</th>

                                        <th width="9%">MC</th>

                                        <th width="9%">MC Type</th>




                                    </tr>

                                </thead>
                                <tbody>

                                </tbody>

                                <tfoot>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>
                                    <td style="text-align:right"></td>


                                </tfoot>
                            </table>

                        </div>

                    </div><!-- /.box-body -->

                    <div class="overlay" style="display:none">

                        <i class="fa fa-refresh fa-spin"></i>

                    </div>

                </div><!-- /.col -->

            </div><!-- /.row -->

    </section>

</div><!-- /.content-wrapper -->

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

            </div>

            <div class="modal-body">

                <img src="" id="imagepreview" style="width: 300px; height: 264px;">

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default danger" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>