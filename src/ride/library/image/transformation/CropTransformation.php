<?php

namespace ride\library\image\transformation;

use ride\library\image\dimension\GenericDimension;
use ride\library\image\exception\ImageException;
use ride\library\image\point\GenericPoint;
use ride\library\image\Image;

/**
 * Transformation to crop an image
 */
class CropTransformation implements Transformation {

    /**
     * Performs a crop transformation on the provided image
     * @param \ride\library\image\Image $image Image to transform
     * @param array $options Extra options for the transformation (width and/or
     * height)
     * @return \ride\library\image\Image new image with the transformation
     * applied
     */
    public function transform(Image $image, array $options) {
        $imageDimension = $image->getDimension();
        $imageWidth = $imageDimension->getWidth();
        $imageHeight = $imageDimension->getHeight();

        if (!isset($options['width'])) {
            throw new ImageException('Could not crop the provided image: no width provided');
        } elseif (!is_numeric($options['width']) || $options['width'] <= 0) {
            throw new ImageException('Could not crop the provided image: width should be a positive number');
        } else {
            $cropWidth = $options['width'];
        }

        if (!isset($options['height'])) {
            throw new ImageException('Could not crop the provided image: no height provided');
        } elseif (!is_numeric($options['height']) || $options['height'] <= 0) {
            throw new ImageException('Could not crop the provided image: height should be a positive number');
        } else {
            $cropHeight = $options['height'];
        }

        $x = isset($options['x']) ? $options['x'] : 0;
        $y = isset($options['y']) ? $options['y'] : 0;

        $resizeWidth = $cropWidth;
        $resizeHeight = $cropHeight;

        if ($imageWidth > $imageHeight) {
            $this->calculateNewSize($imageWidth, $imageHeight, $resizeWidth, $resizeHeight, $x, $y);
        } else {
            $this->calculateNewSize($imageHeight, $imageWidth, $resizeHeight, $resizeWidth, $y, $x);
        }

        $result = $image->resize(new GenericDimension($resizeWidth, $resizeHeight));
        $result = $result->crop(new GenericDimension($cropWidth, $cropHeight), new GenericPoint($x, $y));

        return $result;
    }

    /**
     * Calculate the new sizes
     * @param integer originalA original size A
     * @param integer originalB original size B
     * @param integer thumbnailA maximum thumbnail size A
     * @param integer thumbnailB maximum thumbnail size B
     * @param integer startA start position for A
     * @param integer startB start position for B
     * @return null
     */
    private function calculateNewSize($originalA, $originalB, &$thumbnailA, &$thumbnailB, &$startA, &$startB) {
        $difference = $originalA / $thumbnailA;
        $newA = $thumbnailA;
        $newB = round($originalB / $difference);

        if ($newB < $thumbnailB) {
            $difference = $originalB / $thumbnailB;
            $newA = round($originalA / $difference);
            $newB = $thumbnailB;
            $startA = floor(($newA - $thumbnailA) / 2);
        } else {
            $startB = floor(($newB - $thumbnailB) / 2);
        }

        $thumbnailA = $newA;
        $thumbnailB = $newB;
    }

}
