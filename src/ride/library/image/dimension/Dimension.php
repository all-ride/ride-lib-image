<?php

namespace ride\library\image\dimension;

/**
 * Interface for a dimension
 */
interface Dimension {

    /**
     * Gets the width of the dimension
     * @return integer
     */
    public function getWidth();

    /**
     * Gets the height of the dimension
     * @return integer
     */
    public function getHeight();

}
