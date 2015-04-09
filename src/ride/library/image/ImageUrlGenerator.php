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
     * @param string|array $transformations Name of a transformation or an array
     * with the name of the transformation as key and the options as value
     * @param array $options Options for the transformation (when name provided)
     * @return null
     */
    public function generateUrl($image, $transformations = null, array $options = null);

}
