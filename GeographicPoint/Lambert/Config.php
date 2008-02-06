<?php
/**
 * Configuration for the Lambert Conic Conformal
 *
 * Lambert is useful for large areas in the mid latitudes (Like the whole USA or Europe
 * for example). However, it nees a setup object to calculate values correctly.
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

/**
 * An interface that the configuration object must implement
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
interface GeographicPoint_Lambert_Config
{
    /**
     * Should check whether the configuration is valid
     *
     * @var float
     */
    public function isValid();
}

/**
 * A configuration object for Lambert Conic Conformal
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
class Math_GeographicPoint_Lambert_Config implements GeographicPoint_Lambert_Config
{
    /**
     * Offset in meters added to the final coordinates calculated
     *
     * @var float
     */
    public $false_easting;

    /**
     * Offset in meters added to the final coordinates calculated
     *
     * @var float
     */
    public $false_northing;

    /**
     * define where first parallel the "cone" intersects the earth
     *
     * @var float
     */
    public $first_std_parallel;

    /**
     * define where second parallel the "cone" intersects the earth
     *
     * @var float
     */
    public $second_std_parallel;

    /**
     * "center" longitude of the area being projected
     *
     * @var float
     */
    public $longitude_of_origin;

    /**
     * "center" latitude of the area being projected
     *
     * @var float
     */
    public $latitude_of_origin;

    /**
     * Checks whether the configuration is valid
     *
     * @var float
     */
    function isValid()
    {
        return true;
    }
    /*
    public function getFalseEasting()
    {
        return $this->false_easting;
    }

    public function getFalseNorthing()
    {
        return $this->false_northing;
    }

    public function getLatitudeOfOrigin()
    {
        return $this->latitude_of_origin;
    }

    public function getLongitudeOfOrigin()
    {
        return $this->longitude_of_origin;
    }


    public function getFirstStandardParallel()
    {
        return $this->first_std_parallel;
    }

    public function getSecondStandardParallel()
    {
        return $this->second_std_parallel;
    }
    */
}
?>