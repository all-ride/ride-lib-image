<?php

namespace ride\library\image\color;

use ride\library\image\exception\ImageException;

/**
 * RGB color implementation
 */
abstract class AbstractColor implements Color {

    /**
     * Value for aplha channel
     * @var integer
     */
    protected $alpha;

    /**
     * Sets the alpha value
     * @param integer $alpha Value between 0 and 1
     * @return null
     */
    public function setAlpha($alpha) {
        if (!is_numeric($alpha) || $alpha < 0 || $alpha > 1) {
            throw new ImageException('Could not set the alpha value: provided value is not between 0 and 1');
        }

        $this->alpha = $alpha;
    }

    /**
     * Gets the alpha channel value
     * @return integer Value between 0 and 1
     */
    public function getAlpha() {
        return $this->alpha;
    }

}
