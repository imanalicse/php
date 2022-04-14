<?php

namespace App\DataStructuresAndAlgorithms;

require '../vendor/autoload.php';

class Sorts
{
    public static function selectionSort(array $arr): array
    {
        $n = count($arr);

        for ($i = 0; $i < $n - 1; $i++) {
            $min_index = $i;
            for ($j = $i + 1; $j < $n; $j++) {
                if ($arr[$j] < $arr[$i])
                    $min_index = $j;
            }
            if ($min_index != $i) {
                $temp = $arr[$i];
                $arr[$i] = $arr[$min_index];
                $arr[$min_index] = $temp;
            }
        }

        return $arr;
    }
}

$data = [3, 44, 38, 5, 15, 26, 27, 2, 46, 4];
$sorted = Sorts::selectionSort($data);
echo "<pre>";
print_r($sorted);
echo "</pre>";