<?php
/**
 * Test class
 *
 * Requires Sebastian Bergmann's PHPUnit
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
set_include_path(dirname(__FILE__) . '/../src/' . PATH_SEPARATOR . get_include_path());

require_once 'PHPUnit/Framework.php';
require_once 'Math/GeographicPoint/Lambert.php';

/**
 * Test class
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
class LambertTest extends PHPUnit_Framework_TestCase
{
    private $delta = 0.00001;

    public function testConstruction()
    {
        require_once 'Math/GeographicPoint/Lambert/Config.php';
        $config = new Math_GeographicPoint_Lambert_Config;

        $config->second_parallel = 38.6666;
        $config->first_parallel = 33.33333;
        $config->longitude_of_origin = -122;
        $config->latitude_of_origin = 35.5;
        $config->false_easting = 2000000;
        $config->false_northing = 0;

        $gpoint = new Math_GeographicPoint_Lambert(212968.846202, 2012532.43263, $config);

        $this->assertTrue(is_object($gpoint));
        $this->assertEquals($gpoint->getEasting(), 212968.846202, '', $this->delta);
        $this->assertEquals($gpoint->getNorthing(), 2012532.43263, '', $this->delta);
    }

    public function testConvertToLatitude()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;

        require_once 'Math/GeographicPoint/Lambert/Config.php';
        $config = new Math_GeographicPoint_Lambert_Config;

        $config->second_parallel = 38.6666;
        $config->first_parallel = 33.33333;
        $config->longitude_of_origin = -122;
        $config->latitude_of_origin = 35.5;
        $config->false_easting = 2000000;
        $config->false_northing = 0;

        $gpoint = new Math_GeographicPoint_Lambert(2012532.43263, 212968.846202, $config);

        $latlon = $gpoint->toLatitudeLongitude();

        $this->assertEquals($latlon->getLongitude(), $longitude, '', $this->delta);
        $this->assertEquals($latlon->getLatitude(), $latitude, '', $this->delta);
    }

    public function testConvertToLatitudeAndBackAgainShouldReturnTheSameValues()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;

        $gpoint = new Math_GeographicPoint_LatitudeLongitude($latitude, $longitude);

        require_once 'Math/GeographicPoint/Lambert/Config.php';
        $config = new Math_GeographicPoint_Lambert_Config;

        $config->second_parallel = 38.6666;
        $config->first_parallel = 33.33333;
        $config->longitude_of_origin = -122;
        $config->latitude_of_origin = 35.5;
        $config->false_easting = 2000000;
        $config->false_northing = 0;

        $lambert = $gpoint->toLambert($config);

        $latlon = $lambert->toLatitudeLongitude();

        $this->assertTrue(is_object($latlon));

        $this->assertEquals($latlon->getLatitude(), $latitude, '', $this->delta);
        $this->assertEquals($latlon->getLongitude(), $longitude, '', $this->delta);
    }

}
?>