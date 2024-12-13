<?php echo $header; ?>
<style>.scan-name{cursor: pointer;}span.label{font-size:10pt}table.table-security *{font-family:Consolas;font-size:9pt}table.table-security{border:1px solid #ccf;width:100%;border-collapse:collapse;border:1px solid #8892BF}table.table-security > thead > tr{background:#ccf}table.table-security > thead > tr > th{background:#8892BF;padding:3px 5px;color:#fff;text-align:left;border-bottom:1px solid #8892BF}table.table-security > thead > tr > th + th{border-left:1px solid #99c}table.table-security > tbody > tr > td{padding:3px}table.table-security > tbody > tr:nth-child(odd){background:#eef}table.table-security > tbody > tr:hover{background:#ddf}.changes-list{line-height:38px}.security-scan{margin:0;border:1px solid #e5e5e5;padding:8px 10px;margin-bottom:15px}.security-scan .scan-name a{color:#4e575b;font-size:15px;font-weight:700;text-decoration:none;}.changed{background:#4F5B93;color:#fff}#security-scan-view h4{margin-bottom:15px;font-weight:700}.accordeon_toggle{cursor:pointer}table.table-security.table-scanned{display:none}table td a{color:#666}table td.column_size{/*text-align:right*/}table th.column_type{width:50px}table th.column_size{width:150px}table th.column_mtime{width:140px}table th.column_ctime{width:140px}table th.column_rights{width:50px}table th.column_crc{width:100px}</style>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $action_settings; ?>" data-toggle="tooltip" title="<?php echo $button_settings; ?>" id="button-settings" class="btn btn-primary"><i class="fa fa-cog"></i></a>
        <a href="<?php echo $action_cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
        <div id="security-scan-view">
          <div class="security-scan row">
            <div class="scan-heading pull-left col-sm-4">
              <div class="scan-name"><a href="#" class="scan_name"><?php echo $scan['name'] ?></a></div>
              <div class="scan-date-added"><b><?php echo $scan['user_name'] ?></b>, <?php echo $scan['date_added_ago'] ?></div>
            </div>
            <div class="changes-list col-sm-8">
              <?php if ($scan['scanned_count']): ?>
              <a href="<?php echo $scan['href']; ?>#scanned"><span class="files-scanned label label-default" data-toggle="tooltip" title="<?php echo $text_label_scanned; ?>">  <div class="fa fa-file-o"></div> <?php echo $scan['scanned_count'] ?></span></a>
              <?php endif ?>
              <?php if ($scan['new_count']): ?>
              <a href="<?php echo $scan['href']; ?>#new"><span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $text_label_new; ?>">    <div class="fa fa-plus"></div> <?php echo $scan['new_count'] ?></span></a>
              <?php endif ?>
              <?php if ($scan['changed_count']): ?>
              <a href="<?php echo $scan['href']; ?>#changed"><span class="files-changed label label-warning" data-toggle="tooltip" title="<?php echo $text_label_changed; ?>">  <div class="fa fa-ellipsis-h"></div> <?php echo $scan['changed_count'] ?></span></a>
              <?php endif ?>
              <?php if ($scan['deleted_count']): ?>
              <a href="<?php echo $scan['href']; ?>#deleted"><span class="files-deleted label label-danger" data-toggle="tooltip" title="<?php echo $text_label_deleted; ?>">  <div class="fa fa-minus"></div> <?php echo $scan['deleted_count'] ?></span></a>
              <?php endif ?>
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
          </div>
          <?php if ($scan['new_count']): ?>
            <h4 id="new" onClick="$('.table-new').toggle(1);" class="accordeon_toggle"><?php echo $text_label_new ?>&nbsp;&nbsp;<span class="label label-success">
              <div class="fa fa-plus"></div> <?php echo $scan['new_count'] ?>
            </span>
            </h4>
            <table class="table table-security table-new">
              <thead>
                <tr>
                  <th class="column_name"><?php echo $text_column_name ?></th>
                  <th class="column_type" width="50"><?php echo $text_column_type ?></th>
                  <th class="column_size" width="150"><?php echo $text_column_size ?></th>
                  <th class="column_mtime" width="140"><?php echo $text_column_mtime ?></th>
                  <th class="column_ctime" width="140"><?php echo $text_column_ctime ?></th>
                  <th class="column_rights" width="50"><?php echo $text_column_rights ?></th>
                  <th class="column_crc" width="100"><?php echo $text_column_crc ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($scan['scan_data']['new'] as $file_name => $file_data): ?>
                <tr>
                  <?php if ($file_data['writable']): ?>
                  <td><a href="<?php echo $action_file ?>&file_name=<?php echo urlencode($file_name) ?>" target="_blank"><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></a></td>
                  <?php else: ?>
                  <td><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></td>
                  <?php endif ?>
                  <td><?php echo pathinfo($file_name, PATHINFO_EXTENSION); ?></td>
                  <td class="column_size"><?php echo $file_data['filesize_humanized']; ?></td>
                  <td><?php echo date('d.m.Y H:i:s',$file_data['filemtime']) ?></td>
                  <td><?php echo date('d.m.Y H:i:s',$file_data['filectime']); ?></td>
                  <td><?php echo substr(decoct($file_data['fileperms']), -4); ?></td>
                  <td><?php echo $file_data['crc']; ?></td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          <?php endif ?>
          <?php if ($scan['changed_count']): ?>
            <h4 id="changed" onClick="$('.table-changed').toggle(1);" class="accordeon_toggle">
              <?php echo $text_label_changed ?>&nbsp;&nbsp;<span class="label label-warning">
                <div class="fa fa-ellipsis-h"></div> <?php echo $scan['changed_count'] ?>
              </span>
            </h4>
            <table class="table table-security table-changed">
              <thead>
                <tr>
                  <th class="column_name"><?php echo $text_column_name ?></th>
                  <th class="column_type" width="50"><?php echo $text_column_type ?></th>
                  <th class="column_size" width="150"><?php echo $text_column_size ?></th>
                  <th class="column_mtime" width="140"><?php echo $text_column_mtime ?></th>
                  <th class="column_ctime" width="140"><?php echo $text_column_ctime ?></th>
                  <th class="column_rights" width="50"><?php echo $text_column_rights ?></th>
                  <th class="column_crc" width="100"><?php echo $text_column_crc ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($scan['scan_data']['changed'] as $file_name => $file_data): ?>
                <tr>
                  <?php if ($file_data['writable']): ?>
                  <td><a href="<?php echo $action_file ?>&file_name=<?php echo urlencode($file_name) ?>" target="_blank"><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></a></td>
                  <?php else: ?>
                  <td><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></td>
                  <?php endif ?>
                  <td><?php echo pathinfo($file_name, PATHINFO_EXTENSION); ?></td>
                  <td <?php echo isset($file_data['diff']['filesize']) ? 'class="changed column_size"' : 'class="column_size"'; ?>><?php echo $file_data['new']['filesize_humanized']; ?></td>
                  <td <?php echo isset($file_data['diff']['filemtime']) ? 'class="changed"' : ''; ?>><?php echo date('d.m.Y H:i:s', $file_data['new']['filemtime']) ?></td>
                  <td <?php echo isset($file_data['diff']['filectime']) ? 'class="changed"' : ''; ?>><?php echo date('d.m.Y H:i:s', $file_data['new']['filectime']); ?></td>
                  <td <?php echo isset($file_data['diff']['fileperms']) ? 'class="changed"' : ''; ?>><?php echo substr(decoct($file_data['new']['fileperms']), -4); ?></td>
                  <td <?php echo isset($file_data['diff']['crc']) ? 'class="changed"' : ''; ?>><?php echo $file_data['new']['crc']; ?></td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          <?php endif ?>
          <?php if ($scan['deleted_count']): ?>
            <h4 id="deleted" onClick="$('.table-deleted').toggle(1);" class="accordeon_toggle">
              <?php echo $text_label_deleted ?>&nbsp;&nbsp;<span class="label label-danger">
                <div class="fa fa-minus"></div> <?php echo $scan['deleted_count'] ?>
              </span>
            </h4>
            <table class="table table-security table-deleted">
              <thead>
                <tr>
                  <th class="column_name"><?php echo $text_column_name ?></th>
                  <th class="column_type" width="50"><?php echo $text_column_type ?></th>
                  <th class="column_size" width="150"><?php echo $text_column_size ?></th>
                  <th class="column_mtime" width="140"><?php echo $text_column_mtime ?></th>
                  <th class="column_ctime" width="140"><?php echo $text_column_ctime ?></th>
                  <th class="column_rights" width="50"><?php echo $text_column_rights ?></th>
                  <th class="column_crc" width="100"><?php echo $text_column_crc ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($scan['scan_data']['deleted'] as $file_name => $file_data): ?>
                <tr>
                  <?php if ($file_data['writable']): ?>
                  <td><a href="<?php echo $action_file ?>&file_name=<?php echo urlencode($file_name) ?>" target="_blank"><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></a></td>
                  <?php else: ?>
                  <td><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></td>
                  <?php endif ?>
                  <td><?php echo pathinfo($file_name, PATHINFO_EXTENSION); ?></td>
                  <td class="column_size"><?php echo $file_data['filesize_humanized']; ?></td>
                  <td><?php echo date('d.m.Y H:i:s',$file_data['filemtime']) ?></td>
                  <td><?php echo date('d.m.Y H:i:s',$file_data['filectime']); ?></td>
                  <td><?php echo substr(decoct($file_data['fileperms']), -4); ?></td>
                  <td><?php echo $file_data['crc']; ?></td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          <?php endif ?>
          <?php if ($scan['scanned_count']): ?>
            <h4 id="scanned" onClick="$('.table-scanned').toggle(1);" class="accordeon_toggle">
              <?php echo $text_label_scanned ?>&nbsp;&nbsp;<span class="label label-default">
                <div class="fa fa-file-o"></div> <?php echo $scan['scanned_count'] ?>
              </span>
            </h4>
            <table class="table table-security table-scanned">
              <thead>
                <tr>
                  <th class="column_name"><?php echo $text_column_name ?></th>
                  <th class="column_type" width="50"><?php echo $text_column_type ?></th>
                  <th class="column_size" width="150"><?php echo $text_column_size ?></th>
                  <th class="column_mtime" width="140"><?php echo $text_column_mtime ?></th>
                  <th class="column_ctime" width="140"><?php echo $text_column_ctime ?></th>
                  <th class="column_rights" width="50"><?php echo $text_column_rights ?></th>
                  <th class="column_crc" width="100"><?php echo $text_column_crc ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($scan['scan_data']['scanned'] as $file_name => $file_data): ?>
                <tr>
                  <?php if ($file_data['writable']): ?>
                  <td><a href="<?php echo $action_file ?>&file_name=<?php echo urlencode($file_name) ?>" target="_blank"><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></a></td>
                  <?php else: ?>
                  <td><?php echo str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name); ?></td>
                  <?php endif ?>
                  <td><?php echo pathinfo($file_name, PATHINFO_EXTENSION); ?></td>
                  <td class="column_size"><?php echo $file_data['filesize_humanized']; ?></td>
                  <td><?php echo date('d.m.Y H:i:s',$file_data['filemtime']) ?></td>
                  <td><?php echo date('d.m.Y H:i:s',$file_data['filectime']); ?></td>
                  <td><?php echo substr(decoct($file_data['fileperms']), -4); ?></td>
                  <td><?php echo $file_data['crc']; ?></td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="renameScan" tabindex="0" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $button_cancel ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $text_modal_rename_title ?></h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $action_rename; ?>" method="post" enctype="multipart/form-data" id="form-rename">
          <div class="form-group required">
            <label for="scan_name" class="control-label"><?php echo $entry_scan_name ?></label>
            <input type="text" class="form-control" name="scan_name" id="scan_name" placeholder="<?php echo $text_scan_name_placeholder ?>" autocomplete="off">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel ?></button>
        <button type="submit" id="renameNow" data-loading-text="<?php echo $button_rename_loading ?>" class="btn btn-success"><?php echo $button_rename ?></button>
      </div>
    </div>
  </div>
</div>

<script>
  $('td.not_writable a').on('click', function(event) {
    event.preventDefault();
  });
  $('.scan_name').on('click', function(event) {
    event.preventDefault();
    $('#scan_name').val($(this).text());
    $('#renameScan').modal();
  });
    $('#renameNow').click(function(event){
    event.preventDefault();
    $('#form-rename').submit();
  });

  $('#renameScan').on('shown.bs.modal', function (event) {
    $('#scan_name').focus();
  });

  $('form#form-rename').on('submit', function(event){
    $('#renameNow').button('loading');
  });

</script>
<?php echo $footer; ?>