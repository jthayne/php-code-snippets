<?php

/**
 * stripMysqli - Extend PHP's mysqli_query function to remove unnecessary formatting from
 *               the query in order to help reduce space used in logs as well as allow for
 *               simpler log parsing.
 *
 * @author Joseph Thayne <webadmin@thaynefam.org>
 */
class stripMysqli extends mysqli
{
    function query($query)
    {
        /**
         * Initialize variables
         */
        $skipTillResetSingle = false;
        $skipTillResetDouble = false;
        $reachedFirst = false;

        /**
         * Remove any beginning or ending whitespace before continuing
         */
        $query = trim($query);

        /**
         * Split the string into an array with a single key for each character
         * The array will be looped through to find the extra formatting characters
         */
        $charList = str_split($query);

        $queryLength = count($charList);
        $spCount = 0;
        for ($loopCount = 0; $loopCount < $queryLength; $loopCount++) {
            switch ($charList[$loopCount]) {
            case PHP_EOL:
            case "\r":
            case "\n":
                if ($skipTillResetSingle == false && $skipTillResetDouble == false)
                    unset($charList[$loopCount]);
                break;
            case ' ':
                if ($skipTillResetSingle == false && $skipTillResetDouble == false) {
                    if ($reachedFirst)
                        unset($charList[$loopCount]);
                    else
                        $reachedFirst = true;
                }
                break;
            case '\\':
                $reachedFirst = false;
                $loopCount++;
                break;
            case '\'':
                $reachedFirst = false;
                if ($skipTillResetSingle)
                    $skipTillResetSingle = false;
                else
                    $skipTillResetSingle = true;
                break;
            case '"':
                $reachedFirst = false;
                if ($skipTillResetDouble)
                    $skipTillResetDouble = false;
                else
                    $skipTillResetDouble = true;
                break;
            default:
                $reachedFirst = false;
                break;
            }
        }

        $query = implode('', $charList);

        $result = parent::query($query);

        if (mysqli_error($this)) {
            throw new exception(mysqli_error($this), mysqli_errno($this));
        }

        return $result;
    }
}
