"use strict";

import { ScanList } from "./pages/ScanList.js";
import { ScanView } from "./pages/ScanView.js";
import { Settings } from "./pages/Settings.js";
import { ViewFile } from "./pages/ViewFile.js";
import { C404 } from "./pages/C404.js";

import { Default } from "./layouts/Default.js";

const initI18n = (lang) => {
	return new Proxy(Object(lang), {
		get(target, prop) {
			if (prop in target) {
				return target[prop];
			} else {
				return prop;
			}
		}
	});
}

let i18n = {};

const State = () => ( {
	scans: [],
	groups: [],
	toTrash: [],
	pagination: {},
	breadcrumbs: [],
	appendedBreadcrumbs: [],
	apiEntry: '',
	token: '',
	
	version: '',

	settings: {},

	editor: {
		handle: {},
		response: {
			content: '',
			mode: 'php',
			error: null
		}
	}
});

const Actions = state => ({
	// init
	initAppData: function () {
		state.apiEntry = initialData.api_entry;
		state.appContainer = initialData.app_container;
		state.token = initialData.token;
		state.breadcrumbs = initialData.breadcrumbs;
		state.version = initialData.version;

		state.breadcrumbs[2].onclick = (e) => {
			actions.stop(e);
			m.route.set('/list')
		}

		i18n = initI18n(initialData.i18n);
	},
	// api
	apiRequest: function (method, data) {
		return m.request({
			method: "POST",
			url: 'index.php?route=' + state.apiEntry + '/' + method + '&token=' + state.token,
			body: data ,
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			serialize: (data) => { return m.buildQueryString(data) },
		})
	},
	// helper
	stop: function (e) {
		e.preventDefault();
		e.stopPropagation();
	},
	// scans
	fetchScans: function (page = 1) {
		this.apiRequest('getScans', { page })
		.then((result) => {
			state.listInited = true;
			state.groups = result.groups;
			state.scans = result.scans;
			state.pagination = result.pagination;
			state.toTrash = [];
		});
	},
	fetchScan: function (scan_id) {
		this.apiRequest('getScan', { scan_id: scan_id })
		.then((result) => {
			Object.entries(result.scan_data).forEach((value) => {
				const len = Object.keys(value[1]).length;
				result.scan_data[value[0] + '_expanded'] = len < 100;
			});
			state.scans[scan_id] = result;
		});
	},
	toggleScanData: function (scan_id, scan_data_name) {
		state.scans[scan_id].scan_data[scan_data_name] = !state.scans[scan_id].scan_data[scan_data_name];
	},
	addScan: function () {
		let scan_name = prompt(i18n['entry_scan_name'], '');
		if (scan_name) {
			this.apiRequest('addScan', { scan_name })
			.then((response) => {
				this.fetchScans();
			});
		}
	},
	renameScan: function (scan_id, scan_name) {
		let old_scan_name = state.scans[scan_id].name;
		let new_scan_name = prompt(i18n['entry_scan_name'], old_scan_name);
		if (new_scan_name) {
			this.apiRequest('renameScan', { scan_id, scan_name: new_scan_name })
			.then((response) => {
				this.fetchScan(scan_id);
				this.fetchScans();
			});
		}
	},
	toggleToTrash: function (scanId) {
		var itemIndex = state.toTrash.indexOf(scanId);
		if (itemIndex === -1) {
			state.toTrash.push(scanId);
		} else {
			state.toTrash.splice(itemIndex, 1);
		}
	},
	removeScans: function () {
		let self = this;
		this.apiRequest('deleteScans', { scans: state.toTrash })
		.then(function(result) {
			self.fetchScans();
		})
	},
	copyTableData: function (target, data) {
		var selected = '';
		Object.entries(data).map(fileObject => {
			let key = fileObject[0];
			let file = fileObject[1];
			selected += file.relpath + "\n";
		});
		this.copyToClipboard(target, selected);
	},
	// settings
	getSettings: function () {
		console.log('getSettings fired');
		this.apiRequest('getSettings')
		.then(function(result) {
			state.settings = result;
		})
	},
	saveSettings: function () {
		this.apiRequest('saveSettings', { ...state.settings.settings })
		.then(function(result) {
			// console.log('saved', result);
		})
	},
	generateDefaultSettings: function () {
		let self = this;
		this.apiRequest('generateDefaultSettings')
		.then(function(result) {
			self.getSettings();
		})
	},
	// viewFile
	getFileContent: function (fileName) {
		state.editor.response = {};
		actions.apiRequest('viewFile', {
			file_name: fileName
		}).then((response) => {
			state.editor.response = response;
		})
	},
	// tooltip
	tooltipCreate: function () {
		if (window.jQuery) {
			$(state.appContainer).tooltip({ selector: '[data-toggle="tooltip"]', trigger : 'hover', container: state.appContainer });
		} else {
			console.log('JQUERY not loaded');
		}
	},
	tooltipDestroy: function (elements) {
		if (window.jQuery) {
			$('[data-toggle="tooltip"]').tooltip('destroy');
		} else {
			console.log('JQUERY not loaded');
		}
	},
	copyToClipboard: function(target, text) {
		var textarea = target.appendChild(document.createElement("textarea"));
		textarea.value = text;
		textarea.focus();
		textarea.select();
		document.execCommand('copy');
		textarea.parentNode.removeChild(textarea);
	}
});


let state = State();

const actions = Actions(state);
actions.initAppData();

const root = document.querySelector(state.appContainer);

m.route.prefix = window.location.pathname + window.location.search + '#!';

m.route(root, '/list/1', {
	'/list/:key': { 
		view: (vnode) => {
			return m(Default, {
				state, actions, i18n,
				buttonPanel: ScanList.buttonPanel || null
			}, m(ScanList, { key: vnode.key, state, actions, i18n }));
		},
	},
	'/view/:key': { 
		view: (vnode) => {
			return m(Default, {
				state, actions, i18n,
				buttonPanel: ScanView.buttonPanel || null
			}, m(ScanView, { key: vnode.key, state, actions, i18n }));
		}
	},
	'/settings': { 
		view: (vnode) => {
			return m(Default, {
				state, actions, i18n,
				buttonPanel: Settings.buttonPanel || null
			}, m(Settings, { key: vnode.key, state, actions, i18n }));
		}
	},
	'/viewFile/:key': { 
		view: (vnode) => {
			return m(Default, {
				state, actions, i18n,
				buttonPanel: ViewFile.buttonPanel || null
			}, m(ViewFile, { key: vnode.key, state, actions, i18n }));
		}
	},
	// '/:404...': { 
	//     view: (vnode) => {
	//         return m(C404, { key: vnode.key, state, actions, text404: i18n['text_404'] });
	//     }
	// },
});