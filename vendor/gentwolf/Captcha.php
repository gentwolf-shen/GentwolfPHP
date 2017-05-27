<?php

/**
 * 生成验证码类
 * @author	gentwolf
 * @version	1.0	2015/03/12
 *
 */

namespace gentwolf;

class Captcha {

	/**
	 * 生成图形验证码
	 * @param int $number
	 * @param int $width
	 * @param int $height
	 * @param int $size
	 */
	public static function image($number = 6, $width = 180, $height = 36, $size = 30) {
		$code = Util::getRndStr($number);

		@session_start();
		$_SESSION['gentwolf_captcha'] = $code;

		self::generate($code, $width, $height, $size);
	}

	public static function verify($code, $isIgnoreCase = true) {
		@session_start();

		$sessionCode = isset($_SESSION['gentwolf_captcha']) ? $_SESSION['gentwolf_captcha'] : '';
		if ($isIgnoreCase) {
			return strtolower($sessionCode) === strtolower($code);
		}

		return $sessionCode === $code;
	}

	public static function clear() {
		$_SESSION['gentwolf_captcha'] = '';
	}

	public static function generate($code = 6, $width = 180, $height = 36, $size = 30) {
        $im = imagecreate($width, $height);
        imagecolorallocate($im, self::getColor(150, 255), self::getColor(150, 255), self::getColor(150, 255));

		$fontStyle = dirname(__FILE__) . '/fonts/SpicyRice.ttf';

		//产生随机字符
		$strLen = mb_strlen($code);
        for($i = 0; $i < $strLen; $i++) {
			$char = $code[$i];
			$fontColor = imageColorAllocate($im, self::getColor(0, 150), self::getColor(0, 150), self::getColor(0, 150));
            imagettftext($im, $size, rand(0, 20) - rand(0, 25), 5 + $i * $size, rand($size, 35), $fontColor, $fontStyle, $char);
        }

		//干扰线
        for ($i = 0; $i < 10; $i++){
			$lineColor = imagecolorallocate($im, self::getColor(), self::getColor(), self::getColor());
			imageline($im, rand(0, $width), 0, rand(0, $width), $height, $lineColor);
        }
       
        //干扰点
        for ($i = 0; $i < 150; $i++){
			$fontColor = imageColorAllocate($im, self::getColor(), self::getColor(), self::getColor());
			imagesetpixel($im, rand(0, $width), rand(0, $height), $fontColor);
        }

		header('Content-type: image/png');
		imagepng($im);
        imagedestroy($im);      	
	}

	public static function getColor($min = 0, $max = 0) {
		if (0 == $max) $max = 255;
		return mt_rand($min, $max);
	}
}