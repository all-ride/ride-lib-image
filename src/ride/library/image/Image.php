<?php

namespace ride\library\image;

use ride\library\image\color\Color;
use ride\library\image\color\RgbColor;
use ride\library\image\exception\ImageException;

use \Exception;

/**
 * Image data container with basic manipulations
 */
class Image {

    /**
     * The width of this image
     * @var int
     */
    protected $width;

    /**
     * The height of this image
     * @var int
     */
    protected $height;

    /**
     * Flag to see if this image has alpha channel transparency
     * @var boolean
     */
    protected $alpha;

    /**
     * Internal resource of this image
     * @var resource
     */
    protected $resource;

    /**
     * Array with the identifiers of the allocated colors
     * @var array
     */
    protected $colors;

    /**
     * Construct an image object
     * @param integer $width Width used to create a new image (default 100)
     * @param integer $height Height used to create a new image (default 100)
     * @param boolean $alpha Flag to see if alpha channel is supported
     * @param mixed $image null for a new image, another Image instance for
     * a clone
     * @return null
     */
    public function __construct($width = 100, $height = 100, $alpha = false, $image = null) {
        if (!extension_loaded('gd')) {
            throw new ImageException('Could not create a Image instance. Your PHP installation does not support graphic draw, please install the gd extension.');
        }

        if ($image === null) {
            $this->createResource($width, $height);
            $this->setAlphaTransparency($alpha);

            $color = $this->getTransparentColor();
            if (!$color) {
                $color = new RgbColor(255, 255, 255);
            }

            imageFill($this->resource, 0, 0, $this->allocateColor($color));

            return;
        } elseif (is_resource($image)) {
            $this->resource = $image;
            $this->alpha = $alpha;

            return;
        } elseif ($image instanceof self) {
            $this->createResource($image->width, $image->height);
            $this->copyTransparency($image);
            $this->copyResource($image->resource, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);

            return;
        }

        throw new ImageException('Invalid image provided');
    }

    /**
     * Free the memory of the image
     * @return null
     */
    public function __destruct() {
        if (!$this->resource) {
            return;
        }

        try {
            imageDestroy($this->resource);
        } catch (Exception $exception) {

        }
    }

    /**
     * Gets the image resource
     * @return resource
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     * Get the width of this Image instance
     * @return int width
     */
    public function getWidth() {
        if (!isset($this->width)) {
            $this->width = imagesX($this->resource);
        }

        return $this->width;
    }

    /**
     * Get the height of this Image instance
     * @return int height
     */
    public function getHeight() {
        if (!isset($this->height)) {
            $this->height = imagesY($this->resource);
        }

        return $this->height;
    }

    /**
     * Gets the dimension of this image
     * @return Dimension
     */
    public function getDimension() {
        return new Dimension($this->getWidth(), $this->getHeight());
    }

    /**
     * Crop this image into a new Image instance
     * @param Dimension $dimension The dimension for the cropped image
     * @param Point $start The start point of the crop
     * @return Image new Image instance with a cropped version of this Image instance
     */
    public function crop(Dimension $dimension, Point $start = null) {
        if ($start === null) {
            $x = 0;
            $y = 0;
        } else {
            $x = $start->getX();
            $y = $start->getY();
        }

        if ($x < 0) {
            throw new ImageException('Invalid x provided ' . $x);
        }
        if ($y < 0) {
            throw new ImageException('Invalid y provided ' . $y);
        }
        if ($x > $this->getWidth()) {
            throw new ImageException('X exceeds the image width');
        }
        if ($y > $this->getHeight()) {
            throw new ImageException('Y exceeds the image height');
        }

        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $result = new self($width, $height);
        $result->copyTransparency($this);

        if ($x + $width > $this->getWidth()) {
            throw new ImageException('X + width exceed the image width');
        }
        if ($y + $height > $this->getHeight()) {
            throw new ImageException('Y + height exceed the image height');
        }

        $result->copyResource($this->resource, 0, 0, $x, $y, $width, $height, $width, $height);

        return $result;
    }

    /**
     * Resize this image into a new Image instance
     * @param Dimension $dimension The dimension for the resized image
     * @return Image new Image instance with a resized version of this image
     */
    public function resize(Dimension $dimension) {
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $result = new self($width, $height, $this->alpha);
        $result->copyTransparency($this);

        $result->copyResource($this->resource, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

        return $result;
    }

    /**
     * Rotates this image
     * @param float $degrees Rotation angle, in degrees
     * @param string $uncoveredColor HTML color for the uncovered area
     * @param boolean $handleTransparancy Set to false to skip transparancy handling
     * @return Image new Image instance with a rotated version of this image
     */
    public function rotate($degrees, $uncoveredColor = '#000000', $handleTransparancy = true) {
        $uncoveredColor = $this->allocateColor($uncoveredColor);

        $result = new self(null, null, null, $this);

        if ($handleTransparancy === true) {
            $ignoreTransparancy = 0;
        } else {
            $ignoreTransparancy = true;
        }

        $result->resource = imageRotate($result->resource, $degrees, $uncoveredColor, $handleTransparancy);

        return $result;
    }

    /**
     * Draws a point on the image
     * @param Point $p Point coordinates
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function drawPoint(Point $p, Color $color) {
        $color = $this->allocateColor($color);

        imageSetPixel($this->resource, $p->getX(), $p->getY(), $color);
    }

    /**
     * Draws a line on the image
     * @param Point $p1 Start point of the line
     * @param Point $p2 End point of the line
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function drawLine(Point $p1, Point $p2, Color $color) {
        $color = $this->allocateColor($color);

        imageLine($this->resource, $p1->getX(), $p1->getY(), $p2->getX(), $p2->getY(), $color);
    }

    /**
     * Draws a polygon on the image
     * @param array $points Array with the vectrices of the polygon
     * @param Color $color The color to draw the polygon in
     * @return null
     */
    public function drawPolygon(array $points, Color $color) {
        $color = $this->allocateColor($color);

        $numPoints = 0;
        $p = array();
        foreach ($points as $point) {
            if (!$point instanceof Point) {
                throw new ImageException('Provided points array contains a non-Point variable');
            }

            $p[] = $point->getX();
            $p[] = $point->getY();

            $numPoints++;
        }

        imagePolygon($this->resource, $p, $numPoints, $color);
    }

    /**
     * Draws a rectangle on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param ride\library\image\color\Color $color
     * @param integer $width
     * @return null
     */
    public function drawRectangle(Point $leftTop, Dimension $dimension, Color $color, $width = 1) {
        $color = $this->allocateColor($color);

        $leftTopX = $leftTop->getX();
        $leftTopY = $leftTop->getY();
        $rightBottomX = $leftTopX + $dimension->getWidth();
        $rightBottomY = $leftTopY + $dimension->getHeight();

        for ($i = 1; $i <= $width; $i++) {
            imageRectangle($this->resource, $leftTopX, $leftTopY, $rightBottomX, $rightBottomY, $color);

            $leftTopX++;
            $leftTopY++;
            $rightBottomX--;
            $rightBottomY--;
        }
    }

    /**
     * Fills a rectangle on the image with the provided color
     * @param Point $leftTop
     * @param Dimension $dimension
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function fillRectangle(Point $leftTop, Dimension $dimension, Color $color) {
        $color = $this->allocateColor($color);

        $leftTopX = $leftTop->getX();
        $leftTopY = $leftTop->getY();
        $rightBottomX = $leftTopX + $dimension->getWidth();
        $rightBottomY = $leftTopY + $dimension->getHeight();

        imageFilledRectangle($this->resource, $leftTopX, $leftTopY, $rightBottomX, $rightBottomY, $color);
    }

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $cornerSize The number of pixels which should be round of
     * @param ride\library\image\color\Color $color
     * @param integer $width
     * @return null
     */
    public function drawRoundedRectangle(Point $leftTop, Dimension $dimension, $cornerSize, $color) {
        $color = $this->allocateColor($color);

        $x = $leftTop->getX();
        $y = $leftTop->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $cornerWidth = $cornerSize * 2;
        $innerWidth = $width - $cornerWidth;
        $innerHeight = $height - $cornerWidth;

        // left top
        imageArc($this->resource, $x + $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 180, 270, $color);

        // top
        imageLine($this->resource, $x + $cornerSize, $y, $x + $width - $cornerSize, $y, $color);

        // right top
        imageArc($this->resource, $x + $width - $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 270, 360, $color);

        // center
        imageLine($this->resource, $x, $y + $cornerSize, $x, $y + $height - $cornerSize, $color);
        imageLine($this->resource, $x + $width, $y + $cornerSize, $x + $width, $y + $height - $cornerSize, $color);

        // left down
        imageArc($this->resource, $x + $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 90, 180, $color);

        // down
        imageLine($this->resource, $x + $cornerSize, $y + $height, $x + $width - $cornerSize, $y + $height, $color);

        // right down
        imageArc($this->resource, $x + $width - $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 0, 90, $color);
    }

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $cornerSize The number of pixels which should be round of
     * @param ride\library\image\color\Color $color
     * @param integer $width
     * @return null
     */
    public function fillRoundedRectangle(Point $leftTop, Dimension $dimension, $cornerSize, $color) {
        $color = $this->allocateColor($color);

        $x = $leftTop->getX();
        $y = $leftTop->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $cornerWidth = $cornerSize * 2;
        $innerWidth = $width - $cornerWidth;
        $innerHeight = $height - $cornerWidth;

        // left top
        imageFilledArc($this->resource, $x + $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 180, 270, $color, IMG_ARC_PIE);

        // top
        imageFilledRectangle($this->resource, $x + $cornerSize, $y, $x + $width - $cornerSize, $y + $cornerSize, $color);

        // right top
        imageFilledArc($this->resource, $x + $width - $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 270, 360, $color, IMG_ARC_PIE);

        // center
        imageFilledRectangle($this->resource, $x, $y + $cornerSize, $x + $width, $y + $height - $cornerSize, $color);

        // left down
        imageFilledArc($this->resource, $x + $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 90, 180, $color, IMG_ARC_PIE);

        // down
        imageFilledRectangle($this->resource, $x + $cornerSize, $y + $height - $cornerSize, $x + $width - $cornerSize, $y + $height, $color);

        // right down
        imageFilledArc($this->resource, $x + $width - $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 0, 90, $color, IMG_ARC_PIE);
    }

    /**
     * Draws a arc  of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function drawArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color) {
        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageArc($this->resource, $x, $y, $width, $height, $angleStart, $angleStop, $color);
    }

    /**
     * Fills a arc of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function fillArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color, $type = null) {
        if (!$type) {
            $type = IMG_ARC_PIE;
        }

        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageFilledArc($this->resource, $x, $y, $width, $height, $angleStart, $angleStop, $color, $type);
    }

    /**
     * Draws a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function drawEllipse(Point $center, Dimension $dimension, Color $color) {
        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageEllipse($this->resource, $x, $y, $width, $height, $color);
    }

    /**
     * Fills a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function fillEllipse(Point $center, Dimension $dimension, Color $color) {
        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageFilledEllipse($this->resource, $x, $y, $width, $height, $color);
    }

    /**
     * Draws text on the image
     * @param Point $leftTop Point of the upper left corner
     * @param ride\library\image\color\Color $color
     * @param string $text
     * @return null
     */
    public function drawText(Point $leftTop, Color $color, $text) {
        $color = $this->allocateColor($color);

        imageString($this->resource, 2, $leftTop->getX(), $leftTop->getY(), $text, $color);
    }

    /**
     * Sets alpha channel transparency of this image
     * @param boolean $alpha
     * @return null
     */
    public function setAlphaTransparency($alpha = true) {
        $this->alpha = $alpha;

        imageAlphaBlending($this->resource, !$alpha);
        imageSaveAlpha($this->resource, $alpha);
    }

    /**
     * Gets the whether this image uses alpha channel transparency
     * @return boolean
     */
    public function hasAlphaTransparency() {
        return $this->alpha;
    }

    /**
     * Sets the transparent color of this image
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function setTransparentColor(Color $color) {
        $color = $this->allocateColor($color);

        imageColorTransparent($this->resource, $color);
    }

    /**
     * Gets the transparent color of this image
     * @return ride\library\image\color\Color|null
     */
    public function getTransparentColor() {
        if ($this->alpha) {
            return new RgbColor(0, 0, 0, 0);
        }

        $colorIndex = imageColorTransparent($this->resource);
        if ($colorIndex >= 0 && $colorIndex < imagecolorstotal($this->resource)) {
            // image is transparent by color
            $color = imageColorsForIndex($this->resource, $colorIndex);

            return new RgbColor($color['red'], $color['green'], $color['blue']);
        }

        return null;
    }

    /**
     * Copies the transparency settings of the provided image
     * @param Image $image Image to receive the transparency settings from
     * @return null
     */
    public function copyTransparency(Image $image) {
        if ($image->hasAlphaTransparency()) {
            $this->setAlphaTransparency();
        } else {
            $color = $image->getTransparentColor();
            if ($color) {
                $this->setTransparentColor($color);
            }
        }
    }

    /**
     * Copy an existing internal image resource, or part of it, to this Image instance
     * @param resource existing internal image resource as source for the copy
     * @param int x x-coordinate where the copy starts
     * @param int y y-coordinate where the copy starts
     * @param int resourceX starting x coordinate of the source image resource
     * @param int resourceY starting y coordinate of the source image resource
     * @param int width resulting width of the copy (not of the resulting image)
     * @param int height resulting height of the copy (not of the resulting image)
     * @param int resourceWidth width of the source image resource to copy
     * @param int resourceHeight height of the source image resource to copy
     * @return null
     */
    protected function copyResource($resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight) {
        if (!imageCopyResampled($this->resource, $resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight)) {
            if (!imageCopyResized($this->resource, $resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight)) {
                throw new ImageException('Could not copy the image resource');
            }
        }

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Create a new internal image resource with the given width and height
     * @param int width width of the new image resource
     * @param int height height of the new image resource
     * @return null
     */
    protected function createResource($width, $height) {
        if (!is_numeric($width) || $width <= 0) {
            throw new ImageException('Invalid width provided ' . $width);
        }
        if (!is_numeric($height) || $height <= 0) {
            throw new ImageException('Invalid height provided ' . $height);
        }

        $this->resource = @imageCreateTrueColor($width, $height);

        if ($this->resource === false) {
            $error = error_get_last();

            throw new ImageException('Could not create the image resource: ' . $error['message']);
        }

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Allocates the color in the image
     * @param ride\library\image\color\Color $color Color definition
     * @return integer identifier of the color
     */
    protected function allocateColor(Color $color) {
        if ($this->colors === null) {
            $this->colors = array();
        }

        $code = $color->__toString();

        if (isset($this->colors[$code])) {
            return $this->colors[$code];
        }

        $this->colors[$code] = $color->allocate($this->resource);

        return $this->colors[$code];
    }

}
