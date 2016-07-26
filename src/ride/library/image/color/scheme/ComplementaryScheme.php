<?php

namespace ride\library\image\color\scheme;

use ride\library\image\color\Color;
use ride\library\image\color\HslColor;

/**
 * Complementary color scheme
 */
class ComplementaryScheme implements Scheme {

    /**
     * Gets the colors of this scheme
     * @param ride\library\image\color\Color $color Base color
     * @param integer $number Number of colors to generate
     * @return array Array with Color objects
     */
    public function getColors(Color $baseColor, $number) {
        if (!$baseColor instanceof HslColor) {
            $hslBaseColor = $baseColor->getHslColor();
            $isRgb = true;
        } else {
            $hslBaseColor = $baseColor;
            $isRgb = false;
        }

        $hue = $hslBaseColor->getHue();
        $saturation = $hslBaseColor->getSaturation();
        $lightness = $hslBaseColor->getLightness();
        $alpha = $hslBaseColor->getAlpha();

        if ($hue > 0.5) {
            $complementHue = $hue - 180;
        } else {
            $complementHue = $hue + 180;
        }

        $complementColor = $hslBaseColor->setHue($complementHue);
        if ($isRgb) {
            $complementColor = $complementColor->getRgbColor();
        }

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
                $color = new HslColor($hue, min(1, $saturation + ($i * $factor)), min(1, $lightness + ($i * $factor)), $alpha);
            } else {
                $color = new HslColor($hue, max(0, $saturation - ($i * $factor)), max(0, $lightness - ($i * $factor)), $alpha);
            }

            if ($isRgb) {
                $color = $color->getRgbColor();
            }

            $colors[] = $color;

            $calculateComplementShade = !$calculateComplementShade;
        }

        $colors[] = $baseColor;

        return $colors;
    }

}
