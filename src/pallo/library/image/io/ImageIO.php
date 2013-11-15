<?php

namespace pallo\library\image\io;

use pallo\library\system\file\File;

/**
 * Image input/output interface
 */
interface ImageIO {

    /**
     * Gets the extensions of the image format
     * @return array
     */
    public function getExtensions();

    /**
     * Checks if transparency is supported by the image format
     * @return boolean
     */
    public function supportsTransparancy();

    /**
     * Checks if alpha channel is supported by the image format
     * @return boolean
     */
    public function supportsAlphaChannel();

    /**
     * Reads an image from file
     * @param pallo\library\system\file\File $file File of the image
     * @return resource Internal PHP image resource
     */
    public function read(File $file);

    /**
     * Writes an image to file
     * @param pallo\library\system\file\File $file File of the image
     * @param resource $resource Internal PHP image resource
     * @return null
     */
    public function write(File $file, $resource);

}