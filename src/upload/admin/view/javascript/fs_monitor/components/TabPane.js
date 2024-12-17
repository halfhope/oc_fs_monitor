"use strict";

export const TabPane = {
    view: ({ attrs: { id, active }, children}) => {
        return m('.tab-pane', { key: id, id, class: (active ? 'active' : '') }, children);
    }
}