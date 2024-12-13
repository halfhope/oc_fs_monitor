<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $heading_title ?></title>
	<style type="text/css" media="screen">#editor{position:absolute;top:0;right:0;bottom:0;left: 0;}</style>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ace.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<div id="editor"><?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?></div>
<script>
	var editor = ace.edit("editor");
	editor.setTheme("ace/theme/chrome");
	editor.getSession().setMode("ace/mode/<?php echo $mode; ?>");
</script>
</body>
</html>