<?php

namespace ride\library\image;

/**
 * URL generator for images.
 */
interface ImageUrlGenerator {

    /**
     * Generates a URL for the provided image.
     * @param string $image Path of the image
     * @param string $thumbnailer Name of the thumbnailer to use
     * @param int $width Width for the thumbnailer
     * @param int $height Height for the thumbnailer
     * @return null
     */
    public function generateUrl($image, $thumbnailer = null, $width = 0, $height = 0);

}