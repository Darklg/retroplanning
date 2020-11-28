<?php
define('PROJ_DIR', dirname(__FILE__));
include PROJ_DIR . '/inc/controller.php';
?>
<!DOCTYPE html>
<!--[if lt IE 8 ]><html dir="ltr" lang="fr-FR" class="is_ie7 lt_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 8 ]><html dir="ltr" lang="fr-FR" class="is_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 9 ]><html dir="ltr" lang="fr-FR" class="is_ie9 lt_ie10"><![endif]-->
<!--[if gt IE 9]><html dir="ltr" lang="fr-FR" class="is_ie10"><![endif]-->
<!--[if !IE]><!--><html dir="ltr" lang="fr-FR"><!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <title>Rétro Planning</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" type="text/css" href="assets/style.css" />
</head>
<body>
<h1>Rétro Planning</h1>
<?php
include PROJ_DIR . '/tpl/infos.php';
include PROJ_DIR . '/tpl/calendar.php';
?>
</body>
</html>


