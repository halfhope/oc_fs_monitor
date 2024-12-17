"use strict";

import { Badge } from "./Badge.js";
import { Button } from "./Button.js";

export const ScanSummary = {
	view: ({ attrs: { selected, scanItem, showCheckbox, showButtonView, onCheckboxClick, onNameClick, onEditNameClick, textScanned, textNew, textChanged, textDeleted, textButtonView } }) => {
		return m('.fs-scan-summary', { class: selected ? 'fs-selected' : null }, [

			m('.fs-changes-list', [
				showCheckbox ? m('.fs-checkbox', 
					m('input.form-check-input', { 
						type: 'checkbox',
						checked: selected, 
						value: scanItem.scan_id, 
						onclick: () => {
							onCheckboxClick(parseInt(scanItem.scan_id))
						}
					})
				) : null,
				m('div', [
					m('.fs-scan-name', [
						m('span', {
							onclick: (event) => {
								onNameClick(event, scanItem.scan_id)
								// m.route.set('/view/:key', { key: scanItem.scan_id });
							}
						}, scanItem.name),
						m('span', {
							onclick: (event) => {
								onEditNameClick(event, scanItem.scan_id)
								// m.route.set('/view/:key', { key: scanItem.scan_id });
							}
						}, m('i.fa.fa-edit'))
					]),
					m('.fs-scan-date-added', [
						scanItem.user_name, 
						m.trust('&nbsp;'), 
						m.trust(scanItem.date_added_ago)
					])
				])
			]), // .scan-heading
				
			m('.fs-changes-list', [

				(scanItem.scanned_count > 0) 
				? m(Badge, { 
					className: 'default', 
					title: textScanned,
					icon: 'file-o'}, 
					[m.trust('&nbsp;'), scanItem.scanned_count])
				: null,

				(scanItem.new_count > 0) 
				? m(Badge, { 
					className: 'success', 
					title: textNew,
					icon: 'plus'}, 
					[m.trust('&nbsp;'), scanItem.new_count])
				: null,

				(scanItem.changed_count > 0)
				? m(Badge, { 
					className: 'warning', 
					title: textChanged,
					icon: 'ellipsis-h'}, 
					[m.trust('&nbsp;'), scanItem.changed_count])
				: null,

				(scanItem.deleted_count > 0) 
				? m(Badge, { 
					className: 'danger', 
					title: textDeleted,
					icon: 'minus'}, 
					[m.trust('&nbsp;'), scanItem.deleted_count])
				: null,
				
			]), // .changes-list
			
			m('.fs-changes-list', [ 
				(scanItem.scan_size_rel == 0) 
				? m(Badge, { className: 'primary', title: scanItem.scan_size_abs_humanized}, [m.trust('&nbsp;'), scanItem.scan_size_rel_humanized])
				: (scanItem.scan_size_rel > 0) 
					? m(Badge, { className: 'success', title: scanItem.scan_size_abs_humanized, icon: 'plus'}, [m.trust('&nbsp;'), scanItem.scan_size_rel_humanized])
					: m(Badge, { className: 'danger', title: scanItem.scan_size_abs_humanized, icon: 'minus'}, [m.trust('&nbsp;'), scanItem.scan_size_rel_humanized])
			]), // .changes-list
			
			m('.fs-changes-list.fsm-pull-right', [
			showButtonView 
			? 
				m(Button, { 
					tooltip: textButtonView,
					bclass: 'default',
					icon: 'eye',
					onclick: (e) => {
					}
				})
				: 
				null,
			]) // .changes-list

		])
	}
}