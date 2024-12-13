<div class="tile security-scan-widget" id="fs_monitor_block">
	<style>@import url(//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,greek-ext,vietnamese);.security-scan-widget {font-family: 'Open Sans', sans-serif;}html{overflow-y: scroll;}</style>
	<style>@import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css);</style>
	<style>
	.pull-right{float:right}
	.security-scan-widget{display:inline-block;width:49%}
	.security-scan-widget .dashboard-heading a{color:#fff;padding-right:.5em}
	.security-scan-widget .dashboard-content{min-height:auto;margin-bottom:10px}
	.security-scan-widget .dashboard-content>*{display:inline-block}
	.security-scan-widget span.label{font-size:10pt}
	.security-scan-widget a{text-decoration:none}
	.security-scan-widget .label{display:inline-block;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em}
	.security-scan-widget .label:empty{display:none}
	.security-scan-widget .btn .label{position:relative;top:-1px}
	a.label:hover,a.label:focus{color:#fff;text-decoration:none;cursor:pointer}
	.security-scan-widget .changes-list a{text-decoration:none}
	.security-scan-widget .label-default{background-color:#777}
	.security-scan-widget .label-default[href]:hover,.label-default[href]:focus{background-color:#5e5e5e}
	.security-scan-widget .label-primary{background-color:#1e91cf}
	.security-scan-widget .label-primary[href]:hover,.label-primary[href]:focus{background-color:#1872a2}
	.security-scan-widget .label-success{background-color:#8fbb6c}
	.security-scan-widget .label-success[href]:hover,.label-success[href]:focus{background-color:#75a74d}
	.security-scan-widget .label-info{background-color:#5bc0de}
	.security-scan-widget .label-info[href]:hover,.label-info[href]:focus{background-color:#31b0d5}
	.security-scan-widget .label-warning{background-color:#f38733}
	.security-scan-widget .label-warning[href]:hover,.label-warning[href]:focus{background-color:#e66c0e}
	.security-scan-widget .label-danger{background-color:#f56b6b}
	.label-danger[href]:hover,.label-danger[href]:focus{background-color:#f23b3b}
	</style>
	<div class="dashboard-heading">
		<?php echo $heading_title; ?>
		<div class="pull-right">
			<a href="<?php echo $view_all ?>"><?php echo $text_view_all ?></a>
		</div>
	</div>
	<div class="dashboard-content">
		<?php echo $scan['user_name'] ?>,&nbsp;<?php echo $scan['date_added_ago'] ?>

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
				<div class="fa fa-ellipsis-h"></div> <?php echo $scan['changed_count'] ?>
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
		<div class="pull-right mtop">
			<a href="<?php echo $scan['href']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-primary btn-sm"><?php echo $button_view ?></a>
			<button data-loading-text="<?php echo $button_scan_loading ?>" id="fs_monitor_scan_btn" data-toggle="tooltip" title="<?php echo $button_scan; ?>" class="btn btn-success btn-sm"><div class="fa fa-plus"></div></button>
		</div>
	</div>
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