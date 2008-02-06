<?php
/**
 * Utility methods to calculate distances
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

die('not implemented yet');

/**
 * Utitity methods to calculate distances
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

class Math_GeographicPoint_DistanceCalculator
{
    /**
     * Calculates the Great Circle Distance from the geographic point to another Long/Lat coordinate
     *
     * Formula is from http://williams.best.vwh.net/avform.htm#GCF
     *
     * @param float $lon1 Longitude
     * @param float $lat1 Latitude
     *
     * @return float distance in meters
     */
    public function distanceFrom($lon1, $lat1)
    {
        $lon2 = deg2rad($this->getLong());
        $lat2 = deg2rad($this->getLat());

        $theta = $lon2 - $lon1;
        $dist = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));

        return ($dist * 6366710);
    }

    /**
     * Calculates the distance between two points using Pythagoras's theorm
     * on TM coordinates.
     *
     * @param object $pt GeographicPoint
     *
     * @return float distance
     */
    public function distanceFromUTM($pt)
    {
        $E1 = $pt->getE();
        $N1 = $pt->getN();
        $E2 = $this->getE();
        $N2 = $this->getN();

        $dist = sqrt(pow(($E1 - $E2), 2) + pow(($N1 - $N2), 2));

        return $dist;
    }
}
?>