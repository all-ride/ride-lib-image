<?php

namespace ride\library\image\transformation;

use ride\library\image\dimension\GenericDimension;
use ride\library\image\Image;

/**
 * Transformation to resize an image
 */
class FlipTransformation implements Transformation {

    /**
     * Performs a crop transformation on the provided image
     * @param \ride\library\image\Image $image Image to transform
     * @param array $options Extra options for the transformation (mode)
     * @return \ride\library\image\Image new image with the transformation
     * applied
     */
    public function transform(Image $image, array $options) {
        $mode = isset($options['mode']) ? $options['mode'] : null;

        return $image->flip($mode);
    }

}
