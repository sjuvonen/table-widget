<?php
/*
 * Simple weather widget demonstrating how to manipulate contents of cells
 *
 * Weather icons provided by the Oxygen theme of KDE SC project.
 */

spl_autoload_register(function($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $path = '../src/' . $path . '.php';

    include $path;
});

$columns = array(
    'weather' => '',
    'temp' => 'Temp',
);

$data = array(
    'monday' => array(
        'day' => 'Monday',
        'weather' => 'storm',
        'temp' => 12,
        'info' => '',
    ),

    'tuesday' => array(
        'day' => 'Tuesday',
        'weather' => 'few-clouds',
        'temp' => 20,
        'info' => '',
    ),

    'wednesday' => array(
        'day' => 'Wednesday',
        'weather' => 'clear',
        'temp' => 24,
        'info' => '',
    ),
);

$table = new Samu\Widget\Table\Table();
$table->setCaption('Weather');
$table->setIndexes(array('weather', 'temp'));
$table->setData($data);

$table->transform('weather', function($w) {
    $image = "images/weather/weather-{$w}.png";
    return "<img src=\"{$image}\" alt=\"{$w}\"/>";
});

$table->transform('temp', function($temp, $i, $row) {
    $cell = '';
    $cell .= '<h2>' . htmlspecialchars($row['day']) . '</h2>';
    $cell .= '<span>' . $temp . ' ℃';

    return $cell;
});

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Table Widget</title>
    <meta charset="UTF-8"/>

    <style type="text/css">
        @import url('css/weather.css');
    </style>
</head>
<body>

    <?= $table ?>

</body>
</html>
