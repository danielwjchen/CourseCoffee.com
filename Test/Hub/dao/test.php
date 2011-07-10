<?php

$table_name = 'asdfasdf_dfsdf_vdvdfvgsd';
$table_element = explode('_', $table_name);

foreach ($table_element as $key => $value) {
	$table_element[$key] = ucfirst($value);

}

print_r(implode('', $table_element));

