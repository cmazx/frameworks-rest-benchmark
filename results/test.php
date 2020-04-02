<?php


$s = json_decode(file_get_contents('symfony'), true);
$y = json_decode(file_get_contents('yii2'), true);
$l = json_decode(file_get_contents('laravel'), true);

$data = [];
foreach ($s['results'] as $key => $result) {
    $stimes = array_sum($result['times']) / count($result['times']);
    $data[$result['name']]['symfony'] = $stimes;
    $ytimes = array_sum($y['results'][$key]['times']) / count($y['results'][$key]['times']);
    $data[$result['name']]['yii2'] = $ytimes;
    $ltimes = array_sum($l['results'][$key]['times']) / count($l['results'][$key]['times']);
    $data[$result['name']]['laravel'] = $ltimes;
}

foreach ($data as $name => $values) {
    $d = (round(($data[$name]['symfony'] * 100) / $data[$name]['yii2']) - 100);
    $l = (round(($data[$name]['laravel'] * 100) / $data[$name]['yii2']) - 100) ;
    $data[$name]['diff'] = ($d > 0 ? ('+' . $d) : $d) . ' %';
    $data[$name]['diffLaravel'] =($l > 0 ? ('+' . $l) : $l)  . ' %';
}

$total = [];
foreach ($data as $name => $values) {
    $total['yii2'][] = $values['yii2'];
    $total['symfony'][] = $values['symfony'];
    $total['laravel'][] = $values['laravel'];
}

$total['yii2'] = array_sum($total['yii2']);
$total['symfony'] = array_sum($total['symfony']);
$total['laravel'] = array_sum($total['laravel']);

foreach ($data as $name => $values) {
    echo str_pad($name, 25, ' ', STR_PAD_RIGHT) . 'Symfony:' . $values['diff'] . "\n";
    echo str_pad('', 25, ' ', STR_PAD_RIGHT) . 'Laravel:' . $values['diffLaravel'] . "\n\n";
}
echo str_pad('Avg symfony:', 25, ' ',
        STR_PAD_RIGHT) . round((100 * $total['symfony']) / $total['yii2']) . "%\n";
echo str_pad('Avg laravel:', 25, ' ',
        STR_PAD_RIGHT) . round((100 * $total['laravel']) / $total['yii2']) . "%\n";


