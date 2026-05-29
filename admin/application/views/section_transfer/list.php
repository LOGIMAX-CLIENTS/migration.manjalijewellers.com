<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h1>

            Section Transfer List

            <small></small>

        </h1>

        <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Inventory</a></li>

            <li class="active">Section transfer list</li>

        </ol>

    </section>



    <!-- Main content -->

    <section class="content">

        <div class="row">

            <div class="col-xs-12">



                <div class="box box-primary">

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-12">



                                <div class="col-md-6">

                                    <div class="box box-primary">

                                        <div class="row">

                                            <div class="col-md-6">

                                                <label for="">Type <span class="error"> *</span></label>

                                                <input type="radio" name="section_item_type" class='transfer_type' id="type1" value="1" checked> <label for="type1">Tagged</label> &nbsp;&nbsp;

                                                <input type="radio" name="section_item_type" class='transfer_type' id="type2" value="2"> <label for="type2">Non Tagged</label>

                                            </div>

                                        </div>

                                        <div class="box-body">

                                            <div class="row">

                                                <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>

                                                    <div class="col-md-4">

                                                        <div class="form-group ">

                                                            <label>Select Branch<span class="error">*</span></label>

                                                            <select id="branch_select" class="form-control branch_filter" reqiured></select>

                                                        </div>

                                                    </div>

                                                <?php } else { ?>

                                                    <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">

                                                    <input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">

                                                <?php } ?>



                                                <div class="col-md-4">

                                                    <div class="form-group ">

                                                        <label>Select Product<span class="error">*</span></label>

                                                        <select id="prod_select" class="form-control" style="width:100%;"></select>

                                                    </div>

                                                </div>



                                                <div class="col-md-4">

                                                    <div class="form-group ">

                                                        <label>From Section</label>

                                                        <select id="select_frm_section" class="form-control" style="width:100%;"></select>

                                                    </div>

                                                </div>





                                                <div class="col-md-4">

                                                    <div class="form-group tagged">

                                                        <label></label>

                                                        <input class="form-control" type="text" name="tag_code" id="tag_code" placeholder="Search Tag Code">

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group tagged">

                                                        <label></label>

                                                        <input class="form-control" type="text" name="tag_code_old" id="tag_code_old" placeholder="Search Old Tag Id">

                                                    </div>

                                                </div>

                                                <div class="col-md-4">

                                                    <div class="form-group tagged">

                                                        <label></label>

                                                        <input type="text" id="est_no" class="form-control" style="width:100%;" placeholder="Estimation No." />

                                                    </div>

                                                </div>

                                                <div class="col-md-2">

                                                    <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="section_tag_search" class="btn btn-info">Search</button>

                                                        <input type="hidden" id="allow_order_item_cancel_otp" value="<?php echo $counter_change_otp ?>">

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- <div class="row" id="delete_row">

                                           <div class="col-md-2"> 

                                               <label></label>

                                                   <div class="form-group">

                                                       <button type="button" id="delete_product_mapping" class="btn btn-danger">Delete</button>   

                                                   </div>

                                           </div>

                                       </div> -->

                                        </div>

                                    </div>

                                </div>



                                <div class="col-md-6">

                                    <div class="box box-primary">

                                        <div class="box-body">



                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group ">

                                                        <label>To Section</label>

                                                        <select id="select_to_section" class="form-control" style="width:100%;"></select>

                                                    </div>

                                                </div>

                                                <div class="col-md-2">

                                                    <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="section_transfer" class="btn btn-success">Transfer</button>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <div class="table-responsive product">

                            <table id="section_trans_list" class="table table-bordered table-striped text-center">

                                <thead>

                                    <tr>

                                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all" />All</label></th>

                                        <th style="width:10%;">Branch</th>

                                        <th style="width:20%;">Tag Code</th>

                                        <th style="width:5%;">Old Tag Id</th>

                                        <th style="width:20%;">Section Name</th>

                                        <th style="width:20%;">Product Name</th>

                                        <th style="width:20%;">Pcs</th>

                                        <th style="width:20%;">Gwt</th>

                                        <th style="width:20%;">Nwt</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <td></td>

                                    <td></td>

                                    <td></td>

                                    <td></td>

                                    <td style="font-weight:bold;">TOTAL</td>

                                    <td>:</td>

                                    <td width="60%" style="font-weight:bold;" class="pcs"></td>

                                    <td width="60%" style="font-weight:bold;" class="grs_wt"></td>

                                    <td width="60% " style="font-weight:bold;" class="net_wt"></td>

                                </tbody>

                            </table>

                        </div>



                        <div class="row section_non_tagged " style="display: none;">

                            <div class="col-md-12">

                                <p class="page-header">

                                    Non Tagged Search Result :



                                </p>

                                <div class="table-responsive section" style="display: none;">

                                    <table id="bt_nt_search_list" class="table table-bordered table-striped text-center">

                                        <thead>

                                            <tr>

                                                <th width="10%"><label class="checkbox-inline"><input type="checkbox" id="nt_select_all" name="nt_select_all" value="all" />All</label></th>

                                                <th width="20%">Section</th>

                                                <th width="20%">Product</th>

                                                <th width="20%">Design</th>

                                                <th width="20%">Sub Design</th>

                                                <th width="10%">Pcs</th>

                                                <th width="20%">G.wt</th>

                                                <th width="20%">N.wt</th>

                                            </tr>

                                        </thead>

                                        <tfoot>

                                            <tr>

                                                <th colspan="2">Total</th>

                                                <td><input type="text" class="nt_pieces" disabled="true" placeholder="Pieces" /></td>

                                                <td><input type="text" class="nt_grs_wt" disabled="true" placeholder="Gross Weight" /></td>

                                                <td><input type="text" class="nt_net_wt" disabled="true" placeholder="Net Weight" /></td>

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

                </div><!-- /.box -->

            </div><!-- /.col -->

        </div><!-- /.row -->

    </section><!-- /.content -->

</div><!-- /.content-wrapper -->












<div class="modal fade" id="confirm-sec_transotp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Section Transfer OTP</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12  cancel_otp_confirmation" style="display:block;white-space:no-wrap">
                    OTP will be sent to the administrator for approval. Do you wish to proceed?
                </div>
                <div class="col-md-6 cancel_otp" style="display:none;">
                    <div class='form-group'>
                        <div class='input-group'>
                            <input type="text" id="sectrans_otp" name="sectrans_otp" placeholder="Enter 4 Digit OTP" maxlength="6" class="form-control" required />
                            <span class="input-group-btn">
                                <button type="button" id="resend_cancel_otp" class="btn btn-warning" disabled>Resend</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer order_remarks" style="display: none;">
                <button class="btn btn-danger" type="button" id="cancell_delete" disabled>Delete</button>
            </div>
            <div class="modal-footer cancel_otp_confirmation" style="display: none;">
                <button type="button" class="btn btn-success" id="send_counter_change_otp_yes">Yes</button>
                <button type="button" class="btn btn-danger" id="send_counter_change_otp_no">No</button>
            </div>
            <div class="modal-footer verify_otp" style="display: none;">
                <a href="#" id="verfiy_counter_change_otp" class="btn btn-success">Verify</a>
                <button type="button" class="btn btn-danger" id="counterchange_close_modal">Close</button>
            </div>
        </div>
    </div>
</div>