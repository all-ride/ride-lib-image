<?php

namespace ride\library\image;

use ride\library\image\color\Color;
use ride\library\image\color\RgbColor;
use ride\library\image\dimension\Dimension;
use ride\library\image\dimension\GenericDimension;
use ride\library\image\exception\ImageException;
use ride\library\image\point\Point;
use ride\library\system\file\File;

use \Exception;

/**
 * GD implementation of an image
 */
class GdImage extends AbstractImage implements DrawImage {

    /**
     * Array with the identifiers of the allocated colors
     * @var array
     */
    protected $colors;

    /**
     * Construct an image object
     * @return null
     */
    public function __construct() {
        if (!extension_loaded('gd')) {
            throw new ImageException('Could not create a GdImage instance. Your PHP installation does not support graphic draw, please install the gd extension.');
        }
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
            case 'png':
                // read image resource
                $this->resource = imageCreateFromPng($file->getAbsolutePath());
                if ($this->resource === false) {
                    throw new ImageException('Could not read ' . $file . ': not a valid PNG image');
                }

                // handle transparency
                $this->setHasAlphaTransparency(true);

                break;
            case 'jpeg':
                // read image resource
                $this->resource = imageCreateFromJpeg($file->getAbsolutePath());
                if ($this->resource === false) {
                    throw new ImageException('Could not read ' . $file . ': not a valid JPG image');
                }

                $this->hasAlpha = false;

                break;
            case 'gif':
                // read image resource
                $this->resource = imageCreateFromGif($file->getAbsolutePath());
                if ($this->resource === false) {
                    throw new ImageException('Could not read ' . $file . ': not a valid GIF image');
                }

                // handle transparency
                $this->hasAlpha = false;

                $transparentColor = imageColorTransparent($this->resource);
                if ($transparentColor == -1) {
                    $transparentColorIndex = imageColorAllocate($this->resource, 0, 0, 0);
                    imageColorTransparent($this->resource, $transparentColorIndex);
                }

                break;
            default:
                throw new ImageException('Could not read ' . $file . ': format ' . $format . ' is not supported');
        }

        $this->dimension = null;
        $this->colors = null;
        $this->transparentColor = null;
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

        $file->getParent()->create();

        $format = $this->detectFormat($file, $format);

        switch ($format) {
            case 'png':
                imagePng($this->getResource(), $file->getAbsolutePath());

                break;
            case 'jpeg':
                imageJpeg($this->getResource(), $file->getAbsolutePath());

                break;
            case 'gif':
                imageGif($this->getResource(), $file->getAbsolutePath());

                break;
            default:
                throw new ImageException('Could not write ' . $file . ': format ' . $format . ' is not supported');
        }
    }

    /**
     * Create a new internal image resource with the given width and height
     * @param integer width width of the new image resource
     * @param integer height height of the new image resource
     * @return null
     */
    protected function createResource() {
        $dimension = $this->getDimension();

        $this->resource = @imageCreateTrueColor($dimension->getWidth(), $dimension->getHeight());
        if ($this->resource === false) {
            $error = error_get_last();

            throw new ImageException('Could not create the image resource: ' . $error['message']);
        }

        $this->colors = null;

        if ($this->hasAlpha) {
            $this->setHasAlphaTransparency($this->hasAlpha);
        }

        $fillColor = $this->getTransparentColor();
        if (!$fillColor) {
            $fillColor = new RgbColor(255, 255, 255);
        }

        imageFill($this->resource, 0, 0, $this->allocateColor($fillColor));
    }

    /**
     * Gets the dimension of this image
     * @return \ride\library\image\dimension\Dimension
     */
    public function getDimension() {
        if (!$this->dimension) {
            if ($this->resource) {
                $this->dimension = new GenericDimension(imagesX($this->resource), imagesY($this->resource));
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
        $oldDimension = $this->getDimension();

        $this->dimension = $dimension;

        if ($this->resource) {
            $oldResource = $this->resource;

            $oldWidth = $oldDimension->getWidth();
            $oldHeight = $oldDimension->getHeight();

            $this->createResource();
            $this->copyResource($oldResource, 0, 0, 0, 0, $oldWidth, $oldHeight, $oldWidth, $oldHeight);
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

        $imageWidth = $this->getDimension()->getWidth();
        $imageHeight = $this->getDimension()->getHeight();

        if ($x > $imageWidth) {
            throw new ImageException('X exceeds the image width');
        }
        if ($y > $imageHeight) {
            throw new ImageException('Y exceeds the image height');
        }

        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $result = new self();
        $result->setDimension($dimension);
        $result->copyTransparency($this);

        if ($x + $width > $imageWidth) {
            throw new ImageException('X + width exceed the image width');
        }
        if ($y + $height > $imageHeight) {
            var_export($dimension);
            var_export($start);
            throw new ImageException('Y + height exceed the image height');
        }

        $result->copyResource($this->getResource(), 0, 0, $x, $y, $width, $height, $width, $height);

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

        $result = new self();
        $result->setDimension($dimension);
        $result->copyTransparency($this);

        $result->copyResource($this->getResource(), 0, 0, 0, 0, $width, $height, $this->dimension->getWidth(), $this->dimension->getHeight());

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
        if (!$uncoveredColor) {
            $uncoveredColor = new RgbColor(0, 0, 0);
        }

        $uncoveredColor = $this->allocateColor($uncoveredColor);

        $result = clone $this;

        if ($handleTransparancy === true) {
            $ignoreTransparancy = 0;
        } else {
            $ignoreTransparancy = true;
        }

        $result->resource = imageRotate($result->resource, $degrees, $uncoveredColor, $handleTransparancy);
        $result->dimension = new GenericDimension(imagesX($result->resource), imagesY($result->resource));

        return $result;
    }

    /**
     * Sets whether this image uses a alpha channel for transparency
     * @param boolean $hasAlpha
     * @return null
     */
    public function setHasAlphaTransparency($hasAlpha = true) {
        $this->hasAlpha = $hasAlpha;

        if ($this->resource) {
            imageAlphaBlending($this->resource, !$hasAlpha);
            imageSaveAlpha($this->resource, $hasAlpha);
        }
    }

    /**
     * Gets the whether this image uses alpha channel transparency
     * @return boolean
     */
    public function hasAlphaTransparency() {
        return $this->hasAlpha;
    }

    /**
     * Sets the transparent color of this image
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function setTransparentColor(Color $color) {
        $this->transparentColor = $color;

        if ($this->resource) {
            imageColorTransparent($this->resource, $this->allocateColor($color));
        }
    }

    /**
     * Gets the transparent color of this image
     * @return ride\library\image\color\Color|null
     */
    public function getTransparentColor() {
        if ($this->transparentColor) {
            return $this->transparentColor;
        }

        if ($this->hasAlpha) {
            $this->transparentColor = new RgbColor(0, 0, 0, 0);
        } else {
            $colorIndex = imageColorTransparent($this->getResource());
            if (!($colorIndex >= 0 && $colorIndex < imagecolorstotal($this->resource))) {
                return null;
            }

            // image is transparent by color
            $color = imageColorsForIndex($this->getResource(), $colorIndex);

            $this->transparentColor = new RgbColor($color['red'], $color['green'], $color['blue']);
        }

        return $this->transparentColor;
    }

    /**
     * Copies the transparency settings of the provided image
     * @param Image $image Image to receive the transparency settings from
     * @return null
     */
    protected function copyTransparency(Image $image) {
        if ($image->hasAlphaTransparency()) {
            $this->setHasAlphaTransparency(true);
        } else {
            $color = $image->getTransparentColor();
            if ($color) {
                $this->setTransparentColor($color);
            }
        }
    }

    /**
     * Draws a point on this image
     * @param Point $p Point coordinates
     * @param \ride\library\image\color\Color $color Color for the point
     * @return null
     */
    public function drawPoint(Point $p, Color $color) {
        imageSetPixel($this->getResource(), $p->getX(), $p->getY(), $this->allocateColor($color));
    }

    /**
     * Draws a line on this image
     * @param Point $p1 Start point of the line
     * @param Point $p2 End point of the line
     * @param \ride\library\image\color\Color $color Color for the line
     * @return null
     */
    public function drawLine(Point $p1, Point $p2, Color $color) {
        imageLine($this->getResource(), $p1->getX(), $p1->getY(), $p2->getX(), $p2->getY(), $this->allocateColor($color));
    }

    /**
     * Draws a polygon on this image
     * @param array $points Array with the vectrices of the polygon
     * @param \ride\library\image\color\Color $color Color for the polygon
     * @return null
     */
    public function drawPolygon(array $points, Color $color) {
        $numPoints = 0;
        $p = array();

        foreach ($points as $index => $point) {
            if (!$point instanceof Point) {
                throw new ImageException('Provided points array contains a non-Point variable on index ' . $index);
            }

            $p[] = $point->getX();
            $p[] = $point->getY();

            $numPoints++;
        }

        imagePolygon($this->getResource(), $p, $numPoints, $this->allocateColor($color));
    }

    /**
     * Draws a rectangle on this image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param \ride\library\image\color\Color $color
     * @param integer $width Width of the lines
     * @return null
     */
    public function drawRectangle(Point $leftTop, Dimension $dimension, Color $color, $width = 1) {
        $resource = $this->getResource();

        $leftTopX = $leftTop->getX();
        $leftTopY = $leftTop->getY();
        $rightBottomX = $leftTopX + $dimension->getWidth();
        $rightBottomY = $leftTopY + $dimension->getHeight();

        $color = $this->allocateColor($color);

        for ($i = 1; $i <= $width; $i++) {
            imageRectangle($resource, $leftTopX, $leftTopY, $rightBottomX, $rightBottomY, $color);

            $leftTopX++;
            $leftTopY++;
            $rightBottomX--;
            $rightBottomY--;
        }
    }

    /**
     * Fills a rectangle on this image with the provided color
     * @param Point $leftTop
     * @param Dimension $dimension
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function fillRectangle(Point $leftTop, Dimension $dimension, Color $color) {
        $leftTopX = $leftTop->getX();
        $leftTopY = $leftTop->getY();
        $rightBottomX = $leftTopX + $dimension->getWidth();
        $rightBottomY = $leftTopY + $dimension->getHeight();

        imageFilledRectangle($this->getResource(), $leftTopX, $leftTopY, $rightBottomX, $rightBottomY, $this->allocateColor($color));
    }

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $radius Number of pixels which should be rounded
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function drawRoundedRectangle(Point $leftTop, Dimension $dimension, $radius, Color $color) {
        if (!is_numeric($radius) || $radius < 0) {
            throw new ImageException('Could not fill the rounded rectangle: provided radius is not a number or smaller then 0');
        }

        $resource = $this->getResource();

        $x = $leftTop->getX();
        $y = $leftTop->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $color = $this->allocateColor($color);

        $cornerWidth = $radius * 2;
        $innerWidth = $width - $cornerWidth;
        $innerHeight = $height - $cornerWidth;

        // left top
        imageArc($resource, $x + $radius, $y + $radius, $cornerWidth, $cornerWidth, 180, 270, $color);

        // top
        imageLine($resource, $x + $radius, $y, $x + $width - $radius, $y, $color);

        // right top
        imageArc($resource, $x + $width - $radius, $y + $radius, $cornerWidth, $cornerWidth, 270, 360, $color);

        // center
        imageLine($resource, $x, $y + $radius, $x, $y + $height - $radius, $color);
        imageLine($resource, $x + $width, $y + $radius, $x + $width, $y + $height - $radius, $color);

        // left down
        imageArc($resource, $x + $radius, $y + $height - $radius, $cornerWidth, $cornerWidth, 90, 180, $color);

        // down
        imageLine($resource, $x + $radius, $y + $height, $x + $width - $radius, $y + $height, $color);

        // right down
        imageArc($resource, $x + $width - $radius, $y + $height - $radius, $cornerWidth, $cornerWidth, 0, 90, $color);
    }

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $radius Number of pixels which should be round of
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function fillRoundedRectangle(Point $leftTop, Dimension $dimension, $radius, Color $color) {
        if (!is_numeric($radius) || $radius < 0) {
            throw new ImageException('Could not fill the rounded rectangle: provided radius is not a number or smaller then 0');
        }

        $resource = $this->getResource();

        $x = $leftTop->getX();
        $y = $leftTop->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $color = $this->allocateColor($color);

        $cornerWidth = (integer) $radius * 2;
        $innerWidth = $width - $cornerWidth;
        $innerHeight = $height - $cornerWidth;


        // left top
        imageFilledArc($resource, $x + $radius, $y + $radius, $cornerWidth, $cornerWidth, 180, 270, $color, IMG_ARC_PIE);

        // top
        imageFilledRectangle($resource, $x + $radius, $y, $x + $width - $radius, $y + $radius, $color);

        // right top
        imageFilledArc($resource, $x + $width - $radius, $y + $radius, $cornerWidth, $cornerWidth, 270, 360, $color, IMG_ARC_PIE);

        // center
        imageFilledRectangle($resource, $x, $y + $radius, $x + $width, $y + $height - $radius, $color);

        // left down
        imageFilledArc($resource, $x + $radius, $y + $height - $radius, $cornerWidth, $cornerWidth, 90, 180, $color, IMG_ARC_PIE);

        // down
        imageFilledRectangle($resource, $x + $radius, $y + $height - $radius, $x + $width - $radius, $y + $height, $color);

        // right down
        imageFilledArc($resource, $x + $width - $radius, $y + $height - $radius, $cornerWidth, $cornerWidth, 0, 90, $color, IMG_ARC_PIE);
    }

    /**
     * Draws a arc  of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function drawArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color) {
        imageArc($this->getResource(), $center->getX(), $center->getY(), $dimension->getWidth(), $dimension->getHeight(), $angleStart, $angleStop, $this->allocateColor($color));
    }

    /**
     * Fills a arc of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param \ride\library\image\color\Color $color
     * @return null
     */
    public function fillArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color, $type = null) {
        if (!$type) {
            $type = IMG_ARC_PIE;
        }

        imageFilledArc($this->getResource(), $center->getX(), $center->getY(), $dimension->getWidth(), $dimension->getHeight(), $angleStart, $angleStop, $this->allocateColor($color), $type);
    }

    /**
     * Draws a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function drawEllipse(Point $center, Dimension $dimension, Color $color) {
        imageEllipse($this->getResource(), $center->getX(), $center->getY(), $dimension->getWidth(), $dimension->getHeight, $this->allocateColor($color));
    }

    /**
     * Fills a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param ride\library\image\color\Color $color
     * @return null
     */
    public function fillEllipse(Point $center, Dimension $dimension, Color $color) {
        imageFilledEllipse($this->getResource(), $center->getX(), $center->getY(), $dimension->getWidth(), $dimension->getHeight(), $this->allocateColor($color));
    }

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
    public function drawText(Point $leftTop, $text, Color $color, $font = null, $size = null, $angle = 0) {
        if ($font === null) {
            $font = 2;
        }

        if ($size === null) {
            $size = 11;
        }

        imagettftext($this->getResource(), $size, $angle, $leftBottom->getX(), $leftBottom->getY(), $this->allocateColor($color), $font, $text);
    }

    /**
     * Draws an image
     * @param Point $leftTop Point of the upper left corner
     * @param Image $image
     * @return null
     */
    public function drawImage(Point $leftTop, Image $image) {
        throw new ImageException('Could not draw an image: not implemented');
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
        if (!imageCopyResampled($this->getResource(), $resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight)) {
            if (!imageCopyResized($this->getResource(), $resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight)) {
                throw new ImageException('Could not copy the image resource');
            }
        }

        $this->dimension = new GenericDimension($width, $height);
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

        $code = (string) $color;

        if (isset($this->colors[$code])) {
            return $this->colors[$code];
        }

        list($red, $green, $blue) = str_split(substr($color->getHtmlColor(), 1), 2);

        if ($this->hasAlpha) {
            $colorId = imageColorAllocateAlpha($this->resource, hexdec($red), hexdec($green), hexdec($blue), 127 - ($color->getAlpha() * 127));
        } else {
            $colorId = imageColorAllocate($this->resource, hexdec($red), hexdec($green), hexdec($blue));
        }

        if ($colorId === false) {
            throw new ImageException('Could not allocate color ' . $color);
        }

        $this->colors[$code] = $colorId;

        return $this->colors[$code];
    }

}
