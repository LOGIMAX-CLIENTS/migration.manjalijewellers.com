  <style>
    
html {
    width: 100%;
    height: 100%;
}
body {
    background: #f3f3f3;
    width: 100%;
    height: 100%;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
ul{
    list-style: none;
}
.file-upload-wrapper {
    width: 60%;
    height: 100%;
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
}
.box-fileupload {
    background: #d3e7ff;
    width: 80%;
    padding: 50px;
    border: 2px dashed #a9e4f1;
    border-radius: 3px;
}

html {
    width: 100%;
    height: 100%;
}
body {
    background: #f3f3f3;
    width: 100%;
    height: 100%;
}
* {
    margin: 0;
    padding: 0;
}
ul{
    list-style: none;
}
.maincontent {
    max-width: 1080px;
    margin: 0 auto;
    padding: 50px 0;
}
.file-upload-wrapper {
    width: 100%;
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-flow: column;
    background: #fff;
    padding: 20px 0;
    border-radius: 4px;
}
.box-fileupload {
    background: #d3e7ff;
    width: 90%;
    padding: 50px;
    border: 3px dashed #8fd9ea;
    border-radius: 6px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-flow: column;
}
.file-upload-input {
    display: none;
}
label.file-upload-btn {
    width: 142px;
    height: 120px;
    background-image: url(<?php  echo base_url()."/assets/img/apk.png"?>);
    background-size: cover;
    background-position: center center;
    background-origin: border-box;
    background-repeat: no-repeat;
}

.file-upload-wrapper-title {
    width: 92%;
    min-height: 50px;
    padding: 10px;
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-flow: column;
}
.file-upload-wrapper-title h4 {
    display: inline-block;
    font-size: 20px;
    padding: 10px 0px 6px 0;
    font-weight: 200;
}
.file-upload-wrapper-title hr {
    width: 21%;
    display: inline-block;
    margin: 6px 0;
}
.file-upload-wrapper-title__btn {
    background-color: #4099ff;
    border-color: #4099ff;
    border: none;
    padding: 12px 20px;
    color: #fff;
    cursor: pointer;
    -webkit-transition: all ease-in 0.3s;
    transition: all ease-in 0.3s;
    border-radius: 2px;
}
.box-fileupload__lable {
    font-size: 20px;
    margin: 10px 0;
    color: #1471ad;
}

.error-format {
    background: #ff00003b;
    padding: 15px 10px;
    border-radius: 5px;
    border: 2px solid #f6343b;
    color: #b00707;
    margin: 10px;
}
    </style>
  
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Upload APk
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Apk</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
            
                <div class="box-body">
                <!-- Alert -->
                <!-- <a href="<?php echo base_url()/"index.php/admin_dashboard/Upload_apk" ?>">sadasd</a> -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                        // print_r($message);exit;
                ?>
                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?>
				<div class="maincontent">
        <div class="file-upload-wrapper">
            <div class="file-upload-wrapper-title">
                <h4>Apk Upload</h4>
                <hr />
                <?php echo form_open_multipart('admin_dashboard/upload'); ?>

                <button id="upload_apk"type="submit" name="submit" class="file-upload-wrapper-title__btn">
                    Upload Now
                </button>
            </div>
            <div class="box-fileupload">
                <input type="file" id="apk_file" class="file-upload-input" name="apk_file" style="display:none;">
                <label  name="apk_file" for="apk_file" class="file-upload-btn"></label>
                <p class="box-fileupload__lable"> Here to upload</p>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
				
                 
    <!-- <?php echo form_open_multipart('admin_dashboard/upload'); ?>

    <label class="form-label">Upload Apk File
    <input class="form-control" type="file"  id="apk_file"name="apk_file" />

<button class="btn btn-primary"  >Upload</button>
<?php echo form_close(); ?> -->

    </label>
    
<br> 

                 
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

 



<!-- <script  type="text/javascript"> 
$(document).ready(function(){
    
}); -->



</script>

