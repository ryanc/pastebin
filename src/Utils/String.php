<?php

namespace Utils;

class String
{
    /**
     * This is an constant time comparison algorithm used to prevent timing 
     * attacks.
     *
     * See: http://codahale.com/a-lesson-in-timing-attacks
     *
     * @param string $left
     *  It is recommended that the attacker-controlled input be passed in as the 
     *  left paramter. 
     * @param string $right
     *  It is recommended that the application-controlled value be passed in as 
     *  the right parameter.
     * @return boolean
     *  Returns true if the $left and $right paramters are identical, and false 
     *  otherwise.
     */
    public static function timeSafeCompare($left, $right)
    {
        if (!is_string($left) || !is_string($right)) {
            return false;
        }

        $isSameLength = (strlen($left) === strlen($right));

        if ($isSameLength === true) {
            $result = 0;
            $tmp = $left;
        }

        if ($isSameLength === false) {
            $result = 1;
            $tmp = $right;
        }

        for ($i = 0; $i < strlen($right); $i++) {
            $result |= (ord($tmp[$i]) ^ ord($right[$i]));
        }

        return 0 === $result;
    }
}
