<style>


#bank_deposit_list th:nth-child(3), #bank_deposit_list td:nth-child(3) {

text-align: right;

}

.closing_bal {
  font-size: 16px;
  font-weight: bold;
}


</style>

<?php

function moneyFormatIndia($num) {

  return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);

}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Bank Deposit Master  <span class="closing_bal"> (CASH IN HAND (RETAIL) : <?php echo $this->session->userdata('currency_symbol')?> <span class="cash_in_hand"><?php echo moneyFormatIndia(number_format($dep_cur_balance_retail,2,'.','')); ?>)</span></span> <span class="closing_bal"> (CASH IN HAND (CRM) : <?php echo $this->session->userdata('currency_symbol')?> <span class="cash_in_hand"><?php echo moneyFormatIndia(number_format($dep_cur_balance_chit,2,'.','')); ?>)</span></span>
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Master</a></li>
        <li class="active">Deposit List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Deposit List</h3><span id="total_bank_deposit" class="badge bg-green"></span>   
              <?php if($access['add']==1){?>
                        <a class="btn btn-success pull-right" id="add_bnk_deposit"  href="<?php echo base_url('index.php/deposit/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
           <?php }?>
                      </div><!-- /.box-header -->
            <div class="box-body">
            <!-- Alert -->
            <?php 
              if($this->session->flashdata('chit_alert')) {
                
                $message = $this->session->flashdata('chit_alert');  ?>

                <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                  <?php echo $message['message']; ?>
                </div>

          <?php } ?>   
              
              <div class="table-responsive">
                <table id="bank_deposit_list" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Branch</th>
                      <th>Bank</th>
                      <th>Type</th>    
                      <th>Amount</th>                                           
                      <th>Deposit Type</th>    
                      <th>Ref Id</th>  
                      <th>Entry Date</th>
                      <th>Deposit Date</th>
                      <th>Cash In Hand Date</th>
                      <th>Payment Mode</th> 
                      <th>Narration</th>
                      <th>Deposited By</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tfoot>
                </table>
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

  <!-- modal -->      
  <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Delete Bank Deposit</h4>
        </div>
        <div class="modal-body">
                <strong>Are you sure! You want to delete this deposit?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger btn-confirm" >Delete</a>
          <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->      
