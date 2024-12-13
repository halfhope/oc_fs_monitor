<?php echo $header; ?>
<style>svg.octicon{fill:currentColor;vertical-align:text-bottom;color:#ccc;background:#fff;z-index:2;position:relative}.security-scans-container{position:relative}.security-scan-list{padding-left:25px}.security-scan-list:before{position:absolute;top:0;bottom:0;left:6px;z-index:1;display:block;width:2px;content:"";background-color:#f3f3f3}.security-scan-list .day{margin-left:-25px;font-size:14px;color:#767676;padding-bottom:10px}.security-scan-list .scan-list-checkbox{width:30px;padding-left:5px;line-height:38px;display:inline-block;float:left}.security-scan-list .security-scan + .day{padding-top:10px}.security-scan-list .day span{margin-left:7px}.security-scan{margin:0;border:1px solid #e5e5e5;padding:8px 10px}.security-scan:hover{background:#f7fbfc}.security-scan.row .scan-heading{padding-left:0}.security-scan + .security-scan{border-top:none}.security-scan .scan-name{font-size:15px;font-weight:700}.security-scan .scan-name a{color:#4e575b}.security-scan .scan-date-added{color:#767676}.security-scan .changes-list{color:#767676;line-height:38px}.security-scan span.label{font-size:10pt}</style>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="#" data-toggle="tooltip" title="<?php echo $button_scan; ?>" id="button-scan" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="#" data-toggle="tooltip" title="<?php echo $button_delete; ?>" id="button-delete" class="btn btn-danger" disabled="disabled"><i class="fa fa-trash-o"></i></a>
        <a href="<?php echo $action_settings; ?>" data-toggle="tooltip" title="<?php echo $button_settings; ?>" id="button-settings" class="btn btn-primary"><i class="fa fa-cog"></i></a>
        <!-- <a href="<?php echo $action_cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a> -->
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $panel_title; ?></h3>
      </div>
      <div class="panel-body">
        <div class="security-scans-container">
          <form action="<?php echo $action_delete; ?>" method="post" enctype="multipart/form-data" id="form-scans-list">
            <div class="security-scan-list">
              <?php foreach ($scans as $date_key => $date_scans): ?>
              <div class="day">
                <svg aria-hidden="true" class="octicon" height="16" version="1.1" viewBox="0 0 14 16" width="14"><path d="M10.86 7c-.45-1.72-2-3-3.86-3-1.86 0-3.41 1.28-3.86 3H0v2h3.14c.45 1.72 2 3 3.86 3 1.86 0 3.41-1.28 3.86-3H14V7h-3.14zM7 10.2c-1.22 0-2.2-.98-2.2-2.2 0-1.22.98-2.2 2.2-2.2 1.22 0 2.2.98 2.2 2.2 0 1.22-.98 2.2-2.2 2.2z"></path></svg>
                <span><?php echo $date_key ?></span>
              </div>
              <?php foreach ($date_scans as $key => $scan): ?>
              <div class="security-scan row">

                <div class="scan-heading pull-left col-sm-4 col-xs-12">
                  <div class="scan-list-checkbox">
                    <input type="checkbox" name="scans[<?php echo $scan['scan_id'] ?>]" value="<?php echo $scan['scan_id'] ?>">
                  </div>
                  <div class="scan-name"><a href="<?php echo $scan['href'] ?>"><?php echo $scan['name'] ?></a></div>
                  <div class="scan-date-added"><b><?php echo $scan['user_name'] ?></b>, <?php echo $scan['date_added_ago'] ?></div>
                </div>

                <div class="changes-list col-sm-3 col-xs-6">
                  <?php if ($scan['scanned_count']): ?>
                  <a href="<?php echo $scan['href'] ?>#scanned"><span class="files-scanned label label-default" data-toggle="tooltip" title="<?php echo $text_label_scanned; ?>">  <div class="fa fa-file-o"></div> <?php echo $scan['scanned_count'] ?></span></a>
                  <?php endif ?>

                  <?php if ($scan['new_count']): ?>
                  <a href="<?php echo $scan['href'] ?>#new"><span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $text_label_new; ?>">    <div class="fa fa-plus"></div> <?php echo $scan['new_count'] ?></span></a>
                  <?php endif ?>

                  <?php if ($scan['changed_count']): ?>
                  <a href="<?php echo $scan['href'] ?>#changed"><span class="files-changed label label-warning" data-toggle="tooltip" title="<?php echo $text_label_changed; ?>">  <div class="fa fa-ellipsis-h"></div> <?php echo $scan['changed_count'] ?></span></a>
                  <?php endif ?>

                  <?php if ($scan['deleted_count']): ?>
                  <a href="<?php echo $scan['href'] ?>#deleted"><span class="files-deleted label label-danger" data-toggle="tooltip" title="<?php echo $text_label_deleted; ?>">  <div class="fa fa-minus"></div> <?php echo $scan['deleted_count'] ?></span></a>
                  <?php endif ?>
                </div>

                <div class="changes-list col-sm-3 col-xs-3">
                  <?php if ($scan['scan_size_rel'] == 0): ?>
                    <span class="label label-info" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>"><?php echo $scan['scan_size_rel_humanized'] ?></span>
                  <?php else: ?>
                    <?php if ($scan['scan_size_rel'] > 0): ?>
                    <span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>">    <div class="fa fa-plus"></div> <?php echo $scan['scan_size_rel_humanized'] ?></span>
                    <?php else: ?>
                    <span class="files-added label label-danger" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>">    <div class="fa fa-minus"></div> <?php echo $scan['scan_size_rel_humanized'] ?></span>
                    <?php endif ?>
                  <?php endif ?>
                </div>

                <div class="changes-list col-sm-2 col-xs-3">
                  <div class="pull-right">
                    <a href="<?php echo $scan['href']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-default"><i class="fa fa-eye"></i></a>
                  </div>
                </div>

              </div>
              <?php endforeach ?>

              <?php endforeach ?>

            </div>
          </form>
        </div>
        <div class="pagination"><?php echo $pagination ?></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addScan" tabindex="0" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $button_cancel ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $text_modal_title ?></h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $action_scan; ?>" method="post" enctype="multipart/form-data" id="form-scan">
          <div class="form-group required">
            <label for="scan_name" class="control-label"><?php echo $entry_scan_name ?></label>
            <input type="text" class="form-control" name="scan_name" id="scan_name" placeholder="<?php echo $text_scan_name_placeholder ?>" autocomplete="off">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel ?></button>
        <button type="submit" id="scanNow" data-loading-text="<?php echo $button_scan_loading ?>" class="btn btn-success"><?php echo $button_scan ?></button>
      </div>
    </div>
  </div>
</div>
<script>
  $('#button-scan').click(function(event) {
    event.preventDefault();
    $('#addScan').modal();
  });

  $('#scanNow').click(function(event){
    event.preventDefault();
    $('#form-scan').submit();
  });

  $('#addScan').on('shown.bs.modal', function (event) {
    $('#scan_name').focus();
  });

  $('form#form-scan').on('submit', function(event){
    $('#scanNow').button('loading');
  });

  $('input[type="checkbox"][name^="scans"]').click(function(event) {
    var checked = $('input[type="checkbox"][name^="scans"]:checked').length;
    if (checked >= 1) {
      $('#button-delete').attr('disabled', false);
    }else{
      $('#button-delete').attr('disabled', true);
    }
  });

  $('#button-delete').on('click', function(event) {
    event.preventDefault();
    var checked = $('input[type="checkbox"][name^="scans"]:checked').length;
    if (checked >= 1) {
      $('#form-scans-list').submit();
    }
  });
</script>
<?php echo $footer; ?>