<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

// Heading
$_['heading_title'] = 'FSMonitor - file system monitoring';

// OpenCart Cron jobs
$_['entry_cron_jobs']       	 = 'Cron job';
$_['button_add_task']       	 = 'Add task to cron';
$_['text_cron_interval_hour']    = 'Hour';
$_['text_cron_interval_day']     = 'Day';
$_['text_cron_interval_month']   = 'Month';
$_['error_cron_job_installed']   = 'Cron task already exists. Click the <a href="%s" target="_blank">link</a> (new tab) to configure the cron job.';
$_['success_cron_job_installed'] = 'Cron job successfully added. Click the <a href="%s" target="_blank">link</a> (new tab) to configure the cron job.';

$_['text_mail_subject']       = 'File system monitoring - new events';
$_['text_mail_header']        = 'Last scan by cron at %s was detected new file system events.';
$_['text_mail_link']          = 'Scan:';
$_['text_mail_new_files']     = 'new files.';
$_['text_mail_changed_files'] = 'changed files.';
$_['text_mail_deleted_files'] = 'deleted files.';
$_['text_cron_scan_name']     = 'Automatic scanning';
$_['text_cron_scan_user']     = 'cron';

// Text
$_['text_security']              = 'Security';
$_['text_fs_monitor']            = 'File system monitoring';
$_['text_modules']               = 'Modules';
$_['text_settings']              = 'Settings';
$_['text_modal_title']           = 'New scan';
$_['text_modal_rename_title']    = 'Rename scan';
$_['text_scan_name_placeholder'] = 'More module works';
$_['text_scans_on']              = 'Scans on ';
$_['text_label_scanned']         = 'Scanned';
$_['text_label_new']             = 'New';
$_['text_label_changed']         = 'Changed';
$_['text_label_deleted']         = 'Deleted';
$_['text_view']                  = 'View scan';
$_['text_view_all']              = 'View all';
$_['text_dashboard_scan']        = 'Dashboard';
$_['text_initial_scan']          = 'Initial';
$_['text_date_format_short']     = 'F j';

$_['text_legend_module']  		= 'Module';
$_['text_legend_scanner'] 		= 'Scanner';
$_['text_legend_cron_opencart']	= 'Сron - OpenCart';

// Columns
$_['text_column_name']   = 'Name';
$_['text_column_type']   = 'Type';
$_['text_column_size']   = 'Size';
$_['text_column_mtime']  = 'Date modified';
$_['text_column_ctime']  = 'Date created';
$_['text_column_rights'] = 'Rights';
$_['text_column_crc']    = 'CRC';

// Entry
$_['entry_scan_name']        = 'Scan name';
$_['entry_admin_dir']        = 'Admin dir';
$_['entry_base_path']        = 'Base path';
$_['entry_extensions']       = 'Extenstions';
$_['entry_extensions_help']  = 'Each extension on a new line';
$_['entry_include']          = 'Include dirs';
$_['entry_exclude']          = 'Exclude dirs';
$_['entry_cron_access_key']  = 'Cron access key';
$_['entry_cron_wget']        = 'wget';
$_['entry_cron_curl']        = 'curl';
$_['entry_cron_cli']         = 'cli';
$_['entry_cron_save']        = 'Save cron scan';
$_['entry_cron_save_help']   = 'Scans executed by cron will be added as a simple scans';
$_['entry_cron_notify']      = 'Notify on changes';
$_['entry_cron_notify_help'] = 'You\'ll be notified if there will be changes in the file system';

// Interval datetime functions
$_['text_interval_days']                   = 'days';
$_['text_interval_hours']                  = 'hours';
$_['text_interval_minutes']                = 'minutes';
$_['text_interval_less_than_a_minute']     = 'less than a minute';
$_['text_interval_less_than_a_minute_ago'] = 'less than a minute ago';
$_['text_interval_2_minutes_ago']          = '2 minutes ago';
$_['text_interval_3_minutes_ago']          = '3 minutes ago';
$_['text_interval_4_minutes_ago']          = '4 minutes ago';
$_['text_interval_5_minutes_ago']          = '5 minutes ago';
$_['text_interval_minutes_ago']            = 'minutes ago';
$_['text_interval_1_hour_ago']             = '1 hour ago';
$_['text_interval_2_hour_ago']             = '2 hour ago';
$_['text_interval_3_hour_ago']             = '3 hour ago';
$_['text_interval_4_hour_ago']             = '4 hour ago';
$_['text_interval_today_in']               = 'today in';
$_['text_interval_yesterday_in']           = 'yesterday in';
$_['text_interval_right_now']              = 'right now';
$_['text_interval_in_2_minutes']           = 'in 2 minutes';
$_['text_interval_in_3_minutes']           = 'in 3 minutes';
$_['text_interval_in_4_minutes']           = 'in 4 minutes';
$_['text_interval_in_5_minutes']           = 'in 5 minutes';
$_['text_interval_in_minutes']             = 'in %d minutes';
$_['text_interval_in_an_hour']             = 'in an hour';
$_['text_interval_in_2_hours']             = 'in 2 hours';
$_['text_interval_in_3_hours']             = 'in 3 hours';
$_['text_interval_in_4_hours']             = 'in 4 hours';
$_['text_interval_today_at']               = 'today at';
$_['text_interval_tomorrow_at']            = 'tomorrow at';

// Buttons
$_['button_scan']           = 'Scan';
$_['button_rename']         = 'Rename';
$_['button_settings']       = 'Settings';
$_['button_scan_loading']   = 'Scanning...';
$_['button_rename_loading'] = 'Renaming...';
$_['button_save']           = 'Save';
$_['button_delete']         = 'Delete';
$_['button_cancel']         = 'Cancel';
$_['button_view']           = 'View';
$_['button_generate']       = 'Generate default settings';

//Success
$_['text_success_scan_created']  = 'Success: Scan created!';
$_['text_success_renamed']       = 'Success: Scan renamed!';
$_['text_success_scan_initial']  = 'Success: Initial scan was been created automatically!';
$_['text_success_scans_deleted'] = 'Success: Scans deleted!';
$_['text_success_saved']         = 'Success: Settings saved!';

// Error
$_['error_permission']      = 'Warning: You do not have permission!';
$_['error_empty_name']      = 'Warning: Please input scan name!';
$_['error_form']            = 'Warning: Please check form on errors!';
$_['error_base_path']       = 'Base path is required!';
$_['error_extensions']      = 'Extenstions is required!';
$_['error_cron_access_key'] = 'Access key is required!';

?>