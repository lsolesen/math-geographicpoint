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
require_once 'PHPUnit/Framework.php';
require_once 'Math/GeographicPoint/LatitudeLongitude.php';

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
class LatitudeLongitudeTest extends PHPUnit_Framework_TestCase
{
    private $delta = 0.00001;

    public function testConstruction()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;

        $gpoint = new Math_GeographicPoint_LatitudeLongitude($latitude, $longitude);

        $this->assertTrue(is_object($gpoint));
    }

    public function testConvertLatitudeToUTM()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;

        $gpoint = new Math_GeographicPoint_LatitudeLongitude($latitude, $longitude);
        $utm = $gpoint->toTM();

        $this->assertTrue(is_object($utm));

        $this->assertEquals($utm->getEasting(), 601021.995134, '', $this->delta);
        $this->assertEquals($utm->getNorthing(), 4142193.02983, '', $this->delta);
        $this->assertEquals($utm->getZone(), '10S');
    }

    public function testConvertToUTMWithLongitudeOrigin()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;
        $origin = -122;

        $gpoint = new Math_GeographicPoint_LatitudeLongitude($latitude, $longitude);

        $utm = $gpoint->toTM($origin);

        $this->assertTrue(is_object($utm));

        $this->assertEquals($utm->getNorthing(), 4141590.78955, '', $this->delta);
        $this->assertEquals($utm->getEasting(), 12537.1687295, '', $this->delta);
        $this->assertEquals($utm->getZone(), '10S');
    }


    public function testConvertToUTMAndBackAgainShouldReturnTheSameValues()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;

        $gpoint = new Math_GeographicPoint_LatitudeLongitude($latitude, $longitude);
        $utm = $gpoint->toTM();

        $this->assertTrue(is_object($utm));

        $this->assertEquals($utm->getEasting(), 601021.995134, '', $this->delta);
        $this->assertEquals($utm->getNorthing(), 4142193.02983, '', $this->delta);
        $this->assertEquals($utm->getZone(), '10S');

        $latlon = $utm->toLatitudeLongitude();
        $this->assertTrue(is_object($latlon));
        $this->assertEquals($latlon->getLatitude(), $latitude, '', $this->delta);
        $this->assertEquals($latlon->getLongitude(), $longitude, '', $this->delta);
    }

    public function testConvertToUTMWithOriginAndBackAgainShouldReturnTheSameValues()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;
        $origin = -122;

        $gpoint = new Math_GeographicPoint_LatitudeLongitude($latitude, $longitude);

        $utm = $gpoint->toTM($origin);

        $this->assertTrue(is_object($utm));

        $this->assertEquals($utm->getNorthing(), 4141590.78955, '', $this->delta);
        $this->assertEquals($utm->getEasting(), 12537.1687295, '', $this->delta);
        $this->assertEquals($utm->getZone(), '10S');

        $latlon = $utm->toLatitudeLongitude($origin);

        $this->assertTrue(is_object($latlon));

        $this->assertEquals($latlon->getLatitude(), $latitude, '', $this->delta);
        $this->assertEquals($latlon->getLongitude(), $longitude, '', $this->delta);
    }

    public function testConvertLatitudeToLambert()
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

        $this->assertTrue(is_object($lambert));

        $this->assertEquals($lambert->getEasting(), 2012532.43263, '', $this->delta);
        $this->assertEquals($lambert->getNorthing(), 212968.846202, '', $this->delta);
    }

    public function testConvertToLambertAndBackAgainShouldReturnTheSameValues()
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

        $ll = $lambert->toLatitudeLongitude();

        $this->assertTrue(is_object($ll));

        $this->assertEquals($ll->getLatitude(), $latitude, '', $this->delta);
        $this->assertEquals($ll->getLongitude(), $longitude, '', $this->delta);
    }


}
?>