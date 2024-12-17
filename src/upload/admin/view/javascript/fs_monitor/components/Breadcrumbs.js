"use strict";

export const Breadcrumbs = {
	view: ({ attrs: { items } }) => {
		return m('ul.fs-breadcrumb', [
			items.map(item => {
				return m('li', [
					item?.onclick ? 
						m('a', {
							href: item?.href || '#',
							onclick: item.onclick
						}, item.text)
					: 
						m('a', {
							href: item?.href || '#',
						}, item.text)
				])	
			})
		]);
	}
}