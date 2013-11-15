<?php

namespace pallo\library\image\io;

use pallo\library\image\Image;
use pallo\library\system\file\File;

/**
 * Factory for image objects, reads and writes images from and to file
 */
interface ImageFactory {

    /**
     * Reads an image from file
     * @param pallo\library\system\file\File $file Path to read the image from
     * @return Image
     */
    public function read(File $file);

    /**
     * Writes an image to file
     * @param pallo\library\system\file\File $file Path to write the image to
     * @param Image $image Image to write
     * @return null
     */
    public function write(File $file, Image $image);

}