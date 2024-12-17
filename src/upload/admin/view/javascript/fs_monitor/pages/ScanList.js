"use strict";

import { ScanSummary } from "../components/ScanSummary.js";
import { Pagination } from "../components/Pagination.js";
import { Button } from "../components/Button.js";

export const ScanList = {
	buttonPanel: {
		view: ({ attrs: { state, actions, i18n } }) => {
			return m('.fsm-btn-group', [

				m(Button, { 
					tooltip: i18n['button_add'],
					bclass: 'success',
					icon: 'plus',
					onclick: (e) => {
						actions.stop(e);
						actions.addScan();
					}
				}),

				m(Button, { 
					tooltip: i18n['button_delete'],
					bclass: 'danger',
					icon: 'trash',
					disabled: !state.toTrash.length,
					onclick: (e) => {
						actions.stop(e);
						actions.removeScans()
					}
				}),

				m(Button, { 
					tooltip: i18n['button_settings'],
					bclass: 'primary',
					icon: 'cog',
					onclick: (e) => {
						actions.stop(e);
						m.route.set('/settings')
					}
				}),

			])
		}
	},
	oninit: ({ attrs: { state, actions, i18n } }) => {
		const page = m.route.param('key') || 1;
		const prevPage = state.pagination.page || 1;
		if (prevPage !== page) {
			actions.fetchScans(page);
		}
	},
	view: ({ attrs: { state, actions, i18n }, ...vnode }) => {
		const page = m.route.param('key') || 1;

		return m('div', { key: page }, [

			(state?.scans && Object.keys(state?.scans).length > 0) ? [
					m('.fs-scan-list', [
						state?.groups.map((groupItem, groupKey)=> {
	
							return m('.fs-scan-group', { key: groupKey }, [
								m('.fs-day', groupItem.name),
								m('.fs-scan-group-list', [
									groupItem.children.map((scanKey) => {
										const scanItem = state.scans[scanKey];
										
										return m(ScanSummary, { 
											key: scanItem.scan_id,
											showCheckbox: true,
											showButtonView: true,
											selected: !(state.toTrash.indexOf(parseInt(scanItem.scan_id)) === -1),
											scanItem: scanItem,
											textScanned: i18n['text_label_scanned'], 
											textNew: i18n['text_label_new'], 
											textChanged: i18n['text_label_changed'], 
											textDeleted: i18n['text_label_deleted'],
											textButtonView: i18n['text_view'],
											onNameClick: (event, scan_id) => {
												actions.stop(event);
												m.route.set('/view/:key', { key: scan_id });
											},
											onEditNameClick: (event, scan_id) => {
												actions.stop(event);
												actions.renameScan(scan_id);
											},
											onCheckboxClick: (scan_id) => {
												actions.toggleToTrash(scan_id);
											}
										})
									})
								])
							])
						})
					]),
	
					m(Pagination, { 
						pagination: state.pagination,
						onClick: (page) => {
							state.listInited = true;
							m.route.set('/list/:key', { key: page });
							actions.fetchScans(page);
						}
					})
				]
			:
				m('', i18n['text_list_empty'])
		])
	}
}