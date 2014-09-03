<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo $appName; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $assetsDir; ?>/bootstrap.min.css">
    
    <link rel="stylesheet" href="<?php echo $assetsDir; ?>/codemirror-4.5/lib/codemirror.css">
    <link rel="stylesheet" href="<?php echo $assetsDir; ?>/codemirror-4.5/addon/hint/show-hint.css">
    
    <link rel="stylesheet" href="<?php echo $assetsDir; ?>/style.css">
    <style type="text/css">
      .CodeMirror {
        border: 1px solid #eee;
        height: auto;
      }
      .CodeMirror-scroll {
        overflow-y: hidden;
        overflow-x: auto;
      }
    </style>
  </head>
  <body>
    <div class="app-container">
        <div class="app-content">
            <h1 class="app-name"><?php echo $appName; ?></h1>
            <p class="app-description"><?php echo $appDescription; ?></p>
            <?php echo $content; ?>
        </div>
    </div>

    <script src="<?php echo $assetsDir; ?>/jquery.min.js"></script>
    <!--<script src="<?php echo $assetsDir; ?>/jquery.elastic.js"></script>-->

    

    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/lib/codemirror.js"></script>
    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/addon/hint/show-hint.js"></script>
    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/addon/hint/xml-hint.js"></script>
    

    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/addon/edit/matchbrackets.js"></script>

<!--<script src="<?php echo $assetsDir; ?>/codemirror-4.5/mode/htmlmixed/htmlmixed.js"></script>-->

    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/mode/xml/xml.js"></script>    
    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/mode/clike/clike.js"></script>
    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/mode/php/php.js"></script>
    <script src="<?php echo $assetsDir; ?>/codemirror-4.5/mode/yaml/yaml.js"></script>
    
    
    <script src="<?php echo $assetsDir; ?>/script.js"></script>
    <script src="<?php echo $assetsDir; ?>/code.js"></script>

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-54436944-1', 'auto');
      ga('send', 'pageview');

    </script>

  </body>
</html>