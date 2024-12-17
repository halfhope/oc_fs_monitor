"use strict";

import { Button } from "../components/Button.js";

import { TabContent } from "../components/TabContent.js";
import { TabPane } from "../components/TabPane.js";

import { NavTabs } from "../components/NavTabs.js";
import { NavItem } from "../components/NavItem.js";

export const Settings = {
	buttonPanel: {
		view: ({ attrs: { state, actions, i18n } }) => {
			return m('.fsm-btn-group', [

				m(Button, { 
					tooltip: i18n['button_save'],
					bclass: 'success',
					icon: 'save',
					onclick: (e) => {
						actions.stop(e);
						actions.saveSettings();
					}
				}),
				
				m(Button, { 
					tooltip: i18n['button_generate'],
					bclass: 'warning',
					icon: 'cogs',
					onclick: (e) => {
						actions.stop(e);
						actions.generateDefaultSettings()
					}
				}),
	
				m(Button, { 
					tooltip: i18n['button_cancel'],
					bclass: 'default',
					icon: 'reply',
					onclick: (e) => {
						actions.stop(e);
						history.back();
					}
				}),

			])
		}
	},
	oninit: ({ attrs: { actions } }) => {
		actions.getSettings();
	},
	view: ({ attrs: { state, i18n } }) => {
		return m('div', [
			
			m('fieldset', [
				m('legend', i18n['text_legend_module']),
	
				m('.fs-form-group.fs-required', [
					m('.fs-col-sm-2.fs-control-label', i18n['entry_admin_dir']),
					m('.fs-col-sm-10', [
						m('input.fs-form-control', {
							type: 'text',
							name: 'security_fs_admin_dir',
							placeholder: i18n['entry_admin_dir'],
							value: state.settings?.settings?.security_fs_admin_dir || 'admin',
							oninput: (event) => {
								state.settings.settings.security_fs_admin_dir = event.target.value;
							}
						})
					])
				])
			]), // fieldset
			
			m('fieldset', [
				m('legend', i18n['text_legend_scanner']),
	
				m('.fs-form-group.fs-required', [
					m('.fs-col-sm-2.fs-control-label', i18n['entry_base_path']),
					m('.fs-col-sm-10', [
						m('input.fs-form-control', {
							type: 'text',
							name: 'security_fs_base_path',
							placeholder: i18n['entry_base_path'],
							value: state.settings?.settings?.security_fs_base_path || '',
							oninput: (event) => {
								state.settings.settings.security_fs_base_path = event.target.value;
							}
						}),
						// error
						(state.settings?.error?.error_base_path) ? m('.text-danger', state.settings?.error?.error_base_path) : null
					])
				]),

				m('.fs-form-group.fs-required', [
					m('.fs-col-sm-2.fs-control-label', [
						m('span', {
							'data-toggle': 'tooltip',
							title: i18n['entry_extensions'],
						}, i18n['entry_extensions'])
					]),
					m('.fs-col-sm-10', [
						m('textarea.fs-form-control', {
							cols: 30,
							rows: 10,
							name: 'security_fs_extensions',
							value: state.settings?.settings?.security_fs_extensions || '',
							oninput: (event) => {
								state.settings.settings.security_fs_extensions = event.target.value;
							}
						}),
						// error
						(state?.settings?.error?.error_extensions) ? m('.text-danger', state.settings?.error?.error_extensions) : null
					])
				]),

				m('.fs-form-group', [
					m('.fs-col-sm-2.fs-control-label', [
						m('span', {
							'data-toggle': 'tooltip',
							title: i18n['entry_include_help'],
						}, i18n['entry_include'])
					]),
					m('.fs-col-sm-10', [
						m('textarea.fs-form-control', {
							cols: 30,
							rows: 10,
							name: 'security_fs_include',
							value: state.settings?.settings?.security_fs_include || '',
							oninput: (event) => {
								state.settings.settings.security_fs_include = event.target.value;
							}
						}),
						m('.help-block', m.trust(i18n['entry_include_help_block']))
					])
				]),
				
				m('.fs-form-group', [
					m('.fs-col-sm-2.fs-control-label', [
						m('span', {
							'data-toggle': 'tooltip',
							title: i18n['entry_exclude_help'],
						}, i18n['entry_exclude'])
					]),
					m('.fs-col-sm-10', [
						m('textarea.fs-form-control', {
							cols: 30,
							rows: 10,
							name: 'security_fs_exclude',
							value: state.settings?.settings?.security_fs_exclude || '',
							oninput: (event) => {
								state.settings.settings.security_fs_exclude = event.target.value;
							}
						}),
						m('.help-block', m.trust(i18n['entry_exclude_help_block']))
					])
				]),
	
			]), // fieldset
			
			m('fieldset', [
				m('legend', i18n['text_legend_cron']),
		
				m('.fs-form-group.fs-required', [
					m('.fs-col-sm-2.fs-control-label', i18n['entry_cron_access_key']),
					m('.fs-col-sm-10', [
						m('input.fs-form-control', {
							type: 'text',
							name: 'security_fs_cron_access_key',
							placeholder: i18n['entry_admin_dir'],
							value: state.settings?.settings?.security_fs_cron_access_key || '',
							oninput: (event) => {
								state.settings.settings.security_fs_cron_access_key = event.target.value;
							}
						}),
						// error
						(state.settings?.error?.error_access_key) ? m('.text-danger', state.settings?.error?.error_access_key) : null

					])
				]),

				m('.fs-form-group', [
					m('.fs-col-sm-2.fs-control-label', i18n['entry_cron_wget']),
					m('.fs-col-sm-10', [
						m('input.fs-form-control', {
							type: 'text',
							readonly: 1,
							'data-default': 'security_fs_cron_wget',
							value: state.settings?.entries?.security_fs_cron_wget + state.settings?.settings?.security_fs_cron_access_key + '\'',
						})
					])
				]),
				
				m('.fs-form-group', [
					m('.fs-col-sm-2.fs-control-label', i18n['entry_cron_curl']),
					m('.fs-col-sm-10', [
						m('input.fs-form-control', {
							type: 'text',
							readonly: 1,
							'data-default': 'security_fs_cron_curl',
							value: state.settings?.entries?.security_fs_cron_curl + state.settings?.settings?.security_fs_cron_access_key + '\'',
						})
					])
				]),
				
				m('.fs-form-group', [
					m('.fs-col-sm-2.fs-control-label', i18n['entry_cron_cli']),
					m('.fs-col-sm-10', [
						m('input.fs-form-control', {
							type: 'text',
							readonly: 1,
							'data-default': 'security_fs_cron_cli',
							value: state.settings?.entries?.security_fs_cron_cli + state.settings?.settings?.security_fs_cron_access_key + '\'',
						})
					])
				]),

				m('.fs-form-group', [

					m('.fs-col-sm-2.fs-control-label', 
						m('span', {
							'data-toggle': 'tooltip',
							title: i18n['entry_cron_save_help'],
						}, i18n['entry_cron_save'])
					),

					m('.fs-col-sm-10', [
						
						m('label.radio-inline', [
							m('input', {
								type: 'radio',
								name: 'security_fs_cron_save',
								checked: (parseInt(state.settings?.settings?.security_fs_cron_save)) ? 'checked' : '',
								value: 1,
								onchange: (event) => {
									state.settings.settings.security_fs_cron_save = event.target.value;
								}
							}), 
							m.trust('&nbsp;'),
							i18n['text_yes']
						]),
						
						m('label.radio-inline', [
							m('input', {
								type: 'radio',
								name: 'security_fs_cron_save',
								checked: (!parseInt(state.settings?.settings?.security_fs_cron_save)) ? 'checked' : '',
								value: 0,
								onchange: (event) => {
									state.settings.settings.security_fs_cron_save = event.target.value;
								}
							}), 
							m.trust('&nbsp;'),
							i18n['text_no']
						]),

					])
				]),

				m('.fs-form-group', [

					m('.fs-col-sm-2.fs-control-label', 
						m('span', {
							'data-toggle': 'tooltip',
							title: i18n['entry_cron_notify_help'],
						}, i18n['entry_cron_notify'])
					),

					m('.fs-col-sm-10', [
						
						m('label.radio-inline', [
							m('input', {
								type: 'radio',
								name: 'security_fs_cron_notify',
								checked: (parseInt(state.settings?.settings?.security_fs_cron_notify)) ? 'checked' : '',
								value: 1,
								onchange: (event) => {
									state.settings.settings.security_fs_cron_notify = event.target.value;
								}
							}), 
							m.trust('&nbsp;'),
							i18n['text_yes']
						]),
						
						m('label.radio-inline', [
							m('input', {
								type: 'radio',
								name: 'security_fs_cron_notify',
								checked: (!parseInt(state.settings?.settings?.security_fs_cron_notify)) ? 'checked' : '',
								value: 0,
								onchange: (event) => {
									state.settings.settings.security_fs_cron_notify = event.target.value;
								}
							}), 
							m.trust('&nbsp;'),
							i18n['text_no']
						]),

					])
				]),

			]), // fieldset
			
			m('fieldset', [
				m('legend', i18n['text_legend_notify']),

				m('.fs-form-group', [
					m('.fs-col-sm-2.fs-control-label', i18n['entry_notify_to']),
					m('.fs-col-sm-10', [
						m('select.fs-form-control', {
							onchange: (event) => {
								state.settings.settings.security_fs_notify_to = event.target.value;
							}
						}, [
							['email', 'whatsapp', 'telegram'].map(function(value, index) {
								return m('option', { value: value, selected: state.settings?.settings?.security_fs_notify_to === value }, i18n['tab_' + value])
							})
						])
					])
				]),
				
				m(NavTabs, [

					['email', 'whatsapp', 'telegram'].map((value, index) => {
						return m(NavItem, { 
							active: state.settings?.settings?.security_fs_notify_to === value, 
							href: '#tab-notify-' + value, 
							title: i18n['tab_' + value]
						})
					})
	
				]),

				m(TabContent, [
					m(TabPane, { active: state.settings?.settings?.security_fs_notify_to === 'email', id: 'tab-notify-email'}, [
						
						m('.fs-form-group', [
							m('.fs-col-sm-2.fs-control-label', 
								m('span', {
									'data-toggle': 'tooltip',
									title: i18n['entry_e_emails_help'],
								}, i18n['entry_e_emails'])
							),
							m('.fs-col-sm-10', [
								m('input.fs-form-control', {
									type: 'text',
									name: 'security_fs_e_emails',
									value: state.settings?.settings?.security_fs_e_emails || '',
									onchange: (event) => {
										state.settings.settings.security_fs_e_emails = event.target.value;
									}
								})
							])
						]),

					]),
					m(TabPane, { active: state.settings?.settings?.security_fs_notify_to === 'whatsapp', id: 'tab-notify-whatsapp'}, [
						
						m('.fs-form-group.fs-required', [
							m('.fs-col-sm-2.fs-control-label', i18n['entry_w_phone_number']),
							m('.fs-col-sm-10', [
								m('input.fs-form-control', {
									type: 'text',
									name: 'security_fs_w_phone_number',
									placeholder: i18n['entry_w_phone_number'],
									value: state.settings?.settings?.security_fs_w_phone_number || '',
									oninput: (event) => {
										state.settings.settings.security_fs_w_phone_number = event.target.value;
									}
								})
							])
						]),
						
						m('.fs-form-group.fs-required', [
							m('.fs-col-sm-2.fs-control-label', i18n['entry_w_business_account_id']),
							m('.fs-col-sm-10', [
								m('input.fs-form-control', {
									type: 'text',
									name: 'security_fs_w_business_account_id',
									placeholder: i18n['entry_w_business_account_id'],
									value: state.settings?.settings?.security_fs_w_business_account_id || '',
									oninput: (event) => {
										state.settings.settings.security_fs_w_business_account_id = event.target.value;
									}
								})
							])
						]),
						
						m('.fs-form-group.fs-required', [
							m('.fs-col-sm-2.fs-control-label', i18n['entry_w_api_token']),
							m('.fs-col-sm-10', [
								m('input.fs-form-control', {
									type: 'text',
									name: 'security_fs_w_api_token',
									placeholder: i18n['entry_w_api_token'],
									value: state.settings?.settings?.security_fs_w_api_token || '',
									oninput: (event) => {
										state.settings.settings.security_fs_w_api_token = event.target.value;
									}
								})
							])
						]),

						m('.help-block', [ m.trust(i18n['text_whatsapp_help']) ])

					]),
					m(TabPane, { active: state.settings?.settings?.security_fs_notify_to === 'telegram', id: 'tab-notify-telegram'}, [
						
						m('.fs-form-group.fs-required', [
							m('.fs-col-sm-2.fs-control-label', i18n['entry_t_api_token']),
							m('.fs-col-sm-10', [
								m('input.fs-form-control', {
									type: 'text',
									name: 'security_fs_t_api_token',
									placeholder: i18n['entry_t_api_token'],
									value: state.settings?.settings?.security_fs_t_api_token || '',
									oninput: (event) => {
										state.settings.settings.security_fs_t_api_token = event.target.value;
									}
								})
							])
						]),

						m('.fs-form-group.fs-required', [
							m('.fs-col-sm-2.fs-control-label', i18n['entry_t_channel_id']),
							m('.fs-col-sm-10', [
								m('input.fs-form-control', {
									type: 'text',
									name: 'security_fs_t_channel_id',
									placeholder: i18n['entry_t_channel_id'],
									value: state.settings?.settings?.security_fs_t_channel_id || '',
									oninput: (event) => {
										state.settings.settings.security_fs_t_channel_id = event.target.value;
									}
								})
							])
						]),

						m('.help-block', [ m.trust(i18n['text_telegram_help']) ])

					])
				])
				
			]), // fieldset

		])
	}
}