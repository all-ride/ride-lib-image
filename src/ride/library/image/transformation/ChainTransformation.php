<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

/**
 * Transformation to bundle other transformations in a chain
 */
class ChainTransformation implements Transformation {

    /**
     * Chain of transformations
     * @var array
     */
    private $transformations = array();

    /**
     * Adds a transformation to the chain
     * @param Transformation $transformation
     * @param array $options
     * @return null
     */
    public function addTransformation(Transformation $transformation, array $options) {
        $this->transformations[] = array(
            'implementation' => $transformation,
            'options' => $options,
        );
    }

    /**
     * Performs a blur transformation on the provided image
     * @param \ride\library\image\Image $image Image to transform
     * @param array $options Extra options for the transformation (radius)
     * @return \ride\library\image\Image new image with the transformation
     * applied
     */
    public function transform(Image $image, array $options) {
        foreach ($this->transformations as $transformation) {
            $image = $transformation['implementation']->transform($image, $transformation['options']);
        }

        return $image;
    }

}
