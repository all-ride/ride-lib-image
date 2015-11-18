<?php

namespace ride\library\image;

use ride\library\image\color\Color;
use ride\library\image\dimension\Dimension;
use ride\library\image\dimension\GenericDimension;
use ride\library\image\exception\ImageException;
use ride\library\image\point\Point;
use ride\library\system\file\File;

use \Imagick;

/**
 * Magick implementation of an image
 */
class ImagickImage extends AbstractImage {

    /**
     * Construct an image object
     * @return null
     */
    public function __construct() {
        if (!class_exists('Imagick')) {
            throw new ImageException('Could not create a MagickImage instance. Your PHP installation does not support Image Magick, please install the imagick extension.');
        }
    }

    /**
     * Clone this image
     * @return null
     */
    public function __clone() {
        if ($this->resource) {
            $this->resource = clone $this->resource;
        }
        if ($this->dimension) {
            $this->dimension = clone $this->dimension;
        }
    }

    /**
     * Reads the image from a file
     * @param \ride\library\system\file\File $file
     * @param string $format
     * @return null
     * @throws \ride\library\image\exception\ImageException when the image could
     * not be read
     */
    public function read(File $file, $format = null) {
        if (!$file->isReadable()) {
            throw new ImageException('Could not read ' . $file . ': file not readable');
        }

        $format = $this->detectFormat($file, $format);
        switch ($format) {
            case 'gif':
            case 'png':
            case 'jpeg':
            case 'svg':
                break;
            default:
                throw new ImageException('Could not read ' . $file . ': format ' . $format . ' is not supported');
        }

        $this->resource = new Imagick();
        $this->resource->readImage($file->getAbsolutePath());

        $this->dimension = null;
        $this->hasAlpha = null;
    }

    /**
     * Writes this image to file
     * @param \ride\library\system\file\File $file
     * @param string $format
     * @return null
     * @throws \ride\library\image\exception\ImageException when the image could
     * not be written
     */
    public function write(File $file, $format = null) {
        if (!$file->isWritable()) {
            throw new ImageException('Could not write ' . $file . ': file not writable');
        }

        $format = $this->detectFormat($file, $format);

        $resource = $this->getResource();
        $resource->setImageFormat($format);

        $file->getParent()->create();

        file_put_contents($file->getAbsolutePath(), $resource);
    }

    /**
     * Create a new internal image resource with the given width and height
     * @param integer width width of the new image resource
     * @param integer height height of the new image resource
     * @return null
     */
    protected function createResource() {
        $dimension = $this->getDimension();

        $this->resource = new Imagick();
        $this->resource->setSize($dimension->getWidth(), $dimension->getHeight());
    }

    /**
     * Gets the dimension of this image
     * @return \ride\library\image\dimension\Dimension
     */
    public function getDimension() {
        if (!$this->dimension) {
            if ($this->resource) {
                $geometry = $this->resource->getImageGeometry();

                $this->dimension = new GenericDimension($geometry['width'], $geometry['height']);
            } else {
                $this->dimension = new GenericDimension(100, 100);
            }
        }

        return $this->dimension;
    }

    /**
     * Sets the dimension of this image
     * @param \ride\library\image\dimension\Dimension $dimension
     * @return null
     */
    public function setDimension(Dimension $dimension) {
        $this->dimension = $dimension;

        if ($this->resource) {
        }
    }
    /**
     * Crops this image
     * @param Dimension $dimension Dimension for the cropped image
     * @param Point $start Start point of the crop
     * @return Image new instance with the cropped version of this image
     */
    public function crop(Dimension $dimension, Point $start = null) {
        if ($start === null) {
            $x = 0;
            $y = 0;
        } else {
            $x = $start->getX();
            $y = $start->getY();
        }

        if ($x > $this->getDimension()->getWidth()) {
            throw new ImageException('X exceeds the image width');
        }
        if ($y > $this->getDimension()->getHeight()) {
            throw new ImageException('Y exceeds the image height');
        }

        $result = clone $this;

        $result->resource = $result->resource->coalesceImages();
        foreach ($result->resource as $frame) {
            $frame->cropImage($dimension->getWidth(), $dimension->getHeight(), $x, $y);
            $frame->setImagePage(0, 0, 0, 0);
        }

        $result->dimension = $dimension;

        return $result;
    }

    /**
     * Resizes this image into a new image
     * @param Dimension $dimension Dimension for the resized image
     * @return Image new instance with a resized version of this image
     */
    public function resize(Dimension $dimension) {
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $result = clone $this;

        $result->resource = $result->resource->coalesceImages();
        foreach ($result->resource as $frame) {
            $frame->resizeImage($dimension->getWidth(), $dimension->getHeight(), Imagick::FILTER_CATROM, 1);
        }

        $result->dimension = $dimension;

        return $result;
    }

    /**
     * Rotates this image
     * @param float $degrees Rotation angle, in degrees
     * @param \ride\library\image\color\Color $uncoveredColor Color for the
     * uncovered areas
     * @param boolean $handleTransparancy Set to false to skip transparancy handling
     * @return Image new instance with a rotated version of this image
     */
    public function rotate($degrees, Color $uncoveredColor = null, $handleTransparancy = true) {
        $color = new ImagickPixel((string) $uncoveredColor);

        $result = clone $this;
        $result->resource->rotateImage($color, $degrees);

        $size = $result->resource->getImageGeometry();
        $result->dimension = new Dimension($size['width'], $size['height']);

        return $result;
    }

    /**
     * Flips this image into a new image
     * @param string $mode One of the MODE constants (MODE_HORIZONTAL,
     * MODE_VERTICAL or MODE_BOTH)
     * @return Image new instance with a flipped version of this image
     */
    public function flip($mode) {
        $result = clone $this;
        $result->resource = $result->resource->coalesceImages();

        foreach ($result->resource as $frame) {
            switch ($mode) {
                case self::MODE_HORIZONTAL:
                    $frame->flopImage();

                    break;
                case self::MODE_VERTICAL:
                    $frame->flipImage();

                    break;
                case self::MODE_BOTH:
                    $frame->flopImage();
                    $frame->flipImage();

                    break;
                default:
                    throw new ImageException('Could not flip the image: invalid mode provided');
            }
        }

        return $result;
    }

    /**
     * Gets the whether this image uses alpha channel transparency
     * @return boolean
     */
    public function hasAlphaTransparency() {
        if ($this->hasAlpha === null) {
            if ($this->resource) {
                $this->hasAlpha = $this->resource->getImageAlphaChannel() === Imagick::ALPHACHANNEL_TRANSPARENT;
            } else {
                $this->hasAlpha = false;
            }
        }

        return $this->hasAlpha;
    }

    /**
     * Gets the transparent color of this image
     * @return \ride\library\image\color\Color|null
     */
    public function getTransparentColor() {
        throw new ImageException('Could not get the transparent color: not implemented');
    }

}
