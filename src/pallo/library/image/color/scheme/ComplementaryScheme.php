<?php

namespace pallo\library\image\color\scheme;

use pallo\library\image\color\Color;
use pallo\library\image\color\HslColor;

/**
 * Complementary color scheme
 */
class ComplementaryScheme implements Scheme {

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

        $hue = $baseColor->getHue();
        $saturation = $baseColor->getSaturation();
        $lightness = $baseColor->getLightness();

        $complementColor = clone($baseColor);
        if ($hue > 0.5) {
            $complementHue = $hue - 0.5;
        } else {
            $complementHue = $hue + 0.5;
        }
        $complementColor->setHue($complementHue);

        $colors = array(
        	$complementColor,
        );

        if ($number == 1) {
            return $colors;
        }


        $calculateComplementShade = false;
        $factor = 0.5 / $number;

        $number--;
        for ($i = 1; $i < $number; $i++) {
            if ($calculateComplementShade) {
                array_unshift($colors, new HslColor($hue, min(1, $saturation + ($i * $factor)), min(1, $lightness + ($i * $factor))));
            } else {
                array_unshift($colors, new HslColor($hue, max(0, $saturation - ($i * $factor)), max(0, $lightness - ($i * $factor))));
            }

            $calculateComplementShade = !$calculateComplementShade;
        }

        array_unshift($colors, $baseColor);

        return $colors;
    }

}