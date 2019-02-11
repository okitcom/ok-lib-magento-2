<?php
/**
 * Created by PhpStorm.
 * Date: 9/18/17
 */

namespace OK\Builder;


use OK\Model\Location;

/**
 * Class LocationBuilder
 * @package OK\Builder
 *
 * @api
 * @method LocationBuilder setLat(double $latitude)
 * @method LocationBuilder setLon(double $longitude)
 * @method Location build()
 */
class LocationBuilder extends GenericBuilder
{
    protected function getObject() {
        return new Location();
    }
}