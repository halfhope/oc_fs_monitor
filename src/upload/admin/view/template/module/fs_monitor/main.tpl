<?php echo $header; ?>
<style>@import url(//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,greek-ext,vietnamese);.security-scans-container {font-family: 'Open Sans', sans-serif;}html{overflow-y: scroll;}</style>
<style>@import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css);</style>
<style>svg.octicon{fill:currentColor;vertical-align:text-bottom;color:#ccc;background:#fff;z-index:2;position:relative}.security-scans-container{position:relative}.security-scan-list{padding-left:25px;padding-bottom:10px;}.security-scan-list:before{position:absolute;top:0;bottom:0;left:6px;z-index:1;display:block;width:2px;height:89%;content:"";background-color:#f3f3f3}.security-scan-list .day{margin-left:-25px;font-size:14px;color:#767676;padding-bottom:10px}.security-scan-list .scan-list-checkbox{width:30px;padding-left:5px;line-height:38px;display:inline-block;float:left}.security-scan-list .security-scan + .day{padding-top:10px}.security-scan-list .day span{margin-left:7px}.security-scan{margin:0;border:1px solid #e5e5e5;padding:8px 10px}.security-scan:hover{background:#f7fbfc}.security-scan.row .scan-heading{padding-left:0;display:inline-block}.security-scan + .security-scan{border-top:none}.security-scan .scan-name{font-size:15px;font-weight:700;display:inline-block}.security-scan .scan-name a{color:#4e575b;text-decoration:none}.security-scan .scan-date-added{color:#767676;padding-top:3px;}.security-scan .changes-list{color:#767676;line-height:38px;display:inline-block}.security-scan span.label{font-size:10pt}.label{display:inline;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em}.label:empty{display:none}.btn .label{position:relative;top:-1px}a.label:hover,a.label:focus{color:#fff;text-decoration:none;cursor:pointer}.changes-list a{text-decoration:none}.label-default{background-color:#777}.label-default[href]:hover,.label-default[href]:focus{background-color:#5e5e5e}.label-primary{background-color:#1e91cf}.label-primary[href]:hover,.label-primary[href]:focus{background-color:#1872a2}.label-success{background-color:#8fbb6c}.label-success[href]:hover,.label-success[href]:focus{background-color:#75a74d}.label-info{background-color:#5bc0de}.label-info[href]:hover,.label-info[href]:focus{background-color:#31b0d5}.label-warning{background-color:#f38733}.label-warning[href]:hover,.label-warning[href]:focus{background-color:#e66c0e}.label-danger{background-color:#f56b6b}.label-danger[href]:hover,.label-danger[href]:focus{background-color:#f23b3b}.col-sm-4{width:33.33333%}.col-sm-3{width:25%}.col-sm-2{width:16.66667%}.pull-right{float:right}.pull-left{float:left}.col-sm-2,.col-sm-3,.col-sm-4{position:relative;min-height:1px;padding-left:15px;padding-right:15px;vertical-align:middle}a.button[disabled=disabled]{opacity:.4;cursor:not-allowed}}</style>
<div id="content">
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
				<a href="#addScan" rel="modal:open" title="<?php echo $button_scan; ?>" id="button-scan" class="button"><?php echo $button_scan ?></a>
				<a href="#" title="<?php echo $button_delete; ?>" id="button-delete" class="button" disabled="disabled"><?php echo $button_delete ?></a>
				<a href="<?php echo $action_settings; ?>" id="button-settings" class="button"><?php echo $button_settings ?></a>
			</div>
		</div>
		<div class="content">

			<div class="security-scans-container">
				<form action="<?php echo htmlspecialchars_decode($action_delete); ?>" method="post" enctype="multipart/form-data" id="form-scans-list">
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
								<a href="<?php echo $scan['href'] ?>#scanned">
									<span class="files-scanned label label-default" data-toggle="tooltip" title="<?php echo $text_label_scanned; ?>">
										<div class="fa fa-file-o"></div>&nbsp;<?php echo $scan['scanned_count'] ?>
									</span>
								</a>&nbsp;
								<?php endif ?>

								<?php if ($scan['new_count']): ?>
								<a href="<?php echo $scan['href'] ?>#new">
									<span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $text_label_new; ?>">
										<div class="fa fa-plus"></div>&nbsp;<?php echo $scan['new_count'] ?>
									</span>
								</a>&nbsp;
								<?php endif ?>

								<?php if ($scan['changed_count']): ?>
								<a href="<?php echo $scan['href'] ?>#changed">
									<span class="files-changed label label-warning" data-toggle="tooltip" title="<?php echo $text_label_changed; ?>">
										<div class="fa fa-ellipsis-h"></div>&nbsp;<?php echo $scan['changed_count'] ?>
									</span>
								</a>&nbsp;
								<?php endif ?>

								<?php if ($scan['deleted_count']): ?>
								<a href="<?php echo $scan['href'] ?>#deleted">
									<span class="files-deleted label label-danger" data-toggle="tooltip" title="<?php echo $text_label_deleted; ?>">
										<div class="fa fa-minus"></div>&nbsp;<?php echo $scan['deleted_count'] ?>
									</span>
								</a>&nbsp;
								<?php endif ?>
							</div>

							<div class="changes-list col-sm-2 col-xs-3">
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
						<?php endforeach ?>
						<?php endforeach ?>
						<div class="pagination"><?php echo $pagination ?></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="addScan" style="display: none;">
	<h2 class="modal-title"><?php echo $text_modal_title ?></h2><br>
	<div class="modal-body">
		<form action="<?php echo $action_scan; ?>" method="post" enctype="multipart/form-data" id="form-scan">
			<div class="form-group">
				<span class="required">*</span> <label for="scan_name" class="control-label"><?php echo $entry_scan_name ?></label>
				<input type="text" name="scan_name" id="scan_name" placeholder="<?php echo $text_scan_name_placeholder ?>" autocomplete="off" style="width:99%;margin-top: 5px;">
			</div>
		</form>
	</div>
	<div class="modal-footer" style="margin-top: 10px;float:right;">
		<a href="#addScan" class="button" rel="modal:close"><?php echo $button_cancel ?></a>
		<a id="scanNow" data-loading-text="<?php echo $button_scan_loading ?>" class="button"><?php echo $button_scan ?></a>
	</div>
</div>
<script>
$(document).ready(function() {
	$('#button-scan').click(function(event) {
		event.preventDefault();
		$('#addScan').modal();
		$('#addScan #scan_name').focus();
	});

	$('#scanNow').click(function(event){
		event.preventDefault();
		$('#form-scan').submit();
	});

	$('form#form-scan').on('submit', function(event){
		$('#scanNow').text($('#scanNow').data('loading-text')).attr('disabled', true);
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
		if ($(this).attr('disabled')) {
			return false;
		}
		$('#form-scans-list').submit();
	});
});
</script>
<?php echo $footer; ?>