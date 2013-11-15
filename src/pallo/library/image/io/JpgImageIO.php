<?php

namespace pallo\library\image\io;

use pallo\library\image\exception\ImageException;
use pallo\library\system\file\File;

/**
 * JPG image input/output implementation
 */
class JpgImageIO extends AbstractImageIO {

    /**
     * Gets the extensions of the image format
     * @return array
     */
    public function getExtensions() {
        return array('jpg', 'jpeg');
    }

    /**
     * Read a jpg image from file
     * @param File file of the image
     * @return resource internal PHP image resource of the file
     */
    public function read(File $file) {
        $this->checkIfReadIsPossible($file);

        $image = imageCreateFromJpeg($file->getAbsolutePath());
        if ($image === false) {
            throw new ImageException($file->getPath() . ' is not a valid JPG image');
        }

        return $image;
    }

    /**
     * Write a jpg image to file
     * @param File file of the image
     * @param resource internal PHP image resource
     */
    public function write(File $file, $resource) {
        $this->checkIfWriteIsPossible($file, $resource);

        imageJpeg($resource, $file->getAbsolutePath());
    }

}