<?php
/**
 * @package		FSMonitor - German Translation by https://osworx.net
 * @author		mic - https://osworx.net
 * @copyright	2021 https://shtt.blog/
 * @license		https://shtt.blog/
 */

// Heading
$_['heading_title']					= 'FSMonitor - Dateiüberwachung';

// OpenCart Cron jobs
$_['entry_cron_jobs']				= 'Cronjob';
$_['button_add_task']				= 'Neuer Cronjob';
$_['text_cron_interval_hour']		= 'Stunde';
$_['text_cron_interval_day']		= 'Tag';
$_['text_cron_interval_month']		= 'Monat';
$_['error_cron_job_installed']		= 'Cronjob bereits vorhanden. Bitte <a href="%s" target="_blank">hier klicken</a> zum bearbeiten';
$_['success_cron_job_installed']	= 'Cronjob erfolgreich hinzugefügt. Bitte <a href="%s" target="_blank">hier klicken</a> zum bearbeiten';

$_['text_mail_subject']				= 'FSMonitor - neue Ereignisse';
$_['text_mail_header']				= 'Letzter Scan auf %s hat neue Ereignisse gefunden.';
$_['text_mail_link']				= 'Scan:';
$_['text_mail_new_files']			= 'neue Dateien.';
$_['text_mail_changed_files']		= 'geänderte Dateien.';
$_['text_mail_deleted_files']		= 'gelöschte Dateien.';
$_['text_cron_scan_name']			= 'Automatischer Scan';
$_['text_cron_scan_user']			= 'cron';

// Text
$_['text_security']					= 'Sicherheit';
$_['text_fs_monitor']				= 'Dateiüberwachung';
$_['text_modules']					= 'Module';
$_['text_settings']					= 'Einstellungen';
$_['text_modal_title']				= 'Neuer Scan';
$_['text_modal_rename_title']		= 'Scan umbenennen';
$_['text_scan_name_placeholder']	= 'Name der neuen Dateiüberprüfung';
$_['text_scans_on']					= 'Überprüfe ';
$_['text_label_scanned']			= 'Überprüft';
$_['text_label_new']				= 'Neu';
$_['text_label_changed']			= 'Geändert';
$_['text_label_deleted']			= 'Gelöscht';
$_['text_view']						= 'Überprüfung ansehen';
$_['text_view_all']					= 'Alle ansehen';
$_['text_dashboard_scan']			= 'Übersicht';
$_['text_initial_scan']				= 'Erstscan';
$_['text_date_format_short']		= 'j. F';

$_['text_legend_module']			= 'Module';
$_['text_legend_scanner']			= 'Scanner';
$_['text_legend_cron_opencart']		= 'Сron - OpenCart';

// Columns
$_['text_column_name']				= 'Name';
$_['text_column_type']				= 'Art';
$_['text_column_size']				= 'Größe';
$_['text_column_mtime']				= 'Geändert';
$_['text_column_ctime']				= 'Erstellt';
$_['text_column_rights']			= 'Rechte';
$_['text_column_crc']				= 'Prüfsumme';

// Entry
$_['entry_scan_name']				= 'Scanname';
$_['entry_admin_dir']				= 'Adminverzeichnis';
$_['entry_base_path']				= 'Basispfad';
$_['entry_extensions']				= 'Erweiterungen';
$_['entry_extensions_help']			= 'Pro Zeile 1 Erweiterung';
$_['entry_cron_access_key']			= 'Cron Zugangsschlüssel';
$_['entry_cron_wget']				= 'wget';
$_['entry_cron_curl']				= 'curl';
$_['entry_cron_cli']				= 'cli';
$_['entry_cron_save']				= 'Sichere Cronscan';
$_['entry_cron_save_help']			= 'Ausgeführte Scans per Cron werden als einfache Scans hinzugefügt';
$_['entry_cron_notify']				= 'Benachrichtige bei Änderungen';
$_['entry_cron_notify_help']		= 'Werden Änderungen am Dateisystem entdeckt, erfolgt eine Benachrichtigung';

$_['entry_include']					= 'Verzeichnisse inklusive';
$_['entry_include_help']			= 'Pro Zeile ein Verzeichnis (keine Dateien, keine Wildcards *)';
$_['entry_include_help_block']		= 'Nur absolute Pfade dürfen vewendet werden<br>Beispiel: <code/var/www/username/www-data/andere_webseite.com/</code>';
$_['entry_exclude']					= 'Ausgenommen Verzeichnisse und Dateien';
$_['entry_exclude_help']			= 'Pro Zeile eine Angabe';
$_['entry_exclude_help_block']		= 'Es können sowohl absolute als auch relative Pfade angegeben werden, auch Wildcards *.<br>Beispiel:<ul><li>/var/www/username/www-data/andere_webseite.com/</li><li>catalog/view/theme/revolution/stylesheet/ocdbanner-206.css</li><li>assets/dynamic_stylesheet/*.css</li></ul>';


// Interval datetime functions
$_['text_interval_days']					= 'Tage';
$_['text_interval_hours']					= 'Stunden';
$_['text_interval_minutes']					= 'Minuten';
$_['text_interval_less_than_a_minute']		= 'Weniger als 1 Minute';
$_['text_interval_less_than_a_minute_ago']	= 'Vor kurzem (weniger als 1 Minute)';
$_['text_interval_2_minutes_ago']			= 'Vor 2 Minuten'; // would be better using %s
$_['text_interval_3_minutes_ago']			= 'Vor 3 Minuten';
$_['text_interval_4_minutes_ago']			= 'Vor 4 Minuten';
$_['text_interval_5_minutes_ago']			= 'Vor 5 Minuten';
$_['text_interval_minutes_ago']				= 'Minuten'; // would be better using %s
$_['text_interval_1_hour_ago']				= 'Vor 1 Stunde';  // would be better using %s
$_['text_interval_2_hour_ago']				= 'Vor 2 Stunden';
$_['text_interval_3_hour_ago']				= 'Vor 3 Stunden';
$_['text_interval_4_hour_ago']				= 'Vor 4 Stunden';
$_['text_interval_today_in']				= 'Heute um';
$_['text_interval_yesterday_in']			= 'Gestern um';
$_['text_interval_right_now']				= 'gerade eben';
$_['text_interval_in_2_minutes']			= 'in 2 Minuten'; // would be better using %s
$_['text_interval_in_3_minutes']			= 'in 3 Minuten';
$_['text_interval_in_4_minutes']			= 'in 4 Minuten';
$_['text_interval_in_5_minutes']			= 'in 5 Minuten';
$_['text_interval_in_minutes']				= 'in %d Minuten';
$_['text_interval_in_an_hour']				= 'in 1 Stunde';
$_['text_interval_in_2_hours']				= 'in 2 Stunden';
$_['text_interval_in_3_hours']				= 'in 3 Stunden';
$_['text_interval_in_4_hours']				= 'in 4 Stunden';
$_['text_interval_today_at']				= 'Heute um';
$_['text_interval_tomorrow_at']				= 'Morgen um';

// Buttons
$_['button_scan']							= 'Scan';
$_['button_rename']							= 'Umbenennen';
$_['button_settings']						= 'Einstellungen';
$_['button_scan_loading']					= 'Scanne ..';
$_['button_rename_loading']					= 'Benenn um ..';
$_['button_save']							= 'Sichern';
$_['button_delete']							= 'Löschen';
$_['button_cancel']							= 'Abbrechen';
$_['button_view']							= 'Ansehen';
$_['button_generate']						= 'Standardeinstellungen erstellen';

//Success
$_['text_success_scan_created']				= 'Scan erfolgreich erstellt';
$_['text_success_renamed']					= 'Scan erfolgreich umbenannt';
$_['text_success_scan_initial']				= 'Erster Scan erfolgreich erstellt';
$_['text_success_scans_deleted']			= 'Scans gelöscht';
$_['text_success_saved']					= 'Einstellungen erfolgreich gespeichert';

// Error
$_['error_permission']						= 'Keine Berechtigung für diese Aktion';
$_['error_empty_name']						= 'Name für Scanaktion fehlt';
$_['error_form']							= 'Einstellungen sind nicht vollständig';
$_['error_base_path']						= 'Basispfad ist erforderlich';
$_['error_extensions']						= 'Erweiterungen sind erforderlich';
$_['error_cron_access_key']					= 'Zugangschlüssel ist erforderlich';