<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Laravel Debugbar</title>
    {{$renderer->renderHead()}}
    <style>
      div.phpdebugbar { top: 0 !important; height: 100% !important; }
      div.phpdebugbar-drag-capture { display: none !important; }
      div.phpdebugbar-resize-handle { display: none !important; }
      div.phpdebugbar-body { height: 100% !important; }
    </style>
  </head>
  <body>
    {{$renderer->render()}}
  </body>
</html>