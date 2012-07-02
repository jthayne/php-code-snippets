<?php

class stripMysqli extends mysqli
{
    function query($query)
    {
        $skipTillResetSingle = false;
        $skipTillResetDouble = false;
        $reachedFirst = false;

        $query = trim($query);
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
