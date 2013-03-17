<?php
$tanks = array(
    'ms1' => array('a' => 1),
    'ms2' => array('a' => 2),
    'ms3' => array('b' => 3),
    'ms4' => array('b' => 2),
    'ms5' => array('a' => 3),
    'ms6' => array('a' => 3),
);
$max['a'] = array();
$max['b'] = array();
$m = 0;
foreach ($tanks as $value) {
    print key ($tanks);
}
print_r ($max);
?>