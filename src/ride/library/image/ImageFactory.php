<?php

namespace ride\library\image;

/**
 * Factory for image objects
 */
interface ImageFactory {

    /**
     * Creates an image object
     * @return \ride\library\image\Image
     */
    public function createImage();

}
