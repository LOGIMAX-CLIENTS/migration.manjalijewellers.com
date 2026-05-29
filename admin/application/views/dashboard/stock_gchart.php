<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cockpit.css">

<style>
.chart {
    width: 100%;
    min-height: 330px;
}

.loader-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    background-color: rgba(255, 255, 255, 0.7);
}

.loader {
    display: block;
    position: relative;
    left: 50%;
    top: 50%;
    width: 100px;
    height: 100px;
    margin: -50px 0 0 -50px;
    border-radius: 50%;
    border: 3px solid transparent;
    border-top-color: #3498db;
    -webkit-animation: spin 2s linear infinite;
    /* Chrome, Opera 15+, Safari 5+ */
    animation: spin 2s linear infinite;
    /* Chrome, Firefox 16+, IE 10+, Opera */
}

.loader:before {
    content: "";
    position: absolute;
    top: 5px;
    left: 5px;
    right: 5px;
    bottom: 5px;
    border-radius: 50%;
    border: 3px solid transparent;
    border-top-color: #e74c3c;
    -webkit-animation: spin 3s linear infinite;
    /* Chrome, Opera 15+, Safari 5+ */
    animation: spin 3s linear infinite;
    /* Chrome, Firefox 16+, IE 10+, Opera */
}

.loader:after {
    content: "";
    position: absolute;
    top: 15px;
    left: 15px;
    right: 15px;
    bottom: 15px;
    border-radius: 50%;
    border: 3px solid transparent;
    border-top-color: #f9c922;
    -webkit-animation: spin 1.5s linear infinite;
    /* Chrome, Opera 15+, Safari 5+ */
    animation: spin 1.5s linear infinite;
    /* Chrome, Firefox 16+, IE 10+, Opera */
}

@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
        /* Chrome, Opera 15+, Safari 3.1+ */
        -ms-transform: rotate(0deg);
        /* IE 9 */
        transform: rotate(0deg);
        /* Firefox 16+, IE 10+, Opera */
    }

    100% {
        -webkit-transform: rotate(360deg);
        /* Chrome, Opera 15+, Safari 3.1+ */
        -ms-transform: rotate(360deg);
        /* IE 9 */
        transform: rotate(360deg);
        /* Firefox 16+, IE 10+, Opera */
    }
}

@keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
        /* Chrome, Opera 15+, Safari 3.1+ */
        -ms-transform: rotate(0deg);
        /* IE 9 */
        transform: rotate(0deg);
        /* Firefox 16+, IE 10+, Opera */
    }

    100% {
        -webkit-transform: rotate(360deg);
        /* Chrome, Opera 15+, Safari 3.1+ */
        -ms-transform: rotate(360deg);
        /* IE 9 */
        transform: rotate(360deg);
        /* Firefox 16+, IE 10+, Opera */
    }
}
</style>


<style type="text/css">
.tab-content>.tab-pane {
    height: 1px;
    overflow: hidden;
    display: block;
    visibility: hidden;
}

.tab-content>.active {
    height: auto;
    overflow: auto;
    visibility: visible;
}

.left_tab {
    color: #f8f8f8 !important;
    /* background-color: #ddffff!important; */
    height: 159px;
    margin-bottom: 15px;
    border-radius: 10px;
    padding: 10px;
    font-weight: bold;

}

.right_tab {
    color: #f8f8f8 !important;
    background-color: #12f28c !important;
    height: 106px;
    margin-bottom: 8px;
    border-radius: 10px;
    padding: 10px;
    overflow-y: visible;
    font-weight: bold;
}

.left_element {
    text-align: left;
    font-size: 14px;
}

.center_element {
    margin-bottom: 34px;
    margin-top: 32px;
    text-align: center;
    font-size: 25px;
}

.right_element {
    text-align: right;
    /* font-size:15px; */
}

.center_element_right_tab {
    margin-bottom: 10px;
    margin-top: 10px;
    text-align: center;
    font-size: 23px;
}

.left_element_right_tab {
    text-align: left;
    /* font-size:15px; */
}

.right_element_right_tab {
    text-align: end;
    /* font-size:15px; */
}

.dt_btn {

    font-size: 13px;
}

/* .overlay{
	z-index: 50;
    background: rgba(255,255,255,0.7);
    border-radius: 3px;
} */
</style>


<!-- Content Wrapper. Contains page content -->
<div class="row cock_pit">

    <div class="col-md-12 col-xs-12 container-row">



        <div class="col-md-12 col-xs-12 box-items ">

            <div class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <div class="col-md-3 heading">PRODUCT-WISE STOCK</div>

                    <div class="col-md-3"></div>

                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default stock_dt_range " id="dt_product_stock">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div> -->
                    <?php if($this->session->userdata('branch_settings')==1){?>


                    <?php if($this->session->userdata('id_branch')==''){?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control branch_filter" id="branch_product_stock" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>
                    <?php }else{?>
                    <input type="hidden" id="branch_product_stock" value="<?php echo $this->session->userdata('id_branch');?>">
                    <?php }?>
                    <?php }?>

                    <div class="col-md-3">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_product_stock" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                    <!-- <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div> -->

                </div>

                <div id="stock_product">
                    <div class="col-md-5 col-xs-12">
                        <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div id="product_stock" class="chart" style=""></div>

                        </div>

                        <div id="product_stock_lable" class=""
                            style="overflow-x:scroll;width:100%;float: left;display: flex;">


                        </div>
                    </div>
                    <div class="col-md-7 col-xs-12">
                        <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div id="" style="">

                                <table id="product_wise_stock" class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <td>Product</td>
                                            <td>Pcs</td>
                                            <td>G.WT</td>
                                            <td>N.WT</td>
                                            <td>DIA.WT</td>
                                            <td>%</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tfoot>
                                </table>


                            </div>

                        </div>

                        <!-- <div id="" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div> -->
                    </div>

                    <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                    </div>
                </div>


            </div>


        </div>


    </div>

    <div class="col-md-12 col-xs-12 container-row">

        <div class="col-md-12 col-xs-12 box-items ">
            <div class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <div class="col-md-3 heading">SECTION-WISE STOCK</div>

                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default stock_dt_range " id="dt_section_stock">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div> -->

                    <div class="col-md-3"></div>
                    <?php if($this->session->userdata('branch_settings')==1){?>

                    <!-- <div class="col-md-1"></div> -->
                    <?php if($this->session->userdata('id_branch')==''){?>
                    <div class="col-md-3">
                        <div class="form-group">

                            <select class="form-control branch_filter" id="branch_section_stock" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>
                    <?php }else{?>
                    <input type="hidden" id="branch_section_stock" value="<?php echo $this->session->userdata('id_branch');?>">
                    <?php }?>
                    <?php }?>

                    <div class="col-md-3">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_section_stock" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div class="col-md-5 col-xs-12">
                    <div id="stock_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="section_stock" class="chart" style=""></div>

                    </div>

                    <div id="section_stock_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
                <div class="col-md-7 col-xs-12">
                    <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="" style="">

                            <table id="section_wise_stock" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <td>Section</td>
                                        <td>Pcs</td>
                                        <td>G.WT</td>
                                        <td>N.WT</td>
                                        <td>DIA.WT</td>
                                        <td>%</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tfoot>
                            </table>


                        </div>

                    </div>

                    <!-- <div id="" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div> -->
                </div>
            </div>


        </div>


    </div>






    <div class="col-md-12 col-xs-12 container-row">

        <div class="col-md-12 col-xs-12 box-items ">
            <div class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <div class="col-md-3 heading">KARIGAR-WISE STOCK</div>

                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default stock_dt_range " id="dt_karigar_stock">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div> -->

                    <!-- <div class="col-md-3"></div> -->
                    <?php if($this->session->userdata('branch_settings')==1){?>

                    <!-- <div class="col-md-1"></div> -->
                    <?php if($this->session->userdata('id_branch')==''){?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control branch_filter" id="branch_karigar_stock" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>
                    <?php }else{?>
                    <input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch');?>">
                    <?php }?>
                    <?php }?>

                    <div class="col-md-2">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_karigar_stock" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">


                            <select class="form-control karigar" id="id_karigar_stock" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">


                            <select class="form-control" id="mt_karigar_groupby"  style="width:100%;">

                            <option value="1">Karigar</option>
                            <option value="2">Product</option>
                            <option value="3">Branch</option>

                            </select>
                        </div>
                    </div>

                </div>
                <div class="col-md-5 col-xs-12">
                    <div id="stock_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="karigar_stock" class="chart" style=""></div>

                    </div>

                    <div id="karigar_stock_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
                <div class="col-md-7 col-xs-12">
                    <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="" style="">

                            <table id="karigar_wise_stock" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <td>Karigar</td>
                                        <td>Pcs</td>
                                        <td>G.WT</td>
                                        <td>N.WT</td>
                                        <td>DIA.WT</td>
                                        <td>%</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tfoot>
                            </table>


                        </div>

                    </div>

                </div>
            </div>


        </div>


    </div>

</div>