# Ride: Image Library

Image processing library of the PHP Ride framework.

You need GD or Imagick on your server to use this library.

Optional requirements are:
- pngcrush
- optipng
- jpegoptim
- jpegtran (libjpeg-turbo-progs or libjpeg-progs)
- gifsicle


## Transformations

### Watermark

Add a watermark to your image. This transformation requires following options:

- x: Intager for the x coordinate
- y: Intager for the y coordinate
- watermark: A path to a public file
