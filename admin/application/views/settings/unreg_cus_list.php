  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Customer Details

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Setttings</a></li>

            <li class="active">Company List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">
            
           
          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">File  List</h3>      

                          <!-- <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/customer/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->

                </div><!-- /.box-header -->
                
                <button class="btn btn-primary" id="generate_file" style="margin-left: 30px;">Generate File</button>
               
                
                <a class="btn btn-success" type="submit"  id="compress_file" style="margin-left:30px;" href="<?php  echo base_url('index.php/admin_settings/compress');?>">Download File</a>

                <div class="box-body">
                    
                    <input type="hidden" id="last_id" value></input>
                    <input type="hidden" id="current_count" value=0></input>
                    <input type="hidden" id="tot_count" value=-1></input>

                <!-- Alert -->

                <?php 

                	if($this->session->flashdata('chit_alert'))

                	 {

                		$message = $this->session->flashdata('chit_alert');

                ?>

                       <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

	                    <?php echo $message['message']; ?>

	                  </div>

	                  

	            <?php } ?>      

                  <div class="table-responsive">

                  <table id="file_list" class="table grid table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>File Name</th>
						
                      </tr>

                    </thead>

            

                 <!--   <tfoot>

                      <tr>

                        

                      </tr>

                    </tfoot> -->

                  </table>

                  </div>

                </div><!-- /.box-body -->
                
                  <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
<?php echo form_close();?> 
              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->
