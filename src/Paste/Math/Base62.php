<?php

namespace Paste\Math;

class Base62
{
    const DIGITS = '0123456789';
    const ASCII_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    const ASCII_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    protected $alphabet;

    public function __construct()
    {
        $this->alphabet = self::DIGITS
                        . self::ASCII_LOWERCASE
                        . self::ASCII_UPPERCASE;
    }
    
    public function encode($num)
    {
        $str = '';

        if ($num == 0) {
            return $this->alphabet[0];
        }

        while ($num) {
            $remainder = bcmod($num, 62);
            $num = $this->bcfloor(bcdiv($num, 62));
            $str = $this->alphabet[$remainder] . $str;
        }

        return $str;
    }

    public function decode($str)
    {
        $num = 0;
        $index = 0;

        $length = strlen($str);

        for ($i = 0; $i < $length; $i++) {
            $power = ($length - ($index + 1));
            $char = $str[$i];
            $position = strpos($this->alphabet, $char);
            $num = bcadd($num, bcmul($position, bcpow(62, $power)));
            $index++;
        }

        return $num;
    }

    /**
     * Use the bcmath functions to make a floor() function.
     *
     * Inspired by: http://stackoverflow.com/a/1653826
     *
     * @param string $number
     * @return string
     */
    private function bcfloor($number)
    {
        // If there is not a decimal place, just return the number.
        if (strpos($number, '.') === false) {
            return $number;
        }

        // If the number is negative, subtract 1 which rounds to the lowest
        // negative integer.
        if ($number[0] === '-') {
            return bcsub($number, 1, 0);
        }

        // If the number is positive and whole, add zero.
        return bcadd($number, 0, 0);
    }
}
