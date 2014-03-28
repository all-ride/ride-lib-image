<?php

namespace ride\library\image\io;

use ride\library\image\Image;
use ride\library\system\file\File;

/**
 * Factory for image objects, reads and writes images from and to file
 */
interface ImageFactory {

    /**
     * Gets the supported extensions
     * @return array Array with the extension as key and value
     */
    public function getExtensions();

    /**
     * Reads an image from file
     * @param \ride\library\system\file\File $file Path to read the image from
     * @return Image
     */
    public function read(File $file);

    /**
     * Writes an image to file
     * @param \ride\library\system\file\File $file Path to write the image to
     * @param Image $image Image to write
     * @return null
     */
    public function write(File $file, Image $image);

}