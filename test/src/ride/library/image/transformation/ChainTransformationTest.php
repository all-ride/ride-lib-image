<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

use \PHPUnit_Framework_TestCase;

class ChainTransformationTest extends PHPUnit_Framework_TestCase {

    public function testTransform() {
        $radius = 15;
        $blurOptions = array('radius' => $radius);

        $image = $this->getMockBuilder('ride\\library\\image\\Image')
                      ->getMock();
        $image->expects($this->once())
              ->method('blur')
              ->with($this->equalTo($radius))
              ->will($this->returnValue($image));
        $image->expects($this->once())
              ->method('convertToGrayscale')
              ->will($this->returnValue($image));

        $blurTransformation = new BlurTransformation();
        $grayscaleTransformation = new GrayscaleTransformation();

        $transformation = new ChainTransformation();
        $transformation->addTransformation($blurTransformation, $blurOptions);
        $transformation->addTransformation($grayscaleTransformation, array());

        $transformation->transform($image, array());
    }

}
