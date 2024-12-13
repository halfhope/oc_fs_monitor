<?php echo $header; ?>
<style>table.form input[type=text], table.form textarea{width:100%;}</style>
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
			<h1><img src="view/image/setting.png" alt="" /> <?php echo $panel_title; ?> v<?php echo $version ?></h1>
			<div class="buttons">
				<a onClick="$('#form').submit();" title="<?php echo $button_save; ?>" class="button"><?php echo $button_save; ?></button>
				<a href="<?php echo $action_generate ?>" data-toggle="tooltip" title="<?php echo $button_generate; ?>" class="button"><?php echo $button_generate; ?></a>
				<a href="<?php echo $action_cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?php echo $action_save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">

					<h2><?php echo $text_legend_module ?></h2>

					<table class="form">
						<tr>
							<td><span class="required">*</span> <?php echo $entry_admin_dir; ?>:</td>
							<td>
								<input type="text" name="security_fs_admin_dir" value="<?php echo $security_fs_admin_dir; ?>" placeholder="<?php echo $entry_admin_dir; ?>" id="input-admin-dir" class="form-control" />
							</td>
						</tr>
					</table>

					<h2><?php echo $text_legend_scanner ?></h2>

					<table class="form">
						<tr>
							<td><span class="required">*</span> <?php echo $entry_base_path; ?>:</td>
							<td>
								<input type="text" name="security_fs_base_path" value="<?php echo $security_fs_base_path; ?>" placeholder="<?php echo $entry_base_path; ?>" id="input-base-path" class="form-control" />
								<?php if ($error_base_path) { ?>
								<div class="text-danger"><?php echo $error_base_path; ?></div>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $entry_extensions; ?>:<br><span class="help"><?php echo $entry_extensions_help; ?></span></td>
							<td>
								<textarea name="security_fs_extensions" id="input-extensions" class="form-control" cols="30" rows="10"><?php echo $security_fs_extensions; ?></textarea>
								<?php if ($error_extensions) { ?>
								<div class="text-danger"><?php echo $error_extensions; ?></div>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_include; ?>:<br><span class="help"><?php echo $entry_include_help; ?></span></td>
							<td>
								<textarea name="security_fs_include" id="input-include" class="form-control" cols="30" rows="10"><?php echo $security_fs_include; ?></textarea>
								<div class="help-block"><?php echo $entry_include_help_block; ?></div>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_exclude; ?>:<br><span class="help"><?php echo $entry_exclude_help; ?></span></td>
							<td>
								<textarea name="security_fs_exclude" id="input-exclude" class="form-control" cols="30" rows="10"><?php echo $security_fs_exclude; ?></textarea>
								<div class="help-block"><?php echo $entry_exclude_help_block; ?></div>
							</td>
						</tr>
					</table>

					<h2><?php echo $text_legend_cron ?></h2>

					<table class="form">
						<tr>
							<td><span class="required">*</span> <?php echo $entry_cron_access_key; ?>:</td>
							<td>
								<input type="text" name="security_fs_cron_access_key" value="<?php echo $security_fs_cron_access_key; ?>" id="input-cron-access-key" class="form-control" autocomplete="off"/>
								<?php if ($error_access_key) { ?>
								<div class="text-danger"><?php echo $error_access_key; ?></div>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cron_wget; ?>:</td>
							<td>
								<input type="text" data-default="<?php echo $security_fs_cron_wget; ?>" value="<?php echo $security_fs_cron_wget; ?>" id="input-cron-wget" class="form-control" readonly="1" />
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cron_curl; ?>:</td>
							<td>
								<input type="text" data-default="<?php echo $security_fs_cron_curl; ?>" value="<?php echo $security_fs_cron_curl; ?>" id="input-cron-curl" class="form-control" readonly="1" />
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cron_cli; ?>:</td>
							<td>
								<input type="text" data-default="<?php echo $security_fs_cron_cli; ?>" value="<?php echo $security_fs_cron_cli; ?>" id="input-cron-cli" class="form-control" readonly="1" />
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cron_save; ?>:<br><span class="help"><?php echo $entry_cron_save_help; ?></span></td>
							<td>
								<label class="radio-inline">
									<?php if ($security_fs_cron_save) { ?>
									<input type="radio" name="security_fs_cron_save" value="1" checked="checked" />
									<?php echo $text_yes; ?>
									<?php } else { ?>
									<input type="radio" name="security_fs_cron_save" value="1" />
									<?php echo $text_yes; ?>
									<?php } ?>
								</label>
								<label class="radio-inline">
									<?php if (!$security_fs_cron_save) { ?>
									<input type="radio" name="security_fs_cron_save" value="0" checked="checked" />
									<?php echo $text_no; ?>
									<?php } else { ?>
									<input type="radio" name="security_fs_cron_save" value="0" />
									<?php echo $text_no; ?>
									<?php } ?>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cron_notify; ?>:<br><span class="help"><?php echo $entry_cron_notify_help; ?></span></td>
							<td>
								<label class="radio-inline">
									<?php if ($security_fs_cron_notify) { ?>
									<input type="radio" name="security_fs_cron_notify" value="1" checked="checked" />
									<?php echo $text_yes; ?>
									<?php } else { ?>
									<input type="radio" name="security_fs_cron_notify" value="1" />
									<?php echo $text_yes; ?>
									<?php } ?>
								</label>
								<label class="radio-inline">
									<?php if (!$security_fs_cron_notify) { ?>
									<input type="radio" name="security_fs_cron_notify" value="0" checked="checked" />
									<?php echo $text_no; ?>
									<?php } else { ?>
									<input type="radio" name="security_fs_cron_notify" value="0" />
									<?php echo $text_no; ?>
									<?php } ?>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_emails; ?>:<br><span class="help"><?php echo $entry_emails_help; ?></span></td>
							<td>
								<input type="text" name="security_fs_emails" id="input-emails" class="form-control" value="<?php echo $security_fs_emails; ?>"></textarea>
							</td>
						</tr>
					</table>

				</form>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function($) {

	$('#input-cron-access-key').on('change keyup paste', function(event) {
		event.preventDefault();
		var output_field = $('#input-cron-wget');
		$(output_field).val($(output_field).data('default') + $(this).val() + '\'');

		var output_field = $('#input-cron-curl');
		$(output_field).val($(output_field).data('default') + $(this).val() + '\'');

		var output_field = $('#input-cron-cli');
		$(output_field).val($(output_field).data('default') + $(this).val() + '\'');

	}).trigger('change');
});
</script>
<?php echo $footer; ?>