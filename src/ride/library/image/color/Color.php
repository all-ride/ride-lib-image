<?php

namespace ride\library\image\color;

/**
 * Interface for a color
 */
interface Color {

    /**
     * Gets a string representation of this color
     * @return string
     */
    public function __toString();

    /**
     * Allocates the color to the provided image resource
     * @param resource $resource Image resource
     * @return integer Color identifier
     * @throws ride\library\image\exception\ImageException when the color
     * could not be allocated
     */
    public function allocate($resource);

}