"use strict";

import { ScanSummary } from "../components/ScanSummary.js";
import { TableHeader } from "../components/TableHeader.js";
import { TableFiles } from "../components/TableFiles.js";
import { Button } from "../components/Button.js";

const allTables = [
	{
		id: 'new',
		icon: 'plus',
		badge: 'success'
	}, {
		id: 'changed',
		icon: 'ellipsis-h',
		badge: 'warning'
	}, {
		id: 'deleted',
		icon: 'minus',
		badge: 'danger'
	}, {
		id: 'scanned',
		icon: 'file',
		badge: 'default'
	}
];

export const ScanView = {
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
		const scan_id = m.route.param('key');
		if (!state.scans?.[scan_id]?.scan_data) {
			actions.fetchScan(scan_id);
		}
	},
	view: ({ attrs: { state, actions, i18n }, ...vnode }) => {
		const scan_id = m.route.param('key');

		if (state.scans?.[scan_id]) {
			
			let scan = state.scans?.[scan_id];
			
			return [
				m('.fs-view-scan-summary', { key: scan.scan_id + '_scan_summary' }, [
					m(ScanSummary, { 
						key: scan.scan_id + '_scan_summary',
						showCheckbox: false,
						showButtonView: false,
						scanItem: scan,
						textScanned: i18n['text_label_scanned'], 
						textNew: i18n['text_label_new'], 
						textChanged: i18n['text_label_changed'], 
						textDeleted: i18n['text_label_deleted'],
						onNameClick: (event, scan_id) => {
							actions.stop(event);
							m.route.set('/view/:key', { key: scan_id });
						},
						onEditNameClick: (event, scan_id) => {
							actions.stop(event);
							actions.renameScan(scan_id);
						},
					}),
				]),
				
				scan?.scan_data 
				? 
				m('.sd', { key: scan.scan_id + '_scan_data' }, [

					...allTables.filter((value) => scan.scan_data?.[value.id]).map((value) => {

						return m('.fs-scan-data', { key: scan.scan_id + '_' + value.id + '_table' }, [

							m(TableHeader, {
								onCopyClick: (event, data) => {
									actions.stop(event);
									actions.copyTableData(event.target, scan.scan_data[value.id])
								},
								titleText: i18n['text_label_' + value.id], 
								titleValue: scan[value.id + '_count'], 
								titleBadge: value.badge, 
								titleIcon: value.icon, 
								textCopy: i18n['button_copy'],
								expanded:scan.scan_data[ value.id + '_expanded'],
								onClickExpand: (event) => {
									actions.stop(event);
									actions.toggleScanData(scan.scan_id, value.id + '_expanded');
								}
							}),

							scan.scan_data[value.id + '_expanded'] 
							? 
								m(TableFiles, { 
									files: scan.scan_data[value.id],
									onClick: (e, fileName) => {
										actions.stop(e);
										m.route.set('/viewFile/:key', { key: fileName })
									},
									textColumnName : i18n['text_column_name'],
									textColumnType : i18n['text_column_type'],
									textColumnSize : i18n['text_column_size'],
									textColumnMtime : i18n['text_column_mtime'],
									textColumnCtime : i18n['text_column_ctime'],
									textColumnRights : i18n['text_column_rights'],
									textColumnCrc : i18n['text_column_crc'],
								}) 
							: 
							null

						]);
					})
				])
				:
				m('', { key: scan.scan_id + '_loading' }, i18n['text_loading'] )
			]
		} else {
			return m('div', i18n['text_loading'])
		}
	}
}