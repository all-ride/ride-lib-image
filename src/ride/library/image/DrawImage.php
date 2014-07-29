<?php

namespace ride\library\image;

use ride\library\image\color\Color;
use ride\library\image\dimension\Dimension;
use ride\library\image\point\Point;

interface DrawImage extends Image {

    /**
     * Sets alpha channel transparency of this image
     * @param boolean $alpha
     * @return null
     */
    public function setHasAlphaTransparency($hasAlphaTransparanency);

    /**
     * Sets the transparent color of this image
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function setTransparentColor(Color $color);

    /**
     * Draws a point on this image
     * @param Point $p Point coordinates
     * @param \ride\library\image\color\Color $color Color for the point
     * @return null
     */
    public function drawPoint(Point $p, Color $color);

    /**
     * Draws a line on this image
     * @param Point $p1 Start point of the line
     * @param Point $p2 End point of the line
     * @param \ride\library\image\color\Color $color Color for the line
     * @return null
     */
    public function drawLine(Point $p1, Point $p2, Color $color);

    /**
     * Draws a polygon on this image
     * @param array $points Array with the vectrices of the polygon
     * @param \ride\library\image\color\Color $color Color for the polygon
     * @return null
     */
    public function drawPolygon(array $points, Color $color);

    /**
     * Draws a rectangle on this image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param \ride\library\image\color\Color $color
     * @param integer $width Width of the lines
     * @return null
     */
    public function drawRectangle(Point $leftTop, Dimension $dimension, Color $color, $width = 1);

    /**
     * Fills a rectangle on this image with the provided color
     * @param Point $leftTop
     * @param Dimension $dimension
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function fillRectangle(Point $leftTop, Dimension $dimension, Color $color);

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $radius Number of pixels which should be rounded
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function drawRoundedRectangle(Point $leftTop, Dimension $dimension, $radius, Color $color);

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $radius The number of pixels which should be round of
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function fillRoundedRectangle(Point $leftTop, Dimension $dimension, $radius, Color $color);

    /**
     * Draws a arc  of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function drawArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color);

    /**
     * Fills a arc of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function fillArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color, $type = null);

    /**
     * Draws a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function drawEllipse(Point $center, Dimension $dimension, Color $color);

    /**
     * Fills a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function fillEllipse(Point $center, Dimension $dimension, Color $color);

    /**
     * Draws text on the image
     * @param Point $leftTop Point of the upper left corner
     * @param string $text
     * @param \ride\library\image\color\Color $color
     * @param string $font Font name of absolute path to a ttf file
     * @param integer $size Font size in pixels or points
     * @param integer $angle Angle in degrees
     * @return null
     */
    public function drawText(Point $leftTop, $text, Color $color, $font = null, $size = null, $angle = 0);

    /**
     * Draws an image
     * @param Point $leftTop Point of the upper left corner
     * @param Image $image
     * @return null
     */
    public function drawImage(Point $leftTop, Image $image);

}
