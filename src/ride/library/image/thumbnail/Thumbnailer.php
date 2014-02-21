<?php

namespace ride\library\image\thumbnail;

use ride\library\image\Dimension;
use ride\library\image\Image;

/**
 * Thumbnailer interface
 */
interface Thumbnailer {

    /**
     * Get a thumbnail from an image
     * @param Image image image to get a thumbnail from
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image image instance of the thumbnail
     */
    public function getThumbnail(Image $image, Dimension $dimension);

}