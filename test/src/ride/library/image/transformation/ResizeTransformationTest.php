<?php

namespace ride\library\image\transformation;

use ride\library\image\dimension\GenericDimension;
use ride\library\image\Image;

use \PHPUnit_Framework_TestCase;

class ResizeTransformationTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerTransform
     */
    public function testTransform($dimension, $width, $height) {
        $options = array('width' => $width, 'height' => $height);

        $image = $this->getMockBuilder('ride\\library\\image\\Image')
                      ->getMock();
        $image->expects($this->once())
              ->method('getDimension')
              ->will($this->returnValue(new GenericDimension(800, 600)));
        $image->expects($this->once())
              ->method('resize')
              ->with($this->equalTo($dimension))
              ->will($this->returnValue($image));

        $transformation = new ResizeTransformation();
        $newImage = $transformation->transform($image, $options);

        $this->assertTrue($newImage instanceof Image);
    }

    public function providerTransform() {
        return array(
            array(new GenericDimension(400, 300), 400, null),
            array(new GenericDimension(200, 150), null, 150),
            array(new GenericDimension(100, 75), 100, 100),
        );
    }

}
