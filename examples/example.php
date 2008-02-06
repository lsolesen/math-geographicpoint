<?php
/**
 * Example on how to use the class
 *
 * PHP version 5
 *
 * @category  Math
 * @package   Math_GeographicPoint
 * @author    Brenor Brophy <brenor.brophy@gmail.com>
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright 2007 Brenor Brophy
 * @license   GPL http://www.opensource.org/licenses/gpl-license.php
 * @version   @package-version@
 * @link      http://public.intraface.dk
 */

require 'Math/GeographicPoint/LatitudeLongitude.php';

$my_home = new Math_GeographicPoint_LatitudeLongitude(37.42104, -121.85831);
echo 'I live at: ' . $my_home->toString() .  '<br>';

$utm = $my_home->toTM();
echo 'Which in a UTM projection is: ' .  $utm->toString() .  '<br>';

// Calculate the Longitude Latitude of the point
$ll = $utm->toLatitudeLongitude();
echo 'Which converts back to: '.  $ll->toString() . '<br>';

// Now lets try the same conversion, only this time we will user a "Local"
// Transverse Mercator projection. -122 degrees longitude is close to the
// area of interest so lets use that as our Longitude of Origin
$longOrigin = -122;
$utm = $my_home->toTM($longOrigin);
echo 'In a Local TM projection centered at longitude ' . $longOrigin . ' it is: ' .  $utm->toString() . '<br>';

// Now check the reverse conversion
$ll = $utm->toLatitudeLongitude($longOrigin);
echo 'Converting back gives us: ' . $ll->toString() . '<br>';


// Lets setup a Lambert Conformal Conic projection for Northern California
require_once 'Math/GeographicPoint/Lambert/Config.php';
$config = new Math_GeographicPoint_Lambert_Config;

$config->second_parallel = 38.6666; // 38 40'
$config->first_parallel = 33.33333; // 33 20'
$config->longitude_of_origin = -122;
$config->latitude_of_origin = 35.5;
$config->false_easting = 2000000;
$config->false_northing = 0;

$lambert = $my_home->toLambert($config);

echo 'In a Lambert Projection: ' . $lambert->toString() .  '<br>';

// And convert back to Longitude / Latitude
$ll = $lambert->toLatitudeLongitude();
echo 'And is still: ' . $ll->toString() . '<br>';

?>