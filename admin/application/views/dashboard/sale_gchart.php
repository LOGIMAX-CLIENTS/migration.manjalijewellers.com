<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cockpit.css">


<style type="text/css">

.heading{
    font-weight: bold;
    font-size: 15px;
}

.chart {
    width: 100%;
    min-height: 330px;
}

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
    height: 174px;
    margin-bottom: 15px;
    border-radius: 10px;
    padding: 10px;
    font-weight: bold;

}

.right_tab {
    color: #f8f8f8 !important;
    background-color: #12f28c !important;
    height: 115px;
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
    margin-bottom: 46px;
    margin-top: 39px;
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

#month_on_month{
    width:100%;
    height: 385px;
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


<!-- Content Wrapper. Contains page content -->
<div class="row cock_pit">

    <div class="col-md-12 col-xs-12 container-row">
        <div class="col-md-6 col-xs-12 box-items no-paddingwidth">
            <div id="sales_glance" class="col-md-12 col-xs-12 estimation no-paddingwidth ">
                 <div class="loader-wrapper" style=" display: none;">
                            <div class="loader"></div>
                    </div>
                <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading">SALES AT A GLANCE</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>
                <div class="col-md-12 col-xs-12 dash-item-heading">


                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_sales_glance">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>

                    <div class="col-md-1"></div>

                    <?php if($this->session->userdata('branch_settings')==1){?>

                        <?php if($this->session->userdata('id_branch')==''){?>
                            <div class="col-md-4">
                                <div class="form-group">


                                    <select class="form-control branch_filter" id="sales_branch_glance" multiple
                                        style="width:100%;"></select>
                                </div>
                            </div>

                    <?php }else{?>

                        <input type="hidden" id="sales_branch_glance" value="<?php echo $this->session->userdata('id_branch');?>">

                        <?php }?>

                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_sales_glance" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>


                </div>
                <div  class="col-md-12 col-xs-12">

                    <div id="sales_growth" class="container col-md-12 col-xs-12  inside-items">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="row">
                                    <div class="col-xs-12  box-items " Style=" ">
                                        <div class="sidebarContent left_tab"
                                            style=" background-color: #9207fb!important;">
                                            <div class="left_element">Sales</div>
                                            <div class="center_element" id="sale_amt"></div>
                                            <div class="right_element" id="sale_bills"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--Nested rows-->
                                <div class="row">
                                    <div class="col-xs-12  box-items">
                                        <div class="sidebarContent left_tab"
                                            style=" background-color: #fb0707!important;">
                                            <div class="left_element">Returns</div>
                                            <div class="center_element" id="return_amt"></div>
                                            <div class="right_element" id="return_qty"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <!--Nested row-->
                                <div class="row">
                                    <div class="col-xs-12 box-items ">
                                        <div class="sidebarContent right_tab"
                                            style=" background-color: #16ff00!important;">
                                            <div class="left_element_right_tab">Diamond</div>
                                            <div class="center_element_right_tab" > <span id="dia_wt"></span><span
                                                    style="font-size:14px;"> CT</span></div>
                                            <div class="right_element_right_tab"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--Nested rows-->
                                <div class="row">
                                    <div class="col-xs-12 ">
                                        <div class="sidebarContent right_tab"
                                            style=" background-color: #FD7F20!important;">
                                            <div class="left_element_right_tab">Quantity </div>
                                            <div class="center_element_right_tab"> <span id="sale_net_wt"></span><span
                                                    style="font-size:14px;"> N.wt</span></div>
                                            <div class="right_element_right_tab" id=""> <span
                                                    id="sale_gross_wt"></span><span style="font-size:13px;"> G.wt</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 ">
                                        <div class="sidebarContent right_tab"
                                            style=" background-color: #e815ea!important;">
                                            <div class="left_element_right_tab">Discount </div>
                                            <div class="center_element_right_tab" id="sale_discount"></div>
                                            <div class="right_element_right_tab"></div>
                                        </div>
                                    </div>
                                </div>





                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-6 col-xs-12 box-items no-paddingwidth">
            <div id="sales_top_selling" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                </div>
                <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading">TOP SELLING</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >
                    <!-- <div class="col-md-4 heading">TOP SELLING</div> -->



                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_top_selling">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <?php if($this->session->userdata('branch_settings')==1){?>
                        <?php if($this->session->userdata('id_branch')==''){?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control branch_filter" id="branch_top_selling" multiple
                                    style="width:100%;"></select>
                            </div>
                        </div>
                        <?php }else{?>

                            <input type="hidden" id="branch_top_selling" value="<?php echo $this->session->userdata('id_branch');?>">

                        <?php }?>
                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_top_selling" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div  class="col-md-12 col-xs-12">

                    <div id="sales_growth" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div class="chart"id="top_selling" style=""></div>

                    </div>

                    <div id="top_selling_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
            </div>
        </div>






    </div>


    <div class="col-md-12 col-xs-12 container-row">

        <div class="col-md-6 col-xs-12 box-items ">
            <div id="sales_top_sellers" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
            <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                    </div>
                <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading">TOP SELLERS</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <!-- <div class="col-md-4 heading">TOP SELLERS</div> -->



                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_top_sellers">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <?php if($this->session->userdata('branch_settings')==1){?>
                    <?php if($this->session->userdata('id_branch')==''){?>
                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control branch_filter" id="branch_top_sellers" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>
                    <?php }else{?>
                        <input type="hidden" id="branch_top_sellers" value="<?php echo $this->session->userdata('id_branch');?>">
                    <?php }?>
                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_top_sellers" multiple style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div  class="col-md-12 col-xs-12">



                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">


                        <div class="chart" id="top_sellers" ></div>

                    </div>

                    <div id="top_sellers_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xs-12 box-items ">
            <div id="sales_month_on_month" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                  <div class="loader-wrapper" style=" display: none;">
                            <div class="loader"></div>
                  </div>
                  <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading"> MONTH ON MONTH SALE</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <!-- <div class="col-md-4 heading"> MONTH ON MONTH SALE</div> -->



                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control financial_year" id="finalial_year_month_sales"
                                style="width:100%;"></select>
                            <!-- <button class="btn btn-default sale_dt_range " id="dt_month_sales">
										<span  class ="dt_btn show_date" id=""></span>
										<span  class ="dt_btn payment_list3" style="display:none" id=""></span>
										<span  class="dt_btn payment_list4" style="display:none" id=""></span>

									</button> -->
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <?php if($this->session->userdata('branch_settings')==1){?>
                    <?php if($this->session->userdata('id_branch')==''){?>
                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control branch_filter" id="branch_month_sales"
                                style="width:100%;" multiple ></select>
                        </div>
                    </div>
                    <?php }else{?>
                        <input type="hidden" id="branch_month_sales" value="<?php echo $this->session->userdata('id_branch');?>">
                    <?php }?>
                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_month_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div class="col-md-12 col-xs-12">
                    <div id="sales_growth_month" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="month_on_month" style=""></div>

                    </div>

                    <!-- <canvas id="month_on_month" style="width:100%;max-width:600px"></canvas> -->

                </div>
            </div>
        </div>




    </div>



    <div class="col-md-12 col-xs-12 container-row">

        <div class="col-md-6 col-xs-12 box-items ">

            <div id="sales_branch_wise_compare"  class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">

                  <div class="loader-wrapper" style=" display: none;">
                            <div class="loader"></div>
                  </div>
                  <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading">BRANCH WISE COMPARISION</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">

                    <!-- <div class="col-md-4 heading">BRANCH WISE COMPARISION</div> -->

                    <!-- <div class="col-md-1"></div> -->

                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_branch_compare">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <?php if($this->session->userdata('branch_settings')==1){?>
                        <?php if($this->session->userdata('id_branch')==''){?>
                            <div class="col-md-4">
                                <div class="form-group">


                                    <select class="form-control branch_filter" id="branch_branch_compare" style="width:100%;"
                                        multiple></select>
                                </div>
                            </div>
                        <?php }else{?>

                            <input type="hidden" id="branch_branch_compare" value="<?php echo $this->session->userdata('id_branch');?>">

                        <?php }?>
                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_branch_compare" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>


                <div class="col-md-12 col-xs-12">
                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div class="chart" id="branch_comparison" style="">



                        </div>

                        <div id="branch_comparison_lable" class=""
                            style="overflow-x:scroll;width:100%;float: left;display: flex;">


                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xs-12 box-items ">
            <div id="sales_store_wise_sales"  class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                    <div class="loader-wrapper" style=" display: none;">
                            <div class="loader"></div>
                    </div>
                <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading">STORE WISE SALES-SHARE</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>

                <div   class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">


                    <!-- <div class="col-md-4 heading"> STORE WISE SALES-SHARE</div> -->



                    <div class="col-md-3">
                        <div class="form-group">

                            <button class="btn btn-default sale_dt_range " id="dt_store_sales">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <?php if($this->session->userdata('branch_settings')==1){?>
                        <?php if($this->session->userdata('id_branch')==''){?>
                        <div class="col-md-4">
                            <div class="form-group">


                                <select class="form-control branch_filter" id="branch_store_sales" multiple
                                    style="width:100%;"></select>
                            </div>
                        </div>
                        <?php }else{?>
                            <input type="hidden" id="branch_store_sales" value="<?php echo $this->session->userdata('id_branch');?>">
                        <?php }?>
                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_store_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div class="col-md-12 col-xs-12">
                    <div id="sales_growth_month" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="" style="height: 379px;">
                            <table id="store_wise_sales" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <td>Branch</td>
                                        <td>Share</td>
                                        <td>Amount</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- <canvas id="month_on_month" style="width:100%;max-width:600px"></canvas> -->


                </div>
            </div>
        </div>






    </div>


    <div class="col-md-12 col-xs-12 container-row">

        <div class="col-md-6 col-xs-12 box-items ">

            <div id="sales_avg_branch" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                    <div class="loader-wrapper" style=" display: none;">
                            <div class="loader"></div>
                    </div>

                <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading">AVG WASTAGE BY BRANCH</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <!-- <div class="col-md-4 heading"> AVG WASTAGE BY BRANCH </div> -->

                    <!-- <div class="col-md-1"></div> -->

                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_branch_avg_va">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <div class="col-md-1"></div>

                    <?php if($this->session->userdata('branch_settings')==1){?>
                        <?php if($this->session->userdata('id_branch')==''){?>
                        <div class="col-md-4">
                            <div class="form-group">


                                <select class="form-control branch_filter" id="branch_branch_avg_va" multiple
                                    style="width:100%;"></select>
                            </div>
                        </div>
                        <?php }else{?>
                            <input type="hidden" id="branch_branch_avg_va" value="<?php echo $this->session->userdata('id_branch');?>">
                        <?php }?>
                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                        <select class="form-control" id="group_by" style="width:100%;" >
                                 <option value="0">Branch</option>
                                <option value="1">Product</option>
                                <option value="2">Section</option>

                            </select>
                        </div>
                    </div>

                    <!-- <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_branch_avg_va" multiple
                                style="width:100%;"></select>
                        </div>
                    </div> -->

                </div>
                <div class="col-md-12 col-xs-12">
                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div class="chart" id="branch_avg_va" >



                        </div>

                        <div id="branch_avg_va_lable" class=""
                            style="overflow-x:scroll;width:100%;float: left;display: flex;">


                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-6 col-xs-12 box-items ">
            <div id="sales_customer_wise_sale" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                    </div>
                <div class="col-md-12 col-xs-12 dash-item-heading" ><div class="col-md-12 heading">CUSTOMER WISE SALE</div></div>
                <div class="col-md-12 col-xs-12 dash-item-heading" >  <hr></div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <!-- <div class="col-md-4 heading" >CUSTOMER WISE SALE </div> -->



                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_custome_wise_sale">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <?php if($this->session->userdata('branch_settings')==1){?>
                        <?php if($this->session->userdata('id_branch')==''){?>
                        <div class="col-md-4">
                            <div class="form-group">


                                <select class="form-control branch_filter" id="branch_custome_wise_sale" multiple
                                    style="width:100%;"></select>
                            </div>
                        </div>
                        <?php }else{?>
                            <input type="hidden" id="branch_custome_wise_sale" value="<?php echo $this->session->userdata('id_branch');?>">
                        <?php }?>
                    <?php }?>

                    <div class="col-md-4">
                        <div class="form-group">


                            <select class="form-control metal_filter" id="mt_custome_wise_sale" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div  class="col-md-12 col-xs-12">



                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">


                        <div class="chart" id="custome_wise_sale" ></div>

                    </div>

                    <div id="custome_wise_sale_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
            </div>
        </div>






    </div>






    <div class="col-md-12 col-xs-12 container-row">

        <div class="col-md-12 col-xs-12 box-items ">
            <div id="sales_product_wise_sale" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                 <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                    </div>
                <div  class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">

                    <div class="col-md-3 heading">PRODUCT WISE SALE</div>

                    <div class="col-md-1"></div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_product_sales">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <!-- <div class="col-md-1"></div> -->



                    <?php if($this->session->userdata('branch_settings')==1){?>
                    <?php if($this->session->userdata('id_branch')==''){?>
                    <div class="col-md-3">
                        <div class="form-group">


                            <select class="form-control branch_filter" id="branch_product_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>
                    <?php }else{?>
                        <input type="hidden" id="branch_product_sales" value="<?php echo $this->session->userdata('id_branch');?>">
                    <?php }?>
                    <?php }?>

                    <div class="col-md-3">
                        <div class="form-group">

                            <select class="form-control metal_filter" id="mt_product_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="product_sale" style="width:300px;height:330px;"></div>

                    </div>

                    <div id="product_sale_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="" style="height:330px;">

                            <table id="product_wise_sales" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <td>Product</td>
                                        <td>Total Sale</td>
                                        <td>Percentage</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
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
            <div id="sales_section_wise_sale" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                 <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                    </div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <div class="col-md-3 heading">SECTION WISE SALES</div>

                    <div class="col-md-1"></div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_section_sales">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <!-- <div class="col-md-1"></div> -->
                    <?php if($this->session->userdata('branch_settings')==1){?>
                        <?php if($this->session->userdata('id_branch')==''){?>
                        <div class="col-md-3">
                            <div class="form-group">


                                <select class="form-control branch_filter" id="branch_section_sales" multiple
                                    style="width:100%;"></select>
                            </div>
                        </div>
                        <?php }else{?>

                            <input type="hidden" id="branch_section_sales" value="<?php echo $this->session->userdata('id_branch');?>">

                        <?php }?>
                    <?php }?>

                    <div class="col-md-3">
                        <div class="form-group">

                            <select class="form-control metal_filter" id="mt_section_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="section_sale" style="width:300px;height:330px;"></div>

                    </div>

                    <div id="section_sale_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="" style="height:330px;">

                            <table id="section_wise_sales" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <td>Section</td>
                                        <td>Total Sale</td>
                                        <td>Percentage</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
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
            <div id="sales_employee_wise_sale" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                 <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                    </div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <div class="col-md-3 heading">EMPLOYEE-WISE SALES</div>

                    <div class="col-md-1"></div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_emp_sales">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>
                    <!-- <div class="col-md-1"></div> -->
                    <?php if($this->session->userdata('branch_settings')==1){?>
                    <?php if($this->session->userdata('id_branch')==''){?>
                    <div class="col-md-3">
                        <div class="form-group">


                            <select class="form-control branch_filter" id="branch_emp_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>
                    <?php }else{?>

                        <input type="hidden" id="branch_emp_sales" value="<?php echo $this->session->userdata('id_branch');?>">


                    <?php }?>
                    <?php }?>

                    <div class="col-md-3">
                        <div class="form-group">

                            <select class="form-control metal_filter" id="mt_emp_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="emp_sale" style="width:300px;height:330px;"></div>

                    </div>

                    <div id="emp_sale_lable" class="" style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="" style="height:330px;">

                            <table id="emp_wise_sales" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <td>EMPNAME</td>
                                        <td>Total Sale</td>
                                        <td>Percentage</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
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
            <div id="sales_karigar_wise_sale" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">
                 <div class="loader-wrapper" style=" display: none;">
                        <div class="loader"></div>
                    </div>
                <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">
                    <div class="col-md-3 heading">KARIGAR-WISE SALES</div>

                    <div class="col-md-1"></div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-default sale_dt_range " id="dt_karigar_sales">
                                <span class="dt_btn show_date" id=""></span>
                                <span class="dt_btn payment_list3" style="display:none" id=""></span>
                                <span class="dt_btn payment_list4" style="display:none" id=""></span>

                            </button>
                        </div>
                    </div>

                    <!-- <div class="col-md-1"></div> -->
                    <?php if($this->session->userdata('branch_settings')==1){?>
                        <?php if($this->session->userdata('id_branch')==''){?>
                        <div class="col-md-3">
                            <div class="form-group">


                                <select class="form-control branch_filter" id="branch_karigar_sales" multiple
                                    style="width:100%;"></select>
                            </div>
                        </div>
                        <?php }else{?>
                            <input type="hidden" id="branch_karigar_sales" value="<?php echo $this->session->userdata('id_branch');?>">
                        <?php }?>
                    <?php }?>

                    <div class="col-md-3">
                        <div class="form-group">

                            <select class="form-control metal_filter" id="mt_karigar_sales" multiple
                                style="width:100%;"></select>
                        </div>
                    </div>

                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="karigar_sale" style="width:300px;height:330px;"></div>

                    </div>

                    <div id="karigar_sale_lable" class=""
                        style="overflow-x:scroll;width:100%;float: left;display: flex;">


                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                        <div id="" style="height:330px;">

                            <table id="karigar_wise_sales" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <td>Karigar</td>
                                        <td>Total Sale</td>
                                        <td>Percentage</td>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
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


  <!--  <div class="col-md-12 col-xs-12 container-row">-->

  <!--      <div class="col-md-12 col-xs-12 box-items ">-->
  <!--          <div id="sales_karigar_wise_sale" class="col-md-12 col-xs-12 sales no-paddingwidth blog-box">-->
  <!--               <div class="loader-wrapper" style=" display: none;">-->
  <!--                      <div class="loader"></div>-->
  <!--                  </div>-->
  <!--              <div class="col-md-12 col-xs-12 dash-item-heading no-paddingwidth">-->
  <!--                  <div class="col-md-3 heading">Break Even Analysis</div>-->

  <!--                  <div class="col-md-1"></div>-->

  <!--                  <div class="col-md-2">-->
  <!--                      <div class="form-group">-->
  <!--                          <button class="btn btn-default sale_dt_range " id="dt_break_even">-->
  <!--                              <span class="dt_btn show_date" id=""></span>-->
  <!--                              <span class="dt_btn payment_list3" style="display:none" id=""></span>-->
  <!--                              <span class="dt_btn payment_list4" style="display:none" id=""></span>-->

  <!--                          </button>-->
  <!--                      </div>-->
  <!--                  </div>-->

                    <!-- <div class="col-md-1"></div> -->
  <!--                  <?php if($this->session->userdata('branch_settings')==1){?>-->
  <!--                      <?php if($this->session->userdata('id_branch')==''){?>-->
  <!--                      <div class="col-md-3">-->
  <!--                          <div class="form-group">-->


  <!--                              <select class="form-control branch_filter" id="branch_break_even" multiple-->
  <!--                                  style="width:100%;"></select>-->
  <!--                          </div>-->
  <!--                      </div>-->
  <!--                      <?php }else{?>-->
  <!--                          <input type="hidden" id="branch_break_even" value="<?php echo $this->session->userdata('id_branch');?>">-->
  <!--                      <?php }?>-->
  <!--                  <?php }?>-->

  <!--                  <div class="col-md-3">-->
  <!--                      <div class="form-group">-->

  <!--                          <select class="form-control metal_filter" id="mt_break_even" multiple-->
  <!--                              style="width:100%;"></select>-->
  <!--                      </div>-->
  <!--                  </div>-->

  <!--              </div>-->
  <!--              <div class="col-md-6 col-xs-6">-->

  <!--                  <div id="sales_growth_sell" class="col-md-12 col-xs-12 no-paddingwidth inside-items">-->

                        <!-- <div id="" style="width:300px;height:330px;"> -->

  <!--                      <canvas id="break_even"></canvas>-->
                        <!-- </div> -->

  <!--                  </div>-->

  <!--                  <div id="break_even_lable" class=""-->
  <!--                      style="overflow-x:scroll;width:100%;float: left;display: flex;">-->


  <!--                  </div>-->
  <!--              </div>-->
  <!--              <div class="col-md-6 col-xs-6">-->
  <!--                  <div id="" class="col-md-12 col-xs-12 no-paddingwidth inside-items">-->

  <!--                      <div id="" style="height:330px;">-->

  <!--                          <table id="" class="table table-bordered table-striped text-center">-->
  <!--                              <thead>-->
  <!--                                  <tr>-->
  <!--                                      <td>Karigar</td>-->
  <!--                                      <td>Total Sale</td>-->
  <!--                                      <td>Percentage</td>-->
  <!--                                  </tr>-->
  <!--                              </thead>-->
  <!--                              <tbody>-->

  <!--                              </tbody>-->
  <!--                              <tfoot>-->
  <!--                                  <td></td>-->
  <!--                                  <td></td>-->
  <!--                                  <td></td>-->
  <!--                              </tfoot>-->
  <!--                          </table>-->


  <!--                      </div>-->

  <!--                  </div>-->

                    <!-- <div id="" class=""
	style="overflow-x:scroll;width:100%;float: left;display: flex;">-->


	 <!--       </div> -->-->
  <!--              </div>-->
  <!--          </div>-->


  <!--      </div>-->


  <!--  </div>-->

</div>

