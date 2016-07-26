<?php

namespace ride\library\image\color\scheme;

use ride\library\image\color\Color;
use ride\library\image\color\HslColor;

/**
 * Monochromatic color scheme
 */
class MonochromaticScheme implements Scheme {

    /**
     * Gets the colors of this scheme
     * @param ride\library\image\color\Color $color Base color
     * @param integer $number Number of colors to generate
     * @return array Array with Color objects
     */
    public function getColors(Color $baseColor, $number) {
        $colors = array(
            $baseColor,
        );

        if (!$baseColor instanceof HslColor) {
            $baseColor = $baseColor->getHslColor();
            $isRgb = true;
        } else {
            $isRgb = false;
        }

        $hue = $baseColor->getHue();
        $saturation = $baseColor->getSaturation();
        $lightness = $baseColor->getLightness();
        $alpha = $baseColor->getAlpha();

        $calculateLightShade = false;
        $factor = 0.4 / $number;

        for ($i = 1; $i < $number; $i++) {
            if ($calculateLightShade) {
                $color = new HslColor($hue, min(1, $saturation + ($i * $factor)), min(1, $lightness + ($i * $factor)), $alpha);
            } else {
                $color = new HslColor($hue, max(0, $saturation - ($i * $factor)), max(0, $lightness - ($i * $factor)), $alpha);
            }

            if ($isRgb) {
                $color = $color->getRgbColor();
            }

            if ($calculateLightShade) {
                $colors[] = $color;
            } else {
                array_unshift($colors, $color);
            }

            $calculateLightShade = !$calculateLightShade;
        }

        return $colors;
    }

}
