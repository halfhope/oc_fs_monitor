<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<style>
#fsm {
	font-family: 'Open Sans', sans-serif;
	font-size: 12px;
	color: #666666;
	text-rendering: optimizeLegibility;
}
.fsm-container-fluid {
	padding: 0px 20px;
}
.fsm-page-header {
	margin: 15px 0;
	padding: 0;
	border-bottom: none;
	display:flex;
	align-items: flex-end;
}
.fsm-page-header h1{
	margin: 0px;
	font-weight: 300;
	font-size: 30px;
	color: #4c4d5a;
	text-shadow: 0 1px #fff;
}
.fsm-page-header h1 + h1{
	margin-left: 15px;
}
.fsm-btn-group {
	margin-left: auto;
}
.fsm-page-header .fs-btn + .fs-btn {
	margin-left: 5px;
}
/* helpers */
.fsm-pull-right {
	margin-left: auto;
}
/* panel */
.fsm-panel {
	padding: 15px;
	border: 1px solid #ccc;
	border-top-width: 3px;
	border-top-color: #666;
	border-radius: 4px;
}
/* scans */

.fs-scan-list {
}
.fs-scan-group {
	margin-bottom: 10px;
}
.fs-scan-group .fs-day {
	font-size: 14px;
	color: #767676;
	margin-bottom: 10px
}
.fs-scan-list .fs-scan-summary+.fs-day {
	padding-top: 10px
}
.fs-scan-list .day span {
	margin-left: 7px
}

/* scan summary */
.fs-scan-summary {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-evenly;
	width: 100%;
	/* border-radius: 4px; */
	padding: 5px;
	border: 1px solid #DDDDDD;
	font-size: 1em;
	color: #666;
}
.fs-view-scan-summary {
	margin-bottom: 1em;
}
.fs-scan-summary.fs-selected {
	background: #e9e9e9;
}
.fs-scan-summary:not(.fs-selected):hover {
	background: #f7fbfc
}
.fs-scan-summary+.fs-scan-summary {
	border-top: none;
}
.fs-scan-summary:first-child{
	border-top-left-radius: 4px;
	border-top-right-radius: 4px;
}
.fs-scan-summary:last-child {
	border-bottom-left-radius: 4px;
	border-bottom-right-radius: 4px;
}
.fs-scan-list .fs-checkbox {
	width: 25px;
	display: flex;
	flex-direction: column;
	justify-content: space-evenly;
}
.fs-scan-list .fs-checkbox input[type=checkbox] {
	margin: 0;
}
.fs-scan-list .fs-checkbox input[type=checkbox]:hover {
	cursor: pointer;
}
.fs-scan-name {
	font-size: 15px;
	font-weight: 600;
	cursor:pointer
}
.fs-scan-name a {
	color: #4e575b
}
.fs-scan-name span i.fa{
	margin-left: 10px;
}
.fs-scan-date-added {
	color: #767676
}
.fs-changes-list {
	flex: 25%;
	padding-left: 0 5px;
}
.fs-changes-list:first-child {
	display: flex;
	padding-left: 5px;
}
.fs-changes-list:last-child {
	flex: none;
	width: 50px;
	padding:none;
}
.fs-changes-list + .fs-changes-list{
	border-left: 1px solid #ddd;
	padding-left: 5px;
}
.fs-scan-summary .changes-list {
	color: #767676;
	border-left: 1px solid #ddd;
	padding: 0px 5px;
	line-height: 38px;
}
/* pagination */
.fs-pagination {
	margin: 1em 0 0 2em;
}
/* breadchtumb */
.fs-breadcrumb {
	display: inline-block;
	background: none;
	margin: 0;
	padding: 0 10px;
	border-radius: 0;
}
.fs-breadcrumb > li {
	display: inline-block;
	text-shadow: 0 1px #fff;
}
.fs-breadcrumb li + li:before {
	content: "\f105";
	font-family: FontAwesome;
	color: #BBBBBB;
	padding: 0 5px;
	font-size: 10px;
}
.fs-breadcrumb > li:last-child a {
	color: #1e91cf;
}
.fs-breadcrumb li a {
	color: #999999;
	font-size: 14px;
	padding: 0px;
	margin: 0px;
}
/* badges */
.fs-badge {
	display: inline;
	font-size: 10pt;
	padding: 0.2em 0.6em 0.3em;
	font-weight: bold;
	line-height: 2.5;
	color: #fff;
	text-align: center;
	white-space: nowrap;
	vertical-align: baseline;
	border-radius: 0.25em;
}
.fs-default {
	background-color: #777;
}
.fs-primary {
	background-color: #1e91cf;
}
.fs-success {
	background-color: #4cb64c;
}
.fs-warning {
	background-color: #f3a638;
}
.fs-danger {
	background-color: #e3503e;
}
.fs-badge+.fs-badge {
	margin-left: 5px;
}
/* buttons */
.fs-btn {
	display: inline-block;
	margin-bottom: 0;
	font-weight: normal;
	text-align: center;
	vertical-align: middle;
	touch-action: manipulation;
	cursor: pointer;
	background-image: none;
	border: 1px solid transparent;
	white-space: nowrap;
	padding: 8px 13px;
	font-size: 13px;
	line-height: 1.42857;
	border-radius: 3px;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}
.fs-btn-sm, .fs-btn-group-sm > .fs-btn {
	padding: 4px 9px;
	font-size: 11px;
	line-height: 1.5;
	border-radius: 2px;
}
.fs-btn[disabled] {
	cursor: not-allowed;
	opacity: 0.65;
	filter: alpha(opacity = 65);
	-webkit-box-shadow: none;
	box-shadow: none;
}
.fs-btn.fs-btn-primary {
	color: #fff;
	background-color: #1e91cf;
	border-color: #197bb0;
}
.fs-btn.fs-btn-primary:hover {
	color: #fff;
	background-color: #1872a2;
	border-color: #12567a;
}
.fs-btn.fs-btn-default {
	color: #666;
	background-color: #fff;
	border-color: #ddd;
}
.fs-btn.fs-btn-default:hover {
	color: #666;
	background-color: #e6e6e6;
	border-color: #bebebe;
}
.fs-btn.fs-btn-success {
	color: #fff;
	background-color: #4cb64c;
	border-color: #409e40;
}
.fs-btn.fs-btn-success:hover {
	color: #fff;
	background-color: #3c933c;
	border-color: #2f722f;
}
.fs-btn.fs-btn-warning {
	color: #fff;
	background-color: #f3a638;
	border-color: #f19716;
}
.fs-btn.fs-btn-warning:hover {
	color: #fff;
	background-color: #ea8f0e;
	border-color: #bf750b;
}
.fs-btn.fs-btn-danger {
	color: #fff;
	background-color: #e3503e;
	border-color: #dd3520;
}
.fs-btn.fs-btn-danger:hover {
	color: #fff;
	background-color: #d0321e;
	border-color: #a82818;
}
/* Small devices (landscape phones, 576px and up) */
@media (min-width: 576px) {
	.fs-changes-list {
		flex: 100%;
	}
}
/* X-Large devices (large desktops, 1200px and up) */
<div class="pull-right">
/* Medium devices (tablets, 768px and up) */
<a href="<?php echo $scan['href'] ?>#deleted">
@media (min-width: 768px) {
	.fs-changes-list {
		flex: 30%;
	}
}
/* Large devices (desktops, 992px and up) */
@media (min-width: 992px) {
	.fs-changes-list {
		flex: 30%;
	}
}
/* X-Large devices (large desktops, 1200px and up) */
@media (min-width: 1200px) {
	.fs-changes-list {
		flex: 30%;
	}
}
/* XX-Large devices (larger desktops, 1400px and up) */
@media (min-width: 1400px) {
	.fs-changes-list {
		flex: 30%;
	}
}
/* span.label{font-size:10pt} */
.fs-scan-data {
	margin-bottom: 1em;
}
.fs-table-security tr>td:first-child:hover{
	cursor: pointer;
}
.fs-table-caption{font-size:15px;font-weight:600;margin-bottom:1em;}
table.fs-table-security{table-layout:fixed}
table.fs-table-security *{font-family:Consolas;font-size:9pt;color:#666}
table.fs-table-security{border:1px solid #ccf;width:100%;border-collapse:collapse;border:1px solid #8892BF}
table.fs-table-security > thead > tr{background:#ccf}
table.fs-table-security > thead > tr > th{background:#8892BF;padding:3px 5px;color:#fff;text-align:left;border-bottom:1px solid #8892BF;border-radius:0!important;text-transform:none;}
table.fs-table-security > thead > tr > th + th{border-left:1px solid #99c}
table.fs-table-security > tbody > tr > td{padding:3px}
table.fs-table-security > tbody > tr > td:first-child{overflow:hidden;white-space:nowrap;}
table.fs-table-security > tbody > tr:nth-child(odd){background:#eef}
table.fs-table-security > tbody > tr:hover{background:#ddf}
table.fs-table-security > tbody > tr > td.changed{background:#4F5B93;color:#fff}
table.fs-table-security.fs-table-hidden{display:none}
table td a{color:#666}
table th.fs-col-type{width:50px}
table th.fs-col-size{width:150px}
table th.fs-col-mtime{width:140px}
table th.fs-col-ctime{width:140px}
table th.fs-col-rights{width:50px}
table th.fs-col-crc{width:100px}
.fs-copy-btn:hover, .fs-accordeon-toggle:hover{
	cursor: pointer;
}
fieldset {
	padding: 0;
	margin: 0;
	border: 0;
	min-width: 0;
}
fieldset legend {
	display: block;
	width: 100%;
	padding: 0;
	margin-bottom: 17px;
	font-size: 18px;
	line-height: inherit;
	color: #333;
	border: 0;
	border-bottom: 1px solid #e5e5e5;
	padding-bottom: 5px;
}
.fs-form-group{
	padding-top: 15px;
	padding-bottom: 15px;
	margin-bottom: 0;
	display:flex;
}
.fs-control-label{
	text-align: right;
	margin-bottom: 0;
	padding-top: 9px;
	font-weight: 600;
}
.fs-required .fs-control-label:before{
	content: '* ';
	color: #F00;
	font-weight: bold;
}
.fs-control-label span:after {
	font-family: FontAwesome;
	color: #1E91CF;
	content: "\f059";
	margin-left: 4px;
}
.fs-form-control {
	display: block;
	outline: none;
	width: 100%;
	height: 36px;
	padding: 8px 13px;
	font-size: 13px;
	line-height: 1.42857;
	color: #555;
	background-color: #fff;
	background-image: none;
	border: 1px solid #ccc;
	border-radius: 3px;
	-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
	-webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
	-o-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
	transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
}
.fs-form-control:hover {
	border: 1px solid #b9b9b9;
	border-top-color: #a0a0a0;
	-webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
	box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
}
.fs-form-control[disabled], .fs-form-control[readonly], fieldset[disabled] .fs-form-control {
	background-color: #eee;
	opacity: 1;
}
textarea.fs-form-control {
	min-height: 100px;
}
.fs-col-sm-2 {
	flex: 15%;
}
.fs-col-sm-10 {
	flex:80%;
}
.fs-col-sm-10, .fs-col-sm-2 {
	position: relative;
	min-height: 1px;
	padding-left: 15px;
	padding-right: 15px;
}
html {
	overflow-y: scroll;
}
</style>
</style>
<script>
	const initialData = <?php echo html_entity_decode($initial_data, ENT_QUOTES, 'UTF-8') ?>;
</script>

	<div id="fsm" class="form-horizontal"></div>
	<!-- <script src="https://unpkg.com/mithril/mithril.js"></script> -->
	<script src="/admin/view/javascript/fs_monitor/assets/mithril.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.35.4/ace.js" type="text/javascript" charset="utf-8"></script>
	<script type="module" src="/admin/view/javascript/fs_monitor/index.js"></script>
</div>
<?php echo $footer; ?>