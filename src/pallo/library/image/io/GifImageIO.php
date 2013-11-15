<?php

namespace pallo\library\image\io;

use pallo\library\image\exception\ImageException;
use pallo\library\system\file\File;

/**
 * GIF image input/output implementation
 */
class GifImageIO extends AbstractImageIO {

    /**
     * Gets the extensions of the image format
     * @return array
     */
    public function getExtensions() {
        return array('gif');
    }

    /**
     * Checks if transparency is supported by the image format
     * @return boolean
     */
    public function supportsTransparancy() {
        return true;
    }

    /**
     * Read a gif image from file
     * @param File file of the image
     * @return resource internal PHP image resource of the file
     */
    public function read(File $file) {
        $this->checkIfReadIsPossible($file);

        // create image resource
        $image = imageCreateFromGif($file->getAbsolutePath());
        if ($image === false) {
            throw new ImageException('Could not read ' . $file . ': not a valid GIF image');
        }

        // handle transparency
        $transparentColor = imageColorTransparent($image);
        if ($transparentColor == -1) {
            $transparentColorIndex = imageColorAllocate($image, 0, 0, 0);
            imageColorTransparent($image, $transparentColorIndex);
        }

        return $image;
    }

    /**
     * Write a gif image to file
     * @param File file of the image
     * @param resource internal PHP image resource
     */
    public function write(File $file, $resource) {
        $this->checkIfWriteIsPossible($file, $resource, 'gif');

        imageGif($resource, $file->getAbsolutePath());
    }

}