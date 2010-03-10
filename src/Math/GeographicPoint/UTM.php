<?php
/**
 * Represents a geographic point in Universal Transverse Mercator (UTM)
 *
 * This class also supports a variant of UTM called Local Transverse Mercator.
 *
 * At a high level converting a Long/Lat coordinate in degrees thru a projection will
 * return an Easting/Northing coordinate in meters. That is meters measured on the 'flat'
 * ground. Broadly speaking Transverse Mercator (UTM) is useful for modest sized
 * areas of about 10x10degrees or less. Does not work well for areas near the poles.
 *
 * Converts to a geographic point represented in Latitude/Longitude.
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

require_once 'Math/GeographicPoint.php';
require_once 'Math/GeographicPoint/LatitudeLongitude.php';

/**
 * Represents a geographic point on earth in Universal Transverse Mercator (UTM)
 *
 * @category  Math
 * @package   Math_GeographicPoint
 * @author    Brenor Brophy <brenor.brophy@gmail.com>
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright 2007 Brenor Brophy
 * @license   GPL http://www.opensource.org/licenses/gpl-license.php
 * @version   @package-version@
 * @link      http://public.intraface.dk
 * @example   example.php
 */

class Math_GeographicPoint_UTM extends Math_GeographicPoint
{
    /**
     * UTM Coordinates of the geographic point
     *
     * @var float
     */
    private $utmNorthing, $utmEasting;

    /**
     * UTM Zone of the geographic point
     *
     * @var string
     */
    private $utmZone, $longitude_of_origin, $latitude_of_origin;

    /**
     * Constructs a Universal Transverse Mercator object
     *
     * @param float  $easting  Easting in decimal meters
     * @param float  $northing Northing in decimal meters
     * @param mixed  $zone     Pass in a string, and it has to be the zone is in
     *                         ASCII, for example '10S'.
     *                         Reference zone on this map http://www.dmap.co.uk/utmworld.htm
     *                         Pass in an array with longitude_of_origin and
     *                         latitude_of_origin as keys and the values as doubles.
     * @param string $datum    The datum to use
     *
     * @return void
     */
    public function __construct($easting, $northing, $zone, $datum = '')
    {
        $this->utmEasting  = $easting;
        $this->utmNorthing = $northing;

        if (is_string($zone)) {
            $this->utmZone             = $zone;
            $this->latitude_of_origin  = $this->latitudeToZoneLetter(substr($zone, -1, 1));
            $this->longitude_of_origin = $this->calculateLongitudeOfOrigin(intval($zone));
        } elseif (is_array($zone)) {
            $this->latitude_of_origin  = $zone['latitude_of_origin'];
            $this->longitude_of_origin = $zone['longitude_of_origin'];
            $this->utmZone             = $this->zone();
        }

        parent::__construct($datum);
    }

    private function zone()
    {
        return Math_GeographicPoint_LatitudeLongitude::zoneNumber($this->latitude_of_origin, $this->longitude_of_origin)
           . Math_GeographicPoint_LatitudeLongitude::UTMLetterDesignator($this->latitude_of_origin);
    }

    private function zoneLetter()
    {
        sscanf($this->utmZone, "%d%s", $ZoneNumber, $ZoneLetter);
        return $ZoneLetter;
    }

    private function zoneNumber()
    {
        sscanf($this->utmZone, "%d%s", $ZoneNumber, $ZoneLetter);
        return $ZoneNumber;
    }


    /**
     * +3 puts origin in middle of zone
     */
    static private function calculateLongitudeOfOrigin($ZoneNumber)
    {
         //return (integer)(6 * ($ZoneNumber - 1) - 180);
         return (integer)(($ZoneNumber - 1) * 6 - 180 + 3);
    }

    static private function latitudeToZoneLetter($zoneletter)
    {
        switch ($zoneletter) {
            case 'X':
                // (84 >= $latitude) && ($latitude >= 72)
                $latitude = (84 + 72) / 2;
                break;
            case 'W':
                // (72 > $latitude) && ($latitude >= 64)
                $latitude = (72 + 64) / 2;
                break;
            case 'V':
                // (64 > $latitude) && ($latitude >= 56)
                $latitude = (64 + 56) / 2;
                break;
            case 'U':
                // (56 > $latitude) && ($latitude >= 48)
                $latitude = (56 + 48) / 2;
                break;
            case 'T':
                // (48 > $latitude) && ($latitude >= 40)
                $latitude = (48 + 40) / 2;
                break;
            case 'S':
                // (40 > $latitude) && ($latitude >= 32)
                $latitude = (40 + 32) / 2;
                break;
            case 'R':
                // (32 > $latitude) && ($latitude >= 24)
                $latitude = (32 + 24) / 2;
                break;
            case 'Q':
                // (24 > $latitude) && ($latitude >= 16)
                $latitude = (24 + 16) / 2;
                break;
            case 'P':
                // (16 > $latitude) && ($latitude >= 8)
                $latitude = (16 + 8) / 2;
                break;
            case 'N':
                // ( 8 > $latitude) && ($latitude >= 0)
                $latitude = (8 + 0) / 2;
                break;
            case 'M':
                // ( 0 > $latitude) && ($latitude >= -8)
                $latitude = (0 - 8) / 2;
                break;
            case 'L':
                // (-8 > $latitude) && ($latitude >= -16)
                $latitude = (-8 - 16) / 2;
                break;
            case 'K':
                // (-16 > $latitude) && ($latitude >= -24)
                $latitude = (-16 - 24) / 2;
                break;
            case 'J':
                // (-24 > $latitude) && ($latitude >= -32)
                $latitude = (-24 -32) / 2;
                break;
            case 'H':
                // (-32 > $latitude) && ($latitude >= -40)
                $latitude = (-32 -40 ) / 2;
                break;
            case 'G':
                // (-40 > $latitude) && ($latitude >= -48)
                $latitude = (-40 -48) / 2;
                break;
            case 'F':
                // (-48 > $latitude) && ($latitude >= -56)
                $latitude = (-48 + -56) / 2;
                break;
            case 'E':
                // (-56 > $latitude) && ($latitude >= -64)
                $latitude = (-56 -64) / 2;
                break;
            case 'D':
                // (-64 > $latitude) && ($latitude >= -72)
                $latitude = (-64 -72) / 2;
                break;
            case 'C':
                // (-72 > $latitude) && ($latitude >= -80)
                $latitude = (-72 -80) / 2;
                break;
            default:
                trigger_error($zoneletter . ' is not a valid utm zone letter', E_USER_ERROR);
                break;
        }

        return $latitude;
    }

    /**
     * Return UTM Northing coordinate
     *
     * @return float
     */
    public function getNorthing()
    {
        return $this->utmNorthing;
    }

    /**
     * Return UTM Easting coordinate
     *
     * @return float
     */
    public function getEasting()
    {
        return $this->utmEasting;
    }

    /**
     * Return UTM Zone
     *
     * @return float
     */
    public function getZone()
    {
        return $this->utmZone;
    }

    /**
     * Return Latitude of origin
     *
     * @return float
     */
    public function getLatitudeOfOrigin()
    {
        return $this->latitude_of_origin;
    }

    /**
     * Return Longitude of Origin
     *
     * @return float
     */
    public function getLongitudeOfOrigin()
    {
        return $this->longitude_of_origin;
    }

    /**
     * Returns the UTM as text
     *
     * @return void
     */
    public function toString()
    {
        return 'Northing: ' . $this->utmNorthing . ', Easting: ' . $this->utmEasting . ', Zone: ' . $this->utmZone;
    }

    /**
     * Convert UTM to Longitude/Latitude
     *
     * Equations from USGS Bulletin 1532
     * East Longitudes are positive, West longitudes are negative.
     * North latitudes are positive, South latitudes are negative
     * Lat and Long are in decimal degrees.
     *
     * If a value is passed for $LongOrigin then the function assumes that
     * a Local (to the Longitude of Origin passed in) Transverse Mercator
     * coordinates is to be converted - not a UTM coordinate. This is the
     * complementary function to the previous one. The function cannot
     * tell if a set of Northing/Easting coordinates are in the North
     * or South hemesphere - they just give distance from the equator not
     * direction - so only northern hemesphere lat/long coordinates are returned.
     * If you live south of the equator there is a note later in the code
     * explaining how to have it just return southern hemesphere lat/longs.
     *
     * @param float $LongOrigin Originating longitude
     *
     * @return object
     */
    public function toLatitudeLongitude($LongOrigin = null)
    {
        $ZoneNumber = false;
        $ZoneLetter = false;
        $k0 = 0.9996;
        $e1 = (1 - sqrt(1 - $this->e2)) / (1 + sqrt(1 - $this->e2));
        $falseEasting = 0.0;
        $y = $this->utmNorthing;

        if ($LongOrigin === null) {
            if ($this->isSouthernHemisphere($this->zoneLetter())) {
                $y -= 10000000.0; //remove 10,000,000 meter offset used for southern hemisphere
            }
            $LongOrigin = $this->getLongitudeOfOrigin();
            $falseEasting = 500000.0;
        }

        // $y -= 10000000.0;   // Uncomment line to make LOCAL coordinates return southern hemesphere Lat/Long
        $x = $this->utmEasting - $falseEasting; //remove 500,000 meter offset for longitude - why

        $eccPrimeSquared = ($this->e2) / (1 - $this->e2);

        $M = $y / $k0;
        $mu = $M / ($this->a * (1 - $this->e2 / 4 - 3 * $this->e2 * $this->e2 / 64 - 5 * $this->e2 * $this->e2 * $this->e2 / 256));

        $phi1Rad = $mu + (3 * $e1/2 - 27 * $e1 * $e1 * $e1 / 32) * sin(2 * $mu)
                   + (21 * $e1 * $e1/16-55 * $e1 * $e1 * $e1 * $e1/32) * sin(4 * $mu)
                   +(151 * $e1 * $e1 * $e1 / 96) * sin(6 * $mu);
        $phi1 = rad2deg($phi1Rad);

        $N1 = $this->a / sqrt(1 - $this->e2 * sin($phi1Rad) * sin($phi1Rad));
        $T1 = tan($phi1Rad) * tan($phi1Rad);
        $C1 = $eccPrimeSquared * cos($phi1Rad) * cos($phi1Rad);
        $R1 = $this->a * (1 - $this->e2) / pow(1 - $this->e2 * sin($phi1Rad) * sin($phi1Rad), 1.5);
        $D = $x/($N1 * $k0);

        $tlat = $phi1Rad - ($N1 * tan($phi1Rad) / $R1) * ($D * $D / 2 - (5 + 3 * $T1 + 10 * $C1-4 * $C1 * $C1-9 * $eccPrimeSquared) * $D * $D * $D * $D / 24
                +(61 + 90 * $T1 + 298 * $C1 + 45 * $T1 * $T1 - 252 * $eccPrimeSquared - 3 * $C1 * $C1) * $D * $D * $D * $D * $D * $D / 720);

        // found the latitude
        $latitude = rad2deg($tlat);

        $tlong = ($D - (1 + 2* $T1 + $C1) * $D * $D * $D / 6 + (5 - 2 * $C1 + 28 * $T1 - 3 * $C1 * $C1 + 8 * $eccPrimeSquared + 24 * $T1 * $T1)
                 * $D * $D * $D * $D * $D / 120) / cos($phi1Rad);

        // found the longitude
        $longitude = $LongOrigin + rad2deg($tlong);

        return new Math_GeographicPoint_LatitudeLongitude($latitude, $longitude);
    }

    /**
     * Returns whether zoneletter refers to the southern hemisphere
     *
     * @param string $zone_letter
     *
     * return boolean
     */

    static private function isSouthernHemisphere($zone_letter)
    {
        return ($zone_letter < 'N');
    }

}
?>