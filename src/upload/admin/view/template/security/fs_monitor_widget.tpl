<div class="tile security-scan" id="fs_monitor_block">
	<div class="tile-heading">
		<?php echo $heading_title; ?>
		<div class="pull-right">
			<a href="<?php echo $view_all ?>"><?php echo $text_view_all ?></a>
		</div>
	</div>

	<div class="tile-body">
		<?php echo $scan['date_added_ago'] ?>
		<?php echo $scan['user_name'] ?>

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

		<?php if ($scan['scan_size_rel'] == 0): ?>
			<span class="label label-info" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>"><?php echo $scan['scan_size_rel_humanized'] ?></span>
		<?php else: ?>
			<?php if ($scan['scan_size_rel'] > 0): ?>
			<span class="files-added label label-success" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>">    <div class="fa fa-plus"></div> <?php echo $scan['scan_size_rel_humanized'] ?></span>
			<?php else: ?>
			<span class="files-added label label-danger" data-toggle="tooltip" title="<?php echo $scan['scan_size_abs_humanized'] ?>">    <div class="fa fa-minus"></div> <?php echo $scan['scan_size_rel_humanized'] ?></span>
			<?php endif ?>
		<?php endif ?>
		<div class="pull-right mtop">
			<a href="<?php echo $scan['href']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
			<button data-loading-text="<?php echo $button_scan_loading ?>" id="fs_monitor_scan_btn" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
		</div>
	</div>
	<style>
		.security-scan span.label{font-size:10pt}
		.security-scan .btn > i.fa{font-size:11px;opacity:1}
		.security-scan i.fa.dark{color:#555}
		.security-scan .pull-right.mtop{margin-top:-5px}
		.security-scan .tile-body {line-height:normal}
	</style>
	<script>
		$('#fs_monitor_scan_btn').on('click', function(event) {
			event.preventDefault();
			$('#fs_monitor_scan_btn').button('loading');
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