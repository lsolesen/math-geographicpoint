<?php
/**
 * An abstract class to represents a geographic point
 *
 * A Geographic Point is a point on the Earth's surface. Its location is defined
 * by a longitude and a latitude coordinate. The coordinates define a point on the
 * surface of a sphere.
 *
 * Often we might be interested in representing a geographic point on a flat surface,
 * like for instance a computerscreen or a piece of paper. For instance to plot an
 * array of latitude/longitude points on an image of a map, you need to transform
 * the latitude/longitude coordinates to X/Y coordinates. This is called a map projection.
 *
 * There are many different types of projection. This library provides methods for
 * working with two of the most useful:
 *
 * - Universal Transverse Mercator (UTM) and
 * - Lambert Conformal Conic.
 *
 * The library also supports a variant of UTM called Local Transverse Mercator. It is
 * very useful when you just need to plot a few points on an arbitrary image that covers
 * a modest amount of the Earth (10x10 degrees) and you don't have to deal with UTM zones.
 *
 * At a high level converting a lat/long coordinate in degrees thru a projection will
 * return an Easting/Northing coordinate in meters. That is meters measured on the 'flat'
 * ground. Broadly speaking <strong>Transverse Mercator (UTM)</strong> is useful for modest
 * sized areas of about 10x10degrees or less.
 *
 * <strong>Lambert</strong> is useful for large areas in the mid latitudes (like the whole
 * USA or Europe for example). Neither projection works well for areas near the poles.
 *
 * This library is a complete rewrite of Brenor Brophy's
 * {@link http://www.phpclasses.org/browse/file/10671.html gPoint}.
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
 * Abstract class to represent a geographic point
 *
 * The class must be extended to create a geographic point in the correct representation.
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
abstract class Math_GeographicPoint
{
    /**
     * Reference ellipsoids derived from Peter H. Dana's website-
     * http://www.colorado.edu/geography/gcraft/notes/datum/datum_f.html
     * email: pdana@pdana.com, web page: www.pdana.com
     *
     * Source:
     * Defense Mapping Agency. 1987b. DMA Technical Report: Supplement to Department
     * of Defense World Geodetic System 1984 Technical Report. Part I and II.
     * Washington, DC: Defense Mapping Agency
     *
     * @var array
     */
    private $ellipsoid = array(
        //Ellipsoid name, Equatorial Radius, square of eccentricity
        'Airy'                  => array(6377563, 0.00667054),
        'Australian National'   => array(6378160, 0.006694542),
        'Bessel 1841'           => array(6377397, 0.006674372),
        'Bessel 1841 Nambia'    => array(6377484, 0.006674372),
        'Clarke 1866'           => array(6378206, 0.006768658),
        'Clarke 1880'           => array(6378249, 0.006803511),
        'Everest'               => array(6377276, 0.006637847),
        'Fischer 1960 Mercury'  => array(6378166, 0.006693422),
        'Fischer 1968'          => array(6378150, 0.006693422),
        'GRS 1967'              => array(6378160, 0.006694605),
        'GRS 1980'              => array(6378137, 0.00669438),
        'Helmert 1906'          => array(6378200, 0.006693422),
        'Hough'                 => array(6378270, 0.00672267),
        'International'         => array(6378388, 0.00672267),
        'Krassovsky'            => array(6378245, 0.006693422),
        'Modified Airy'         => array(6377340, 0.00667054),
        'Modified Everest'      => array(6377304, 0.006637847),
        'Modified Fischer 1960' => array(6378155, 0.006693422),
        'South American 1969'   => array(6378160, 0.006694542),
        'WGS 60'                => array(6378165, 0.006693422),
        'WGS 66'                => array(6378145, 0.006694542),
        'WGS 72'                => array(6378135, 0.006694318),
        'WGS 84'                => array(6378137, 0.00669438));

    /**
     * Equatorial Radius
     *
     * @var float
     */
    public $a;

    /**
     * Square of eccentricity
     *
     * @var float
     */
    public $e2;

    /**
     * Selected datum
     *
     * @var string
     */
    private $datum;

    /**
     * Creates a new Geographic Point
     *
     * The $datum must be one of the following:
     *
     * "Airy", "Australian National", "Bessel 1841", "Bessel 1841 Nambia",
     * "Clarke 1866", "Clarke 1880", "Everest", "Fischer 1960 Mercury",
     * "Fischer 1968", "GRS 1967", "GRS 1980", "Helmert 1906", "Hough",
     * "International", "Krassovsky", "Modified Airy", "Modified Everest",
     * "Modified Fischer 1960", "South American 1969", "WGS 60", "WGS 66",
     * "WGS 72", "WGS 84"
     *
     * @param string $datum Refers to the key of $this->$ellipsoid
     *
     * @return void
     */
    public function __construct($datum = '')
    {
        if (empty($datum)) {
            $datum = 'WGS 84'; // Default datum is WGS 84
        }
        $this->a = $this->ellipsoid[$datum][0];     // Set datum Equatorial Radius
        $this->e2 = $this->ellipsoid[$datum][1];    // Set datum Square of eccentricity
        $this->datum = $datum;                      // Save the datum

    }

}
?>