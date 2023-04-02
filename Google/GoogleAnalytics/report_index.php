<?php
namespace App\Google\GoogleAnalytics;
require '../../vendor/autoload.php';


// require 'vendor/autoload.php';

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;

/**
 * TODO(developer): Replace this variable with your Google Analytics 4
 *   property ID before running the sample.
 */
$property_id = 'YOUR-GA4-PROPERTY-ID';
$property_id = '213025502';

// Using a default constructor instructs the client to use the credentials
// specified in GOOGLE_APPLICATION_CREDENTIALS environment variable.
$client = new BetaAnalyticsDataClient();

// Make an API call.
$response = $client->runReport([
    'property' => 'properties/' . $property_id,
    'dateRanges' => [
        new DateRange([
            'start_date' => '2020-03-31',
            'end_date' => 'today',
        ]),
    ],
    'dimensions' => [new Dimension(
        [
            'name' => 'city',
        ]
    ),
    ],
    'metrics' => [new Metric(
        [
            'name' => 'activeUsers',
        ]
    )
    ]
]);

// Print results of an API call.
print 'Report result: ' . PHP_EOL;

foreach ($response->getRows() as $row) {
    print $row->getDimensionValues()[0]->getValue()
        . ' ' . $row->getMetricValues()[0]->getValue() . PHP_EOL;
}