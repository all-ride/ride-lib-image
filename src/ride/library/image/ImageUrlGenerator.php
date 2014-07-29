<?php

namespace ride\library\image;

/**
 * URL generator for images.
 */
interface ImageUrlGenerator {

    /**
     * Gets the cache directory of this generator
     * @return \ride\library\system\file\File
     */
    public function getCacheDirectory();

    /**
     * Generates a URL for the provided image.
     * @param string|\ride\library\system\file\File $image Path or File instance to the image
     * @param string $transformation Name of the transformation to use
     * @param array $options Options for the transformation
     * @return null
     */
    public function generateUrl($image, $transformation = null, array $options = null);

}
