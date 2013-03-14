<?php
/*
 * Music album list widget.
 *
 * Demonstrates usage of sorting functionality, inserting custom data between
 * rows, setting footer and header items
 */

spl_autoload_register(function($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $path = '../src/' . $path . '.php';

    include $path;
});

function sort_data(&$data, $key, $dir) {
    return usort($data, function($a, $b) use($key, $dir) {
        if ($key == 'id') {
            $x = $a[$key] - $b[$key];
        } else {
            $x = strcasecmp($a[$key], $b[$key]);
        }
        
        return $dir == 'asc' ? $x : $x * -1;
    });
}

if (isset($_GET['s'])) {
    list($column, $direction) = explode(',', $_GET['s']);
} else {
    $column = 'id';
    $direction = 'asc';
}

$columns = array(
    'id' => 'ID',
    'name' => 'Name',
    'artist' => 'Artist',
);

$data = array(
    ['id' => 1,     'artist' => 'Scooter',     'name' => 'Under the Radar Over the Top'],
    ['id' => 2,     'artist' => 'Scooter',     'name' => '24 Carat Gold'],
    ['id' => 3,     'artist' => 'CSS',         'name' => 'Donkey'],
    ['id' => 4,     'artist' => 'Metric',      'name' => 'Synthetica'],
    ['id' => 5,     'artist' => 'CSS',         'name' => 'Cansei de ser Sexy'],
    ['id' => 6,     'artist' => 'Scooter',     'name' => 'Wicked'],
    ['id' => 7,     'artist' => 'Metric',      'name' => 'Fantasies'],
    ['id' => 8,     'artist' => 'Metric',      'name' => 'Live It Out'],
    ['id' => 9,     'artist' => 'CSS',         'name' => 'La Liberacion'],
    ['id' => 10,    'artist' => 'Shpongle',    'name' => 'Tales of the Inexpressible'],
);

$artists = array_unique(array_map(function($row) { return $row['artist']; }, $data));

sort_data($data, $column, $direction);

$table = new Samu\Widget\Table\Table();

$table->getHeader()
    ->setUrlPrototype('?s=:column,:direction')
    ->setSortColumn($column)->setSortDirection($direction);

$table->getFooter()
    ->setContent('name', sprintf('%d albums', count($data)))
    ->setContent('artist', sprintf('%d artists', count($artists)));

$table->setSortable(true);
$table->setColumns($columns)->setData($data);

$table->setWidth('id', 30);

$table->before(function($row, $table) use ($column) {
    static $char = '';
    
    $data = $row->getData();

    switch ($column) {
        case 'artist':
            if (($c = $data['artist'][0]) != $char) {
                $char = $c;
                print '<tr class="header"><td colspan="20"> ' . $c . '</td></tr>';
            }
            break;

    }
});

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Table Widget</title>
    <meta charset="UTF-8"/>

    <style type="text/css">
        @import url('css/music.css');
    </style>
</head>
<body>

    <?= $table ?>

</body>
</html>
