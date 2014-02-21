<?php

namespace ride\library\image\thumbnail;

use ride\library\image\Dimension;
use ride\library\image\Image;

/**
 * Abstract image thumbnailer
 */
abstract class AbstractThumbnailer implements Thumbnailer {

    /**
     * Get a thumbnail from the given image
     * @param Image image source image for the thumbnail
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image Image instance of the thumbnail
     */
    public function getThumbnail(Image $image, Dimension $dimension) {
        if ($image->getWidth() <= $dimension->getWidth() && $image->getHeight() <= $dimension->getHeight()) {
            return $image;
        }

        return $this->createThumbnail($image, $dimension);
    }

    /**
     * Create a thumbnail from the given image
     * @param Image image source image for the thumbnail
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image Image instance of the thumbnail
     */
    abstract protected function createThumbnail(Image $image, Dimension $dimension);

}