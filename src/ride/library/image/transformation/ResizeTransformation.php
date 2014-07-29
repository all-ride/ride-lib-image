<?php

namespace ride\library\image\transformation;

use ride\library\image\dimension\GenericDimension;
use ride\library\image\Image;

/**
 * Transformation to resize an image
 */
class ResizeTransformation implements Transformation {

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

        $resizeWidth = isset($options['width']) ? $options['width'] : $imageWidth;
        $resizeHeight = isset($options['height']) ? $options['height'] : $imageHeight;

        if ($imageWidth > $imageHeight) {
            $this->calculateNewSize($imageWidth, $imageHeight, $resizeWidth, $resizeHeight);
        } else {
            $this->calculateNewSize($imageHeight, $imageWidth, $resizeHeight, $resizeWidth);
        }

        return $image->resize(new GenericDimension($resizeWidth, $resizeHeight));
    }

    /**
     * Calculate the new sizes
     * @param int originalA original size A
     * @param int originalB original size B
     * @param int thumbnailA maximum thumbnail size A
     * @param int thumbnailB maximum thumbnail size B
     */
    private function calculateNewSize($originalA, $originalB, &$thumbnailA, &$thumbnailB) {
        $ratio = $originalA / $thumbnailA;
        $newA = $thumbnailA;
        $newB = round($originalB / $ratio);

        if ($newB > $thumbnailB) {
            $ratio = $originalB / $thumbnailB;
            $newA = round($originalA / $ratio);
            $newB = $thumbnailB;
        }

        $thumbnailA = $newA;
        $thumbnailB = $newB;
    }

}
