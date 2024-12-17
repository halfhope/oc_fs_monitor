"use strict";

export const Badge = {
    view: ({ attrs: { className, title, icon }, children }) => {
        return m('span.fs-badge', {
            class: 'fs-' + className
        },
            m('span.files-new.fs-label', {
                'data-toggle': 'tooltip',
                title: title,
            }, [
                icon ? m('i.fa.fa-' + icon) : null,
                children
            ])
        );
    }
}