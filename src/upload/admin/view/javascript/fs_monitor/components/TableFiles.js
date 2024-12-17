"use strict";

export const TableFiles = {
    view: ({ attrs: { files, onClick, textColumnName, textColumnType, textColumnSize, textColumnMtime, textColumnCtime, textColumnRights, textColumnCrc } }) => {
        return m('table.fs-table-security', [
            m('thead', m('tr', [
                m('th.fs-col-name', textColumnName),
                m('th.fs-col-type', { width:50 }, textColumnType),
                m('th.fs-col-size', { width:150 }, textColumnSize),
                m('th.fs-col-mtime', { width:140 }, textColumnMtime),
                m('th.fs-col-ctime', { width:140 }, textColumnCtime),
                m('th.fs-col-rights', { width:50 }, textColumnRights),
                m('th.fs-col-crc', { width:100 }, textColumnCrc),
            ])),
            m('tbody', [
                Object.entries(files).map(fileObject => {
                    let key = fileObject[0];
                    let file = fileObject[1];

                    return m('tr', [
                        m('td', { onclick: (e) => {
                            onClick(e, key);
                        } }, file.relpath),
                        m('td', file.extension),
                        m('td', { 
                            class: (file.diff?.filesize) ? 'changed' : null, 
                            'data-sort': file.int_filesize 
                        }, file.filesize),
                        m('td', { 
                            class: (file.diff?.filemtime) ? 'changed' : null, 
                            'data-sort': file.int_filemtime 
                        }, file.filemtime),
                        m('td', { 
                            class: (file.diff?.filectime) ? 'changed' : null, 
                            'data-sort': file.int_filectime 
                        }, file.filectime),
                        m('td', { 
                            class: (file.diff?.fileperms) ? 'changed' : null, 
                        }, file.fileperms),
                        m('td', { 
                            class: (file.diff?.crc) ? 'changed' : null, 
                        }, file.crc)
                    ])
                })
            ])
        ]);
    }
}