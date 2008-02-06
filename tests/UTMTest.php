<?php
/**
 * Test class for UTM
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
require_once 'Math/GeographicPoint/UTM.php';

/**
 * Test class for UTM
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
class UTMTest extends PHPUnit_Framework_TestCase
{
    private $delta = 0.000001;

    public function testConstruction()
    {
        $easting = 8583143;
        $northing = 3742104;
        $zone = '32V';

        $gpoint = new Math_GeographicPoint_UTM($easting, $northing, $zone);

        $this->assertTrue(is_object($gpoint));
    }

    public function testConvertUTMToLatitude()
    {
        $longitude = -121.85831;
        $latitude = 37.42104;

        $utm = new Math_GeographicPoint_UTM(601021.995134, 4142193.02983, '10S');

        $latlon = $utm->toLatitudeLongitude();

        $this->assertTrue(is_object($latlon));

        $this->assertEquals($latlon->getLatitude(), $latitude, '', $this->delta);
        $this->assertEquals($latlon->getLongitude(), $longitude, '', $this->delta);
    }

    public function testConvertFromUTMToLatitudeAndBackAgainShouldReturnTheSameValues()
    {
        $easting = 601021.995134;
        $northing = 4142193.02983;
        $zone = '10S';

        $utm = new Math_GeographicPoint_UTM($easting, $northing, $zone);

        $latlon = $utm->toLatitudeLongitude();

        $new_utm = $latlon->toTM();

        $this->assertEquals($new_utm->getEasting(), $easting, '', $this->delta);
        $this->assertEquals($new_utm->getNorthing(), $northing, '', $this->delta);
        $this->assertEquals($new_utm->getZone(), $zone);

    }

    public function testConvertBetweenUTMAndLatitudeWithLongitudeOrigin()
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

}
?>