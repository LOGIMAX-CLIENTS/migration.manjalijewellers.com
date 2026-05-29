
    <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>EMAIL
            <small>Mail</small>
          </h1>
          <ol class="breadcrumb">
             <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Email</a></li>
            <li class="active">Send Mail </li>
            
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Compose New Message</h3>
                </div><!-- /.box-header -->
                <?php echo form_open_multipart('email/send');?>
                <div class="box-body">
                           <div class="row">
           	<div class="col-xs-12">
           		            <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-<?php echo $message['icon'];?>"></i> <?php echo $message['title']; ?></h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?>  
           	</div>
           	
           </div>
                  <div class="form-group">
                    <input class="form-control" name="mail[to]" placeholder="To:" required="true">
                  </div>
                  <div class="form-group">
                    <input class="form-control" name="mail[subject]" placeholder="Subject:" required="true">
                  </div>
                  <div class="form-group">
                    <textarea  id="compose-textarea" name="mail[message]" class="form-control" style="height: 300px">
                
                    </textarea>
                  </div>
           <!--       <div class="form-group">
                    <div class="btn btn-default btn-file">
                      <i class="fa fa-paperclip"></i> Attachment
                      <input type="file" name="attachment">
                    </div>
                    <p class="help-block">Max. 32MB</p>
                  </div>-->
                </div><!-- /.box-body -->
                
                <div class="box-footer">
                 <div class="col-xs-offset-5">
									<button  type="submit" class="btn btn-primary"> Send</button>
									<button   type="button" class="btn btn-default btn-cancel"> Cancel</button>
								  </div> <br/>
                </div><!-- /.box-footer -->
                </form>
              </div><!-- /. box -->
          

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->