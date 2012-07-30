<?php

namespace Paste\Math;

class Base62
{
    const DIGITS = '0123456789';

    const ASCII_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';

    const ASCII_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * The base 62 alphabet.
     *
     * @var string
     */
    protected $alphabet;

    public function __construct()
    {
        // Build an alphabet for the base converter.
        $this->alphabet = self::DIGITS
                        . self::ASCII_LOWERCASE
                        . self::ASCII_UPPERCASE;
    }
    
    /**
     * Convert a base 10 number to base 62.
     *
     * Example:
     * <code>
     * use Paste\Math\Base62;
     *
     * $encoder = new Base62;
     * $num = $encoder->encode(1234);
     * </code>
     *
     * @param string|integer $num A base 10 number.
     * @return string A base 62 number.
     */
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

    /**
     * Convert a base 62 number to base 10.
     *
     * Example:
     * <code>
     * use Paste\Math\Base62;
     *
     * $encoder = new Base62;
     * $num = $encoder->decode('1a2b');
     * </code>
     *
     * @param string $num A base 62 number.
     * @return string A base 10 number.
     */
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
     * @param string $num
     * @return string
     */
    private function bcfloor($num)
    {
        // If there is not a decimal place, just return the number.
        if (strpos($num, '.') === false) {
            return $num;
        }

        // If the number is negative, subtract 1 which rounds to the lowest
        // negative integer.
        if ($num[0] === '-') {
            return bcsub($num, 1, 0);
        }

        // If the number is positive and whole, add zero.
        return bcadd($num, 0, 0);
    }
}
