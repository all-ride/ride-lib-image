<?php

namespace pallo\library\image\color\scheme;

use pallo\library\image\color\Color;
use pallo\library\image\color\HslColor;

/**
 * Monochromatic color scheme
 */
class MonochromaticScheme implements Scheme {

    /**
     * Gets the colors of this scheme
     * @param pallo\library\image\color\Color $color Base color
     * @param integer $number Number of colors to generate
     * @return array Array with Color objects
     */
    public function getColors(Color $baseColor, $number) {
        if (!$baseColor instanceof HslColor) {
            $baseColor = $baseColor->getHslColor();
        }

        $colors = array(
        	$baseColor,
        );

        $hue = $baseColor->getHue();
        $saturation = $baseColor->getSaturation();
        $lightness = $baseColor->getLightness();

        $calculateLightShade = false;
        $factor = 0.4 / $number;

        for ($i = 1; $i < $number; $i++) {
            if ($calculateLightShade) {
                $colors[] = new HslColor($hue, min(1, $saturation + ($i * $factor)), min(1, $lightness + ($i * $factor)));
            } else {
                $colors[] = new HslColor($hue, max(0, $saturation - ($i * $factor)), max(0, $lightness - ($i * $factor)));
            }

            $calculateLightShade = !$calculateLightShade;
        }

        return $colors;
    }

}