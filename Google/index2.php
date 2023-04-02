<?php
require '../vendor/autoload.php';

use App\Utils\DataTypes;


echo "<a href='http://localhost:8000/connect_to_google.php'>Connect to Google</a>";

echo "<pre>";
print_r(DataTypes::convertToBoolean("true"));
echo "</pre>";