<?php

class stripMysqli extends mysqli
{
    function query($query)
    {
        $skipTillResetSingle = false;
        $skipTillResetDouble = false;
        $reachedFirst = false;

        $c = str_split($query);

        $n = count($c);
        $spCount = 0;
        for ($x=0; $x < $n; $x++) {
            switch ($c[$x]) {
            case PHP_EOL:
            case "\r":
            case "\n":
                if ($skipTillResetSingle == false && $skipTillResetDouble == false)
                    unset($c[$x]);
                break;
            case ' ':
                if ($skipTillResetSingle == false && $skipTillResetDouble == false) {
                    if ($reachedFirst)
                        unset($c[$x]);
                    else
                        $reachedFirst = true;
                }
                break;
            case '\\':
                $reachedFirst = false;
                $x++;
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

        $q = implode('', $c);


        $result = parent::query($query);

        if (mysqli_error($this)) {
            throw new exception(mysqli_error($this), mysqli_errno($this));
        }

        return $result;
    }
}
