<?php

$year = date('Y');
$month = date('m');

echo json_encode(array(
    array(
        'id' => 111,
        'title' => "Testowe wydarzenie",
        'start' => "$year-$month-10",
        'url' => "http://yahoo.com/"
    ),
    array(
        'id' => 222,
        'title' => "Inne wydarzenie do testów",
        'start' => "$year-$month-20",
        'end' => "$year-$month-22",
        'url' => "http://yahoo.com/"
    )
));

