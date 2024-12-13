<?php echo $header; ?>
<style>.form-group+.form-group{border:none}</style>
<?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" data-target="#form-settings" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $action_generate ?>" data-toggle="tooltip" title="<?php echo $button_generate; ?>" class="btn btn-warning"><i class="fa fa-cogs"></i></a>
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
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $panel_title; ?></h3><h3 class="panel-title pull-right">v<?php echo $version ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action_save; ?>" method="post" enctype="multipart/form-data" id="form-settings" class="form-horizontal">

					<fieldset>

						<legend><?php echo $text_legend_module ?></legend>

						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-admin-dir"><?php echo $entry_admin_dir; ?></label>
							<div class="col-sm-10">
								<input type="text" name="security_fs_admin_dir" value="<?php echo $security_fs_admin_dir; ?>" placeholder="<?php echo $entry_admin_dir; ?>" id="input-admin-dir" class="form-control" />
							</div>
						</div>

					</fieldset>

					<fieldset>

						<legend><?php echo $text_legend_scanner ?></legend>

						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-base-path"><?php echo $entry_base_path; ?></label>
							<div class="col-sm-10">
								<input type="text" name="security_fs_base_path" value="<?php echo $security_fs_base_path; ?>" placeholder="<?php echo $entry_base_path; ?>" id="input-base-path" class="form-control" />
								<?php if ($error_base_path) { ?>
								<div class="text-danger"><?php echo $error_base_path; ?></div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-extensions"><span data-toggle="tooltip" title="<?php echo $entry_extensions_help; ?>"><?php echo $entry_extensions; ?></span></label>
							<div class="col-sm-10">
								<textarea name="security_fs_extensions" id="input-extensions" class="form-control" cols="30" rows="10"><?php echo $security_fs_extensions; ?></textarea>
								<?php if ($error_extensions) { ?>
								<div class="text-danger"><?php echo $error_extensions; ?></div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-include"><span data-toggle="tooltip" title="<?php echo $entry_include_help; ?>"><?php echo $entry_include; ?></span></label>
							<div class="col-sm-10">
								<textarea name="security_fs_include" id="input-include" class="form-control" cols="30" rows="10"><?php echo $security_fs_include; ?></textarea>
								<div class="help-block"><?php echo $entry_include_help_block; ?></div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-exclude"><span data-toggle="tooltip" title="<?php echo $entry_exclude_help; ?>"><?php echo $entry_exclude; ?></span></label>
							<div class="col-sm-10">
								<textarea name="security_fs_exclude" id="input-exclude" class="form-control" cols="30" rows="10"><?php echo $security_fs_exclude; ?></textarea>
								<div class="help-block"><?php echo $entry_exclude_help_block; ?></div>
							</div>
						</div>
					</fieldset>

					<fieldset>

						<legend><?php echo $text_legend_cron ?></legend>

						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-cron-access-key"><?php echo $entry_cron_access_key; ?></label>
							<div class="col-sm-10">
								<input type="text" name="security_fs_cron_access_key" value="<?php echo $security_fs_cron_access_key; ?>" id="input-cron-access-key" class="form-control" autocomplete="off"/>
								<?php if ($error_access_key) { ?>
								<div class="text-danger"><?php echo $error_access_key; ?></div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-cron-wget"><?php echo $entry_cron_wget; ?></label>
							<div class="col-sm-10">
								<input type="text" data-default="<?php echo $security_fs_cron_wget; ?>" value="<?php echo $security_fs_cron_wget; ?>" id="input-cron-wget" class="form-control" readonly="1" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-cron-curl"><?php echo $entry_cron_curl; ?></label>
							<div class="col-sm-10">
								<input type="text" data-default="<?php echo $security_fs_cron_curl; ?>" value="<?php echo $security_fs_cron_curl; ?>" id="input-cron-curl" class="form-control" readonly="1" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-cron-cli"><?php echo $entry_cron_cli; ?></label>
							<div class="col-sm-10">
								<input type="text" data-default="<?php echo $security_fs_cron_cli; ?>" value="<?php echo $security_fs_cron_cli; ?>" id="input-cron-cli" class="form-control" readonly="1" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $entry_cron_save_help; ?>"><?php echo $entry_cron_save; ?></span></label>
							<div class="col-sm-10">
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
						</div>
					</fieldset>
		
					<fieldset>

						<legend><?php echo $text_legend_notify ?></legend>

						<div class="form-group">
							
							<label for="" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $entry_cron_notify_help; ?>"><?php echo $entry_cron_notify; ?></span></label>
							<div class="col-sm-10">
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
							</div>
						
						</div>
						
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"><?php echo $entry_notify_to; ?></label>
							<div class="col-sm-10">
								<select name="security_fs_notify_to" id="#input-notify-to" class="form-control">
									<option value="email" <?php echo $security_fs_notify_to == 'email' ? 'selected' : '' ?>><?php echo $tab_email ?></option>
									<option value="whatsapp" <?php echo $security_fs_notify_to == 'whatsapp' ? 'selected' : '' ?>><?php echo $tab_whatsapp ?></option>
									<option value="telegram" <?php echo $security_fs_notify_to == 'telegram' ? 'selected' : '' ?>><?php echo $tab_telegramm ?></option>
								</select>
							</div>
						</div>	
						
						<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#tab-email" class="active"><?php echo $tab_email; ?></a></li>
							<li><a data-toggle="tab" href="#tab-whatsapp"><?php echo $tab_whatsapp; ?></a></li>
							<li><a data-toggle="tab" href="#tab-telegram"><?php echo $tab_telegramm; ?></a></li>
						</ul>

						<div class="tab-content">

							<div class="tab-pane active" id="tab-email">
								
								<div class="form-group">
									<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $entry_e_emails_help; ?>"><?php echo $entry_e_emails; ?></span></label>
									<div class="col-sm-10">
										<input type="text" name="security_fs_e_emails" id="input-emails" class="form-control" value="<?php echo $security_fs_e_emails; ?>">
									</div>
								</div>
							
							</div>
							
							<div class="tab-pane" id="tab-whatsapp">
								
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_w_phone_number; ?></label>
									<div class="col-sm-10">
										<input type="text" name="security_fs_w_phone_number" id="input-emails" class="form-control" value="<?php echo $security_fs_w_phone_number; ?>">
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_w_business_account_id; ?></label>
									<div class="col-sm-10">
										<input type="text" name="security_fs_w_business_account_id" id="input-emails" class="form-control" value="<?php echo $security_fs_w_business_account_id; ?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_w_api_token; ?></label>
									<div class="col-sm-10">
										<input type="text" name="security_fs_w_api_token" id="input-emails" class="form-control" value="<?php echo $security_fs_w_api_token; ?>">
									</div>
								</div>
								<div class="help-block"><?php echo $text_whatsapp_help; ?></div>
							</div>

							<div class="tab-pane" id="tab-telegram">

								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_t_api_token; ?></label>
									<div class="col-sm-10">
										<input type="text" name="security_fs_t_api_token" id="input-emails" class="form-control" value="<?php echo $security_fs_t_api_token; ?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_t_channel_id; ?></label>
									<div class="col-sm-10">
										<input type="text" name="security_fs_t_channel_id" id="input-emails" class="form-control" value="<?php echo $security_fs_t_channel_id; ?>">
									</div>
								</div>

								<div class="help-block"><?php echo $text_telegram_help; ?></div>
							</div>

						</div>

					</fieldset>
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