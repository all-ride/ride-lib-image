<?php

namespace ride\library\image;

use ride\library\image\exception\ImageException;
use ride\library\system\file\File;

/**
 * Abstract implementation for an image
 */
abstract class AbstractImage implements Image {

    /**
     * Image resource for the underlaying library
     * @var resource
     */
    protected $resource;

    /**
     * Dimension of the image
     * @var \ride\library\image\dimension\Dimension
     */
    protected $dimension;

    /**
     * Flag to see if this image has alpha channel transparency
     * @var boolean
     */
    protected $hasAlpha;

    /**
     * Instance of the transparent color
     * @var \ride\library\image\color\Color
     */
    protected $transparentColor;

    /**
     * Detects the file format
     * @param \ride\library\system\file\File $file
     * @param string $format
     * @return string
     * @throws \ride\library\image\exception\ImageException when the format
     * could not be detected
     */
    protected function detectFormat(File $file, $format = null) {
        if (!$format) {
            $format = $file->getExtension();
            if (!$format) {
                throw new ImageException('Could not detect image format: file has no extension and no format provided');
            }
        }

        if ($format === 'jpg') {
            $format = 'jpeg';
        }

        return $format;
    }

    /**
     * Gets the image resource
     * @return resource
     */
    public function getResource() {
        if (!$this->resource) {
            $this->createResource();
        }

        return $this->resource;
    }

    /**
     * Creates the resource for this image
     * @param integer width width of the new image resource
     * @param integer height height of the new image resource
     * @return resource
     */
    abstract protected function createResource();

}
