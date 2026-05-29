  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Services&nbsp;&nbsp;<small><strong></strong></small>
            
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
            <audio id="audio">
                <source id="source" src="<?php echo base_url('assets/audio/hangouts.mp3'); ?>" type="audio/mpeg"> 
            </audio>
              <div id="notibox" class="box">
                <div class="box-header">
                    <label>Branch</label>
                    <select type="text" name="branch" id="id_branch" style="min-width:100px">
                        <option value=1>1 - Usman Rd</option>
                        <option value=2>2 - Ranganathan ST</option>
                        <option value=3>3 - Coimbatore</option>
                        <option value=4>4 - Tirunelveli</option>
                        <option value=5>5 - Madurai</option>
                        <option value=6>6 - Online</option>
                        <option value=7>7 - MDU</option>
                    </select>
                    <label>Entry Date</label>
                    <input type="text" name="entry_date" id="entry_date" placeholder="YYYY-MM-DD"/>
                  <button class="btn btn-success pull-right" id="tempToMain">Temp to Main</button> 
                </div><!-- /.box-header -->
                <div class="box-body">
                    <h4>Result : </h4>
                    <span id="result"></span>
                    <br/>
                    <div>
                        <pre><?php print_r($tempData);?></pre>
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
       