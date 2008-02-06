<?php
/**
 * Represents a geographic point in Latitude and Longitude
 *
 * A Geographic Point is a point on the Earth's surface. Its location is defined
 * by a Longitude and a Latitude coordinate. These coordinates define a point on the
 * surface of a sphere.
 *
 * This class can convert the geographic point to
 *
 * - UTM Northing/Easting
 * - Lambert Conic Conformal Northing/Easting
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
require_once 'Math/GeographicPoint/UTM.php';
require_once 'Math/GeographicPoint/Lambert.php';

/**
 * Represents a geographic point in latitude an longitude
 *
 * Can convert the point to UTM and Lambert Conic Conformal.
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

class Math_GeographicPoint_LatitudeLongitude extends Math_GeographicPoint
{

    /**
     * Latitude & Longitude of the point
     *
     * @var float
     */
    private $latitude, $longitude;

    /**
     * Sets the latitude and longitude
     *
     * North latitudes are positive, South
     * latitudes are negative. $long and $lat should be in decimal degrees.
     *
     * Longitudes
     * east of the prime meridian (i.e. east of Greenwich) are positive, longitudes
     * west of the prime meridian are negative.
     *
     * @param float  $lat   Latitude
     * @param float  $long  Longitude
     * @param string $datum The datum to use
     *
     * @return void
     */
    public function __construct($latitude, $longitude, $datum = '')
    {
        $this->latitude  = $latitude;
        $this->longitude = $this->longitude = $longitude;

        parent::__construct($datum);
    }

    /**
     * Get Latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Get Longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

   /**
     * Returns the latitude and longitude as text
     *
     * @return void
     */
    public function toString()
    {
        return 'Latitude: ' . $this->latitude . ', Longitude: ' . $this->longitude;
    }

    /**
     * Convert Longitude/Latitude to UTM
     *
     * Equations from USGS Bulletin 1532
     * East Longitudes are positive, West longitudes are negative.
     * North latitudes are positive, South latitudes are negative
     * Lat and Long are in decimal degrees
     *
     * UTM coordinates are useful when dealing with paper maps. Basically the
     * map covers a single UTM zone which is 6 degrees of longitude.
     * So you really don't care about an object crossing two zones. You just get a
     * second map of the other zone.
     *
     * However, if you happen to live in a place that
     * straddles two zones (For example the Santa Barbara area in CA straddles zone 10
     * and zone 11) Then it can become a real pain having to have two maps all the time.
     * So relatively small parts of the world (like say California) create their own
     * version of UTM coordinates that are adjusted to cover the whole area of interest
     * on a single map. These are called state grids. The projection system is the
     * usually same as UTM (i.e. Transverse Mercator), but the central meridian
     * aka Longitude of Origin is selected to suit the longitude of the area being
     * mapped (like being moved to the central meridian of the area) and the grid
     * may cover more than the 6 degrees of longitude found on a UTM map.
     *
     * Areas
     * that are wide rather than long (think Montana as an example) may still
     * have to have a couple of maps to cover the whole state because TM projection
     * looses accuracy as you move further away from the Longitude of Origin, 15 degrees
     * is usually the limit.
     *
     * Now, in the case where we want to generate electronic maps that may be
     * placed pretty much anywhere on the globe we really don't want to deal with the
     * issue of UTM zones in our coordinate system. We would really just like a
     * grid that is fully contigious over the area of the map we are drawing. Similiar
     * to the state grid, but local to the area we are interested in.
     *
     * I call this
     * Local Transverse Mercator and I have modified the function below to also
     * make this conversion. If you pass a Longitude value to the function as $LongOrigin
     * then that is the Longitude of Origin that will be used for the projection.
     *
     * Easting coordinates will be returned (in meters) relative to that line of
     * longitude - So an Easting coordinate for a point located East of the longitude
     * of origin will be a positive value in meters, an Easting coordinate for a point
     * West of the longitude of Origin will have a negative value in meters.
     *
     * Northings
     * will always be returned in meters from the equator same as the UTM system. The
     * UTMZone value will be valid for Long/Lat given - though it is not meaningful
     * in the context of Local TM. If a NULL value is passed for $LongOrigin
     * then the standard UTM coordinates are calculated.
     *
     * UTM zone is used to calculate the $LongOrigin value. However, if a user passes
     * a value for $LongOrigin when the call the method then there is no need to
     * calculate it. The user value is used rather than the value calculated from the
     * UTM zone.
     *
     * A Transverse Mercator projection can be calculated for any Longitude of Origin.
     * The UTM system simply defines 60 specific Longitudes (0,6,12,18 etc - i.e.
     * every 6 degrees) and assigns each a zone number. If the user passes in a value
     * for $LongOrigin then they want to use some non UTM longitude to calculate the
     * Transverse Projection, this is what the documentation calls a Local Transverse
     * Mercator projection.
     *
     * @todo Find out whether UTM should have longitude of origin or zone number,
     *       and how you should set it.
     *
     * @param float $LongOrigin Originating longitude - used for localized maps
     *
     * @return object
     */
    public function toTM($LongOrigin = null)
    {
        $k0 = 0.9996;
        $falseEasting = 0.0;

        $utmZone = '';

        //Make sure the longitude is between -180.00 .. 179.9
        //$LongTemp = ($this->longitude+180) - (integer)(($this->longitude + 180) / 360) * 360 - 180;
        $LongTemp = $this->sanitizeLongitude($this->longitude);
        $LatRad = deg2rad($this->latitude);
        $LongRad = deg2rad($LongTemp);

        if ($LongOrigin === null) {
            $ZoneNumber = $this->zoneNumber($this->latitude, $LongTemp);
            $LongOrigin = $this->longitudeOfOrigin($ZoneNumber);

            //compute the UTM Zone from the latitude and longitude
            $utmZone = sprintf("%d%s", $ZoneNumber, $this->UTMLetterDesignator($this->latitude));
            // We also need to set the false Easting value adjust the UTM easting coordinate
            $falseEasting = 500000.0;
        }

        $LongOriginRad = deg2rad($LongOrigin);

        $eccPrimeSquared = ($this->e2) / ( 1 - $this->e2);

        $N = $this->a / sqrt(1 - $this->e2 * sin($LatRad) * sin($LatRad));
        $T = tan($LatRad) * tan($LatRad);
        $C = $eccPrimeSquared * cos($LatRad) * cos($LatRad);
        $A = cos($LatRad) * ($LongRad - $LongOriginRad);

        $M = $this->a*((1 - $this->e2 / 4 - 3 * $this->e2 * $this->e2 / 64  - 5 * $this->e2 * $this->e2 * $this->e2 / 256) * $LatRad
             - (3*$this->e2/8 + 3 * $this->e2 * $this->e2 / 32  + 45 * $this->e2 * $this->e2 * $this->e2 / 1024) * sin(2 * $LatRad)
             + (15 * $this->e2 * $this->e2 / 256 + 45 * $this->e2 * $this->e2 * $this->e2 / 1024) * sin(4 * $LatRad)
             - (35 * $this->e2 * $this->e2 * $this->e2 / 3072) * sin(6 * $LatRad));

        $utmEasting = ($k0 * $N * ($A+(1 - $T + $C) * $A * $A * $A / 6
                        + (5 - 18 * $T + $T * $T + 72 * $C-58 * $eccPrimeSquared) * $A * $A * $A * $A * $A / 120)
                        + $falseEasting);

        $utmNorthing = ($k0 * ($M + $N * tan($LatRad) * ($A * $A / 2 + (5 - $T + 9 * $C + 4 * $C * $C) * $A * $A * $A * $A / 24
                     + (61 - 58 * $T + $T * $T + 600 * $C - 330 * $eccPrimeSquared) * $A * $A * $A * $A * $A * $A / 720)));
        if($this->latitude < 0) {
            $utmNorthing += 10000000.0; //10000000 meter offset for southern hemisphere
        }

        $data = array(
            'longitude_of_origin' => $LongOrigin,
            'latitude_of_origin' => $this->latitude
        );

        return new Math_GeographicPoint_UTM($utmEasting, $utmNorthing, $data);
    }

    /**
     * Make sure the longitude is between -180.00 .. 179.9
     *
     * If longitude is greater or smaller a new longitude to fit this span will be
     * calculated.
     *
     * @param float $longitude Longitude to sanitize
     *
     * @return float Longitude between -180.00 .. 179.9
     */
    static private function sanitizeLongitude($longitude)
    {
        return ($longitude + 180) - (integer)(($longitude + 180) / 360) * 360 - 180;
    }

    /**
     * Calculate UTM Zone Number
     *
     * @param float $longitude Longitude to calculate from
     *
     * @return integer
     */
    static private function calculateUTMZoneNumber($longitude)
    {
        return ((integer)(($longitude + 180) / 6) + 1);
    }

    static public function zoneNumber($latitude, $longitude)
    {
        $ZoneNumber = self::calculateUTMZoneNumber($longitude);
        if (self::zoneIs32($latitude, $longitude)) {
            $ZoneNumber = 32;
        }
        if (self::zoneIsSvalbard($latitude)) {
            $ZoneNumber = self::getSvalbardZone($longitude);
        }
        return $ZoneNumber;
    }

    static public function longitudeOfOrigin($ZoneNumber)
    {
        return ($ZoneNumber - 1) * 6 - 180 + 3;  //+3 puts origin in middle of zone
    }

    static public function utmZone()
    {

    }

    /**
     * Returns whether point is in UTM Zone 32
     *
     * @param float $longitude Longitude to calculate from
     * @param float $latitude  Latitude to calculate from
     *
     * @return integer
     */
    static private function zoneIs32($latitude, $longitude)
    {
        return ($latitude >= 56.0 && $latitude < 64.0 && $longitude >= 3.0 && $longitude < 12.0);
    }

    /**
     * Returns whether point is in Svalbard
     *
     * @param float $latitude  Latitude to calculate from
     *
     * @return integer
     */
    static private function zoneIsSvalbard($latitude)
    {
        return ($latitude >= 72.0 && $latitude < 84.0);
    }

    /**
     * Returns the zone in Svalbard
     *
     * @param float $latitude  Latitude to calculate from
     *
     * @return integer
     */
    static private function getSvalbardZone($longitude)
    {
        if ($longitude >= 0.0  && $longitude <  9.0) {
            $ZoneNumber = 31;
        } elseif ($longitude >= 9.0  && $longitude < 21.0) {
            $ZoneNumber = 33;
        } elseif ($longitude >= 21.0 && $longitude < 33.0) {
            $ZoneNumber = 35;
        } elseif ($longitude >= 33.0 && $longitude < 42.0) {
            $ZoneNumber = 37;
        }
        return $ZoneNumber;
    }


    /**
     * This routine determines the correct UTM letter designator
     *
     * This routine determines the correct UTM letter designator for the given latitude
     * returns 'Z' if latitude is outside the UTM limits of 84N to 80S
     *
     * @todo improve error handling of this method
     *
     * @param float $latitude Latitude
     *
     * @return string
     */
    static public function UTMLetterDesignator($latitude)
    {
        if((84 >= $latitude) && ($latitude >= 72)) $LetterDesignator = 'X';
        elseif((72 > $latitude) && ($latitude >= 64)) $LetterDesignator = 'W';
        elseif((64 > $latitude) && ($latitude >= 56)) $LetterDesignator = 'V';
        elseif((56 > $latitude) && ($latitude >= 48)) $LetterDesignator = 'U';
        elseif((48 > $latitude) && ($latitude >= 40)) $LetterDesignator = 'T';
        elseif((40 > $latitude) && ($latitude >= 32)) $LetterDesignator = 'S';
        elseif((32 > $latitude) && ($latitude >= 24)) $LetterDesignator = 'R';
        elseif((24 > $latitude) && ($latitude >= 16)) $LetterDesignator = 'Q';
        elseif((16 > $latitude) && ($latitude >= 8)) $LetterDesignator = 'P';
        elseif(( 8 > $latitude) && ($latitude >= 0)) $LetterDesignator = 'N';
        elseif(( 0 > $latitude) && ($latitude >= -8)) $LetterDesignator = 'M';
        elseif((-8 > $latitude) && ($latitude >= -16)) $LetterDesignator = 'L';
        elseif((-16 > $latitude) && ($latitude >= -24)) $LetterDesignator = 'K';
        elseif((-24 > $latitude) && ($latitude >= -32)) $LetterDesignator = 'J';
        elseif((-32 > $latitude) && ($latitude >= -40)) $LetterDesignator = 'H';
        elseif((-40 > $latitude) && ($latitude >= -48)) $LetterDesignator = 'G';
        elseif((-48 > $latitude) && ($latitude >= -56)) $LetterDesignator = 'F';
        elseif((-56 > $latitude) && ($latitude >= -64)) $LetterDesignator = 'E';
        elseif((-64 > $latitude) && ($latitude >= -72)) $LetterDesignator = 'D';
        elseif((-72 > $latitude) && ($latitude >= -80)) $LetterDesignator = 'C';
        else $LetterDesignator = 'Z'; //This is here as an error flag to show that the Latitude is outside the UTM limits

        return $LetterDesignator;
    }

    /**
     * Convert Longitude/Latitude to Lambert Conic Easting/Northing
     *
     * Converts a Latitude/Longitude coordinate to an Northing/
     * Easting coordinate on a Lambert Conic Projection.
     *
     * The Northing/Easting parameters calculated are in meters (because the datum
     * used is in meters) and are relative to the falseNorthing/falseEasting coordinate.
     * Which in turn is relative to the Lat/Long of origin.
     *
     * The formula were obtained from URL: http://www.ihsenergy.com/epsg/guid7_2.html.
     *
     * @return object
     */
    public function toLambert(GeographicPoint_Lambert_Config $config)
    {
        if (!$config->isValid()) {
            trigger_error('configuration is invalid', E_USER_ERROR);
        }

        $e = sqrt($this->e2);

        $phi    = deg2rad($this->latitude);                      // Latitude to convert
        $lamda  = deg2rad($this->longitude);                     // Lonitude to convert

        // gotten from the config element
        $phi1   = deg2rad($config->first_parallel);         // Latitude of 1st std parallel
        $phi2   = deg2rad($config->second_parallel);        // Latitude of 2nd std parallel
        $phio   = deg2rad($config->latitude_of_origin);              // Latitude of Origin
        $lamdao = deg2rad($config->longitude_of_origin);             // Longitude of Origin

        $m1 = cos($phi1) / sqrt(( 1 - $this->e2 * sin($phi1) * sin($phi1)));
        $m2 = cos($phi2) / sqrt(( 1 - $this->e2 * sin($phi2) * sin($phi2)));
        $t1 = tan((pi() / 4) - ($phi1 / 2)) / pow(((1 - $e * sin($phi1)) / (1 + $e * sin($phi1))), $e/2);
        $t2 = tan((pi() / 4) - ($phi2 / 2)) / pow(((1 - $e * sin($phi2)) / (1 + $e * sin($phi2))), $e/2);
        $to = tan((pi() / 4) - ($phio / 2)) / pow(((1 - $e * sin($phio)) / (1 + $e * sin($phio))), $e/2);
        $t  = tan((pi() / 4) - ($phi / 2)) / pow(((1 - $e * sin($phi)) / (1 + $e * sin($phi))), $e/2);
        $n  = (log($m1) - log($m2)) / (log($t1) - log($t2));
        $F  = $m1/($n * pow($t1, $n));
        $rf = $this->a * $F * pow($to, $n);
        $r  = $this->a * $F * pow($t, $n);
        $theta = $n * ($lamda - $lamdao);

        $this->lccEasting  = $config->false_easting + $r * sin($theta);
        $this->lccNorthing = $config->false_northing + $rf - $r * cos($theta);

        return new Math_GeographicPoint_Lambert($this->lccEasting, $this->lccNorthing, $config);
    }

}
?>