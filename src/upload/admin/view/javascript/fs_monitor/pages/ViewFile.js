"use strict";

import { Button } from "../components/Button.js";

export const ViewFile = {
	buttonPanel: {
		view: ({ attrs: { state, actions, i18n } }) => {
			return m('.fsm-btn-group', [

				m(Button, { 
					tooltip: i18n['button_cancel'],
					bclass: 'default',
					icon: 'reply',
					onclick: (e) => {
						actions.stop(e);
						history.back();
					}
				}),
				
			]);
		}
	},
	oninit: ({ attrs: { state, actions, i18n } }) => {
		const fileName = m.route.param('key') || 1;
		actions.getFileContent(fileName);
	},
	onupdate: ({ attrs: { state, actions, i18n } }) => {
		if (!state.editor.response?.error) {
			state.editor.handle = ace.edit('editor');
			state.editor.handle.setOptions({
				maxLines: 150
			});
			state.editor.handle.setTheme('ace/theme/chrome');
			state.editor.handle.getSession().setMode('ace/mode/' + (state.editor.response?.mode || 'php'));
		}
	},
	view: ({ attrs: { state, actions, i18n }, ...vnode }) => {
		const fileName = m.route.param('key') || 1;

		return (state.editor.response?.error) 
		? 
			m('.alert.alert-danger', state.editor.response.error)
		:
			(state.editor.response?.content) 
			? 
				m('#editor', { key: fileName }, [
					state.editor.response?.content || ''
				])
			:
				m('', i18n['text_loading'])
	}
}