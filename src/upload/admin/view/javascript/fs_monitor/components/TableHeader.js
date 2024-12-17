"use strict";

export const TableHeader = {
    view: ({ attrs: { titleText, titleValue, titleBadge, titleIcon, textCopy, onCopyClick, expanded, onClickExpand } }) => {
        return m('.fs-table-caption', [
            m('span.fs-accordeon-toggle', {
                onclick: onClickExpand
            }, [
                m('i.fa', { className: expanded ? 'fa-chevron-up' : 'fa-chevron-down' }),
                m.trust('&nbsp;'),
                titleText,
                m.trust('&nbsp;'),
                m('span.fs-badge.fs-' + titleBadge, [
                    m('i.fa.fa-' + titleIcon),
                    m.trust('&nbsp;'), titleValue
                ])
            ]),
            m.trust('&nbsp;'),
            m('span.fs-btn.fs-btn-default.fs-btn-sm', { 
                'data-toggle': 'tooltip',
                'title': textCopy,
                onclick: onCopyClick
            }, m('i.fa.fa-copy'))
        ]);
    }
}