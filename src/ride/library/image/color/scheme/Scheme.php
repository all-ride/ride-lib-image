<?php

namespace ride\library\image\color\scheme;

use ride\library\image\color\Color;

/**
 * Interface for a color scheme
 */
interface Scheme {

    /**
     * Gets the colors of this scheme
     * @param \ride\library\image\color\Color $color Base color
     * @param integer $number Number of colors to generate
     * @return array Array with Color objects
     */
    public function getColors(Color $baseColor, $number);

}