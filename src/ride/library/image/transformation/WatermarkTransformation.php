<?php

namespace ride\library\image\transformation;

use ride\library\image\point\GenericPoint;
use ride\library\image\ImageFactory;
use ride\library\image\Image;
use ride\library\system\file\browser\FileBrowser;

/**
 * Transformation to add a watermark to an image
 */
class WatermarkTransformation implements Transformation {

    /**
     * Constructs a new transformation
     * @param \ride\library\system\file\browser\FileBrowser $fileBrowser
     * @param \ride\library\image\ImageFactory $imageFactory
     * @return null
     */
    public function __construct(FileBrowser $fileBrowser, ImageFactory $imageFactory) {
        $this->fileBrowser = $fileBrowser;
        $this->imageFactory = $imageFactory;
    }

    /**
     * Performs a watermark transformation on the provided image
     * @param \ride\library\image\Image $image Image to transform
     * @param array $options Extra options for the transformation (watermark, x, y)
     * @return \ride\library\image\Image new image with the transformation
     * applied
     */
    public function transform(Image $image, array $options) {
        $x = array_key_exists('x', $options) ? $options['x'] : 0;
        $y = array_key_exists('y', $options) ? $options['y'] : 0;

        $path = array_key_exists('watermark', $options) ? $options['watermark'] : 0;
        if (!$path) {
            return $image;
        }

        $file = $this->fileBrowser->getFile($path);
        if (!$file) {
            $file = $this->fileBrowser->getPublicFile($path);
            if (!$file) {
                return $image;
            }
        }

        $watermark = $this->imageFactory->createImage();
        $watermark->read($file);

        $topLeft = new GenericPoint($x, $y);

        return $image->drawImage($topLeft, $watermark);
    }

}
