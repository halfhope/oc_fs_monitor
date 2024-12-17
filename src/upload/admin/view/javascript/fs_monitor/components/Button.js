"use strict";

export const Button = {
	view: ({ attrs: { tooltip, bclass, icon, text, disabled, onclick } }) => {
		return m('button', {
			href: '#',
			'data-toggle': tooltip ? 'tooltip' : null,
			title: tooltip,
			class: 'fs-btn fs-btn-' + bclass,
			disabled: disabled ? 'disabled' : null,
			onclick: onclick
		}, [
			icon ? m('i.fa.fa-' + icon) : null,
			text ? text : null
		])
	}
}