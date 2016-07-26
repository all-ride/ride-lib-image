# Ride: Image Library

Image processing library of the PHP Ride framework.

You need GD or Imagick on your server to use this library.

## Image

The _Image_ interface is the base of this library.

Through this interface, you can:
- read and write images
- get information about your image like the dimension
- basic manipulations like resizing and cropping

The _DrawImage_ interface extends from _Image_ and adds methods to draw points, lines, ... on the image.

Implementations are available for GD and Imagick.

## ImageFactory

The _ImageFactory_ interface offers a generic way to create the instances needed to work with images.

A generic implementation is provided through _GenericImageFactory_.

## Point

The _Point_ interface is to define a coordinate on an X-Y axis.
It's used for image manipulations.
A point can go positive or negative on an axis.

A generic implementation is provided through _GenericPoint_.

## Dimansion

The _Dimension_ interface defines a dimension of an image or manipulation.

A generic implementation is provided through _GenericDimension_.

## Transformation

The _Transformation_ interface offers a way to apply batch image manipulations.

There are implementations available for crop, resize, flip, blur, grayscale, watermark and chain manipulations.

### Blur

Blurs your image. 

This transformation has following options:

- radius: optional and defaults to 10

### Crop

Crops your image by resizing it first to an optimal dimension. 

This transformation requires the following options:

- width: width to crop to in pixels
- height: height to crop to in pixels

### Flip

Flips your image over the X-axis and/or the Y-axis.

This transformation requires the following options:

- mode: can be horizontal, vertical or both.

### Grayscale

Converts your image to a grayscaled image.

This transformations has no options.

### Resize

Resizes your image to fit a maximum width and/or height.

This transformation requires one of the following options:

- width: maximum width of the resulting image
- height: maximum height of the resulting image

### Watermark

Adds a watermark to your image. 

This transformation has the following options:

- x: Point on the X-axis, defaults to 0
- y: Point on the Y-axis, defaults to 0 
- watermark: relative path to the watermark image

## Optimizer

The _Optimizer_ interface can be used to implement optimalizations on image files.
It's intented to optimize the file sizes but can be used for other purposes as well.

A generic implementation is provided through _GenericOptimizer_.

### GenericOptimizer

The generic image optimizer uses the following binaries on your system:
- pngcrush
- optipng
- jpegoptim
- jpegtran (libjpeg-turbo-progs or libjpeg-progs)
- gifsicle

_Note: Unexistant binaries are ignored._

## Color

The _Color_ interface offers an easy way to work with colors.

The interface is currently implemented for the RGB and the HSL color model.

## ColorSchem

The _ColorScheme_ interface is to generate a number of colors for a specified base color.

The interface is currently implemented for monochomatic and complementary schemes.

## Code Sample

Check this code sample to see the possibilities of this library:

```php
<?php

use ride\library\image\exception\ImageException;
use ride\library\image\GenericImageFactory;
use ride\library\image\Image;
use ride\library\system\file\File;

function foo(File $file) {
    // create an image factory, this one uses GD
    $imageFactory = new GenericImageFactory('gd');
    
    // use the image factory to create an image
    $image = $imageFactory->createImage();
    
    try {
        // read an image file
        $image->read($file);
    } catch (ImageException $exception) {
        // file could not be read or invalid image
        return false;
    }
    
    // get some properties of the image
    $hasTransparancy = $image->hasAlphaTransparancy();
    $transparentColor = $image->getTransparentColor();
    $dimension = $image->getDimension();
    
    $dimension->getWidth();
    $dimension->getHeight();
    
    // do some manipulations
    $dimension = $dimension->setWidth($dimension->getWidth() / 2);
    $image = $image->resize($dimension);
    
    $dimension = $dimension->setHeight($dimension->getHeight() / 2);
    $image = $image->crop($dimension);
    
    $image = $image->flip(Image::MODE_HORIZONTAL);
    $image = $image->rotate(90);
    $image = $image->blur();
    $image = $image->convertToGrayscale();
    
    // write the result back to the file
    $image->write($file);
}
```
