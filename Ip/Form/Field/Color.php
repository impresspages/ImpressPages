<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Color extends Field
{

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment)
    {
        return '
<div class="input-group">
    <span class="input-group-addon"><i></i></span>
    <input ' . $this->getAttributesStr($doctype)
        . ' class="form-control ipsColorPicker '
        . implode(' ', $this->getClasses())
        . '" name="' . htmlspecialchars($this->getName()) . '" '
        . $this->getValidationAttributesStr($doctype)
        . ' type="text" value="' . htmlspecialchars($this->getValue()) . '" />
</div>
        ';

    }

    /**
     * Validate input value
     *
     * @param array $values all values of the form
     * @param string $valueKey key of value in values array that needs to be validated
     * @param \Ip\Form $environment
     * @return bool|string return string on error or false on success
     */
    public function validate($values, $valueKey, $environment)
    {
        $colors = array(' Transparent', 'transparent', ' AliceBlue', 'aliceblue', ' AntiqueWhite', 'antiquewhite', ' Aqua', 'aqua', ' Aquamarine', 'aquamarine', ' Azure', 'azure', ' Beige', 'beige', ' Bisque', 'bisque', ' Black', 'black', ' BlanchedAlmond', 'blanchedalmond', ' Blue', 'blue', ' BlueViolet', 'blueviolet', ' Brown', 'brown', ' BurlyWood', 'burlywood', ' CadetBlue', 'cadetblue', ' Chartreuse', 'chartreuse', ' Chocolate', 'chocolate', ' Coral', 'coral', ' CornflowerBlue', 'cornflowerblue', ' Cornsilk', 'cornsilk', ' Crimson', 'crimson', ' Cyan', 'cyan', ' DarkBlue', 'darkblue', ' DarkCyan', 'darkcyan', ' DarkGoldenRod', 'darkgoldenrod', ' DarkGray', 'darkgray', ' DarkGrey', 'darkgrey', ' DarkGreen', 'darkgreen', ' DarkKhaki', 'darkkhaki', ' DarkMagenta', 'darkmagenta', ' DarkOliveGreen', 'darkolivegreen', ' Darkorange', 'darkorange', ' DarkOrchid', 'darkorchid', ' DarkRed', 'darkred', ' DarkSalmon', 'darksalmon', ' DarkSeaGreen', 'darkseagreen', ' DarkSlateBlue', 'darkslateblue', ' DarkSlateGray', 'darkslategray', ' DarkSlateGrey', 'darkslategrey', ' DarkTurquoise', 'darkturquoise', ' DarkViolet', 'darkviolet', ' DeepPink', 'deeppink', ' DeepSkyBlue', 'deepskyblue', ' DimGray', 'dimgray', ' DimGrey', 'dimgrey', ' DodgerBlue', 'dodgerblue', ' FireBrick', 'firebrick', ' FloralWhite', 'floralwhite', ' ForestGreen', 'forestgreen', ' Fuchsia', 'fuchsia', ' Gainsboro', 'gainsboro', ' GhostWhite', 'ghostwhite', ' Gold', 'gold', ' GoldenRod', 'goldenrod', ' Gray', 'gray', ' Grey', 'grey', ' Green', 'green', ' GreenYellow', 'greenyellow', ' HoneyDew', 'honeydew', ' HotPink', 'hotpink', ' IndianRed', 'indianred', ' Indigo', 'indigo', ' Ivory', 'ivory', ' Khaki', 'khaki', ' Lavender', 'lavender', ' LavenderBlush', 'lavenderblush', ' LawnGreen', 'lawngreen', ' LemonChiffon', 'lemonchiffon', ' LightBlue', 'lightblue', ' LightCoral', 'lightcoral', ' LightCyan', 'lightcyan', ' LightGoldenRodYellow', 'lightgoldenrodyellow', ' LightGray', 'lightgray', ' LightGrey', 'lightgrey', ' LightGreen', 'lightgreen', ' LightPink', 'lightpink', ' LightSalmon', 'lightsalmon', ' LightSeaGreen', 'lightseagreen', ' LightSkyBlue', 'lightskyblue', ' LightSlateGray', 'lightslategray', ' LightSlateGrey', 'lightslategrey', ' LightSteelBlue', 'lightsteelblue', ' LightYellow', 'lightyellow', ' Lime', 'lime', ' LimeGreen', 'limegreen', ' Linen', 'linen', ' Magenta', 'magenta', ' Maroon', 'maroon', ' MediumAquaMarine', 'mediumaquamarine', ' MediumBlue', 'mediumblue', ' MediumOrchid', 'mediumorchid', ' MediumPurple', 'mediumpurple', ' MediumSeaGreen', 'mediumseagreen', ' MediumSlateBlue', 'mediumslateblue', ' MediumSpringGreen', 'mediumspringgreen', ' MediumTurquoise', 'mediumturquoise', ' MediumVioletRed', 'mediumvioletred', ' MidnightBlue', 'midnightblue', ' MintCream', 'mintcream', ' MistyRose', 'mistyrose', ' Moccasin', 'moccasin', ' NavajoWhite', 'navajowhite', ' Navy', 'navy', ' OldLace', 'oldlace', ' Olive', 'olive', ' OliveDrab', 'olivedrab', ' Orange', 'orange', ' OrangeRed', 'orangered', ' Orchid', 'orchid', ' PaleGoldenRod', 'palegoldenrod', ' PaleGreen', 'palegreen', ' PaleTurquoise', 'paleturquoise', ' PaleVioletRed', 'palevioletred', ' PapayaWhip', 'papayawhip', ' PeachPuff', 'peachpuff', ' Peru', 'peru', ' Pink', 'pink', ' Plum', 'plum', ' PowderBlue', 'powderblue', ' Purple', 'purple', ' Red', 'red', ' RosyBrown', 'rosybrown', ' RoyalBlue', 'royalblue', ' SaddleBrown', 'saddlebrown', ' Salmon', 'salmon', ' SandyBrown', 'sandybrown', ' SeaGreen', 'seagreen', ' SeaShell', 'seashell', ' Sienna', 'sienna', ' Silver', 'silver', ' SkyBlue', 'skyblue', ' SlateBlue', 'slateblue', ' SlateGray', 'slategray', ' SlateGrey', 'slategrey', ' Snow', 'snow', ' SpringGreen', 'springgreen', ' SteelBlue', 'steelblue', ' Tan', 'tan', ' Teal', 'teal', ' Thistle', 'thistle', ' Tomato', 'tomato', ' Turquoise', 'turquoise', ' Violet', 'violet', ' Wheat', 'wheat', ' White', 'white', ' WhiteSmoke', 'whitesmoke', ' Yellow', 'yellow', ' YellowGreen', 'yellowgreen');

        if (preg_match('/^#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$\b/', $values[$valueKey])) {
            return parent::validate($values, $valueKey, $environment);
        } else if (in_array(strtolower($values[$valueKey]), $colors)) {
            return parent::validate($values, $valueKey, $environment);
        } else {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                return __('Incorrect color code', 'Ip-admin', false);
            } else {
                return __('Incorrect color code', 'Ip', false);
            }
        }
    }

}
