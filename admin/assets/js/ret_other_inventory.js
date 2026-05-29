var path = url_params();
var other_inventory_item = [];
var branch_details = [];
var other_inventory_ref_no = [];
var inventory_item = [];
var img_resource = [];
var total_files = [];
var pre_img_files = [];
var pre_img_resource = [];
var ctrl_page = path.route.split('/');
var row_num = $('#scheme_map_table tbody tr').length;
var sizeNames = [];
$(document).ready(function () {
    //Adding scheme map for gift code starts
    if (ctrl_page[1] == 'purchase_entry' && ctrl_page[2] == 'add') {
        Webcam.set({
            width: 290,
            height: 190,
            image_format: 'jpg',
            jpeg_quality: 90
        });
        Webcam.attach('#my_camera');
        Webcam.on('error', function (err) {
            console.log('Error accessing webcam:', err);
        });

        $(document).on('keydown', function (e) {
            if (e.ctrlKey && e.which === 73) {
                take_snapshot('pre_images');
            }
        });
    }


    $('input[type=checkbox][name=select_customer_type]').change(function () {
        if (this.value) {
            display_customer_type(this.value);
        }

    });
    if (ctrl_page[1] == 'other_inventory' && ctrl_page[2] == 'add') {
        scheme_add_row();
        $('input[type=checkbox][name=select_customer_type][value=2]').prop('checked', true);
        $('#issue_to').val(2);
        row_num++;
    }
    // if (ctrl_page[1] == 'other_inventory' && ctrl_page[2] == 'edit') {

    //     let issue_to = $('#issue_to').val();
    //     $("#table_length").val(row_num);
    //     var table_rows = $('#scheme_map_table tbody tr').length;
    //     if (table_rows > 0) {
    //         $('input[type=checkbox][name=select_customer_type][value=issue_to]').prop('checked', true);
    //         display_customer_type(issue_to);
    //     }
    //     else {
    //         $('input[type=checkbox][name=select_customer_type][value=issue_to]').prop('checked', true);
    //     }
    //     for (var i = 1; i <= table_rows; i++) {
    //         load_scheme_select(i);
    //     }
    //     row_num++;

    // }

    if (ctrl_page[1] == 'other_inventory' && ctrl_page[2] == 'edit') {


        $("#table_length").val(row_num);
        var table_rows = $('#scheme_map_table tbody tr').length;
        if (table_rows > 0) {
            $('input[type=checkbox][name=select_customer_type][value=1]').prop('checked', true);
            display_customer_type(1);
        }
        else {
            $('input[type=checkbox][name=select_customer_type][value=2]').prop('checked', true);
        }
        for (var i = 1; i <= table_rows; i++) {
            load_scheme_select(i);
        }
        row_num++;

    }

    //Adding scheme map for gift code ends
    $('.date').datepicker({ dateFormat: 'yyyy-mm-dd', })
    switch (ctrl_page[1]) {

        case 'inventory_category':
            switch (ctrl_page[2]) {
                case 'list':
                    get_item_category_list();
                    break;
            }
            break;
        case 'other_inventory':
            switch (ctrl_page[2]) {
                case 'add':
                    get_item_size_list();
                    get_uom_list();
                    get_itemfor_list();
                    get_branch_details();
                    break;
                case 'edit':
                    get_item_size_list();
                    get_uom_list();
                    get_itemfor_list();
                    break;
                case 'list':
                    set_other_inventory();
                    break;
            }
            break;

        case 'purchase_entry':
            switch (ctrl_page[2]) {
                case 'add':
                    get_other_inventory_item();
                    get_supplier();
                    break;
                case 'list':
                    get_supplier();
                    var date = new Date();
                    var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 0, 1);
                    var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());
                    var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());
                    $('#from_date').html(from_date);
                    $('#to_date').html(to_date);
                    get_other_inventory_purchase_items();
                    $('#date_range_picker').daterangepicker(
                        {
                            ranges: {
                                'Today': [moment(), moment()],
                                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                'This Month': [moment().startOf('month'), moment().endOf('month')],
                                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                            },
                            startDate: moment().subtract(6, 'days'),
                            endDate: moment()
                        },
                        function (start, end) {
                            $('#date_range_picker').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
                            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                            $('#from_date').text(start.format('DD-MM-YYYY'));
                            $('#to_date').text(end.format('DD-MM-YYYY'));
                        }
                    );


                    break;
            }

            break;
        case 'product_details':
            switch (ctrl_page[2]) {
                case 'list':
                    get_other_inventory_product_details();
                    break;
                // case 'pro_det':
                // get_other_product_details(ctrl_page[3]);
                // break;
                case 'add':
                    get_other_inventory_ref_no();
                    $('#select_ref_no').select2({
                        placeholder: 'Select Ref no',
                        allowClear: true
                    });

                    $("#select_item,#item_filter").select2(
                        {

                            placeholder: "Select Item",

                            allowClear: true

                        });
                    break;
            }

            break;

        case 'stock_details':
            switch (ctrl_page[2]) {

                case 'list':

                    var date = new Date();
                    var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 0, 1);
                    var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());
                    var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());
                    $('#from_date').html(from_date);
                    $('#to_date').html(to_date);
                    $('#date_range_picker').daterangepicker(
                        {
                            ranges: {
                                'Today': [moment(), moment()],
                                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                'This Month': [moment().startOf('month'), moment().endOf('month')],
                                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                            },
                            startDate: moment().subtract(6, 'days'),
                            endDate: moment()
                        },
                        function (start, end) {
                            $('#date_range_picker').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
                            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                            $('#from_date').text(start.format('DD-MM-YYYY'));
                            $('#to_date').text(end.format('DD-MM-YYYY'));
                        }
                    );

                    get_other_item_category_list();

                    break;
            }

            break;

        case 'issue_item':


            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 0, 1);
            var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());
            var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());
            $('#from_date').html(from_date);
            $('#to_date').html(to_date);
            get_other_inventory_item_issue_details();
            $('#date_range_picker').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(6, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#date_range_picker').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#from_date').text(start.format('DD-MM-YYYY'));
                    $('#to_date').text(end.format('DD-MM-YYYY'));
                }
            );

            if ($('#id_branch').val() != '') {
                get_invnetory_item();
                get_bill_details();
            }
            break;

        case 'item_size':
            set_packing_item_size_list();
            fetchAllSizes();
            break;

        case 'available_stock':
            get_invnetory_item_list();
            get_item_size_list();
            get_other_item_category_list();
            break;

        case 'product_mapping':
            get_other_inventory_item();
            get_ActiveProduct();
            get_product_mapping_details();
            break;

        case 'reorder_report':
            reorder_report();
            break;
    }
    if (ctrl_page[1] == 'get_pro_detail_list') {
        get_other_product_details();
    }
});
function get_item_category_list() {
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_other_inventory/inventory_category/ajax?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var access = data.access;
            var list = data.list;
            var oTable = $('#item_list').DataTable();
            oTable.clear().draw();
            if (list != null && list.length > 0) {
                oTable = $('#item_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "order": [[0, "desc"]],
                    "aaData": list,
                    "aoColumns": [{ "mDataProp": "id_other_item_type" },
                    { "mDataProp": "name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_other_item_type
                            if (row.asbillable == 1) {
                                return 'Cost';
                            }
                            else {
                                return 'Free';
                            }
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_other_item_type
                            if (row.expirydatevalidate == 1) {
                                return 'Having';
                            }
                            else {
                                return 'No Validity';
                            }
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            status_url = (access.edit == '1') ? base_url + "index.php/admin_ret_other_inventory/otheritem_status/" + (row.status == 1 ? 0 : 1) + "/" + row.id_other_item_type : "#";
                            return "<a href='" + status_url + "'><i class='fa " + (row.status == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.status == 1 ? 'green' : 'red') + "'></i></a>"
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_other_item_type
                            edit_url = (access.edit == '1' ? base_url + 'index.php/admin_ret_other_inventory/inventory_category/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/admin_ret_other_inventory/inventory_category/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            // action_content = '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';
                            var action_content = "";
                            // if (access.edit == "1") {
                            //     action_content +=
                            //         '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a> ';
                            // }
                            // if (access.delete == "1") {
                            //     action_content +=
                            //         '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';
                            // }
                            // Check if edit access is allowed
                            if (access.edit == "1") {
                                action_content +=
                                    '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit"></i></a> ';
                            }

                            // Check if delete access is allowed and the item is not in use
                            if (access.delete == "1" && row.item_type_used_count == 0) {
                                action_content +=
                                    '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a>';
                            } else if (access.delete == "1" && row.item_type_used_count > 0) {
                                // If the item type is in use, show a disabled delete button or provide some indication to the user
                                action_content +=
                                    '<a href="#" class="btn btn-danger btn-del disabled" title="Cannot delete, item type in use"><i class="fa fa-trash"></i></a>';
                            }

                            return action_content;
                        }
                    }]
                });

            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//Other Inventory Item
$("#other_item_img").change(function () {
    event.preventDefault();
    validateImage(this);
});
function validateImage() {
    var preview = $('#other_item_img_preview');
    if (arguments[0].files[0].size > 1048576) {
        alert('File size cannot be greater than 1 MB');
        arguments[0].value = "";
        preview.css('display', 'none');
    }
    else {
        var fileName = arguments[0].value;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        ext = ext.toLowerCase();
        if (ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg") {
            alert("Upload JPG or PNG Images only");
            arguments[0].value = "";
            preview.css('display', 'none');
        }
        else {
            var file = arguments[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.prop('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
                preview.css('display', '');
            }
            else {
                preview.prop('src', '');
                preview.css('display', 'none');
            }
        }
    }
}
function set_other_inventory() {
    var company_name = $('#company_name').val();
    var branch_name = ($('#id_branch').val() != '' && $('#id_branch').val() != undefined ? $('#id_branch').val() : $("#branch_select option:selected").text());
    var report_name = "Other Inventoy Report";
    var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"
        + "<span>" + report_print(branch_name, report_name, '', '', '') + "</span>";
    my_Date = new Date();
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/other_inventory/ajax?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            success: function (data) {
                //console.log(data);
                var item = data.list;
                var access = data.access;
                var qrcode = data.qrcode;
                console.log(item);
                if (access.add == '0') {
                    $('#add_details').attr('disabled', 'disabled');
                }
                var oTable = $('#other_item').DataTable();
                oTable.clear().draw();
                if (item != null && item.length > 0) {
                    oTable = $('#other_item').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,
                        "scrollX": '100%',
                        "bSort": true,
                        "dom": 'lBfrtip',
                        "order": [[0, "desc"]],
                        "columnDefs": [
                            {
                                targets: [6],
                                className: 'dt-body-right'
                            },
                        ],
                        "buttons": [
                            {
                                extend: 'print',
                                footer: true,
                                title: "",
                                messageTop: title,
                                customize: function (win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                },
                            },
                            {
                                extend: 'excel',
                                footer: true,
                                title: "Other Inventory Report",
                            }
                        ],
                        "aaData": item,
                        "aoColumns": [{ "mDataProp": "id_other_item" },
                        { "mDataProp": "name" },
                        { "mDataProp": "sku_id" },
                        { "mDataProp": "type_name" },
                        { "mDataProp": "size" },
                        { "mDataProp": "issue_preference" },
                        { "mDataProp": "unit_price" },
                        {
                            "mDataProp": function (row, type, val, meta) {
                                if (row.image != '') {
                                    var img_src = base_url + 'assets/img/other_inventory/' + row.sku_id + '/' + row.image;
                                    return '<a href="' + img_src + '" target="_blank"><img class="img_src" src="' + img_src + '" width="30" height="30"></a>';
                                }
                                else {
                                    var img_src = base_url + 'assets/img/no_image.png';
                                    return '<img class="img_src" src="' + img_src + '" width="30" height="30">';
                                }
                            },

                        },
                        // { "mDataProp": function ( row, type, val, meta ) {
                        //     if(row.qr_image!='')
                        //     {
                        //         var img_src=base_url+'other_product_qrcode/'+row.sku_id+'/'+row.qr_image;
                        //         return '<a href="'+img_src+'" target="_blank"><img src="'+img_src+'" width="30" height="30"></a>';
                        //     }
                        //     else
                        //     {
                        //         var img_src=base_url+'other_product_qrcode/'+row.sku_id+'/no_image.png';
                        //         return '<img class="img_src" src="'+img_src+'" width="30" height="30">';
                        //     }

                        // },
                        // },
                        {
                            "mDataProp": function (row, type, val, meta) {
                                id = row.id_other_item
                                edit_url = (access.edit == '1' ? base_url + 'index.php/admin_ret_other_inventory/other_inventory/edit/' + id : '#');
                                delete_url = (access.delete == '1' ? base_url + 'index.php/admin_ret_other_inventory/other_inventory/delete/' + id : '#');
                                delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                                print_url = (access.edit == '1' ? base_url + 'index.php/admin_ret_other_inventory/other_inventory/print_qrcode/' + id : '#');

                                // Check if the item is in use
                                var canDelete = (access.delete == '1' && row.item_used_count == 0); // Ensure access and item is not used

                                // action_content = '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a><a href="' + print_url + '"target="_blank"  class="btn btn-info btn-print"><i class="fa fa-print" ></i></a>';

                                // Generate action content dynamically based on canDelete value
                                var action_content = '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit"></i></a>';

                                if (canDelete) {
                                    // Only show delete button if the item can be deleted
                                    action_content += '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a>';
                                } else {
                                    // Disable delete button if item cannot be deleted
                                    action_content += '<a href="#" class="btn btn-danger btn-del disabled" title="Cannot delete - Item in use"><i class="fa fa-trash"></i></a>';
                                }

                                // Add print button
                                action_content += '<a href="' + print_url + '" target="_blank" class="btn btn-info btn-print"><i class="fa fa-print"></i></a>';

                                return action_content;
                            }
                        }]
                    });
                }
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
if ($('#sku_id').length > 0) {
    $('#sku_id').on('blur onchange', function () {
        if (this.value.length > 0) {
            check_skuid_avail(this.value);
        }
        else {
            $(this).val();
            $(this).attr('placeholder', 'Enter sku id');
            $(this).focus();
        }
    });
}
function check_skuid_avail(sku_id) {
    $("div.overlay").css("display", "block");
    $.ajax({
        type: 'POST',
        data: { 'sku_id': sku_id },
        url: base_url + 'index.php/admin_ret_other_inventory/check_sku_id',
        dataType: 'json',
        success: function (avail) {
            if (avail == 1) {
                $('#sku_id').val('');
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Id already exists" });
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//Other Inventory Item
function get_itemfor_list() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_other_inventory/get_inventory_category',
        dataType: 'json',
        success: function (data) {
            var id = $("#item_for").val();
            $.each(data, function (key, item) {
                $("#itemfor").append(
                    $("<option></option>")
                        .attr("value", item.id_other_item_type)
                        .text(item.name)
                );
            });

            $("#itemfor").select2(
                {
                    placeholder: "Select Category",
                    allowClear: true
                });

            $("#itemfor").select2("val", (id != '' && id > 0 ? id : ''));
            $(".overlay").css("display", "none");
        }
    });
}
function get_uom_list() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_catalog/uom/active_uom',
        dataType: 'json',
        success: function (data) {
            var id = $("#id_uom").val();
            $.each(data, function (key, item) {
                $("#select_uom").append(
                    $("<option></option>")
                        .attr("value", item.uom_id)
                        .text(item.uom_name)
                );
            });

            $("#select_uom").select2(
                {
                    placeholder: "Select UOM",
                    allowClear: true
                });

            $("#select_uom").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
function get_item_size_list() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_other_inventory/item_size/get_ActivePackagingItemSize',
        dataType: 'json',
        success: function (data) {
            var id = $("#id_inv_size").val();
            $.each(data, function (key, item) {
                $("#select_size").append(
                    $("<option></option>")
                        .attr("value", item.id_inv_size)
                        .text(item.size_name)
                );
            });

            $("#select_size").select2(
                {
                    placeholder: "Select Size",
                    allowClear: true
                });

            $("#select_size").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
function get_other_item_category_list() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_other_inventory/get_ActiveCategory',
        dataType: 'json',
        success: function (data) {
            var id = $("#select_type").val();
            $.each(data, function (key, item) {
                $("#select_type").append(
                    $("<option></option>")
                        .attr("value", item.id_other_item_type)
                        .text(item.name)
                );
            });

            $("#select_type").select2(
                {
                    placeholder: "Select Category",
                    allowClear: true
                });

            $("#select_type").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
function get_other_inventory_ref_no() {
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/get_other_inventory_ref_no',
        dataType: 'json',
        success: function (data) {
            other_inventory_ref_no = data;
            var id = $("#select_ref_no").val();
            $.each(data, function (key, item) {
                $("#select_ref_no").append(
                    $("<option></option>")
                        .attr("value", item.otr_inven_pur_id)
                        .data("other-inventory-id", item.otr_inven_pur_id)
                        .data("itemid", item.inv_pur_itm_itemid)
                        .text(item.otr_inven_pur_order_ref)
                );

            });

            $("select_ref_no").select2(
                {
                    placeholder: "Select Ref no",
                    allowClear: true
                });

            $("#select_ref_no").select2("val", (id != '' && id > 0 ? id : ''));

        }
    });
}
var isSelectItemChanging = false;
// $('#select_prod_item').on('change', function () {
//     var otherInventoryId = $("#select_ref_no option:selected").data("other-inventory-id");
//     var selectedValue = this.value;
//     if (selectedValue!='')
//     {
//         setTimeout(function () {
//             get_other_inv_product_details(otherInventoryId,selectedValue);
//         }, 0);
//     }
//     $("#prod_details > tbody > tr").remove();
//     if (!isSelectItemChanging) {
//         if ($('#select_prod_item').val() != '') {
//             var trHtml = '';
//             isSelectItemChanging = true;
//             console.log(inventory_item);
//             $.each(inventory_item, function (key, item) {
//                 trHtml += '<tr>'
//                     + '<td><input type="checkbox" name="order_items[inv_pur_itm_id][]" class="pur_id" value=' + item.inv_pur_itm_id + ' checked=""></td>'
//                     + '<td style="text-align:center"><input type="hidden" class="item_id" name="order_items[itemid][]" value="' + item.id_other_item + '"><input type="hidden" class="quantity" name="order_items[quantity][]" value="' + item.no_of_pcs + '">' + item.no_of_pcs + '</td>'
//                     + '<td style="text-align:right"><input type="hidden" class="piece" name="piece" ><input type="hidden" class="rate" name=""order_items[rate][]" value="' + item.rate + '"><input type="text" class="no_of_pcs" name="order_items[pieces][]" value="' + item.tag + '"></td>'
//                     + '<td style="text-align:right"><input type="hidden" class="balance" name="balance" value="' + item.balance + '">' + item.balance + '</td>'
//                     + '</tr>';
//             });
//             if ($('#prod_details > tbody  > tr').length > 0) {
//                 $('#prod_details > tbody > tr:first').before(trHtml);
//             } else {
//                 $('#prod_details tbody').append(trHtml);
//             }
//         }
//         isSelectItemChanging = false;
//     }
// });
$('#select_ref_no').on('change', function () {
    var otherInventoryId = $("#select_ref_no option:selected").data("other-inventory-id");
    var selectedValue = this.value;
    if (selectedValue !== '') {
        get_other_inv_product_details(otherInventoryId, '', function () {
            // This callback function will be executed after the AJAX call is completed
            $("#prod_details > tbody > tr").remove();
            if (!isSelectItemChanging) {
                if ($('#select_prod_item').val() !== '') {
                    var trHtml = '';
                    isSelectItemChanging = true;
                    $.each(inventory_item, function (key, item) {
                        trHtml += '<tr>'
                            + '<td><input type="checkbox" name="order_items[inv_pur_itm_id][]" class="pur_id" value=' + item.inv_pur_itm_id + ' checked=""></td>'
                            + '<td style="text-align:right">' + item.product_name + '</td>'
                            + '<td style="text-align:center"><input type="hidden" class="item_id" name="order_items[itemid][]" value="' + item.id_other_item + '"><input type="hidden" class="quantity" name="order_items[quantity][]" value="' + item.no_of_pcs + '">' + money_format_india(parseFloat(item.no_of_pcs).toFixed(0)) + '</td>'
                            + '<td style="text-align:right"><input type="hidden" class="piece" name="piece" ><input type="hidden" class="rate" name="order_items[rate][]" value="' + item.rate + '"><input type="number" id ="tag_pcs" class="no_of_pcs" name="order_items[pieces][]" value="' + money_format_india(parseFloat(item.balance).toFixed(0)) + '"></td>'
                            + '<td style="text-align:right"><input type="hidden" class="balance" name="balance" value="' + item.balance + '">' + money_format_india(parseFloat(item.balance).toFixed(0)) + '</td>'
                            + '</tr>';
                        $('#tag_pcs').val(item.tag);
                    });
                    if ($('#prod_details > tbody > tr').length > 0) {
                        $('#prod_details > tbody > tr:first').before(trHtml);
                    } else {
                        $('#prod_details tbody').append(trHtml);
                    }
                }
                isSelectItemChanging = false;
            }
        });
    }
});
function get_other_inv_product_details(id_other_item, item_id, callback) {
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/get_other_inventory_details',
        dataType: 'json',
        data: { "id_other_item": id_other_item, "item_id": '' },
        success: function (data) {
            inventory_item = data;
            console.log(inventory_item);
            // Execute the callback function after the AJAX call is completed
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}
$('#select_all').click(function (event) {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
    event.stopPropagation();
});
// $('#select_ref_no').on('change', function () {
//     if (!isSelectItemChanging && this.value != '' && this.value != null && this.value != 0) {
//         var otherInventoryId = $("#select_ref_no option:selected").data("other-inventory-id");
//         get_other_inventory_product_item(otherInventoryId);
//         // $('#select_prod_item').trigger('change');
//     }
// });
// function get_other_inv_product_details(id_other_item,item_id) {
//     $.ajax({
//         type: 'POST',
//         url: base_url + 'index.php/admin_ret_other_inventory/get_other_inventory_details',
//         dataType: 'json',
//         data: { "id_other_item": id_other_item, "item_id":item_id},
//         success: function (data) {
//             inventory_item = data;
//           console.log(inventory_item);
//         }
//     });
// }
function get_other_inventory_product_item(other_invnetory_item_id) {
    $('#select_prod_item option').remove();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/get_other_inventory_product',
        dataType: 'json',
        data: { "id_other_item": other_invnetory_item_id, 'item_id': '' },
        success: function (data) {
            var id = $("#item_id").val();
            $.each(data, function (key, item) {
                $("#select_prod_item,#item_filter").append(
                    $("<option></option>")
                        .attr("value", item.id_other_item)
                        .text(item.name)
                );
            });
            $("#select_prod_item,#item_filter").select2({
                placeholder: "Select Item",
                allowClear: true
            });
            $("#select_prod_item").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
// $('#tag_pcs').on('keyup', function () {
//         console.log('Keyup event triggered');

//         var row = $(this).closest('tr');
//         noOfPcsInput = $(this);
//         console.log('noOfPcsInput:', noOfPcsInput);
//         var balance = parseFloat(row.find('.balance').val());
//         var noOfPcs = parseFloat(noOfPcsInput.val());

//         console.log('balance:', balance);
//         console.log('noOfPcs:', noOfPcs);
//         if (noOfPcs > balance) {
//             $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Pieces is greater than Balance.." });
//             noOfPcsInput.val('');
//         }
// });
$(document).on('input', ".no_of_pcs", function () {
    var row = $(this).closest('tr');
    blc = parseFloat(row.find('.balance').val());
    if ($(this).val() > blc) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Pieces is greater than Balance.." });
        row.find('.no_of_pcs').val('');
    }
});
$('#prod_inventory_submit').on('click', function () {
    $("div.overlay").css("display", "block");
    var allow_submit = true;
    if ($('#select_ref_no').val() == '' || $('#select_ref_no').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Select Ref No.." });
        allow_submit = false;
    }
    if (allow_submit) {
        if ($('#prod_details > tbody  > tr').length > 0) {
            var selectedItems = [];
            $('#prod_details > tbody > tr').each(function () {
                var row = $(this);
                var checkBox = row.find("input[name='order_items[inv_pur_itm_id][]']");
                if (checkBox.is(":checked")) {
                    var itemID = row.find('.item_id').val();
                    var noOfPcs = row.find('.no_of_pcs').val();
                    var balance = row.find('.balance').val();
                    var quantity = row.find('.quantity').val();
                    var rate = row.find('.rate').val();
                    selectedItems.push({
                        'inv_pur_itm_id': checkBox.val(),
                        'itemid': itemID,
                        'pieces': noOfPcs,
                        'balance': balance,
                        'quantity': quantity,
                        'rate': rate
                    });

                }
            });
            // Use the selectedItems array as needed
            console.log(selectedItems);
            if (selectedItems.length > 0) {
                var items = JSON.stringify(selectedItems);
                $('#prod_inventory_submit').prop('disabled', true);
                my_Date = new Date();
                var url = base_url + "index.php/admin_ret_other_inventory/product_details/save?nocache=" + my_Date.getUTCSeconds();
                $.ajax({
                    url: url,
                    data: { "order_items": items },
                    type: "POST",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status) {
                            $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
                            $("div.overlay").css("display", "none");
                            window.location.reload();
                            // window.location.href = base_url + 'index.php/admin_ret_other_inventory/product_details/list';
                            window.open(base_url + 'index.php/admin_ret_other_inventory/product_other_inventory_print/' + data.ref_no + '?nocache=' + my_Date.getUTCSeconds(), '_blank');
                        } else {
                            $('#prod_inventory_submit').prop('disabled', false);
                            $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
                            $("div.overlay").css("display", "none");
                        }
                    },
                    error: function (error) {
                        $("div.overlay").css("display", "none");
                    }
                });
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Select the Item" });
                $("div.overlay").css("display", "none");
            }
        } else {
            $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>No Record Found" });
        }
    }
});
// $('#prod_inventory_submit').on('click', function () {
//     $("div.overlay").css("display", "block");

//     var allow_submit=true;
//     if($('#select_ref_no').val()=='' || $('#select_ref_no').val()==null)
//     {
//         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Ref No.."});
//         allow_submit=false;
//     }

//     if(allow_submit)
//     {
//      if ($('#prod_details > tbody  > tr').length > 0) {
//         var selectedItems = [];
//         $('#prod_details > tbody > tr').each(function () {
//             var row = $(this);
//             var checkBox = row.find("input[name='order_items[inv_pur_itm_id][]']");

//             if (checkBox.is(":checked")) {
//                 var itemID = row.find('.item_id').val();
//                 var noOfPcs = row.find('.no_of_pcs').val();
//                 var balance = row.find('.balance').val();
//                 var quantity = row.find('.quantity').val();
//                 var rate = row.find('.rate').val();

//                 selectedItems.push({
//                     'inv_pur_itm_id': checkBox.val(),
//                     'itemid': itemID,
//                     'pieces': noOfPcs,
//                     'balance': balance,
//                     'quantity': quantity,
//                     'rate': rate
//                 });
//                 if (noOfPcs > balance) {
//                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Pieces is greater than Balance.."});
//                     noOfPcsInput.val('');
//                 }
//             }
//         });

//         // Use the selectedItems array as needed
//         console.log(selectedItems);
//         if (selectedItems.length > 0) {
//             // var form_data = $('#prod_inventory_entry').serialize();
//             // form_data+= '&selected_items=' + JSON.stringify(selectedItems);
//               var items = JSON.stringify(selectedItems);
//             $('#prod_inventory_submit').prop('disabled', true);
//             my_Date = new Date();
//             var url = base_url + "index.php/admin_ret_other_inventory/product_details/save?nocache=" + my_Date.getUTCSeconds();
//             $.ajax({
//                 url: url,
//                 data:{"order_items":items},
//                 type: "POST",
//                 dataType: "JSON",
//                 success: function (data) {
//                     if (data.status) {
//                         $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
//                         $("div.overlay").css("display", "none");
//                           // window.location.reload();

//                     window.location.href=base_url+'index.php/admin_ret_other_inventory/product_details/list';
//                     } else {
//                         $('#prod_inventory_submit').prop('disabled', false);
//                         $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
//                         $("div.overlay").css("display", "none");
//                     }
//                 },
//                 error: function (error) {
//                     $("div.overlay").css("display", "none");
//                 }
//             });
//         } else {
//             $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Select the Item" });
//             $("div.overlay").css("display", "none");
//         }
//     } else {
//         $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>No Record Found" });
//     }
//   }
// });
function get_other_inventory_item() {
    $('#select_item option').remove();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/get_other_inventory_item',
        dataType: 'json',
        success: function (data) {
            var id = $("#select_item").val();
            $.each(data, function (key, item) {
                $("#select_item,#item_filter").append(
                    $("<option></option>")
                        .attr("value", item.id_other_item)
                        .text(item.name)
                );
            });
            $("#select_item,#item_filter").select2({
                placeholder: "Select Item",
                allowClear: true
            });
            $("#select_item").select2("val", (id != '' && id > 0 ? id : ''));
            if ($('#item_filter').length) {
                $("#item_filter").select2("val", "");
            }
        }
    });
}
function get_supplier() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_other_inventory/get_supplier',
        dataType: 'json',
        success: function (data) {
            var id = $("#select_karigar").val();
            $.each(data, function (key, item) {
                $("#select_karigar").append(
                    $("<option></option>")
                        .attr("value", item.id_karigar)
                        .data("kar_state", item.id_state)
                        .text(item.karigar_name)
                );
            });

            $("#select_karigar").select2(
                {
                    placeholder: "Select Supplier",
                    allowClear: true
                });

            $("#select_karigar").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
$('#buy_quantity,#buy_rate').on('keyup', function () {
    calculate_item_cost();
    calculate_item_gst_amt();
});
$('#tax_amount').on('keyup', function () {
    calculate_item_gst_amt();
});
function calculate_item_cost() {
    var buy_quantity = (isNaN($('#buy_quantity').val()) || $('#buy_quantity').val() == '' ? 0 : $('#buy_quantity').val());
    var buy_rate = (isNaN($('#buy_rate').val()) || $('#buy_rate').val() == '' ? 0 : $('#buy_rate').val());

    var buy_amount = parseFloat(parseFloat(buy_quantity) * parseFloat(buy_rate)).toFixed(2);
    $('#buy_amount').val(buy_amount);
}
function calculate_item_gst_amt() {
    // Get input values
    var buy_quantity = isNaN(parseFloat($('#buy_quantity').val())) ? 0 : parseFloat($('#buy_quantity').val());
    var buy_rate = isNaN(parseFloat($('#buy_rate').val())) ? 0 : parseFloat($('#buy_rate').val());
    var tax_amount = isNaN(parseFloat($('#tax_amount').val())) ? 0 : parseFloat($('#tax_amount').val());
    // Calculate buy amount
    var buy_amount = (buy_quantity * buy_rate).toFixed(2);
    // Calculate GST amount
    var gst_amount = ((tax_amount * buy_amount) / 100).toFixed(2);
    // Calculate total amount including GST
    var total_amount = (parseFloat(gst_amount) + parseFloat(buy_amount)).toFixed(2);
    // Update the result fields
    $('#gst_amount').val(total_amount);
    $('#pur_gst_amount').val(gst_amount);

}
$('#add_item_info').on('click', function () {

    var allow_submit = true;
    if ($('#select_karigar').val() == '' || $('#select_karigar').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Select Supplier.." });
        allow_submit = false;
    }
    else if ($('#select_item').val() == '' || $('#select_item').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Select Item.." });
        allow_submit = false;
    }
    else if ($('#buy_quantity').val() == '' || $('#buy_quantity').val() == 0) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The Pieces.." });
        allow_submit = false;
    }
    else if ($('#buy_rate').val() == '' || $('#buy_rate').val() == 0) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The Buying Rate.." });
        allow_submit = false;
    }
    else if ($('#tax_amount').val() == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The GST %.." });
        allow_submit = false;
    }

    if ($('#pur_details > tbody').length > 0) {
        $('#pur_details > tbody tr').each(function (idx, row) {
            curRow = $(this);
            if (curRow.find('.item_id').val() == $('#select_item').val()) {
                allow_submit = false;
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Item Already Added.." });
                return true;
            }
        });
    }

    if (allow_submit) {
        var trHtml = '';
        var cmp_state = $('#cmp_state').val();
        var kar_state = $('#kar_state').val();
        var igst = 0;
        var cgst = 0;
        var sgst = 0;
        var gst_amount = 0;
        if (cmp_state == kar_state) {
            cgst += ($('#pur_gst_amount').val() / 2);
            sgst += ($('#pur_gst_amount').val() / 2);
            gst_amount += $('#pur_gst_amount').val();
        }
        else {
            igst += $('#pur_gst_amount').val();
            gst_amount += $('#pur_gst_amount').val();
        }
        trHtml += '<tr>'
            + '<td style="text-align:center"><input type="hidden" class="item_id" name="order_items[itemid][]" value="' + $('#select_item').val() + '">' + $('#select_item option:selected').text() + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="quantity" name="order_items[quantity][]" value="' + $('#buy_quantity').val() + '">' + $('#buy_quantity').val() + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="rate" name="order_items[rate][]" value="' + $('#buy_rate').val() + '">' + $('#buy_rate').val() + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="amount" name="order_items[amount][]" value="' + $('#buy_amount').val() + '">' + $('#buy_amount').val() + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="pur_gst_amount" name="order_items[pur_gst_amount][]" value="' + $('#pur_gst_amount').val() + '"><input type="hidden" class="tax_amount" name="order_items[tax_amount][]" value="' + $('#tax_amount').val() + '">' + $('#tax_amount').val() + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="cgst" name="order_items[cgst][]" value="' + cgst + '">' + cgst + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="sgst" name="order_items[sgst][]" value="' + sgst + '">' + sgst + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="igst" name="order_items[igst][]" value="' + igst + '">' + igst + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="total_gst_amount" name="order_items[total][]" value="' + gst_amount + '">' + parseFloat(gst_amount).toFixed(2) + '</td>'
            + '<td style="text-align:right"><input type="hidden" class="gst_amount" name="order_items[gst][]" value="' + $('#gst_amount').val() + '">' + $('#gst_amount').val() + '</td>'
            + '<td style="text-align:center"><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
            + '</tr>';
        if ($('#pur_details > tbody  > tr').length > 0) {
            $('#pur_details > tbody > tr:first').before(trHtml);
        } else {
            $('#pur_details tbody').append(trHtml);
        }
        calculate_grand_total();
        reset_order_items();
    }
});
function remove_row(curRow) {
    curRow.remove();
    calculate_grand_total();
}
function reset_order_items() {
    $('#select_item').select2("val", "");
    $('#buy_quantity').val("");
    $('#buy_rate').val("");
    $('#buy_amount').val("");
    $('#tax_amount').val("");
    $('#gst_amount').val("");
}
function serializeOrderItems() {
    var orderItems = [];
    $('#pur_details > tbody tr').each(function (idx, row) {
        var item = {};
        $(this).find('input[type="hidden"]').each(function () {
            var name = $(this).attr('class');
            var value = $(this).val();
            item[name] = value;
        });
        orderItems.push(item);
    });
    return orderItems;
}
// Usage example
$('#inventory_submit').on('click', function () {
    $("div.overlay").css("display", "block");
    if ($('#pur_details > tbody  > tr').length > 0) {
        var orderItemsArray = serializeOrderItems();
        console.log(orderItemsArray);
        var form_data = $('#inventory_entry').serializeArray();
        var tag_img = $('#tag_img').attr("data-img");
        var dataObject = {};
        // Add form_data to the object
        form_data.forEach(function (item) {
            dataObject[item.name] = item.value;
        });
        // Add tag_img to the object
        dataObject['tag_img'] = tag_img;
        // Use a different name for the order items array
        dataObject['order_items_array'] = orderItemsArray;
        console.log(dataObject['order_items_array']);
        $('#inventory_submit').prop('disabled', true);

        my_Date = new Date();
        var url = base_url + "index.php/admin_ret_other_inventory/purchase_entry/save?nocache=" + my_Date.getUTCSeconds();
        $.ajax({
            url: url,
            data: dataObject,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                if (data.status) {

                    $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });

                    $("div.overlay").css("display", "none");

                    // window.location.reload();


                    window.open(base_url + 'index.php/admin_ret_other_inventory/purchase_entry/purchase_details/' + data.otr_inv_pur_id + '?nocache=' + my_Date.getUTCSeconds(), '_blank');
                    window.location.href = base_url + 'index.php/admin_ret_other_inventory/purchase_entry/list';
                }
                else {
                    $('#inventory_submit').prop('disabled', false);
                    $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
                    $("div.overlay").css("display", "none");
                }
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
    }
    else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>No Record Found" });
    }
});
$('#purchase_item_search').on('click', function () {
    get_other_inventory_purchase_items();
});
function get_other_inventory_purchase_items() {

    var company_name = $('#company_name').val();
    var from_date = $('#from_date').html();
    var to_date = $('#to_date').html();
    var branch_name = ($('#id_branch').val() != '' && $('#id_branch').val() != undefined ? $('#id_branch').val() : $("#branch_select option:selected").text());
    var report_name = "Purchase Entry Report";
    var optional = '';
    var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"
        + "<span>" + report_print(branch_name, report_name, from_date, to_date, optional) + "</span>";
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/purchase_entry/ajax?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            data: { "from_date": $('#from_date').html(), "to_date": $('#to_date').html(), "id_karigar": $("#select_karigar").val() },
            success: function (data) {
                //console.log(data);
                var item = data.list;
                var access = data.access;
                console.log(item);
                if (access.add == '0') {
                    $('#add_pur_details').attr('disabled', 'disabled');
                }
                var oTable = $('#other_item_pur').DataTable();
                oTable.clear().draw();
                if (item != null && item.length > 0) {
                    oTable = $('#other_item_pur').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,
                        "scrollX": '100%',
                        "bSort": true,
                        "dom": 'lBfrtip',
                        "order": [[0, "desc"]],
                        "columnDefs": [

                            {
                                targets: [7, 8, 9, 10],
                                className: 'dt-body-right'
                            },
                        ],
                        "buttons": [
                            {
                                extend: 'print',
                                footer: true,
                                title: '',
                                messageTop: title,
                                customize: function (win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', '10px');
                                },
                            },
                            {
                                extend: 'excel',
                                footer: true,
                                title: "Purchase Entry Report",
                            }
                        ],
                        "aaData": item,
                        "aoColumns": [
                            { "mDataProp": "otr_inven_pur_id" },
                            { "mDataProp": "supplier_name" },
                            {
                                "mDataProp": function (row, type, val, meta) {

                                    if (row.image_details[0] != '' && row.image_details[0] != null) {

                                        var img_src = base_url + 'assets/img/purchase_entry/' + row.image_details[0].image;
                                    }
                                    else {
                                        var img_src = base_url + 'assets/img/no_image.png';
                                    }
                                    // var image = "<img  class='img_src' id='img_prev' src="+img_src+"  width='35' height='35' onClick=tag_imgprev($(this).closest('tr')) ></img>"
                                    var image = '<img src=' + img_src + ' width="50" height="55"><br><a class="btn btn-secondary order_img"  id="edit" data-toggle="modal" data-id=' + row.otr_inven_pur_id + '><i class="fa fa-eye" ></i></a>'
                                    return image;
                                }
                            },
                            { "mDataProp": "entry_date" },
                            { "mDataProp": "pur_order_ref_no" },
                            { "mDataProp": "supplier_order_ref_no" },
                            { "mDataProp": "supplier_bill_date" },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india(parseFloat(row.tot_pcs).toFixed(0));
                                },
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india(parseFloat(row.no_of_pcs).toFixed(0));
                                },
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india(parseFloat(row.balance).toFixed(0));
                                },
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india(parseFloat(row.tot_amount).toFixed(2));
                                },
                            },
                            { "mDataProp": "bill_status" },

                            {
                                "mDataProp": null,
                                "sClass": "control center",
                                "sDefaultContent": '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    id = row.otr_inven_pur_id

                                    print_url = (access.edit == '1' ? base_url + 'index.php/admin_ret_other_inventory/purchase_entry/purchase_details/' + id : '#');

                                    // action_content='<a href="'+print_url+'"target="_blank"  class="btn btn-info btn-print"><i class="fa fa-print" ></i></a>';
                                    action_content = '<a href="' + print_url + '" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip"><i class="fa fa-print" ></i></a>' + (row.purchase_bill_status == 1 && row.no_of_pcs == 0  && access.delete =='1' ? '<button class="btn btn-warning" onclick="confirm_delete(' + id + ')"><i class="fa fa-close" ></i></button>' : '');

                                    return action_content;

                                }
                            },
                        ],
                        "footerCallback": function (row, data, start, end, display) {
                            if (item.length > 0) {
                                var api = this.api(), data;
                                for (var i = 0; i <= data.length - 1; i++) {
                                    var intVal = function (i) {
                                        return typeof i === 'string' ?
                                            i.replace(/[\$,]/g, '') * 1 :
                                            typeof i === 'number' ?
                                                i : 0;
                                    };
                                    $(api.column(0).footer()).html('Total');
                                    tot_pcs = api
                                        .column(7)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(7).footer()).html(money_format_india(parseFloat(tot_pcs).toFixed(0)));
                                    no_of_pcs = api
                                        .column(8)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(8).footer()).html(money_format_india(parseFloat(no_of_pcs).toFixed(0)));
                                    balance = api
                                        .column(9)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(9).footer()).html(money_format_india(parseFloat(balance).toFixed(0)));
                                    total_amount = api
                                        .column(10)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(10).footer()).html(money_format_india(parseFloat(total_amount).toFixed(2)));

                                }
                            }
                            else {
                                var api = this.api(), data;
                                $(api.column(8).footer()).html('');
                                $(api.column(9).footer()).html('');
                                $(api.column(10).footer()).html('');
                                $(api.column(11).footer()).html('');
                            }
                        }

                    });


                    var anOpen = [];
                    $(document).on('click', "#other_item_pur .control", function () {
                        var nTr = this.parentNode;
                        var i = $.inArray(nTr, anOpen);
                        if (i === -1) {
                            $('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');
                            oTable.fnOpen(nTr, fnFormatRowpurchaseDetails(oTable, nTr), 'details');
                            anOpen.push(nTr);
                        }
                        else {
                            $('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
                            oTable.fnClose(nTr);
                            anOpen.splice(i, 1);
                        }
                    });

                }
                $("div.overlay").css("display", "none");
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
function confirm_delete(otr_inven_pur_id) {
    $('#otr_inven_pur_id').val(otr_inven_pur_id);
    $('#confirm-purchase-delete').modal('show');
}
$('#cancel_remark').on('keypress', function () {
    if (this.value.length > 6) {
        $('#purchase_cancel').prop('disabled', false);
    } else {
        $('#purchase_cancel').prop('disabled', true);
    }
});
$('#purchase_cancel').on('click', function () {
    $('#purchase_cancel').prop('disabled', true);
    my_Date = new Date();
    $.ajax({
        type: 'POST',
        url: base_url + "index.php/admin_ret_other_inventory/purchase_entry/cancel_purchase_entry?nocache=" + my_Date.getUTCSeconds(),
        dataType: 'json',
        data: { 'cancel_reason': $('#cancel_remark').val(), 'otr_inven_pur_id': $('#otr_inven_pur_id').val() },
        success: function (data) {
            window.location.reload();
        }
    });
});
function fnFormatRowpurchaseDetails(oTable, nTr) {
    var oData = oTable.fnGetData(nTr);
    var rowDetail = '';
    var prodTable =
        '<div class="innerDetails">' +
        '<table class="table table-responsive table-bordered text-center table-sm">' +
        '<tr class="bg-teal">' +
        '<th>S.No</th>' +
        '<th>Product</th>' +
        '<th>Pcs</th>' +
        '<th>Rate</th>' +
        '<th>Amount</th>' +
        '</tr>';
    var pur_details = oData.pur_details;
    console.log(pur_details);
    if (pur_details.length > 0) {
        $.each(pur_details, function (idx, val) {
            //   branch_summary_url=base_url+'index.php/admin_ret_lot/branch_acknowladgement/1/'+val.tag_lot_id+'/'+val.current_branch;
            //   branch_url=base_url+'index.php/admin_ret_lot/branch_acknowladgement/2/'+val.tag_lot_id+'/'+val.current_branch;
            prodTable +=
                '<tr class="prod_det_btn">' +
                '<td>' + parseFloat(idx + 1) + '</td>' +
                '<td>' + val.product_name + '</td>' +

                '<td>' + money_format_india(parseFloat(val.tot_pcs).toFixed(0)) + '</td>' +
                '<td>' + money_format_india(parseFloat(val.tot_rate).toFixed(2)) + '</td>' +
                '<td>' + money_format_india(parseFloat(val.tot_amount).toFixed(2)) + '</td>' +
                // '<td><a href="'+branch_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Branch Copy Detailed"><i class="fa fa-print" ></i></a><a href="'+branch_summary_url+'" target="_blank" class="btn btn-secondary btn-print" data-toggle="tooltip" title="Branch Copy Summary"><i class="fa fa-print" ></i></a></td>'+
                '</tr>';
        });
    }
    rowDetail = prodTable + '</table></div>';
    return rowDetail;
}
$(document).on('click', "#other_item_pur a.order_img", function (e) {
    e.preventDefault();
    id = $(this).data('id');
    $("#edit-id").val(id);
    view_dup_tag_history_imgs(id);
});
$("#imageModal_bulk_edit").on("hidden.bs.modal", function () {
    $('#order_images').empty();
});
function view_dup_tag_history_imgs(order_id_img1) {
    update_tag_img_id1 = order_id_img1;
    data = [];
    var tag_codeimage1 = base_url + 'assets/img/purchase_entry';
    $('#imageModal_bulk_edit').modal('show');
    $(".overlay").css('display', "none");
    $.ajax({
        data: ({ 'item_id': order_id_img1 }),
        url: base_url + "index.php/admin_ret_other_inventory/get_img_by_item_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            retrive_img = data;
            for (i = 0; i < data.length; i++) {
                img_src = data[i].image;
                var preview = $('#order_images');
                var img = tag_codeimage1 + '/' + img_src;
                if (img_src) {
                    div = document.createElement("div");
                    div.setAttribute('class', 'col-md-3 images');
                    div.setAttribute('id', 'order_img_edit_' + [i]);

                    $('.images').css('margin-right', '25px');
                    key = [i];
                    param = img_src;

                    div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><img class='thumbnail' src='" + img + "'" + "style='width: 300px;height: 250px;'/><a href=" + img + " download=" + img_src + "><i class='fa fa-download btn btn-success'>Download</i></div></div></a>";
                    preview.append(div);

                }
            }

        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function get_other_inventory_product_details() {
    var company_name = $('#company_name').val();
    var branch_name = ($('#id_branch').val() != '' && $('#id_branch').val() != undefined ? $('#id_branch').val() : $("#branch_select option:selected").text());
    var report_name = "Tagging Report";
    var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"
        + "<span>" + report_print(branch_name, report_name, '', '', '') + "</span>";
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/product_details/ajax?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            // data:{"from_date":$('#from_date').html(),"to_date":$('#to_date').html()},
            success: function (data) {
                //console.log(data);
                var item = data.list;
                var access = data.access;
                console.log(item);
                if (access.add == '0') {
                    $('#add_pro_details').attr('disabled', 'disabled');
                }
                var oTable = $('#other_item_product').DataTable();
                oTable.clear().draw();
                if (item != null && item.length > 0) {
                    oTable = $('#other_item_product').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,

                        "scrollX": '100%',
                        "bSort": true,

                        "dom": 'lBfrtip',
                        "order": [[0, "desc"]],
                        "columnDefs": [
                            {
                                targets: [3],
                                className: 'dt-body-right'
                            },
                        ],

                        "buttons": [
                            {
                                extend: 'print',
                                footer: true,
                                title: "",
                                messageTop: title,
                                customize: function (win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                },
                            },
                            {
                                extend: 'excel',
                                footer: true,
                                title: "Tagging Report",
                            }
                        ],
                        "aaData": item,
                        "aoColumns": [
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    var url = base_url + 'index.php/admin_ret_other_inventory/get_pro_detail_list/' + row.otr_inven_pur_id;

                                    return '<a href=' + url + ' target="_blank">' + row.otr_inven_pur_id + '</a>';

                                },

                            },
                            { "mDataProp": "ref_no" },
                            { "mDataProp": "entry_date" },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india(parseFloat(row.no_of_pcs).toFixed(0));
                                },
                            },
                        ],
                        "footerCallback": function (row, data, start, end, display) {
                            if (item.length > 0) {
                                var api = this.api(), data;
                                for (var i = 0; i <= data.length - 1; i++) {
                                    var intVal = function (i) {
                                        return typeof i === 'string' ?
                                            i.replace(/[\$,]/g, '') * 1 :
                                            typeof i === 'number' ?
                                                i : 0;
                                    };
                                    $(api.column(1).footer()).html('Total');
                                    pcs = api
                                        .column(3)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(3).footer()).html(parseFloat(pcs).toFixed(0));
                                }
                            } else {
                                var api = this.api(), data;
                                $(api.column(3).footer()).html('');
                            }
                        }
                    });
                }
                $("div.overlay").css("display", "none");
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
function get_other_product_details() {
    my_Date = new Date();
    var other_item_id = ctrl_page[2];
    $("div.overlay").css("display", "block");
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/product_tag_detail?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            data: { 'other_inv_item': other_item_id },
            success: function (data) {
                //console.log(data);
                var list = data.list;
                var access = data.access;

                console.log(list);
                var oTable = $('#other_inventory_product').DataTable();
                oTable.clear().draw();
                if (list != null) {
                    oTable = $('#other_inventory_product').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,
                        "bSort": true,
                        "order": [[0, "desc"]],
                        "columnDefs": [
                            {
                                targets: [3, 4],
                                className: 'dt-body-right'
                            },
                        ],
                        "aaData": list,
                        "aoColumns": [
                            { "mDataProp": "pur_item_detail_id" },
                            { "mDataProp": "item_ref_no" },

                            { "mDataProp": "item_name" },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india(parseFloat(row.piece).toFixed(0));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india(parseFloat(row.amount).toFixed(2));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    id = row.otr_inven_pur_id

                                    print_url = (id != '' ? base_url + 'index.php/admin_ret_other_inventory/other_inventory_print/' + row.item_ref_no : '#');

                                    action_content = '<a href="' + print_url + '"target="_blank"  class="btn btn-info btn-print"><i class="fa fa-print" ></i></a>';

                                    return action_content;

                                }

                            }],
                        "footerCallback": function (row, data, start, end, display) {
                            if (list.length > 0) {
                                var api = this.api(), data;
                                for (var i = 0; i <= data.length - 1; i++) {
                                    var intVal = function (i) {
                                        return typeof i === 'string' ?
                                            i.replace(/[\$,]/g, '') * 1 :
                                            typeof i === 'number' ?
                                                i : 0;
                                    };
                                    $(api.column(1).footer()).html('Total');
                                    piece = api
                                        .column(3)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(3).footer()).html(money_format_india(parseFloat(piece).toFixed(0)));
                                    amount = api
                                        .column(4)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(4).footer()).html(money_format_india(parseFloat(amount).toFixed(2)));
                                }
                            } else {
                                var api = this.api(), data;
                                $(api.column(3).footer()).html('');
                                $(api.column(4).footer()).html('');
                            }
                        }
                    });
                }
                $("div.overlay").css("display", "none");
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
function calculate_grand_total() {
    var grand_total_quantity = 0;
    var grand_total_amount = 0;
    var grand_total_gst = 0;

    $('#pur_details > tbody tr').each(function (idx, row) {
        curRow = $(this);
        var quantity = parseFloat(curRow.find('.quantity').val()) || 0;
        var amount = parseFloat(curRow.find('.amount').val()) || 0;
        var gst = parseFloat(curRow.find('.gst_amount').val()) || 0;
        grand_total_quantity += quantity;
        grand_total_amount += amount;
        grand_total_gst += gst;
    });
    $('.pur_quantity').html(grand_total_quantity);
    $('.pur_amount').html(grand_total_amount.toFixed(2));
    $('.pur_gst_amount').html(grand_total_gst.toFixed(2));
}
//Purchase Entry
//Stock Details
$('#stock_details_search').on('click', function () {
    get_other_inventory_stock_details();
});
function get_other_inventory_stock_details() {
    var company_name = $('#company_name').val();
    var from_date = $("#from_date").html();
    var to_date = $("#to_date").html();
    var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());
    var report_name = "Stock In & Out Report";
    var optional = $('#select_type option:selected').html() != '' && $('#select_type option:selected').html() != undefined ? $('#select_type option:selected').html() + " - " : '';
    var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"
        + "<span>" + report_print(branch_name, report_name, from_date, to_date, optional) + "</span>";
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/stock_details/ajax?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            data: { "from_date": $('#from_date').html(), "to_date": $('#to_date').html(), 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val()), "id_other_item": "", 'id_other_item_type': $('#select_type').val() },
            success: function (data) {
                var oTable = $('#stock_details').DataTable();
                oTable.clear().draw();
                if (data != null && data.length > 0) {
                    oTable = $('#stock_details').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,
                        "order": [[0, "desc"]],
                        "scrollX": '100%',
                        "bSort": true,
                        "dom": 'lBfrtip',
                        "columnDefs": [
                            {
                                targets: [2, 3, 4, 5, 6, 7, 8, 9],
                                className: 'dt-body-right'
                            },
                        ],
                        "buttons": [
                            {
                                extend: 'print',
                                footer: true,
                                title: '',
                                messageTop: title,
                                customize: function (win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', '10px');
                                },
                            },
                            {
                                extend: 'excel',
                                footer: true,
                                title: "Stock In & Out Report",
                            }
                        ],
                        "aaData": data,
                        "aoColumns": [
                            { "mDataProp": "item_name" },
                            { "mDataProp": "type_name" },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.op_blc_pcs).toFixed(0)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.op_blc_amt).toFixed(2)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.inw_pcs).toFixed(0)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.inw_amount).toFixed(2)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.out_pcs).toFixed(0)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.out_amount).toFixed(2)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.closing_pcs).toFixed(0)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.closing_amt).toFixed(2)));
                                }
                            },
                        ],
                        "footerCallback": function (row, data, start, end, display) {
                            if (data.length > 0) {
                                var api = this.api(), data;
                                for (var i = 0; i <= data.length - 1; i++) {
                                    var intVal = function (i) {
                                        return typeof i === 'string' ?
                                            i.replace(/[\$,]/g, '') * 1 :
                                            typeof i === 'number' ?
                                                i : 0;
                                    };
                                    $(api.column(0).footer()).html('Total');
                                    op_blc_pcs = api
                                        .column(2)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(2).footer()).html(money_format_india(parseFloat(op_blc_pcs).toFixed(0)));
                                    op_blc_amt = api
                                        .column(3)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(3).footer()).html(money_format_india(parseFloat(op_blc_amt).toFixed(2)));
                                    inw_pcs = api
                                        .column(4)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(4).footer()).html(money_format_india(parseFloat(inw_pcs).toFixed(0)));
                                    inw_amt = api
                                        .column(5)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(5).footer()).html(money_format_india(parseFloat(inw_amt).toFixed(2)));
                                    out_pcs = api
                                        .column(6)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(6).footer()).html(money_format_india(parseFloat(out_pcs).toFixed(0)));
                                    out_amt = api
                                        .column(7)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(7).footer()).html(money_format_india(parseFloat(out_amt).toFixed(2)));
                                    cls_pcs = api
                                        .column(8)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);
                                    $(api.column(8).footer()).html(money_format_india(parseFloat(cls_pcs).toFixed(0)));
                                    cls_amt = api
                                        .column(9)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);

                                    $(api.column(9).footer()).html(money_format_india(parseFloat(cls_amt).toFixed(2)));

                                }
                            } else {
                                var api = this.api(), data;
                                $(api.column(3).footer()).html('');
                                $(api.column(4).footer()).html('');
                                $(api.column(5).footer()).html('');
                                $(api.column(6).footer()).html('');
                                $(api.column(7).footer()).html('');
                                $(api.column(8).footer()).html('');
                                $(api.column(9).footer()).html('');
                            }
                        }
                    });
                }
                $("div.overlay").css("display", "none");
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
//Stock Details
//Item Issue
$('#branch_select').on('change', function () {
    if (this.value != '' && this.value != null && this.value != 0) {
        $('#id_branch').val(this.value);
        if (ctrl_page[1] != 'available_stock') {
            get_invnetory_item();
            get_bill_details();
        }
    }
});
$('#select_karigar').on('change', function () {
    var state = $("#select_karigar option:selected").data("kar_state");
    if (this.value != '' && this.value != null && this.value != 0) {

        $('#kar_state').val(state);


    }

});

function get_invnetory_item() {
    $('#select_item option').remove();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/get_invnetory_item',
        dataType: 'json',
        data: { "id_branch": $('#id_branch').val() },
        success: function (data) {
            other_inventory_item = data;
            var id = $("#select_item").val();
            $.each(data, function (key, item) {
                $("#select_item").append(
                    $("<option></option>")
                        .attr("value", item.id_other_item)
                        .text(item.item_name)
                );
            });

            $("#select_item").select2(
                {
                    placeholder: "Select Item",
                    allowClear: true
                });

            $("#select_item").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
function get_invnetory_item_list() {
    $('#select_item option').remove();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/other_inventory',
        dataType: 'json',
        success: function (data) {
            other_inventory_item = data.list;
            var id = $("#select_item").val();
            $.each(other_inventory_item, function (key, item) {
                $("#select_item").append(
                    $("<option></option>")
                        .attr("value", item.id_other_item)
                        .text(item.item_name)
                );
            });

            $("#select_item").select2(
                {
                    placeholder: "Select Item",
                    allowClear: true
                });

            $("#select_item").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
function get_bill_details() {
    $('#select_bill_no option').remove();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/get_bill_details',
        dataType: 'json',
        data: { "id_branch": $('#id_branch').val() },
        success: function (data) {
            var id = $("#select_bill_no").val();
            $.each(data, function (key, item) {
                $("#select_bill_no").append(
                    $("<option></option>")
                        .attr("value", item.bill_id)
                        .text(item.cus_bill_no)
                );
            });

            $("#select_bill_no").select2(
                {
                    placeholder: "Select Bill No",
                    allowClear: true
                });

            $("#select_bill_no").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
function get_customer() {
    $('#select_item option').remove();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_ret_other_inventory/get_customer',
        dataType: 'json',
        data: { "id_branch": $('#branch_select').val() },
        success: function (data) {
            var id = $("#id_uom").val();
            $.each(data, function (key, item) {
                $("#select_customer").append(
                    $("<option></option>")
                        .attr("value", item.id_customer)
                        .text(item.cus_name)
                );
            });

            $("#select_customer").select2(
                {
                    placeholder: "Select Customer",
                    allowClear: true
                });

            $("#select_customer").select2("val", (id != '' && id > 0 ? id : ''));
        }
    });
}
$('#item_issue').on('click', function () {
    if ($('#id_branch').val() == '' || $('#id_branch').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Select Branch.." });
    }
    else if ($('#select_item').val() == '' || $('#select_item').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Select Branch.." });
    }
    else if ($('#remarks').val() == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The Remarks.." });
    }
    else {
        inventory_item_issue();
    }
});
function inventory_item_issue() {
    $("div.overlay").css("display", "block");
    var form_data = $('#inventory_issue').serialize();
    $('#item_issue').prop('disabled', true);
    my_Date = new Date();
    var url = base_url + "index.php/admin_ret_other_inventory/issue_item/save?nocache=" + my_Date.getUTCSeconds();
    $.ajax({
        url: url,
        data: form_data,
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            if (data.status) {
                $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
                $("div.overlay").css("display", "none");
                $('#item_issue').prop('disabled', false);
                window.location.reload();
            }
            else {
                $('#item_issue').prop('disabled', false);
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
                $("div.overlay").css("display", "none");
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
$('#issue_total_pcs').on('keyup', function () {
    $.each(other_inventory_item, function (key, items) {
        if (items.id_other_item == $('#select_item').val()) {
            var available_pcs = items.tot_pcs;
            var item_total_pcs = $('#issue_total_pcs').val();
            if (parseFloat(available_pcs) < item_total_pcs) {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Available Pieces is " + items.tot_pcs });
                $('#issue_total_pcs').val("");
            }
        }
    });
});
$('#search_issue_item').on('click', function () {
    get_other_inventory_item_issue_details();
});
function get_other_inventory_item_issue_details() {
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/issue_item/ajax?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            data: { "from_date": $('#from_date').html(), "to_date": $('#to_date').html(), 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $(".branch_filter").val()), "id_other_item": "" },
            success: function (data) {
                var oTable = $('#issue_list').DataTable();
                oTable.clear().draw();
                if (data != null && data.length > 0) {
                    oTable = $('#issue_list').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,
                        "bSort": true,
                        "order": [[0, "desc"]],
                        "aaData": data,
                        "aoColumns": [
                            { "mDataProp": "id_inventory_issue" },
                            { "mDataProp": "branch_name" },
                            { "mDataProp": "item_name" },
                            { "mDataProp": "issue_date" },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    var url = base_url + 'index.php/admin_ret_billing/billing_invoice/' + row.bill_id;
                                    return '<a href=' + url + ' target="_blank">' + row.bill_no + '</a>';
                                },
                            },
                            { "mDataProp": "cus_name" },
                            { "mDataProp": "no_of_pieces" },
                            { "mDataProp": "approx_amt" },
                            { "mDataProp": "given_by" },
                            { "mDataProp": "remarks" },
                        ],
                        "footerCallback": function (row, data, start, end, display) {
                            if (data.length > 0) {
                                var api = this.api(), data;
                                for (var i = 0; i <= data.length - 1; i++) {
                                    var intVal = function (i) {
                                        return typeof i === 'string' ?
                                            i.replace(/[\$,]/g, '') * 1 :
                                            typeof i === 'number' ?
                                                i : 0;
                                    };

                                    $(api.column(0).footer()).html('Total');
                                    total_pcs = api
                                        .column(6)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);

                                    $(api.column(6).footer()).html(parseFloat(total_pcs).toFixed(0));

                                    total_amt = api
                                        .column(7)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);

                                    $(api.column(7).footer()).html(parseFloat(total_amt).toFixed(2));
                                }
                            } else {
                                var api = this.api(), data;
                                $(api.column(6).footer()).html('');
                                $(api.column(7).footer()).html('');
                                $(api.column(8).footer()).html('');
                            }
                        }
                    });
                }
                $("div.overlay").css("display", "none");
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
//Item Issue
//size master
/* $('#add_new_item_size').on('click', function () {

    fetchAllSizes();

    if ($('#size_name').val() == '' || $('#size_name').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The Size.." });
        allow_submit = false;
    }
    if (isDuplicateSize($('#size_name').val())) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Size already exists. Please enter a different size', "timeOut": "5000"});
        allow_submit = false;
    } else {
        $('#add_new_item_size').prop('disabled', true);
        my_Date = new Date();
        $.ajax(
            {
                url: base_url + "index.php/admin_ret_other_inventory/item_size/save?nocache=" + my_Date.getUTCSeconds(),
                dataType: "JSON",
                type: "POST",
                data: { "size_name": $('#size_name').val() },
                success: function (data) {

                    if (data.status) {
                        $('#size_name').val('');
                        $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
                        $('#add_new_item_size').prop('disabled', false);
                        set_packing_item_size_list();
                    }
                    else {
                        $('#size_name').val('');
                        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
                        $('#add_new_item_size').prop('disabled', false);
                    }
                },
                error: function (error) {
                    $("div.overlay").css("display", "none");
                }
            });
    }
}); */

$('#add_item_size').on('click', function () {

    // fetchAllSizes();

    let allow_submit = true;

    if ($('#size_name').val() == '' || $('#size_name').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The Size.." });
        allow_submit = false;
        $('#add_item_size').prop('disabled', false);
    }
    if (isDuplicateSize($('#size_name').val().trim())) {

        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Size already exists. Please enter a different size', "timeOut": "5000" });
        allow_submit = false;
        $('#add_item_size').prop('disabled', false);
    }
    if (allow_submit) {
        $('#add_item_size').prop('disabled', true);
        my_Date = new Date();
        $.ajax(
            {
                url: base_url + "index.php/admin_ret_other_inventory/item_size/save?nocache=" + my_Date.getUTCSeconds(),
                dataType: "JSON",
                type: "POST",
                data: { "size_name": $('#size_name').val().trim() },
                success: function (data) {

                    if (data.status) {
                        $('#confirm-add').modal('toggle');
                        $('#size_name').val('');
                        $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
                        $('#add_item_size').prop('disabled', false);
                        set_packing_item_size_list();
                    }
                    else {
                        $('#size_name').val('');
                        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
                        $('#add_item_size').prop('disabled', false);
                    }
                },
                error: function (error) {
                    $("div.overlay").css("display", "none");
                }
            });
    }
});
function set_packing_item_size_list() {
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/item_size/ajax?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "GET",
            success: function (data) {
                var list = data.list
                var access = data.access
                var oTable = $('#size_list').DataTable();
                oTable.clear().draw();
                if (list != null && list.length > 0) {
                    oTable = $('#size_list').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,
                        "bSort": true,
                        "order": [[0, "desc"]],
                        "aaData": list,
                        "aoColumns": [
                            { "mDataProp": "id_inv_size" },
                            { "mDataProp": "size_name" },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    active_url = (access.edit == "1") ? base_url + "index.php/admin_ret_other_inventory/packaging_item_size_status/" + (row.status == 1 ? 0 : 1) + "/" + row.id_inv_size : '#';
                                    return "<a href='" + active_url + "'><i class='fa " + (row.status == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.status == 1 ? 'green' : 'red') + "'></i></a>"
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    id = row.id_inv_size
                                    edit_target = (access.edit == '0' ? "" : "#confirm-edit");
                                    delete_url = (access.delete == '1' ? base_url + 'index.php/admin_ret_other_inventory/item_size/delete/' + id : '#');
                                    delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                                    // action_content = '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';
                                    var action_content = "";
                                    if (access.edit == "1") {
                                        action_content +=
                                            '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i></a>';
                                    }
                                    if (access.delete == "1") {
                                        action_content +=
                                            ' <a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';
                                    }

                                    return action_content;
                                }
                            }
                        ],
                    });
                }
                $("div.overlay").css("display", "none");
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
$(document).on('click', "#size_list a.btn-edit", function (e) {
    $("#id_inv_size").val('');
    e.preventDefault();
    id = $(this).data('id');
    get_packaging_size(id);
    $("#edit-id").val(id);
});
function get_packaging_size(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/admin_ret_other_inventory/item_size/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            $('#ed_size_name').val(data.size_name);
            $('#id_inv_size').val(data.id_inv_size);
        }
    });
}
$('#update_size').on('click', function () {
    if ($('#ed_size_name').val() == '' || $('#ed_size_name').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The Size.." });
        allow_submit = false;
    }
    else {
        $('#update_size').prop('disabled', true);
        my_Date = new Date();
        $.ajax(
            {
                url: base_url + "index.php/admin_ret_other_inventory/item_size/update?nocache=" + my_Date.getUTCSeconds(),
                dataType: "JSON",
                type: "POST",
                data: { "size_name": $('#ed_size_name').val(), 'id_inv_size': $("#id_inv_size").val() },
                success: function (data) {

                    if (data.status) {
                        $('#confirm-edit').modal('toggle');
                        $('#ed_size_name').val('');
                        $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
                        $('#update_size').prop('disabled', false);
                        set_packing_item_size_list();
                    }
                    else {
                        $('#ed_size_name').val('');
                        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
                        $('#update_size').prop('disabled', false);
                    }
                },
                error: function (error) {
                    $("div.overlay").css("display", "none");
                }
            });
    }
});
//size master
//Available Stock 
$('#avail_stock_details_search').on('click', function () {
    available_stock_details();
});
function report_print(branch_name, report_name, from_date, to_date, optional) {
    if (branch_name == '') {
        branch_name = "ALL"
    }
    var data = "<span>" + report_name + " - " + branch_name + "</span></br>"
        + "<span>" + (optional != '' ? optional : '') + "</span>"
        + (from_date != '' && to_date != '' ? "<span>FROM&nbsp;:&nbsp;" + from_date + " &nbsp;&nbsp;TO&nbsp;&nbsp; " + to_date + "</span></br> " : '')
        + $('.hidden-xs').html() + " &nbsp; - &nbsp;" + "</span>" + "<span style='font-size:11pt;'>" + getDisplayDateTime() + "</span></br>";
    return data;
}
function getDisplayDateTime() {
    var today = new Date();
    var dispdate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();
    var disptime = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    return dispdate + " " + disptime;
}
function available_stock_details() {
    $("div.overlay").css("display", "block");
    var company_name = $('#company_name').val();
    var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());
    var report_name = 'AVAILABLE PRODUCT WISE STOCK REPORT';
    var prod_select = $('#select_item option:selected').html() != '' && $('#select_item option:selected').html() != undefined ? $('#select_item option:selected').html() + ' - ' : '';
    var optional = prod_select;
    var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"
        + "<span>" + report_print(branch_name, report_name, '', '', optional) + "</span>";
    my_Date = new Date();
    $.ajax(
        {
            url: base_url + "index.php/admin_ret_other_inventory/available_stock/ajax?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            data: { 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val()), "id_size": $('#select_size').val(), 'id_other_item': $('#select_item').val(), 'id_other_item_type': $('#select_type').val() },
            success: function (data) {
                var oTable = $('#stock_details').DataTable();
                oTable.clear().draw();
                if (data != null && data.length > 0) {
                    oTable = $('#stock_details').dataTable({
                        "bDestroy": true,
                        "bInfo": true,
                        "bFilter": true,
                        "order": [[0, "desc"]],
                        "scrollX": '100%',
                        "bSort": true,
                        "dom": 'lBfrtip',
                        "columnDefs": [
                            {
                                targets: [4, 5],
                                className: 'dt-body-right'
                            },
                        ],
                        "buttons": [
                            {
                                extend: 'print',
                                footer: true,
                                title: '',
                                messageTop: title,
                                customize: function (win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                },
                            },

                            {
                                extend: 'excel',
                                footer: true,
                                title: "AVAILABLE PRODUCT WISE STOCK REPORT",
                            }
                        ],
                        "aaData": data,
                        "aoColumns": [
                            { "mDataProp": "branch_name" },
                            { "mDataProp": "item_name" },
                            { "mDataProp": "type_name" },
                            { "mDataProp": "size_name" },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.tot_pcs).toFixed(0)));
                                }
                            },
                            {
                                "mDataProp": function (row, type, val, meta) {
                                    return money_format_india((parseFloat(row.tot_amount).toFixed(2)));
                                }
                            },
                        ],
                        "footerCallback": function (row, data, start, end, display) {
                            if (data.length > 0) {
                                var api = this.api(), data;
                                for (var i = 0; i <= data.length - 1; i++) {
                                    var intVal = function (i) {
                                        return typeof i === 'string' ?
                                            i.replace(/[\$,]/g, '') * 1 :
                                            typeof i === 'number' ?
                                                i : 0;
                                    };

                                    $(api.column(0).footer()).html('Total');
                                    total_pcs = api
                                        .column(4)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);

                                    $(api.column(4).footer()).html(money_format_india(parseFloat(total_pcs).toFixed(0)));

                                    total_amt = api
                                        .column(5)
                                        .data()
                                        .reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);

                                    $(api.column(5).footer()).html(money_format_india(parseFloat(total_amt).toFixed(2)));
                                }
                            } else {
                                var api = this.api(), data;
                                $(api.column(4).footer()).html('');
                                $(api.column(5).footer()).html('');
                            }
                        }
                    });
                }
                $("div.overlay").css("display", "none");
            },
            error: function (error) {
                $("div.overlay").css("display", "none");
            }
        });
}
//Available Stock 
//Product Mapping
function get_ActiveProduct() {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + "index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),
        dataType: 'json',
        success: function (data) {
            var id = $("#select_product").val();

            $("#select_product").append($("<option></option>").attr("value", 0).text("All"));

            $.each(data, function (key, item) {
                $("#select_product,#prod_filter").append(
                    $("<option></option>")
                        .attr("value", item.pro_id)
                        .text(item.product_name)
                );
            });

            $("#select_product,#prod_filter").select2(
                {
                    placeholder: "Select Product",
                    allowClear: false
                });

            $("#select_product").select2("val", (id != '' && id > 0 ? id : ''));

            if ($('#prod_filter').length) {
                $("#prod_filter").select2("val", "");
            }
        }
    });
}
$('#update_product_mapping').on('click', function () {
    if ($('#select_product').val() == '' || $('#select_product').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Product..' });
        $("#update_product_mapping").prop('disabled', false);
    } else if ($('#select_item').val() == '' || $('#select_item').val() == null) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Item..' });
        $("#update_product_mapping").prop('disabled', false);
    }
    else {
        update_product_mapping();
    }
});
function update_product_mapping() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_other_inventory/update_product_mapping?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
        data: { 'id_product': $('#select_product').val(), 'id_other_item': $('#select_item').val() },
        type: "POST",
        dataType: "json",
        async: false,
        success: function (data) {
            if (data.status) {
                $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.msg });
                get_product_mapping_details();
            }
            else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.msg });

            }
            $('#select_item').select2("val", "");
            $('#select_product').select2("val", "");
            $('#update_product_mapping').prop('disabled', false);
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
$('#search_design_maping').on('click', function () {
    get_product_mapping_details();
});
function get_product_mapping_details() {
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_other_inventory/product_mapping?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        data: { 'id_product': $('#prod_filter').val(), 'id_other_item': $('#item_filter').val() },
        dataType: 'json',
        cache: false,
        success: function (data) {
            var list = data.list;
            var access = data.access;

            var oTable = $('#mapping_list').DataTable();
            oTable.clear().draw();
            if (list != null && list.length > 0) {
                oTable = $('#mapping_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "order": [[0, "desc"]],
                    "aaData": list,
                    "aoColumns": [
                        {
                            "mDataProp": function (row, type, val, meta) {
                                chekbox = '<input type="checkbox" class="inv_des_id" name="inv_des_id[]" value="' + row.inv_des_id + '"/>'
                                return chekbox + " " + row.inv_des_id;
                            }
                        },
                        { "mDataProp": "product_name" },
                        { "mDataProp": "item_name" },
                        { "mDataProp": "size_name" },
                    ]
                });
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
$("#delete_product_mapping").on('click', function () {
    if ($("input[name='inv_des_id[]']:checked").val()) {
        var selected = [];
        var approve = false;
        $("#mapping_list tbody tr").each(function (index, value) {
            if ($(value).find("input[name='inv_des_id[]']:checked").is(":checked")) {
                transData = {
                    'inv_des_id': $(value).find(".inv_des_id").val(),
                }
                selected.push(transData);
            }
        })
        req_data = selected;
        delete_product_mapping(req_data);
    }
    else {
        $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>Please Select Item" });
    }
});

function delete_product_mapping(data = "") {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_ret_other_inventory/delete_product_mapping?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
        data: { 'req_data': data },
        type: "POST",
        async: false,
        dataType: 'json',
        success: function (data) {
            if (data.status) {
                $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.msg });
            }
            location.reload(false);
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            console.log(error);
            $("div.overlay").css("display", "none");
        }
    });
}
//Product Mapping
$('#tab_tot_summary').on('click', function () {
    if (ctrl_page[2] != 'edit') {
        set_item_reorder();
    }
});
function get_branch_details() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/branch/branchname_list',
        dataType: 'json',
        success: function (data) {
            branch_details = data;
        }
    });
}
function set_item_reorder() {
    var row = '';
    $('#total_items tbody ').empty();
    $.each(branch_details.branch, function (key, item) {
        row += '<tr>'
            + '<td>' + item.name + '<input type="hidden" class="form-control id_branch"  name="pieces[' + key + '][id_branch]" value=' + item.id_branch + '></td>'
            + '<td><input type="number" class="form-control min_pcs" name="pieces[' + key + '][min_pcs]" value="" placeholder="Enter Min Pcs"></td>'
            + '<td><input type="number" class="form-control max_pcs" name="pieces[' + key + '][max_pcs]"  value="" placeholder="Enter Max Pcs"></td>'
            + '</tr>';
    });
    $('#total_items tbody ').append(row);
}
$('#reorder_details_search').on('click', function () {
    reorder_report();
});
function reorder_report() {
    var company_name = $('#company_name').val();
    var branch_name = ($('#id_branch').val() != '' && $('#id_branch').val() != undefined ? $('#id_branch').val() : $("#branch_select option:selected").text());
    var report_name = "Reorder Report";
    var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"
        + "<span>" + report_print(branch_name, report_name, '', '', '') + "</span>";
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_other_inventory/reorder_report?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        data: { 'id_branch': $('#branch_select').val() },
        dataType: 'json',
        cache: false,
        success: function (data) {
            var list = data.list;
            var access = data.access;

            var oTable = $('#reorder_details').DataTable();
            oTable.clear().draw();
            if (list != null && list.length > 0) {
                oTable = $('#reorder_details').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "dom": 'lBfrtip',
                    "order": [[0, "desc"]],

                    "columnDefs": [
                        {
                            targets: [],
                            className: 'dt-body-right'
                        },
                        {
                            targets: [0, 1],
                            className: 'dt-body-left'
                        },
                    ],
                    "buttons": [
                        {
                            extend: 'print',
                            footer: true,
                            title: "",
                            messageTop: title,
                            customize: function (win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                        {
                            extend: 'excel',
                            footer: true,
                            title: "Reorder Report",
                        }
                    ],
                    "aaData": list,
                    "aoColumns": [
                        { "mDataProp": "branch_name" },
                        { "mDataProp": "item_name" },
                        { "mDataProp": "min_pcs" },
                        { "mDataProp": "max_pcs" },
                        {
                            "mDataProp": function (row, type, val, meta) {
                                if (row.available_pcs > row.max_pcs) {
                                    return '<span class="badge bg-green">' + row.available_pcs + '</span>';
                                }
                                else if (row.available_pcs < row.min_pcs) {
                                    return '<span class="badge bg-red">' + row.available_pcs + '</span>';
                                }
                                else {
                                    return row.available_pcs;
                                }
                            }
                        },
                    ]
                });
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//Adding scheme map for gift code starts
// function display_customer_type()
// {
//     var checkedOption = $('input[type=checkbox][name=select_customer_type]:checked').val();
//     if(checkedOption==1)
//     {
//         $("#chit_customer_div").css("pointer-events","auto");
//         $("#chit_customer_div").css("display","block");

//     }
//     else if(checkedOption==2)
//     {
//         $("#chit_customer_div").css("pointer-events","none");
//         $("#chit_customer_div").css("display","none");
//     }
//     else
//     {
//         $("#chit_customer_div").css("pointer-events","none");
//         $("#chit_customer_div").css("display","none");
//     }
// }
function display_customer_type(check) {
    // Store the value of the selected option
    // var checkedOption = $('input[type=checkbox][name=select_customer_type]:checked').val();
    var checkedOption = check;
    // Uncheck all checkboxes
    $("input[type=checkbox][name=select_customer_type]").prop('checked', false);
    // Check the selected checkbox
    $("input[type=checkbox][name=select_customer_type][value='" + checkedOption + "']").prop('checked', true);
    // Store the selected option value in #issue_to
    $("#issue_to").val(checkedOption);
    // If an option is selected
    if (checkedOption == 1 || checkedOption == 0) {

        var table_rows = $('#scheme_map_table tbody tr').length;
        // Enable and display chit_customer_div
        $("#chit_customer_div").css("pointer-events", "auto").css("display", "block");
        if (table_rows == 0) {
            scheme_add_row();
        }
    } else {
        // Disable and hide chit_customer_div
        $("#chit_customer_div").css("pointer-events", "none").css("display", "none");
    }
}
$('#addRow').on('click', function () {

    scheme_add_row();
});
$(document).on('click', '.addDynamicRow', function (event) {
    event.preventDefault();
    scheme_add_row();
});
function scheme_add_row() {
    var allFilled = true;
    // Check each existing row for filled inputs
    $('#scheme_map_table tbody tr').each(function () {
        var selectValue = $(this).find('select').val();
        var inputVal = $(this).find('input[type="number"].quantity').val();
        var inputVal2 = $(this).find('input[type="number"].tenurefrom').val();
        var inputVal3 = $(this).find('input[type="number"].tenureto').val();

        if (!selectValue || !inputVal || inputVal2 == '' || inputVal3 == '') {
            allFilled = false;
            return false; // Break out of the loop if any row has empty inputs
        }
    });
    if (allFilled) {
        load_scheme_select(row_num);
        var newRow = '<tr>' +
            '<td><select style="width:150px;" id="scheme_select_' + row_num + '" name="scheme_select[' + row_num + '][]"   multiple></select></td>' +
            // onchange="set_selected_value(this.value,'+row_num+')"

            '<td>From<input type="number" id="tenurefrom_' + row_num + '" class="tenurefrom" name="tenurefrom[' + row_num + ']" style="width: 50px; display: inline-block;" step="any">To<input type="number"  class="tenureto" id="tenureto_' + row_num + '" name="tenureto[' + row_num + ']" style="width: 50px; display: inline-block;"></td>' +
            '<td><input type="number"  class="quantity"  name="quantity[' + row_num + ']"></td>' +
            '<td><button class="btn btn-success btn-sm addDynamicRow" style="margin-top:-2px;" data-rownum="' + row_num + '"><i class="fa fa-plus"></i>Add</button><button class="deleteRow btn btn-danger btn-sm" style="margin-top:-2px;"><i class="fa fa-trash"></i>Remove</button></td>' +
            '</tr>';

        $('#scheme_map_table tbody').append(newRow);
        $("#table_length").val(row_num);
        row_num++;
    }
    else {
        alert('Please fill in all existing rows before adding a new row.');
    }
}
$('#scheme_map_table').on('click', '.deleteRow', function () {
    $(this).closest('tr').remove();
});
function load_scheme_select(row_num) {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/get/schemename_list',
        dataType: 'json',
        success: function (data) {
            var $select = $('#scheme_select_' + row_num);
            $select.empty(); // Clear existing options
            $.each(data, function (key, item) {
                if (item.has_gift == 1 && item.active == 1) {
                    $select.append(
                        $("<option></option>")
                            .attr("value", item.id_scheme)
                            .text(item.scheme_name)
                    );
                }
            });
            $select.attr('name', 'scheme_select[' + row_num + '][]'); // Change name attribute
            var id_scheme = $("#scheme_select_hidden_" + row_num).val();
            console.log(id_scheme);
            if (id_scheme != '') {
                $select.val(id_scheme).trigger('change');
            }
            $select.select2({
                placeholder: "Select Scheme",
                allowClear: true
            });
        }
    });
}
//   function load_scheme_select(row_num) {
//     $.ajax({
//         type: 'GET',
//         url: base_url + 'index.php/get/schemename_list',
//         dataType: 'json',
//         success: function (data) {
//             var $select = $('#scheme_select_' + row_num);
//             $select.empty(); // Clear existing options
//             // $select.append($('<option></option>')
//             //     .attr('value', '')
//             //     .text('Select Scheme')
//             // );
//             $.each(data, function (key, item) {
//                 if(item.has_gift==1 && item.active==1)
//                 {
//                     $select.append(
//                         $("<option></option>")
//                         .attr("value", item.id_scheme)
//                         .text(item.scheme_name)
//                     );
//                 }

//             });
//             var id_scheme=$("#scheme_select_hidden_"+ row_num).val();
//             console.log(id_scheme);
//             if(id_scheme!='')
//             {
//                 $select.val(id_scheme).trigger('change');
//             }
//             $select.select2({
//                 placeholder: "Select Scheme",
//                 allowClear: true
//             });
//         }
//     });
// }
// function set_selected_value(selected,table_rows)
// 			{
// 				//quantity input will only be enable if the value selected in dropdown
// 				if(selected)
// 				{
// 					$("#scheme_select"+table_rows).val(selected);

// 				}
// 				else
// 				{
// 					$("#scheme_select"+table_rows).val("");

// 				}
// 			}
function isValid(data) {
    if (data != '' && data != null && data != undefined) {
        return true;
    }
    else {
        return false;
    }
}
//Adding scheme map for gift code ends
//TAG IMAGE
function take_snapshot(type) {
    //Snap Shots Disables
    $('#snap_shots').prop('disabled', true);
    if (type == 'pre_images') {
        preview = 'uploadArea_p_stn';
    }
    Webcam.snap(function (data_uri) {
        $(".image-tag").val(data_uri);
        pre_img_resource.push({ 'src': data_uri, 'name': (Math.floor(100000 + Math.random() * 900000)) + 'jpg', 'is_default': "0" });
        pre_img_files.push(data_uri);
        alert("Your Webcam Images Take Snap Shot Successfullys.");
    });
    if (pre_img_resource.length > 0) {
        $("#image_lot_list").css('display', 'block');
        $("#lot_images_count").text(pre_img_resource.length);
    }
    else {
        $("#image_lot_list").css('display', 'none');
        $("#lot_images_count").text('0');
    }
    setTimeout(function () {
        var resource = [];
        $('#' + preview + ' div').remove();
        if (type == 'pre_images') {
            resource = pre_img_resource;
        }
        $.each(resource, function (key, item) {
            if (item) {
                var div = document.createElement("div");
                div.setAttribute('class', 'images');
                div.setAttribute('id', +key);
                param = { "key": key, "preview": preview, "stone_type": type };
                div.innerHTML += "<span style='float:left;'><a onclick='remove_stn_img(" + JSON.stringify(param) + ")' style='cursor:pointer;color:inherit;' data-toggle='tooltip' data-placement='bottom' title='Delete This Images'>&nbsp;<i class='fa fa-trash'></i></a></span>&nbsp;&nbsp;<label style='display:none;'>Is Default</label><span><input type='hidden' class='tag_default_" + key + "'  value='0' onchange='default_stn_img(" + JSON.stringify(param) + ",event)' data-toggle='tooltip' data-placement='bottom' title='Click Here To Set Default Image' style='float:right;margin-right:20px;'></span><img class='thumbnail' src='" + item.src + "'" + "style='width: 100px;height: 100px;'/>";
                $('#' + preview).append(div);
            }
            $('#lot_img_upload').css('display', '');
        });
        $('#snap_shots').prop('disabled', false);
        var default_keyimage = typeof
            localStorage.getItem("key") !== 'undefined' ? localStorage.getItem("key") : '0';
        if (default_keyimage) {
            $(".tag_default_" + default_keyimage).prop('checked', true);
            $(".tag_default_" + default_keyimage).val('1');
            localStorage.setItem("key", default_keyimage);
        }
        else {
            $(".tag_default_0").prop('checked', true);
            $(".tag_default_0").val('1');
            localStorage.setItem("key", '0');
        }
    }, 1000);
}
function image_preview_validaion(type) {
    if (type == 'pre_images') {
        preview = 'uploadArea_p_stn';
    }
    if (pre_img_resource.length > 0) {
        $("#image_lot_list").css('display', 'block');
    }
    else {
        $("#image_lot_list").css('display', 'none');
    }
    setTimeout(function () {
        var resource = [];
        $('#' + preview + ' div').remove();
        if (type == 'pre_images') {
            resource = pre_img_resource;
        }
        $.each(resource, function (key, item) {
            if (item) {
                var div = document.createElement("div");
                div.setAttribute('class', 'images');
                div.setAttribute('id', +key);
                param = { "key": key, "preview": preview, "stone_type": type };
                div.innerHTML += "<span style='float:left;'><a onclick='remove_stn_img(" + JSON.stringify(param) + ")' style='cursor:pointer;color:inherit;' data-toggle='tooltip' data-placement='bottom' title='Delete This Images'>&nbsp;<i class='fa fa-trash'></i></a></span>&nbsp;&nbsp;<label style='display:none;'>Is Default</label><span><input type='hidden' class='tag_default_" + key + "'  value='0' onchange='default_stn_img(" + JSON.stringify(param) + ",event)' data-toggle='tooltip' data-placement='bottom' title='Click Here To Set Default Image' style='float:right;margin-right:20px;'></span><img class='thumbnail' src='" + item.src + "'" + "style='width: 100px;height: 100px;'/>";
                $('#' + preview).append(div);
            }
        });
        var catRow = $('#custom_active_id').val();
        var default_keyimage = $('#tag_img_default').val();
        if (default_keyimage) {
            $(".tag_default_" + default_keyimage).prop('checked', true);
            $(".tag_default_" + default_keyimage).val('1');
            localStorage.setItem("key", default_keyimage);
        }
        else {
            $(".tag_default_0").prop('checked', true);
            $(".tag_default_0").val('1');
            localStorage.setItem("key", '0');
        }
    }, 100);
}
function grn_update_image_upload(curRow, id) {
    // Check Validations
    pre_img_resource = [];
    pre_img_files = [];
    if ($('#tag_images').val() != '') {
        var pre_images = JSON.parse($('#tag_images').val());

        pre_img_resource = pre_images;
        image_preview_validaion('pre_images');
    }
    else {
        image_preview_validaion('pre_images');
    }
    $('#grn_imageModal').modal('show');
    // Image Key Storage Validations Remove Local Storage
    localStorage.removeItem("key");
    $('#bulktag_images').trigger('click');

}
$("#pre_images").on('change', function () {
    validateCertifImg(this.id);
});
function validateCertifImg(type) {
    if (type == 'pre_images') {
        preview = 'uploadArea_p_stn';
    }
    var files = event.target.files;
    var html_1 = "";
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (file.size > 1048576) {
            alert('File size cannot be greater than 1 MB');
            files[i] = "";
            return false;
        }
        else {
            var fileName = file.name;
            var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
            ext = ext.toLowerCase();
            if (ext != "jpg" && ext != "png" && ext != "jpeg") {
                alert("Upload JPG or PNG Images only");
                files[i] = "";
            }
            else {
                var reader = new FileReader();
                reader.onload = function (event) {
                    if (type == 'pre_images') {
                        pre_img_resource.push({ 'src': event.target.result, 'name': fileName });
                        pre_img_files.push(file);
                    }
                }
                if (file) {
                    reader.readAsDataURL(file);
                }
                /*else
                {
                    preview.prop('src','');
                }*/
            }
        }
    }
    setTimeout(function () {
        var resource = [];
        $('#' + preview + ' div').remove();
        if (type == 'pre_images') {
            resource = pre_img_resource;
        }
        $.each(resource, function (key, item) {
            if (item) {
                var div = document.createElement("div");
                div.setAttribute('class', 'col-md-4');
                div.setAttribute('id', +key);
                param = { "key": key, "preview": preview, "stone_type": type };
                //div.innerHTML+= "<a onclick='remove_stn_img('"+key+"','"+preview+"','"+type+"')'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +
                div.innerHTML += "<a onclick='remove_stn_img(" + JSON.stringify(param) + ")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +
                    "style='width: 100px;height: 100px;'/>";
                $('#' + preview).append(div);
            }
            $('#lot_img_upload').css('display', '');
        });
    }, 1000);
}
function default_stn_img(param, event) {
    var current_status = event.target.checked;
    var parameter = param.key;
    $('#uploadArea_p_stn div').each(function (index, value) {
        var image_class = $(this).find('span input').attr('class');
        var image_class_key = 'tag_default_' + parameter;
        if (image_class == image_class_key) {
            $("." + image_class).prop('checked', true);
            $("." + image_class).val('1');
            localStorage.setItem("key", parameter);
        }
        else {
            $("." + image_class).prop('checked', false);
            $("." + image_class).val('0');
        }
    });
}
function remove_stn_img(param) {
    var current_status = $(".tag_default_" + param.key).is(':checked');
    $('#' + param.preview + ' #' + param.key).remove();
    if (pre_img_resource.length == 1) {
        pre_img_resource = [];
        $("#lot_images_count").text(pre_img_resource.length);
        if (ctrl_page[2] == 'edit') {
            remove_img(file, 'certificates', 'precious_st_certif', id, imgs);
        }
    }
    else {
        if (param.stone_type == 'pre_images') {
            pre_img_resource.splice(param.key, 1);
            $("#lot_images_count").text(pre_img_resource.length);
            if (ctrl_page[2] == 'edit') {
                remove_img(file, 'certificates', 'precious_st_certif', id, imgs);
            }
        }
    }
    if (current_status == true) {
        var image_class_first = $('#uploadArea_p_stn div').find('span input').attr('class');
        $("." + image_class_first).prop('checked', true);
        $("." + image_class_first).val('1');
        var image_class_key = image_class_first.split('_');
        localStorage.setItem("key", image_class_key[2]);
    }
}
$('#grn_imageModal  #grn_update_img').on('click', function () {
    var set_inddefault_keyimage = typeof localStorage.getItem("key") !== 'undefined' ? localStorage.getItem("key") : '0';
    $('#grn_imageModal').modal('toggle');
    var copyrow_validation = $('#tag_img_copy').val();
    if (copyrow_validation == '1') {
        $('#tag_img_copy').val('2');
    }

    $('#tag_img').attr("data-img", encodeURIComponent(JSON.stringify(pre_img_resource)));
    $('#tag_images').val((JSON.stringify(pre_img_resource)));
    $('#tag_img_url').val(encodeURIComponent(JSON.stringify(pre_img_resource)));
    $('#tag_img_default').val(set_inddefault_keyimage);
    var get_default_image = pre_img_resource[set_inddefault_keyimage];
    if (Object.keys(get_default_image).length > 0) {
        if (get_default_image.src != "") {
            $('#tagging_set_images').attr("src", get_default_image.src);
        }
    }
    else {
        var type = base_url + 'assets/img/no_image.png';
        $('#tagging_set_images').attr("src", type);
    }
});
function remove_tag_img(param) {
    $('#' + param.key).remove();
    pre_img_resource.splice(param.key, 1);
}
$('#bulktag_images').on('change', function () {
    validateBulkTagImages();
});
function validateBulkTagImages() {
    //$('#wast_img_preview').html('');
    var preview = $('#pre_images');
    var files = event.target.files;
    for (var i = 0; i < files.length; i++) {
        const compress = new Compress();
        const product_images = [files[i]];
        compress.compress(product_images, {
            size: 4, // the max size in MB, defaults to 2MB
            quality: 0.75, // the quality of the image, max is 1,
            maxWidth: 1920, // the max width of the output image, defaults to 1920px
            maxHeight: 1920, // the max height of the output image, defaults to 1920px
            resize: true // defaults to true, set false if you do not want to resize the image width and height
        }).then((results) => {
            const output = results[0];
            total_files.push(output);
            const file = Compress.convertBase64ToFile(output.data, output.ext);
            if (output.endSizeInMb < 2) {
                pre_img_resource.push({ "src": output.prefix + output.data, 'name': output.alt, 'is_default': "0" });
            }
            else {
                alert('File size cannot be greater than 1 MB');
                files[i] = "";
                return false;
            }
        });
    }
    setTimeout(function () {
        var resource = [];
        resource = pre_img_resource;
        image_preview_validaion('pre_images');
    }, 500);
}
//TAG IMAGE
$('#myForm').submit(function () {
    // Disable the submit button to prevent double submission
    $('#inventory_type_submit').prop('disabled', true);
});

function fetchAllSizes() {

    sizeNames = [];

    $.ajax({
        url: base_url + "index.php/admin_ret_other_inventory/get_all_sizes",
        dataType: "JSON",
        type: "GET",
        success: function (data) {
            console.log(data);
            $.each(data, function (key, value) {
                // sizeNames = (value.size_name).toLowerCase();
                sizeNames.push(value.size_name.toLowerCase());
            });

        }
    });
    console.log(sizeNames);
}

function isDuplicateSize(sizeName) {
    // Ensure sizeName is converted to lowercase for comparison
    return sizeNames.includes(sizeName.toLowerCase());
}

$(document).ready(function () {
    // Listen for the modal 'hidden' event
    $('#modal-close-button, #modal-footer-close-button').on('click', function () {
        // Reset the input field value
        $('#size_name').val('');
    });
});

// Other inventory category duplicate name entry.
$('#name').on('blur',function(){
    var cat_name = $('#name').val();
    if(cat_name != ''){
        $.ajax({
            type : 'POST',
            data : {'name': cat_name}, 
            dataType : 'JSON',
            url : base_url + "index.php/admin_ret_other_inventory/CheckIsNameDuplicate",
            success : function(data){
                console.log(data);
                if(data.status == false){
                    $('#name').val('');
                    $('#name').focus();
                    $.toaster({ priority: 'danger', title: 'Warning!', message: '' +"</br>"+ data.message ,settings: {'timeout': 4000} });
                }
            }

        })
    }
});