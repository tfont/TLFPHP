<?php

/**
 * @param $lat1
 * @param $lng1
 * @param $lat2
 * @param $lng2
 * @param bool $miles
 *
 * @return float
 */
function calculate_distance($lat1, $lng1, $lat2, $lng2, $miles = FALSE)
{
	$pi80  = M_PI / 180;

	$lat1 *= $pi80;
	$lng1 *= $pi80;
	$lat2 *= $pi80;
	$lng2 *= $pi80;

	$r    = 6372.797; // mean radius of Earth in km
	$dlat = $lat2 - $lat1;
	$dlng = $lng2 - $lng1;

	$a  = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
	$c  = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$km = $r * $c;

	return (float) ($miles ? ($km * 0.621371192) : $km);
}
