<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

/**
 * Transformation to grayscale an image
 */
class GrayscaleTransformation implements Transformation {

    /**
     * Performs a grayscale transformation on the provided image
     * @param \ride\library\image\Image $image Image to transform
     * @param array $options Extra options for the transformation (mode)
     * @return \ride\library\image\Image new image with the transformation
     * applied
     */
    public function transform(Image $image, array $options) {
        return $image->convertToGrayscale();
    }

}
