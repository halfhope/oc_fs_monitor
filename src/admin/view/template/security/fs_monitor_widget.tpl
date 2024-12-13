<div class="tile security-scan" id="fs_monitor_block">
	<div class="dashboard-heading">
		<?php echo $heading_title; ?>
		<div class="pull-right">
			<a href="<?php echo $view_all ?>"><?php echo $text_view_all ?></a>
		</div>
	</div>

	<div class="dashboard-content">
		<?php echo $scan['date_added_ago'] ?>
		<?php echo $scan['user_name'] ?>

		<?php if ($scan['scanned_count']): ?>
		<a href="<?php echo $scan['href'] ?>#scanned"><span class="files-scanned label label-default" data-toggle="tooltip" title="<?php echo $text_label_scanned; ?>"><?php echo $scan['scanned_count'] ?></span></a>
		<?php endif ?>

		<?php if ($scan['new_count']): ?>
		<a href="<?php echo $scan['href'] ?>#new"><span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $text_label_new; ?>"> + <?php echo $scan['new_count'] ?></span></a>
		<?php endif ?>

		<?php if ($scan['changed_count']): ?>
		<a href="<?php echo $scan['href'] ?>#changed"><span class="files-changed label label-warning" data-toggle="tooltip" title="<?php echo $text_label_changed; ?>"> ... <?php echo $scan['changed_count'] ?></span></a>
		<?php endif ?>

		<?php if ($scan['deleted_count']): ?>
		<a href="<?php echo $scan['href'] ?>#deleted"><span class="files-deleted label label-danger" data-toggle="tooltip" title="<?php echo $text_label_deleted; ?>"> - </div> <?php echo $scan['deleted_count'] ?></span></a>
		<?php endif ?>

		<?php if ($scan['scan_size_rel'] == 0): ?>
			<span class="label label-info" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>"><?php echo $scan['scan_size_rel_humanized'] ?></span>
		<?php else: ?>
			<?php if ($scan['scan_size_rel'] > 0): ?>
			<span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>"> + <?php echo $scan['scan_size_rel_humanized'] ?></span>
			<?php else: ?>
			<span class="files-added label label-danger" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>"> - <?php echo $scan['scan_size_rel_humanized'] ?></span>
			<?php endif ?>
		<?php endif ?>
		<div class="pull-right mtop">
			<a href="<?php echo $scan['href']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-primary btn-sm"><?php echo $button_view ?></a>
			<button data-loading-text="<?php echo $button_scan_loading ?>" id="fs_monitor_scan_btn" data-toggle="tooltip" title="<?php echo $button_scan; ?>" class="btn btn-success btn-sm">+</button>
		</div>
	</div>
	<style>
		.pull-right{float:right}
		.security-scan {display:inline-block;width:49%;}
		.security-scan .dashboard-heading a{color:#fff;padding-right:.5em;}
		.security-scan .dashboard-content{min-height:auto;margin-bottom:10px;}
		.security-scan .dashboard-content>*{display:inline-block}
		.security-scan span.label{font-size:10pt}
		.security-scan a{text-decoration:none;}
		
		.security-scan .label{display:inline-block;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em}
		.security-scan .label:empty{display:none}
		.security-scan .btn .label{position:relative;top:-1px}a.label:hover,a.label:focus{color:#fff;text-decoration:none;cursor:pointer}
		.security-scan .changes-list a{text-decoration:none}
		.security-scan .label-default{background-color:#777}
		.security-scan .label-default[href]:hover,.label-default[href]:focus{background-color:#5e5e5e}
		.security-scan .label-primary{background-color:#1e91cf}
		.security-scan .label-primary[href]:hover,.label-primary[href]:focus{background-color:#1872a2}
		.security-scan .label-success{background-color:#8fbb6c}
		.security-scan .label-success[href]:hover,.label-success[href]:focus{background-color:#75a74d}
		.security-scan .label-info{background-color:#5bc0de}
		.security-scan .label-info[href]:hover,.label-info[href]:focus{background-color:#31b0d5}
		.security-scan .label-warning{background-color:#f38733}
		.security-scan .label-warning[href]:hover,.label-warning[href]:focus{background-color:#e66c0e}
		.security-scan .label-danger{background-color:#f56b6b}.label-danger[href]:hover,.label-danger[href]:focus{background-color:#f23b3b}
	</style>
	<script>
		$('#fs_monitor_scan_btn').on('click', function(event) {
			event.preventDefault();
			var $btn = $('#fs_monitor_scan_btn');
			$btn.html($btn.data('loading-text'));
			$btn.attr('disabled','disabled');
			$.ajax({
				url: '<?php echo $reload_widget ?>',
				type: 'GET',
				dataType: 'html'
			})
			.success(function(response) {
				$('#fs_monitor_block').html($(response).filter('#fs_monitor_block').html());
			});
		});
	</script>
</div>