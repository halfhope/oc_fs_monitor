"use strict";
export const NavItem = {
    view: ({ attrs: { active, href, title }, children }) => {
        return m('li', { key: href, class: (active ? 'active' : '') }, [
            m('a[role="tab"][data-toggle=tab]', { href }, [
                title,
                children
            ])
        ]);
    }
}