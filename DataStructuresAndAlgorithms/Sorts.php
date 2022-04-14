<?php

namespace App\DataStructuresAndAlgorithms;

use App\Logger\Log;

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

    public static function bubbleSort(array $arr): array {
        $n = count($arr);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n - $i -1; $j++) {
                if ($arr[$j] > $arr[$j+1]) {
                    $temp = $arr[$j];
                    $arr[$j] = $arr[$j+1];
                    $arr[$j+1] = $temp;
                }
            }
        }
        return $arr;
    }

    public static function insertionSort(array $arr): array {
        $n = count($arr);
        for ($i = 1; $i < $n; $i++) {
            $item = $arr[$i];
            $j = $i - 1;
            while ($j >= 0 && $arr[$j] > $item) {
                $arr[$j+1] = $arr[$j];
                $j = $j - 1;
            }
            $arr[$j+1] = $item;
        }
        return $arr;
    }
}

$data = [3, 44, 38, 5, 15, 26, 27, 2, 46, 4];
$sorted = Sorts::insertionSort($data);
echo "<pre>";
print_r($sorted);
echo "</pre>";