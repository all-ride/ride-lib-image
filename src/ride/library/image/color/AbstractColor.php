<?php

namespace ride\library\image\color;

use ride\library\image\exception\ImageException;

/**
 * Abstract color implementation
 */
abstract class AbstractColor implements Color {

    /**
     * Value for aplha channel
     * @var float
     */
    protected $alpha = 1;

    /**
     * Constructs a new color
     * @param float $alpha Alpha value between 0 (transparant) and 1 (opaque)
     * @return null
     */
    public function __construct($alpha = 1) {
        $this->validatePercentValue($alpha, 'alpha');

        $this->alpha = $alpha;
    }

    /**
     * Sets the alpha value
     * @param float $alpha Value between 0 and 1
     * @return Color New instance with adjusted alpha value
     */
    public function setAlpha($alpha) {
        $this->validatePercentValue($alpha, 'alpha');

        $color = clone $this;
        $color->alpha = $alpha;

        return $color;
    }

    /**
     * Gets the alpha channel value
     * @return float Value between 0 and 1
     */
    public function getAlpha() {
        return $this->alpha;
    }

    /**
     * Validates a percent value
     * @param mixed $value Value to validate
     * @param string $name Name of the variable
     * @return null
     * @throws ride\library\image\exception\ImageException when the value is invalid
     */
    protected function validatePercentValue($value, $name) {
        if (!is_numeric($value) || $value < 0 || $value > 1) {
            throw new ImageException('Could not set the ' . $name . ' value: provided value is not between 0 and 1');
        }
    }

}
