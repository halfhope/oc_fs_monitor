"use strict";

export const Pagination = {
	view: ({ attrs : { pagination, onClick } } ) => {
		
		const total = Math.floor(pagination.total / pagination.limit);
		
		const onClickHandler = (event, index) => {
			event.stopPropagation();
			event.preventDefault();
			onClick(index);
		};
		
		let items = [];
		// prev
		items.push(
			m('li', { class: pagination.page == 1 ? 'disabled' : null }, 
				m('a', {
					href: '#',
					onclick: (event) => { onClickHandler(event, index) }
				}, m.trust('&laquo;')),
			)
		);
		// pages
		for (let index = 1; index <= total; index++) {
			items.push(
				m('li', { class: pagination.page == index ? 'active' : null }, [
					m('a', { 
						href: '#', 
						onclick: (event) => { onClickHandler(event, index) }
					}, index),
				])
			);
		}
		// next
		items.push(
			m('li', { class: pagination.page == total ? 'disabled' : null }, 
				m('a', {
					href: '#',
					onclick: (event) => { onClickHandler(event, index) }
				}, m.trust('&raquo;'))
			)
		)

		return items.length > 2 ? 
			pagination ? 
				m('ul.pagination', [...items]) 
			: null
		: null;
	}
}