<?php

namespace ride\library\image\point;

/**
 * Interface for the coordinates of a point
 */
interface Point {

    /**
     * Gets the value on the X-axis
     * @return integer
     */
    public function getX();

    /**
     * Gets the value on the Y-axis
     * @return integer
     */
    public function getY();

}
