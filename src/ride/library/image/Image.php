<?php

namespace ride\library\image;

use ride\library\image\color\Color;
use ride\library\image\dimension\Dimension;
use ride\library\image\point\Point;
use ride\library\system\file\File;

/**
 * Interface for an image
 */
interface Image {

    /**
     * Name of the horizontal property
     * @var string
     */
    const MODE_HORIZONTAL = 'horizontal';

    /**
     * Name of the vertical property
     * @var string
     */
    const MODE_VERTICAL = 'vertical';

    /**
     * Name of the both property
     * @var string
     */
    const MODE_BOTH = 'both';

    /**
     * Reads the image from a file
     * @param \ride\library\system\file\File $file
     * @param string $format
     * @return null
     * @throws \ride\library\image\exception\ImageException when the image could
     * not be read
     */
    public function read(File $file, $format = null);

    /**
     * Writes this image to file
     * @param \ride\library\system\file\File $file
     * @param string $format
     * @return null
     * @throws \ride\library\image\exception\ImageException when the image could
     * not be written
     */
    public function write(File $file, $format = null);

    /**
     * Gets the resource for the underlaying library
     * @return resource
     */
    public function getResource();

    /**
     * Gets the dimension of this image
     * @return \ride\library\image\dimension\Dimension
     */
    public function getDimension();

    /**
     * Sets the dimension of this image
     * @param \ride\library\image\dimension\Dimension $dimension
     * @return null
     */
    public function setDimension(Dimension $dimension);

    /**
     * Crops this image
     * @param \ride\library\image\dimension\Dimension $dimension Dimension for
     * the cropped image
     * @param Point $start Start point of the crop
     * @return Image new instance with the cropped version of this image
     */
    public function crop(Dimension $dimension, Point $start = null);

    /**
     * Resizes this image into a new image
     * @param \ride\library\image\dimension\Dimension $dimension Dimension for
     * the resized image
     * @return Image new instance with a resized version of this image
     */
    public function resize(Dimension $dimension);

    /**
     * Rotates this image
     * @param float $degrees Rotation angle, in degrees
     * @param \ride\library\image\color\Color $uncoveredColor Color for the
     * uncovered areas
     * @param boolean $handleTransparancy Set to false to skip transparancy handling
     * @return Image new instance with a rotated version of this image
     */
    public function rotate($degrees, Color $uncoveredColor = null, $handleTransparancy = true);

    /**
     * Flips this image into a new image
     * @param string $mode Flip mode, this can be one of the constants:
     * HORIZONTAL, VERTICAL, BOTH
     * @return Image new instance with a resized version of this image
     */
    public function flip($mode);

    /**
     * Gets the whether this image uses alpha channel transparency
     * @return boolean
     */
    public function hasAlphaTransparency();

    /**
     * Gets the transparent color of this image
     * @return \ride\library\image\color\Color|null
     */
    public function getTransparentColor();

}
