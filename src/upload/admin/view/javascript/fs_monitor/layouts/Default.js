"use strict";

import { Breadcrumbs } from "../components/Breadcrumbs.js";
import { Panel } from "../components/Panel.js";

export const Default = {
	onupdate: ( { attrs: { actions } } ) => {
		actions.tooltipDestroy();
		actions.tooltipCreate();
	},
	view: ({ attrs: { state, actions, i18n, buttonPanel }, children }) => {
		return m('', [
			m('.fsm-container-fluid', [
				m('.fsm-page-header', [

					m('h1', i18n['heading_title']),

					state.version ? m('h1', 'v' + state.version) : null,

					m(Breadcrumbs, { items: [...state.breadcrumbs] }),
					
					m(buttonPanel, { state, actions, i18n })

				])
			]),
			m('.fsm-container-fluid', [
				m(Panel, {
					icon: 'shield', 
					title: i18n['heading_title'], 
					version: 'v1.2.1' 
				}, children)
			])
		])
	}
}