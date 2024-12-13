<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

// Heading
$_['heading_title'] = 'Мониторинг файловой системы';

// Text
$_['text_security']              = 'Безопасность';
$_['text_fs_monitor']            = 'Мониторинг файловой системы';
$_['text_modules']               = 'Модули';
$_['text_settings']              = 'Настройки';
$_['text_modal_title']           = 'Новое сканирование';
$_['text_modal_rename_title']    = 'Переименование сканирования';
$_['text_scan_name_placeholder'] = 'Например, обновление версии магазина';
$_['text_scans_on']              = 'Сканирования на ';
$_['text_label_scanned']         = 'Просканировано';
$_['text_label_new']             = 'Новые';
$_['text_label_changed']         = 'Изменены';
$_['text_label_deleted']         = 'Удалены';
$_['text_view']                  = 'Просмотр сканирования';
$_['text_view_all']              = 'Смотреть все';
$_['text_dashboard_scan']        = 'Панель управления';
$_['text_initial_scan']          = 'Первичное';
$_['text_date_format_short']     = 'd.m.Y';

$_['text_legend_module']  = 'Модуль';
$_['text_legend_scanner'] = 'Сканер';
$_['text_legend_cron']    = 'Cron (автосканирование)';
$_['text_legend_notify']  = 'Уведомления';

// Columns
$_['text_column_name']   = 'Имя';
$_['text_column_type']   = 'Тип';
$_['text_column_size']   = 'Размер';
$_['text_column_mtime']  = 'Дата модификации';
$_['text_column_ctime']  = 'Дата создания';
$_['text_column_rights'] = 'Права';
$_['text_column_crc']    = 'CRC';

// Entry
$_['entry_scan_name']        	= 'Название сканирования';
$_['entry_admin_dir']        	= 'Директория административного раздела';
$_['entry_base_path']        	= 'Базовый путь для сканера';
$_['entry_extensions']       	= 'Расширения файлов';
$_['entry_extensions_help']  	= 'Каждое расширение на новой строке.';
$_['entry_include']          	= 'Добавить дополнительную директорию';
$_['entry_include_help']     	= 'Каждый путь на новой строке. Только папки.';
$_['entry_include_help_block']  = 'Можно использовать только абсолютные пути к папкам без маски.<br>Например:<ul><li>/var/www/username/www-data/other_sitename.com/</li></ul>';
$_['entry_exclude']          	= 'Исключить директории или файлы';
$_['entry_exclude_help']     	= 'Каждый путь на новой строке. Папки и файлы, по маске.';
$_['entry_exclude_help_block']  = 'Можно использовать как абсолютные, так и относительные пути к папкам и файлам с маской.<br>Например:<ul><li>/var/www/username/www-data/other_sitename.com/</li><li>catalog/view/theme/revolution/stylesheet/ocdbanner-206.css</li><li>assets/dynamic_stylesheet/*.css</li></ul>';
$_['entry_cron_access_key']  	= 'Пароль доступа к cron скрипту';
$_['entry_cron_wget']        	= 'wget';
$_['entry_cron_curl']        	= 'curl';
$_['entry_cron_cli']         	= 'cli';
$_['entry_cron_save']        	= 'Сохранять сканирования cron';
$_['entry_cron_save_help']   	= 'При изменении файлов автоматическое сканирование будет добавлено как обычное.';
$_['entry_cron_notify']      	= 'Уведомлять при изменении';
$_['entry_cron_notify_help'] 	= 'Вы будете уведомлены при изменении файлов.';
$_['entry_notify_to'] 	        = 'Способ уведомления';

$_['tab_email'] 	            = 'Email';
$_['tab_whatsapp'] 	            = 'WhatsApp';
$_['tab_telegramm'] 	        = 'Telegram';

$_['entry_e_emails'] 	        = 'Email адрес для уведомлений';
$_['entry_e_emails_help'] 	    = 'Введите адреса разделенные запятой';

$_['entry_w_phone_number'] 	        = 'Phone number';
$_['entry_w_business_account_id'] 	= 'Business account ID';
$_['entry_w_api_token'] 	        = 'API token';
$_['text_whatsapp_help'] 	        = 'Необходим бизнес аккаунт с привязанным номером телефона. Инструкция доступна по <a href="https://turivishal.medium.com/permanent-access-token-for-whatsapp-business-apis-c81e1dfc86c7" target="_blank">ссылке</a>';

$_['entry_t_api_token'] 	        = 'API token';
$_['entry_t_channel_id'] 	        = 'Channel ID';
$_['text_telegram_help'] 	        = 'Инструкция по созданию бота и получения API token доступна по <a href="https://medium.com/@Myreviewbites/how-to-send-message-through-telegram-bot-api-3c3530a53685" target="_blank">ссылке</a>';

// Interval datetime functions
$_['text_interval_days']                   = 'дни';
$_['text_interval_hours']                  = 'часы';
$_['text_interval_minutes']                = 'минуты';
$_['text_interval_less_than_a_minute']     = 'менее минуты';
$_['text_interval_less_than_a_minute_ago'] = 'менее минуты назад';
$_['text_interval_2_minutes_ago']          = '2 минуты назад';
$_['text_interval_3_minutes_ago']          = '3 минуты назад';
$_['text_interval_4_minutes_ago']          = '4 минуты назад';
$_['text_interval_5_minutes_ago']          = '5 минут назад';
$_['text_interval_minutes_ago']            = 'минут назад';
$_['text_interval_1_hour_ago']             = '1 час назад';
$_['text_interval_2_hour_ago']             = '2 часа назад';
$_['text_interval_3_hour_ago']             = '3 часа назад';
$_['text_interval_4_hour_ago']             = '4 часа назад';
$_['text_interval_today_in']               = 'сегодня в';
$_['text_interval_yesterday_in']           = 'вчера в';
$_['text_interval_right_now']              = 'только что';
$_['text_interval_in_2_minutes']           = 'в течении 2-х минут';
$_['text_interval_in_3_minutes']           = 'в течении 3-х минут';
$_['text_interval_in_4_minutes']           = 'в течении 4-х минут';
$_['text_interval_in_5_minutes']           = 'в течении 5-и минут';
$_['text_interval_in_minutes']             = 'в течении %d минут';
$_['text_interval_in_an_hour']             = 'в течении часа';
$_['text_interval_in_2_hours']             = 'в течении 2-х часов';
$_['text_interval_in_3_hours']             = 'в течении 3-х часов';
$_['text_interval_in_4_hours']             = 'в течении 4-х часов';
$_['text_interval_today_at']               = 'сегодня в';
$_['text_interval_tomorrow_at']            = 'завтра в';

// Buttons
$_['button_scan']           = 'Сканировать';
$_['button_rename']         = 'Переименовать';
$_['button_settings']       = 'Настройки';
$_['button_scan_loading']   = 'Сканирование...';
$_['button_rename_loading'] = 'Переименование...';
$_['button_save']           = 'Сохранить';
$_['button_delete']         = 'Удалить';
$_['button_copy']           = 'Скопировать';
$_['button_cancel']         = 'Отмена';
$_['button_view']           = 'Просмотр';
$_['button_generate']       = 'Генерировать настройки по умолчанию';

//Success
$_['text_success_scan_created']  = 'Сканирование создано!';
$_['text_success_renamed']       = 'Сканирование переименовано!';
$_['text_success_scan_initial']  = 'Первичное сканирование создано автоматически!';
$_['text_success_scans_deleted'] = 'Выбранные элементы были удачно удалены!';
$_['text_success_saved']         = 'Настройки удачно сохранены!';

// Error
$_['error_permission']      = 'У вас не достаточно прав!';
$_['error_empty_name']      = 'Пожалуйста, вветите название сканирования!';
$_['error_form']            = 'Пожалуйста, проверьте форму на ошибки!';
$_['error_base_path']       = 'Базовый путь является обязательным полем!';
$_['error_extensions']      = 'Это поле является обязательным для работы сканера!';
$_['error_cron_access_key'] = 'Это поле является обязательным для безопасной работы сканера!';

?>
