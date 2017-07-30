<?php

namespace gentwolf;

class Valid {
    public static function isEmail($email) {
        return (bool) preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD', (string)$email);
    }

    public static function isLoginName($str, $min = 2, $max = 20) {
        return (bool)preg_match('/[a-z0-9\-]{'. $min .','. $max .'}$/ui', (string)$str);
    }

    public static function isMobile($str) {
        return (bool)preg_match('/^1[345789]\d{9}$/', (string)$str);
    }

    public static function isAlpha($str, $min = 2, $max = 10) {
        return (bool)preg_match('/^[a-z]{'. $min .','. $max .'}$/i', $str);
    }

    public static function isAlphaNumber($str, $min = 2, $max = 20) {
        return (bool)preg_match('/^[a-z0-9]{'. $min .','. $max .'}$/i', (string)$str);
    }

    public static function isChinese($str, $min = 2, $max = 10) {
        return (bool)preg_match('/^[\x{4e00}-\x{9fa5}]{'. $min .','. $max .'}$/ui', $str);
    }

    public static function isDate($str) {
        return (bool)preg_match('/^(19|20)[0-9]{2}[\/\-](0?[1-9]|1[0-2])[\/\-](0?[1-9]|[12][0-9]|3[0-1])$/', $str);
    }

    public static function isSite($str) {
        return (bool)preg_match('/^http[s]?:\/\/([0-9a-z_\-]{1,20}\.[0-9a-z_\-]{1,20}\/?(.{1,}\/?){1,})+$/', $str);
    }

    public static function isString($str, $min, $max) {
        return empty($str) ? false : (isset($str[$min - 1]) && isset($str[strlen($str) - 1]));
    }
}