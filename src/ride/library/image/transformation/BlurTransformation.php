<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

/**
 * Transformation to blur an image
 */
class BlurTransformation implements Transformation {

    /**
     * Performs a blur transformation on the provided image
     * @param \ride\library\image\Image $image Image to transform
     * @param array $options Extra options for the transformation (radius)
     * @return \ride\library\image\Image new image with the transformation
     * applied
     */
    public function transform(Image $image, array $options) {
        $radius = isset($options['radius']) ? $options['radius'] : 10;

        return $image->blur($radius);
    }

}
