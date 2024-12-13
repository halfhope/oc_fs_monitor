<?php echo $header; ?>
<div id="content">
	<style>@import url(//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,greek-ext,vietnamese);#security-scan-view {font-family: 'Open Sans', sans-serif;}html{overflow-y: scroll;}</style>
	<style>@import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css);</style>
	<style>
	a:hover,a:focus:a:visited{color:#14628c;text-decoration:none}
	table td a{color:#666;text-decoration:none}
	.scan-date-added{color:#767676;padding-top:3px}
	span.label{font-size:10pt}
	table.table-security *{font-family:'Consolas'!important;font-size:9pt}
	table.table-security{border:1px solid #ccf;width:100%;border-collapse:collapse;border:1px solid #8892BF;table-layout:fixed}
	table.table-security > thead > tr{background:#ccf}
	table.table-security > thead > tr > th{background:#8892BF;padding:3px 5px;color:#fff;text-align:left;border-bottom:1px solid #8892BF}
	table.table-security > thead > tr > th + th{border-left:1px solid #99c}
	table.table-security > tbody > tr > td{padding:3px}
	table.table-security > tbody > tr > td:first-child{overflow:hidden;white-space:nowrap;}
	table.table-security > tbody > tr:nth-child(odd){background:#eef}
	table.table-security > tbody > tr:hover{background:#ddf}
	.changes-list{line-height:38px}
	.security-scan{margin:0;border:1px solid #e5e5e5;padding:8px 10px;margin-bottom:15px}
	.security-scan .scan-name a{color:#4e575b;font-size:15px;font-weight:700;text-decoration:none}
	.changed{background:#4F5B93;color:#fff}
	#security-scan-view .table_caption{margin:1em 0;font-weight:700;color:#444;font-size:14px}
	.accordeon_toggle{cursor:pointer}
	table.table-security.table-hidden{display:none}
	table td a{color:#666}
	table th.column_type{width:50px}
	table th.column_size{width:150px}
	table th.column_mtime{width:140px}
	table th.column_ctime{width:140px}
	table th.column_rights{width:50px}
	table th.column_crc{width:100px}
	.label{display:inline;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em}
	.label:empty{display:none}
	.btn .label{position:relative;top:-1px}
	a.label:hover,a.label:focus{color:#fff;text-decoration:none;cursor:pointer}
	.changes-list a{text-decoration:none}
	.label-default{background-color:#777}
	.label-default[href]:hover,.label-default[href]:focus{background-color:#5e5e5e}
	.label-primary{background-color:#1e91cf}
	.label-primary[href]:hover,.label-primary[href]:focus{background-color:#1872a2}
	.label-success{background-color:#8fbb6c}
	.label-success[href]:hover,.label-success[href]:focus{background-color:#75a74d}
	.label-info{background-color:#5bc0de}
	.label-info[href]:hover,.label-info[href]:focus{background-color:#31b0d5}
	.label-warning{background-color:#f38733}
	.label-warning[href]:hover,.label-warning[href]:focus{background-color:#e66c0e}
	.label-danger{background-color:#f56b6b}
	.label-danger[href]:hover,.label-danger[href]:focus{background-color:#f23b3b}
	.col-sm-4{width:33.33333%}
	.col-sm-3{width:25%}
	.col-sm-2{width:16.66667%}
	.col-sm-8{width:66.66667%}
	.pull-right{float:right}
	.pull-left{float:left}
	.col-sm-2,.col-sm-3,.col-sm-4{position:relative;min-height:1px;padding-left:15px;padding-right:15px;vertical-align:middle}
	.btn-copy{cursor:pointer}
	</style>
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<?php if ($success) { ?>
	<div class="success"><?php echo $success; ?></div>
	<?php } ?>
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
						<div class="scan-name"><a href="#" class="scan_name"><?php echo $scan['name'] ?></a></div>
						<div class="scan-date-added"><b><?php echo $scan['user_name'] ?></b>, <?php echo $scan['date_added_ago'] ?></div>
					</div>
					<div class="changes-list col-sm-8">
						<?php if ($scan['scanned_count']): ?>
						<a href="<?php echo $scan['href']; ?>#scanned">
							<span class="files-scanned label label-default" data-toggle="tooltip" title="<?php echo $text_label_scanned; ?>">
								<div class="fa fa-file-o"></div>&nbsp;<?php echo $scan['scanned_count'] ?>
							</span>
						</a>&nbsp;
						<?php endif ?>
						<?php if ($scan['new_count']): ?>
						<a href="<?php echo $scan['href']; ?>#new">
							<span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $text_label_new; ?>">
								<div class="fa fa-plus"></div>&nbsp;<?php echo $scan['new_count'] ?>
							</span>
						</a>&nbsp;
						<?php endif ?>
						<?php if ($scan['changed_count']): ?>
						<a href="<?php echo $scan['href']; ?>#changed">
							<span class="files-changed label label-warning" data-toggle="tooltip" title="<?php echo $text_label_changed; ?>">
								<div class="fa fa-ellipsis-h"></div>&nbsp;<?php echo $scan['changed_count'] ?>
							</span>
						</a>&nbsp;
						<?php endif ?>
						<?php if ($scan['deleted_count']): ?>
						<a href="<?php echo $scan['href']; ?>#deleted">
							<span class="files-deleted label label-danger" data-toggle="tooltip" title="<?php echo $text_label_deleted; ?>">
								<div class="fa fa-minus"></div>&nbsp;<?php echo $scan['deleted_count'] ?>
							</span>
						</a>&nbsp;
						<?php endif ?>
						<?php if ($scan['scan_size_rel'] == 0): ?>
							<span class="label label-info" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>"><?php echo $scan['scan_size_rel_humanized'] ?></span>
						<?php else: ?>
							<?php if ($scan['scan_size_rel'] > 0): ?>
							<span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>">
								<div class="fa fa-plus"></div>&nbsp;<?php echo $scan['scan_size_rel_humanized'] ?>
							</span>
							<?php else: ?>
							<span class="files-added label label-danger" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>">
								<div class="fa fa-minus"></div>&nbsp;<?php echo $scan['scan_size_rel_humanized'] ?>
							</span>
							<?php endif ?>
						<?php endif ?>
					</div>
				</div>
				<?php if ($scan['new_count']): ?>
					<div class="table_caption">
						<span id="new" onClick="$('.table-new').toggle(1);" class="accordeon_toggle"><?php echo $text_label_new ?>&nbsp;
							<span class="label label-success">
								<div class="fa fa-plus"></div>&nbsp;<?php echo $scan['new_count'] ?>
							</span>
						</span>&nbsp;
						<span class="label label-default btn-copy" data-target="table-new" data-toggle="tooltip" title="<?php echo $button_copy; ?>">
							<i class="fa fa-copy"></i>
						</span>
					</div>
					<table class="table table-security table-new<?php echo ($scan['new_count'] > 100) ? ' table-hidden' : '' ?>">
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
								<td><a href="<?php echo $action_file ?>&file_name=<?php echo urlencode($file_name) ?>" target="_blank"><?php echo $file_data['relpath'] ?></a></td>
								<td><?php echo $file_data['extension'] ?></td>
								<td class="column_size"><?php echo $file_data['filesize']; ?></td>
								<td><?php echo $file_data['filemtime'] ?></td>
								<td><?php echo $file_data['filectime'] ?></td>
								<td><?php echo $file_data['fileperms'] ?></td>
								<td><?php echo $file_data['crc']; ?></td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
				<?php endif ?>
				<?php if ($scan['changed_count']): ?>
					<div class="table_caption">
						<span id="changed" onClick="$('.table-changed').toggle(1);" class="accordeon_toggle">
							<?php echo $text_label_changed ?>&nbsp;
							<span class="label label-warning">
								<div class="fa fa-ellipsis-h"></div>&nbsp;<?php echo $scan['changed_count'] ?>
							</span>
						</span>&nbsp;
						<span class="label label-default btn-copy" data-target="table-changed" data-toggle="tooltip" title="<?php echo $button_copy; ?>">
							<i class="fa fa-copy"></i>
						</span>
					</div>
					<table class="table table-security table-changed<?php echo ($scan['changed_count'] > 100) ? ' table-hidden' : '' ?>">
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
								<td><a href="<?php echo $action_file ?>&file_name=<?php echo urlencode($file_name) ?>" target="_blank"><?php echo $file_data['relpath'] ?></a></td>
								<td><?php echo $file_data['extension'] ?></td>
								<td class="<?php echo isset($file_data['diff']['filesize']) ? 'changed column_size' : 'column_size'; ?>"><?php echo $file_data['filesize']; ?></td>
								<td class="<?php echo isset($file_data['diff']['filemtime']) ? 'changed' : ''; ?>"><?php echo $file_data['filemtime']; ?></td>
								<td class="<?php echo isset($file_data['diff']['filectime']) ? 'changed' : ''; ?>"><?php echo $file_data['filectime']; ?></td>
								<td class="<?php echo isset($file_data['diff']['fileperms']) ? 'changed' : ''; ?>"><?php echo $file_data['fileperms']; ?></td>
								<td class="<?php echo isset($file_data['diff']['crc']) ? 'changed' : ''; ?>"><?php echo $file_data['crc']; ?></td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
				<?php endif ?>
				<?php if ($scan['deleted_count']): ?>
					<div class="table_caption">
						<span id="deleted" onClick="$('.table-deleted').toggle(1);" class="accordeon_toggle">
							<?php echo $text_label_deleted ?>&nbsp;<span class="label label-danger">
								<div class="fa fa-minus"></div>&nbsp;<?php echo $scan['deleted_count'] ?>
							</span>
						</span>&nbsp;
						<span class="label label-default btn-copy" data-target="table-deleted" data-toggle="tooltip" title="<?php echo $button_copy; ?>">
							<i class="fa fa-copy"></i>
						</span>
					</div>
					<table class="table table-security table-deleted<?php echo ($scan['deleted_count'] > 100) ? ' table-hidden' : '' ?>">
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
								<td><a href="<?php echo $action_file ?>&file_name=<?php echo urlencode($file_name) ?>" target="_blank"><?php echo $file_data['relpath'] ?></a></td>
								<td><?php echo $file_data['extension'] ?></td>
								<td class="column_size"><?php echo $file_data['filesize']; ?></td>
								<td><?php echo $file_data['filemtime'] ?></td>
								<td><?php echo $file_data['filectime'] ?></td>
								<td><?php echo $file_data['fileperms'] ?></td>
								<td><?php echo $file_data['crc']; ?></td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
				<?php endif ?>
				<?php if ($scan['scanned_count']): ?>
					<div class="table_caption">
						<span id="scanned" onClick="showScanned()" class="accordeon_toggle">
							<?php echo $text_label_scanned ?>&nbsp;
							<span class="label label-default">
								<div class="fa fa-file-o"></div>&nbsp;<?php echo $scan['scanned_count'] ?>
							</span>
						</span>
					</div>
					<div id="scanned_wrapper"></div>
				<?php endif ?>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="renameScan" style="display: none;">
	<h2 class="modal-title"><?php echo $text_modal_rename_title ?></h2><br>
	<div class="modal-body">
		<form action="<?php echo $action_rename; ?>" method="post" enctype="multipart/form-data" id="form-rename">
			<div class="form-group">
				<span class="required">*</span> <label for="scan_name" class="control-label"><?php echo $entry_scan_name ?></label>
				<input type="text" name="scan_name" id="scan_name" placeholder="<?php echo $text_scan_name_placeholder ?>" autocomplete="off" style="width:99%;margin-top: 5px;" value="<?php echo $scan['name'] ?>">
			</div>
		</form>
	</div>
	<div class="modal-footer" style="margin-top: 10px;float:right;">
		<a href="#renameScan" class="button" rel="modal:close"><?php echo $button_cancel ?></a>
		<a id="renameNow" data-loading-text="<?php echo $button_rename_loading ?>" class="button"><?php echo $button_rename ?></a>
	</div>
</div>
<script>
$(document).ready(function() {
	$('.scan_name').on('click', function(event) {
		event.preventDefault();
		$('#renameScan').modal();
	});
	$('#renameNow').click(function(event) {
		event.preventDefault();
		$('#form-rename').submit();
	});
	$('.btn-copy').on('click', function(event) {
		event.preventDefault();
		var target = $(this).data('target');
		var $rows = $('table.' + target + ' tbody tr');
		var selected = '';
		$.each($rows, function(index, val) {
			selected += $(val).find('td:first>a').html() + "\n";
		});
		copyToClipboard(this, selected);
		$(this).toggleClass('label-success','label-default');
	});	
});
var copyToClipboard = function(elem, text) {
	var textarea = elem.appendChild(document.createElement("textarea"));
	textarea.value = text;
	textarea.focus();
	textarea.select();
	document.execCommand('copy');
	textarea.parentNode.removeChild(textarea);
}
var scanned_files = <?php echo json_encode($scan['scan_data']['scanned']) ?>;
var showScanned = function() {
	var $scanned_wrapper = $('#scanned_wrapper');
	if ($scanned_wrapper.html() == '') {
		$scanned_wrapper.html('<center><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></center>');
		html = '';
		html += '<table class="table table-security table-scanned table-hidden">';
			html += '<thead>';
				html += '<tr>';
					html += '<th class="column_name"><?php echo $text_column_name ?></th>';
					html += '<th class="column_type" width="50"><?php echo $text_column_type ?></th>';
					html += '<th class="column_size" width="150"><?php echo $text_column_size ?></th>';
					html += '<th class="column_mtime" width="140"><?php echo $text_column_mtime ?></th>';
					html += '<th class="column_ctime" width="140"><?php echo $text_column_ctime ?></th>';
					html += '<th class="column_rights" width="50"><?php echo $text_column_rights ?></th>';
					html += '<th class="column_crc" width="100"><?php echo $text_column_crc ?></th>';
				html += '</tr>';
			html += '</thead>';
			html += '<tbody>';
				$.each(scanned_files, function(index, val) {
				html += '<tr>';
					html += '<td><a href="<?php echo $action_file ?>&file_name=' + index + '" target="_blank">' + val['relpath'] + '</a></td>';
					html += '<td>' + val['extension'] + '</td>';
					html += '<td class="column_size">' + val['filesize'] + '</td>';
					html += '<td>' + val['filemtime'] + '</td>';
					html += '<td>' + val['filectime'] + '</td>';
					html += '<td>' + val['fileperms'] + '</td>';
					html += '<td>' + val['crc'] + '</td>';
				html += '</tr>';
				});
			html += '</tbody>';
		html += '</table>';
		$scanned_wrapper.html(html);
	}
	$scanned_wrapper.find('.table-scanned').toggle(1);
}
</script>
<?php echo $footer; ?>