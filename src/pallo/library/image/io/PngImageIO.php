<?php

namespace pallo\library\image\io;

use pallo\library\image\exception\ImageException;
use pallo\library\system\file\File;

/**
 * PNG image input/output implementation
 */
class PngImageIO extends AbstractImageIO {

    /**
     * Gets the extensions of the image format
     * @return array
     */
    public function getExtensions() {
        return array('png');
    }

    /**
     * Checks if transparency is supported by the image format
     * @return boolean
     */
    public function supportsTransparancy() {
        return true;
    }

    /**
     * Checks if alpha channel is supported by the image format
     * @return boolean
     */
    public function supportsAlphaChannel() {
        return true;
    }

    /**
     * Read a png image from file
     * @param File file of the image
     * @return resource internal PHP image resource of the file
     */
    public function read(File $file) {
        $this->checkIfReadIsPossible($file);

        // read image resource
        $image = imageCreateFromPng($file->getAbsolutePath());
        if ($image === false) {
            throw new ImageException($file->getPath() . ' is not a valid PNG image');
        }

        // handle transparency
        imageAlphaBlending($image, false);
        imageSaveAlpha($image, true);

        return $image;
    }

    /**
     * Write a png image to file
     * @param File file of the image
     * @param resource internal PHP image resource
     */
    public function write(File $file, $resource) {
        $this->checkIfWriteIsPossible($file, $resource);

        imagePng($resource, $file->getAbsolutePath());
    }

}