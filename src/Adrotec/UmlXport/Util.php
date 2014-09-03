<?php

namespace Adrotec\UmlXport;

class Util {

    /**
     * Converts a word to its plural form.
     * @param string $name the word to be pluralized
     * @return string the pluralized word
     */
    public static function pluralize($name) {
        $rules = array(
            '/move$/i' => 'moves',
            '/foot$/i' => 'feet',
            '/child$/i' => 'children',
            '/human$/i' => 'humans',
            '/man$/i' => 'men',
            '/tooth$/i' => 'teeth',
            '/person$/i' => 'people',
            '/([m|l])ouse$/i' => '\1ice',
            '/(x|ch|ss|sh|us|as|is|os)$/i' => '\1es',
            '/([^aeiouy]|qu)y$/i' => '\1ies',
            '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
            '/(shea|lea|loa|thie)f$/i' => '\1ves',
            '/([ti])um$/i' => '\1a',
            '/(tomat|potat|ech|her|vet)o$/i' => '\1oes',
            '/(bu)s$/i' => '\1ses',
            '/(ax|test)is$/i' => '\1es',
            '/s$/' => 's',
        );
        foreach ($rules as $rule => $replacement) {
            if (preg_match($rule, $name))
                return preg_replace($rule, $replacement, $name);
        }
        return $name . 's';
    }

    public static function generateLabel($value) {
        return ucwords(trim(strtolower(str_replace(array('-', '_', '.'), ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $value)))));
    }

    public static function arrayVal($value) {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        $processedValue = array();
        foreach ($value as $key => $val) {
            $val = trim($val);
            if (!empty($val)) {
                $processedValue[] = $val;
            }
        }
        return $processedValue;
    }

}
