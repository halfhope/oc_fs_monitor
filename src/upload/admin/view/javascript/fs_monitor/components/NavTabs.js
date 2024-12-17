"use strict";
export const NavTabs = {
    view: ({ attrs: { title }, children }) => {
        return m('ul.nav.nav-tabs', [
            title ? m('li.nav-title', title) : null,
            children
        ]);
    }
}