<?php

namespace ride\library\image\transformation;

use ride\library\image\dimension\GenericDimension;
use ride\library\image\Image;

use \PHPUnit_Framework_TestCase;

class CropTransformationTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerTransform
     */
    public function testTransform($dimension, $width, $height) {
        $options = array('width' => $width, 'height' => $height);

        $image = $this->getMock(Image::class);
        $image->expects($this->once())
              ->method('getDimension')
              ->will($this->returnValue(new GenericDimension(800, 600)));
        $image->expects($this->once())
              ->method('resize')
              ->will($this->returnValue($image));
        $image->expects($this->once())
              ->method('crop')
              ->with($this->equalTo($dimension))
              ->will($this->returnValue($image));

        $transformation = new CropTransformation();
        $newImage = $transformation->transform($image, $options);

        $this->assertTrue($newImage instanceof Image);
    }

    public function providerTransform() {
        return array(
            array(new GenericDimension(400, 400), 400, 400),
            array(new GenericDimension(100, 100), 100, 100),
        );
    }

}
