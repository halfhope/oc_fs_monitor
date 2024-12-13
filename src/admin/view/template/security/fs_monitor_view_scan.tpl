<?php echo $header; ?>
<style>a:hover,a:focus:a:visited{color:#14628c;text-decoration:none;}table td a{color: #666;text-decoration: none;}.scan-date-added{color:#767676;padding-top:3px;}span.label{font-size:10pt}table.table-security *{font-family:Consolas;font-size:9pt}table.table-security{border:1px solid #ccf;width:100%;border-collapse:collapse;border:1px solid #8892BF}table.table-security > thead > tr{background:#ccf}table.table-security > thead > tr > th{background:#8892BF;padding:3px 5px;color:#fff;text-align:left;border-bottom:1px solid #8892BF}table.table-security > thead > tr > th + th{border-left:1px solid #99c}table.table-security > tbody > tr > td{padding:3px}table.table-security > tbody > tr:nth-child(odd){background:#eef}table.table-security > tbody > tr:hover{background:#ddf}.changes-list{line-height:38px}.security-scan{margin:0;border:1px solid #e5e5e5;padding:8px 10px;margin-bottom:15px}.security-scan .scan-name{color:#4e575b;font-size:15px;font-weight:700}.changed{background:#4F5B93;color:#fff}#security-scan-view h4{margin-bottom:15px;font-weight:700;color:#444;font-size:14px;}.accordeon_toggle{cursor:pointer}table.table-security.table-scanned{display:none}table td a{color:#666}table th.column_type{width:50px}table th.column_size{width:150px}table th.column_mtime{width:140px}table th.column_ctime{width:140px}table th.column_rights{width:50px}table th.column_crc{width:100px}.label{display:inline-block;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em}.label:empty{display:none}.btn .label{position:relative;top:-1px}a.label:hover,a.label:focus{color:#fff;text-decoration:none;cursor:pointer}.changes-list a{text-decoration:none}.label-default{background-color:#777}.label-default[href]:hover,.label-default[href]:focus{background-color:#5e5e5e}.label-primary{background-color:#1e91cf}.label-primary[href]:hover,.label-primary[href]:focus{background-color:#1872a2}.label-success{background-color:#8fbb6c}.label-success[href]:hover,.label-success[href]:focus{background-color:#75a74d}.label-info{background-color:#5bc0de}.label-info[href]:hover,.label-info[href]:focus{background-color:#31b0d5}.label-warning{background-color:#f38733}.label-warning[href]:hover,.label-warning[href]:focus{background-color:#e66c0e}.label-danger{background-color:#f56b6b}.label-danger[href]:hover,.label-danger[href]:focus{background-color:#f23b3b}.col-sm-4{width:33.33333%}.col-sm-3{width:25%}.col-sm-2{width:16.66667%}.col-sm-8{width:66.66667%}.pull-right{float:right}.pull-left{float:left}.col-sm-2,.col-sm-3,.col-sm-4{position:relative;min-height:1px;padding-left:15px;padding-right:15px;vertical-align:middle}</style>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?php echo $panel_title; ?></h1>
      <div class="buttons">
        <a href="<?php echo $action_settings; ?>" title="<?php echo $button_settings; ?>" class="button"><?php echo $button_settings; ?></a>
        <a href="<?php echo $action_cancel; ?>" title="<?php echo $button_cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
      </div>
    </div>
    <div class="content">
      <div id="security-scan-view">

        <div class="security-scan row">
          <div class="scan-heading pull-left col-sm-4">
            <div class="scan-name"><?php echo $scan['name'] ?></div>
            <div class="scan-date-added"><b><?php echo $scan['user_name'] ?></b>, <?php echo $scan['date_added_ago'] ?></div>
          </div>
          <div class="changes-list col-sm-8">
            <?php if ($scan['scan_data']['scanned']): ?>
            <a href="<?php echo $scan['href']; ?>#scanned"><span class="files-scanned label label-default" data-toggle="tooltip" title="<?php echo $text_label_scanned; ?>"><?php echo $scan['scan_data']['scanned_count'] ?></span></a>
            <?php endif ?>
            <?php if ($scan['scan_data']['new']): ?>
            <a href="<?php echo $scan['href']; ?>#new"><span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $text_label_new; ?>">+<?php echo $scan['scan_data']['new_count'] ?></span></a>
            <?php endif ?>
            <?php if ($scan['scan_data']['changed']): ?>
            <a href="<?php echo $scan['href']; ?>#changed"><span class="files-changed label label-warning" data-toggle="tooltip" title="<?php echo $text_label_changed; ?>">...<?php echo $scan['scan_data']['changed_count'] ?></span></a>
            <?php endif ?>
            <?php if ($scan['scan_data']['deleted']): ?>
            <a href="<?php echo $scan['href']; ?>#deleted"><span class="files-deleted label label-danger" data-toggle="tooltip" title="<?php echo $text_label_deleted; ?>">-<?php echo $scan['scan_data']['deleted_count'] ?></span></a>
            <?php endif ?>
            <?php if ($scan['scan_data']['scan_size_compared'] == 0): ?>
              <span class="label label-info" data-toggle="tooltip" title="<?php echo $scan['scan_data']['scan_size_humanized'] ?>"><?php echo $scan['scan_data']['scan_size_compared_humanized'] ?></span>
            <?php else: ?>
              <?php if ($scan['scan_data']['size_up']): ?>
              <span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $scan['scan_data']['scan_size_humanized'] ?>">+<?php echo $scan['scan_data']['scan_size_compared_humanized'] ?></span>
              <?php else: ?>
              <span class="files-added label label-danger" data-toggle="tooltip" title="<?php echo $scan['scan_data']['scan_size_humanized'] ?>">-<?php echo $scan['scan_data']['scan_size_compared_humanized'] ?></span>
              <?php endif ?>
            <?php endif ?>
          </div>
        </div>
        <?php if ($scan['scan_data']['new']): ?>
          <h4 id="new" onClick="$('.table-new').toggle(1);" class="accordeon_toggle"><?php echo $text_label_new ?>&nbsp;&nbsp;<span class="label label-success">
            +<?php echo $scan['scan_data']['new_count'] ?>
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
        <?php if ($scan['scan_data']['changed']): ?>
          <h4 id="changed" onClick="$('.table-changed').toggle(1);" class="accordeon_toggle">
            <?php echo $text_label_changed ?>&nbsp;&nbsp;<span class="label label-warning">
              ...<?php echo $scan['scan_data']['changed_count'] ?>
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
        <?php if ($scan['scan_data']['deleted']): ?>
          <h4 id="deleted" onClick="$('.table-deleted').toggle(1);" class="accordeon_toggle">
            <?php echo $text_label_deleted ?>&nbsp;&nbsp;<span class="label label-danger">
              -<?php echo $scan['scan_data']['deleted_count'] ?>
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
        <?php if ($scan['scan_data']['scanned']): ?>
          <h4 id="scanned" onClick="$('.table-scanned').toggle(1);" class="accordeon_toggle">
            <?php echo $text_label_scanned ?>&nbsp;&nbsp;<span class="label label-default">
              <?php echo $scan['scan_data']['scanned_count'] ?>
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
<script>
  $('td.not_writable a').on('click', function(event) {
    event.preventDefault();
  });
</script>
<?php echo $footer; ?>