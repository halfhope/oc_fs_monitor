<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $heading_title ?></title>
  <style type="text/css" media="screen">#editor{position:absolute;top:0;right:0;bottom:0;left: 0;}</style>
</head>
<body>
<div id="editor"><?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?></div>
<script src="https://cdn.jsdelivr.net/g/ace@1.2.4(min/ace.js+min/mode-php.js+min/mode-rhtml.js+min/mode-twig.js+min/snippets/javascript.js+min/snippets/css.js+min/snippets/json.js+min/mode-xml.js)" type="text/javascript" charset="utf-8"></script>
<script>
  var editor = ace.edit("editor");
  editor.setTheme("ace/theme/monokai");
  editor.getSession().setMode("ace/mode/<?php echo $mode; ?>");
</script>
</body>
</html>