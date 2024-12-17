"use strict";

export const Panel = {
	view: ({ attrs: { icon, title, version }, children }) => {

		return m('.fsm-panel', children);
	}
}