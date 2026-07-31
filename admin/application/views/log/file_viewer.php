<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      System Log Viewer
      <small>View and manage file-based application logs</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Log</a></li>
      <li class="active">Log file viewer</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Date & Part Filter Panel -->
    <div class="box box-default" style="margin-bottom: 15px;">
      <div class="box-body" style="padding: 10px 15px;">
        <div class="row">
          <!-- Date Selection -->
          <div class="col-md-3">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="log-datepicker" style="font-size: 12px; margin-bottom: 3px;">Select Log Date:</label>
              <div class="input-group date">
                <div class="input-group-addon" style="cursor: pointer; background-color: #f4f4f4;">
                  <i class="fa fa-calendar text-red"></i>
                </div>
                <input type="text" class="form-control pull-right" id="log-datepicker" value="<?php echo date('Y-m-d'); ?>" readonly style="background-color:#fff; cursor:pointer;">
              </div>
            </div>
          </div>
          <!-- Log Part Selection -->
          <div class="col-md-3">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="log-part-select" style="font-size: 12px; margin-bottom: 3px;">Select Log Part (Subfolder):</label>
              <select class="form-control select2" id="log-part-select" style="width: 100%;">
                <option value="">-- Select Date First --</option>
              </select>
            </div>
          </div>
          <!-- Visual Helper Hint -->
          <div class="col-md-6 text-muted text-right" style="padding-top: 25px; font-size: 11px;">
            <i class="fa fa-info-circle"></i> Logs are grouped automatically by the selected folder date and its components.
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Left Column: Log Files Table -->
      <div class="col-md-5">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-files-o text-red"></i> Files in Part: <span id="selected-part-title" class="text-navy">None</span></h3>
            <span class="badge bg-red pull-right" id="files-count-badge">0 Files</span>
          </div><!-- /.box-header -->
          
          <!-- Independent Scrolling container for files list -->
          <div class="box-body" id="files-list-container">
            <div class="table-responsive">
              <table id="log_files_table" class="table table-bordered table-striped table-hover text-center" style="width:100%">
                <thead>
                  <tr>
                    <th style="width: 15%">Source</th>
                    <th style="width: 55%">File Details</th>
                    <th style="width: 15%">Size</th>
                    <th style="width: 15%">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Filled dynamically via JS -->
                </tbody>
              </table>
            </div>
          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div><!-- /.col -->

      <!-- Right Column: Log File Viewer Pane -->
      <div class="col-md-7">
        <div class="box box-success" id="viewer-container-box">
          <div class="box-header with-border">
            <h3 class="box-title" id="viewer-title"><i class="fa fa-terminal text-green"></i> Log Reader</h3>
            <div class="box-tools pull-right" id="viewer-actions" style="display: none;">
              <button type="button" class="btn btn-box-tool text-blue" id="btn-copy-log" title="Copy to Clipboard">
                <i class="fa fa-copy" style="font-size: 14px;"></i> Copy
              </button>
              <button type="button" class="btn btn-box-tool text-orange" id="btn-scroll-bottom" title="Scroll to Bottom">
                <i class="fa fa-arrow-down" style="font-size: 14px;"></i> Bottom
              </button>
              <button type="button" class="btn btn-box-tool text-green" id="btn-refresh-log" title="Refresh Log File">
                <i class="fa fa-refresh" style="font-size: 14px;"></i> Refresh
              </button>
              <a href="#" class="btn btn-box-tool text-olive" id="btn-download-log" title="Download File">
                <i class="fa fa-download" style="font-size: 14px;"></i> Download
              </a>
            </div>
          </div><!-- /.box-header -->

          <div class="box-body" id="viewer-body">
            <!-- Initial Empty State -->
            <div id="viewer-empty-state" class="text-center" style="padding: 120px 0;">
              <i class="fa fa-file-text-o text-muted" style="font-size: 60px; margin-bottom: 20px;"></i>
              <p class="lead text-muted" id="empty-state-text">Select a log file from the list to view its contents.</p>
            </div>

            <!-- Loader -->
            <div id="viewer-loader" class="text-center" style="display: none; padding: 120px 0;">
              <i class="fa fa-refresh fa-spin text-green" style="font-size: 40px; margin-bottom: 20px;"></i>
              <p class="text-muted">Loading log file contents...</p>
            </div>

            <!-- Log Reader Content -->
            <div id="viewer-content-area" style="display: none;">
              <!-- Meta Info & Filtering -->
              <div class="well well-sm" style="margin-bottom: 10px; background-color: #f9f9f9; padding: 10px;">
                <div class="row">
                  <div class="col-sm-5">
                    <p style="margin: 0; font-size: 12px;"><strong>File Path:</strong> <span id="log-meta-path" class="text-navy" style="word-break:break-all;"></span></p>
                    <p style="margin: 0; font-size: 12px;"><strong>File Size:</strong> <span id="log-meta-size"></span></p>
                  </div>
                  <div class="col-sm-7">
                    <div class="input-group input-group-sm">
                      <span class="input-group-addon"><i class="fa fa-search"></i></span>
                      <input type="text" id="log-search" class="form-control" placeholder="Search log lines...">
                      <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="btn-clear-search" title="Clear Search">
                          <i class="fa fa-times"></i>
                        </button>
                        <button class="btn btn-info" type="button" id="btn-toggle-filter-view" data-filtered="false" title="Show only matching lines">
                          Filter View
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Warning for Truncated Large Files -->
              <div id="log-truncated-warning" class="alert alert-warning alert-dismissible" style="display: none; margin-bottom: 10px; padding: 8px 30px 8px 15px;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="right: -2px; top: -2px;">&times;</button>
                <i class="icon fa fa-warning"></i> <strong>Large File!</strong> Showing only the last 2,000 lines. Download the full file for complete logs.
              </div>

              <!-- File lines (Independent Scroll panel) -->
              <div id="log-display-container">
                <pre id="log-content"></pre>
              </div>
            </div>
          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Styling for premium layout and independent scrolling -->
<style>
  /* Left Panel scrolling container */
  #files-list-container {
    height: calc(100vh - 250px);
    min-height: 480px;
    overflow-y: auto;
    position: relative;
  }

  /* Right Panel log content scrolling container */
  #log-content {
    font-family: 'Consolas', 'Courier New', Courier, monospace;
    background-color: #151515;
    color: #e5e7eb;
    padding: 12px 15px;
    height: calc(100vh - 350px);
    min-height: 380px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
    border-radius: 4px;
    border: 1px solid #2d2d2d;
    margin: 0;
    font-size: 12px;
    line-height: 1.6;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.8);
  }

  .log-line {
    border-left: 2px solid transparent;
    padding-left: 6px;
    transition: background-color 0.15s ease;
  }
  
  .log-line:hover {
    background-color: #22252a;
    border-left-color: #4b5563;
  }

  /* Log Level Colors */
  .log-error {
    color: #f87171 !important; /* Soft Red */
    background-color: rgba(248, 113, 113, 0.08);
    border-left-color: #ef4444;
  }
  
  .log-warning {
    color: #fbbf24 !important; /* Soft Orange/Yellow */
    background-color: rgba(251, 191, 36, 0.08);
    border-left-color: #f59e0b;
  }
  
  .log-success {
    color: #34d399 !important; /* Soft Green */
    background-color: rgba(52, 211, 153, 0.08);
    border-left-color: #10b981;
  }
  
  .log-info {
    color: #60a5fa !important; /* Soft Blue */
    background-color: rgba(96, 165, 250, 0.08);
    border-left-color: #3b82f6;
  }
  
  .log-direct {
    color: #c084fc !important; /* Purple for directAPI */
    background-color: rgba(192, 132, 252, 0.08);
    border-left-color: #a855f7;
  }

  .log-line-num {
    color: #6b7280;
    display: inline-block;
    width: 40px;
    user-select: none;
    border-right: 1px solid #374151;
    margin-right: 8px;
    padding-right: 4px;
    text-align: right;
  }

  /* Highlight matching terms */
  .highlight-match {
    background-color: #eab308;
    color: #000 !important;
    font-weight: bold;
    border-radius: 2px;
    padding: 0 2px;
  }

  /* Active log file row highlight */
  .log-file-row.active-log {
    background-color: #e8f0fe !important;
    border-left: 3px solid #3c8dbc;
  }
</style>

<!-- Scripts -->
<script>
function initLogViewer() {
  // Initialize DataTables on files list
  var table = $('#log_files_table').DataTable({
    "pageLength": 10,
    "ordering": false, 
    "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    "language": {
      "search": "Quick Filter:"
    }
  });

  // Global variables to store files data
  var filesData = {};
  var currentKey = null;
  var rawLogLines = [];

  // Initialize Bootstrap Datepicker
  $('#log-datepicker').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true
  }).on('changeDate', function(e) {
    fetchLogsForDate($(this).val());
  });

  // Fetch logs for the default loaded date
  fetchLogsForDate($('#log-datepicker').val());

  // Function to load log parts and file lists for a specific date
  function fetchLogsForDate(date) {
    $('#viewer-empty-state').show();
    $('#viewer-content-area').hide();
    $('#viewer-loader').hide();
    $('#viewer-actions').hide();
    $('#empty-state-text').text('Select a log file from the list to view its contents.');
    
    // Clear dropdown and table
    $('#log-part-select').empty().append('<option value="">Loading parts...</option>');
    table.clear().draw();
    $('#selected-part-title').text('None');
    $('#files-count-badge').text('0 Files');
    
    $.ajax({
      url: '<?php echo base_url("index.php/log/ajax_get_logs_by_date"); ?>',
      type: 'POST',
      data: { date: date },
      dataType: 'json',
      success: function(res) {
        $('#log-part-select').empty();
        filesData = {};
        
        if (res.status && res.parts && res.parts.length > 0) {
          filesData = res.files || {};
          
          // Populate parts dropdown
          res.parts.forEach(function(part) {
            var label = part;
            // Beautify labels if they are known directories
            if (part === 'directAPI') label = 'Direct API';
            else if (part === 'manual') label = 'Manual Payments';
            else if (part === 'existing') label = 'Existing Logs';
            else if (part === 'general') label = 'General Logs';
            else label = part.charAt(0).toUpperCase() + part.slice(1);
            
            $('#log-part-select').append('<option value="' + part + '">' + label + '</option>');
          });
          
          // Automatically trigger load for first part
          var firstPart = res.parts[0];
          $('#log-part-select').val(firstPart).trigger('change');
        } else {
          $('#log-part-select').append('<option value="">-- No Logs Found --</option>');
          $('#empty-state-text').html('<span class="text-warning"><i class="fa fa-info-circle"></i> No logs found for ' + date + '.</span><br>Try selecting another date (e.g. 2026-07-27).');
          $('#selected-part-title').text('None');
          $('#files-count-badge').text('0 Files');
        }
      },
      error: function(xhr, status, error) {
        $('#log-part-select').empty().append('<option value="">Error loading parts</option>');
        var errMsg = 'An error occurred while fetching log parts: ';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errMsg += xhr.responseJSON.message;
        } else if (xhr.status === 404) {
            errMsg += 'Endpoint not found (404).';
        } else if (xhr.status === 500) {
            errMsg += 'Internal Server Error (500).';
        } else {
            errMsg += (error || 'Unknown error');
        }
        alert(errMsg);
      }
    });
  }

  // When Log Part dropdown is changed
  $('#log-part-select').on('change', function() {
    var selectedPart = $(this).val();
    if (!selectedPart) return;
    
    var beautifiedLabel = $("#log-part-select option:selected").text();
    $('#selected-part-title').text(beautifiedLabel);
    
    populateFilesTable(selectedPart);
  });

  // Populate dynamic DataTable with files of selected part
  function populateFilesTable(part) {
    table.clear();
    
    var files = filesData[part] || [];
    $('#files-count-badge').text(files.length + ' Files');
    
    if (files.length === 0) {
      table.draw();
      return;
    }
    
    files.forEach(function(file) {
      var sourceLabel = file.source === 'Root' 
        ? '<span class="label label-primary"><i class="fa fa-folder"></i> Root</span>' 
        : '<span class="label label-warning"><i class="fa fa-gears"></i> Admin</span>';
        
      var detailsHtml = '<div class="text-left">' +
          '<strong class="text-navy">' + file.name + '</strong>' +
          '<div class="text-muted" style="font-size: 11px; word-break: break-all; margin-top: 2px;">' + file.relative_path + '</div>' +
          '<div class="text-muted" style="font-size: 10px; margin-top: 2px;"><i class="fa fa-clock-o"></i> ' + file.modified_time + '</div>' +
          '</div>';
          
      var sizeHtml = '<span class="badge bg-gray">' + file.size + '</span>';
      
      var actionHtml = '<div class="btn-group-vertical">' +
          '<button class="btn btn-info btn-xs btn-view-log" data-key="' + file.key + '" title="View File">' +
          '<i class="fa fa-eye"></i> View' +
          '</button>' +
          '<a href="<?php echo base_url("index.php/log/download_file"); ?>/' + file.key + '" class="btn btn-default btn-xs text-olive" title="Download File" style="margin-top: 2px;">' +
          '<i class="fa fa-download"></i> Download' +
          '</a>' +
          '</div>';
          
      table.row.add([
        sourceLabel,
        detailsHtml,
        sizeHtml,
        actionHtml
      ]);
    });
    
    table.draw();
  }

  // Row selection trigger on clicking log row
  $(document).on('click', '.log-file-row', function(e) {
    if ($(e.target).closest('button, a').length > 0) return;
    
    var key = $(this).find('.btn-view-log').data('key');
    $('#log_files_table tbody tr').removeClass('active-log');
    $(this).addClass('active-log');
    
    loadLogFile(key);
  });

  // When View button is clicked
  $(document).on('click', '.btn-view-log', function(e) {
    e.preventDefault();
    var key = $(this).data('key');
    $('#log_files_table tbody tr').removeClass('active-log');
    $(this).closest('tr').addClass('active-log');
    
    loadLogFile(key);
  });

  // Function to load log contents via AJAX
  function loadLogFile(key) {
    currentKey = key;
    
    $('#viewer-empty-state').hide();
    $('#viewer-content-area').hide();
    $('#viewer-loader').show();
    $('#viewer-actions').hide();
    
    $.ajax({
      url: '<?php echo base_url("index.php/log/ajax_get_file_content"); ?>',
      type: 'POST',
      data: { key: key },
      dataType: 'json',
      success: function(res) {
        $('#viewer-loader').hide();
        
        if (res.status) {
          $('#viewer-title').html('<i class="fa fa-terminal text-green"></i> viewing: ' + res.filename);
          $('#log-meta-path').text(res.path);
          $('#log-meta-size').text(res.size);
          
          if (res.truncated) {
            $('#log-truncated-warning').show();
          } else {
            $('#log-truncated-warning').hide();
          }
          
          $('#btn-download-log').attr('href', '<?php echo base_url("index.php/log/download_file"); ?>/' + key);
          $('#viewer-actions').show();
          
          rawLogLines = res.content.split(/\r?\n/);
          renderLogLines();
          
          $('#viewer-content-area').show();
          scrollToBottom();
          
          $('#log-search').val('');
          $('#btn-toggle-filter-view').attr('data-filtered', 'false').removeClass('btn-warning').addClass('btn-info').text('Filter View');
        } else {
          alert('Error: ' + res.message);
          $('#viewer-empty-state').show();
        }
      },
      error: function(xhr, status, error) {
        $('#viewer-loader').hide();
        alert('An error occurred while loading log file: ' + error);
        $('#viewer-empty-state').show();
      }
    });
  }

  // Render processed lines to the log element
  function renderLogLines(filterText = "", onlyMatches = false) {
    var html = "";
    var searchLower = filterText.toLowerCase();
    
    for (var i = 0; i < rawLogLines.length; i++) {
      var line = rawLogLines[i];
      var lineLower = line.toLowerCase();
      var isMatch = searchLower === "" || lineLower.indexOf(searchLower) !== -1;
      
      if (onlyMatches && !isMatch) {
        continue;
      }
      
      var displayLine = line;
      if (filterText !== "" && isMatch) {
        var regex = new RegExp(escapeRegExp(filterText), "gi");
        displayLine = line.replace(regex, function(match) {
          return '<span class="highlight-match">' + match + '</span>';
        });
      }
      
      var cls = "";
      if (lineLower.indexOf('error') !== -1 || lineLower.indexOf('fail') !== -1 || lineLower.indexOf('exception') !== -1 || lineLower.indexOf('severity: error') !== -1) {
        cls = "log-error";
      } else if (lineLower.indexOf('warning') !== -1 || lineLower.indexOf('warn') !== -1) {
        cls = "log-warning";
      } else if (lineLower.indexOf('success') !== -1 || lineLower.indexOf('approved') !== -1 || lineLower.indexOf('verified') !== -1) {
        cls = "log-success";
      } else if (lineLower.indexOf('info') !== -1) {
        cls = "log-info";
      } else if (lineLower.indexOf('directapi') !== -1) {
        cls = "log-direct";
      }
      
      var lineNumber = i + 1;
      html += '<div class="log-line ' + cls + '">' +
                '<span class="log-line-num">' + lineNumber + '</span>' +
                displayLine +
              '</div>';
    }
    
    $('#log-content').html(html || '<div class="text-muted text-center" style="padding: 20px;">No matching log lines found.</div>');
  }

  // Live search input handler
  $('#log-search').on('keyup input', function() {
    var searchVal = $(this).val();
    var onlyMatches = $('#btn-toggle-filter-view').attr('data-filtered') === 'true';
    renderLogLines(searchVal, onlyMatches);
  });

  // Clear search
  $('#btn-clear-search').on('click', function() {
    $('#log-search').val('');
    var onlyMatches = $('#btn-toggle-filter-view').attr('data-filtered') === 'true';
    renderLogLines('', onlyMatches);
  });

  // Toggle filter view (only display matches)
  $('#btn-toggle-filter-view').on('click', function() {
    var isFiltered = $(this).attr('data-filtered') === 'true';
    var searchVal = $('#log-search').val();
    
    if (isFiltered) {
      $(this).attr('data-filtered', 'false').removeClass('btn-warning').addClass('btn-info').text('Filter View');
      renderLogLines(searchVal, false);
    } else {
      $(this).attr('data-filtered', 'true').removeClass('btn-info').addClass('btn-warning').text('Showing Matches Only');
      renderLogLines(searchVal, true);
    }
  });

  // Refresh current file
  $('#btn-refresh-log').on('click', function() {
    if (currentKey) {
      loadLogFile(currentKey);
    }
  });

  // Scroll to bottom
  $('#btn-scroll-bottom').on('click', function() {
    scrollToBottom();
  });

  function scrollToBottom() {
    var logContent = $('#log-content');
    if (logContent.length) {
      logContent.scrollTop(logContent[0].scrollHeight);
    }
  }

  // Copy to clipboard
  $('#btn-copy-log').on('click', function() {
    var logText = rawLogLines.join('\n');
    var tempTextarea = $('<textarea>');
    $('body').append(tempTextarea);
    tempTextarea.val(logText).select();
    document.execCommand('copy');
    tempTextarea.remove();
    
    var origText = $(this).html();
    $(this).html('<i class="fa fa-check text-green"></i> Copied!');
    var btn = $(this);
    setTimeout(function() {
      btn.html(origText);
    }, 2000);
  });

  function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }
}

// Safe initialization after jQuery and datepicker load from footer
if (window.jQuery && window.jQuery.fn && window.jQuery.fn.datepicker) {
  initLogViewer();
} else {
  window.addEventListener('load', function() {
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.datepicker) {
      initLogViewer();
    } else {
      var interval = setInterval(function() {
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.datepicker) {
          clearInterval(interval);
          initLogViewer();
        }
      }, 50);
    }
  });
}
</script>
