<?php
function numToAlpha($index)
{
    $alpha = '';
    while ($index >= 0) {
        $alpha = chr(97 + ($index % 26)) . $alpha;
        $index = intdiv($index, 26) - 1;
    }
    return $alpha;
}
