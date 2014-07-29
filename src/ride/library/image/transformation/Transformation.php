<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

/**
 * Interface for generic image transformations
 */
interface Transformation {

    /**
     * Performs a transformation on the provided image
     * @param \ride\library\image\Image $image Image to transform
     * @param array $options Extra options for the transformation
     * @return \ride\library\image\Image new image with the transformation
     * applied
     */
    public function transform(Image $image, array $options);

}
