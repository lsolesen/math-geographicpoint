<?php
/**
 * Represents a geographic point in Lambert Conic Conformal
 *
 * Lambert is useful for large areas in the mid latitudes (Like the whole USA or Europe
 * for example). Does not work well for areas near the poles.
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
 * Represents a geographic point in Lambert Conic Conformal
 *
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

class Math_GeographicPoint_Lambert extends Math_GeographicPoint
{

    /**
     * Coordinates
     *
     * @var float
     */
    private $lccEasting, $lccNorhting;

    /**
     * Config object
     *
     * @var object
     */
     private $config;

    /**
     * Must be deleted
     *
     * @var object
     */
    private $lat, $long;

    /**
     * Creates a Lambert projection geograpic point object
     *
     * @param float  $easting  Easting in decimal meters
     * @param float  $northing Northing in decimal meters
     * @param object $config   Configuration of the Lambert calculations
     * @param string $datum    The datum to use
     *
     * @return void
     */
    public function __construct($easting, $northing, GeographicPoint_Lambert_Config $config, $datum = '')
    {
        $this->lccEasting  = $easting;
        $this->lccNorthing = $northing;
        $this->config = $config;

        parent::__construct($datum);
    }

    /**
     * Returns lccNorthing
     *
     * @return float
     */
    public function getNorthing()
    {
        return $this->lccNorthing;
    }

    /**
     * Returns lccEasting
     *
     * @return float
     */
    public function getEasting()
    {
        return $this->lccEasting;
    }

    /**
     * Returns Lambert as a string
     *
     * @return string
     */
    public function toString()
    {
        return "Northing: ".$this->lccNorthing.", Easting: ".$this->lccEasting;
    }

    /**
     * Converts Easting/Northing on a Lambert Conic projection to Longitude/Latitude
     *
     * The Northing/Easting parameters are in meters (because the datum
     * used is in meters) and are relative to the falseNorthing/falseEasting
     * coordinate. Which in turn is relative to the Lat/Long of origin
     *
     * The formula is from http://www.ihsenergy.com/epsg/guid7_2.html.
     *
     * @return object
     */
    public function toLatitudeLongitude()
    {
        $e = sqrt($this->e2);

        $phi1   = deg2rad($this->config->first_parallel);         // Latitude of 1st std parallel
        $phi2   = deg2rad($this->config->second_parallel);        // Latitude of 2nd std parallel
        $phio   = deg2rad($this->config->latitude_of_origin);     // Latitude of  Origin
        $lamdao = deg2rad($this->config->longitude_of_origin);    // Longitude of  Origin
        $E      = $this->lccEasting;
        $N      = $this->lccNorthing;
        $Ef     = $this->config->false_easting;
        $Nf     = $this->config->false_northing;

        $m1 = cos($phi1) / sqrt((1 - $this->e2 * sin($phi1) * sin($phi1)));
        $m2 = cos($phi2) / sqrt((1 - $this->e2 * sin($phi2)*sin($phi2)));
        $t1 = tan((pi()/4)-($phi1/2)) / pow(((1 - $e * sin($phi1)) / (1 + $e * sin($phi1))), $e/2);
        $t2 = tan((pi()/4)-($phi2/2)) / pow(((1 - $e * sin($phi2)) / (1 + $e * sin($phi2))), $e/2);
        $to = tan((pi()/4)-($phio/2)) / pow(((1 - $e * sin($phio)) / (1 + $e * sin($phio))), $e/2);
        $n  = (log($m1) - log($m2)) / (log($t1) - log($t2));
        $F  = $m1/($n * pow($t1, $n));
        $rf = $this->a * $F * pow($to, $n);
        $r_ = sqrt(pow(($E - $Ef), 2) + pow(($rf - ($N - $Nf)), 2));
        $t_ = pow($r_ / ($this->a * $F), (1 / $n));
        $theta_ = atan(($E - $Ef)/($rf - ($N - $Nf)));

        $lamda  = $theta_ / $n + $lamdao;
        $phi0   = (pi() / 2) - 2 * atan($t_);
        $phi1   = (pi() / 2) - 2 * atan($t_ * pow(((1 - $e * sin($phi0)) / (1 + $e * sin($phi0))), $e / 2));
        $phi2   = (pi() / 2) - 2 * atan($t_ * pow(((1 - $e * sin($phi1)) / (1 + $e * sin($phi1))), $e / 2));
        $phi    = (pi() / 2) - 2 * atan($t_ * pow(((1 - $e * sin($phi2)) / (1 + $e * sin($phi2))), $e / 2));

        $this->lat  = rad2deg($phi);
        $this->long = rad2deg($lamda);

        return new Math_GeographicPoint_LatitudeLongitude($this->lat, $this->long);
    }
}
?>