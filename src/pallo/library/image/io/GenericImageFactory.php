<?php

namespace pallo\library\image\io;

use pallo\library\image\exception\ImageException;
use pallo\library\image\Image;
use pallo\library\system\file\File;

/**
 * Factory for image objects, reads and writes images from and to file
 */
class GenericImageFactory implements ImageFactory {

    /**
     * Supported extensions
     * @var array
     */
    protected $extensions = array();

    /**
     * Adds an I/O implementation for a image format
     * @param pallo\library\image\io\ImageIO $imageIO
     * @return null
     */
    public function addImageIO(ImageIO $imageIO) {
        $extensions = $imageIO->getExtensions();
        foreach ($extensions as $extension) {
            $this->extensions[$extension] = $imageIO;
        }
    }

    /**
     * Reads an image from file
     * @param pallo\library\system\file\File $file Path to read the image from
     * @return Image
     */
    public function read(File $file) {
        $extension = $file->getExtension();
        if (!isset($this->extensions[$extension])) {
            throw new ImageException('Could not read ' . $file . ': ' . $extension . ' is not supported');
        }

        $resource = $this->extensions[$extension]->read($file);

        return new Image(null, null, $this->extensions[$extension]->supportsAlphaChannel(), $resource);
    }

    /**
     * Writes an image to file
     * @param pallo\library\system\file\File $file Path to write the image to
     * @param Image $image Image to write
     * @return null
     */
    public function write(File $file, Image $image) {
        $extension = $file->getExtension();
        if (!isset($this->extensions[$extension])) {
            throw new ImageException('Could not write ' . $file . ': ' . $extension . ' is not supported');
        }

        $file->getParent()->create();

        $this->extensions[$extension]->write($file, $image->getResource());
    }

}