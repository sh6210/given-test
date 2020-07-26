<?php

$contents = file_get_contents($argv[1]);
$explodedContents = explode("\n", $contents);

foreach ($explodedContents as $row) {

    if (empty($row)) break;

    $eachRowElements = explode(",", $row);

    $binInfo = explode(':', $eachRowElements[0]);
    $binVal = trim($binInfo[1], '"');

    $amountInfo = explode(':', $eachRowElements[1]);
    $amountVal = trim($amountInfo[1], '"');

    $currencyInfo = explode(':', $eachRowElements[2]);
    $currencyVal = trim($currencyInfo[1], '"}');

    try {
        $binResults = @file_get_contents('https://lookup.binlist.net/' . $binVal);

        if (!$binResults){
            echo "\n Sorry didn't find result for binValue: $binVal";
            return;
        }

        $$binResults = json_decode($binResults);
        $isEu = isEu($$binResults->country->alpha2);

        $currentExchangeRates = @file_get_contents('https://api.exchangeratesapi.io/latest');
        $rate = @json_decode($currentExchangeRates, true)['rates'][$currencyVal];

        if ($currencyVal == 'EUR' or $rate == 0) {
            $amntFixed = $amountVal;
        }

        if ($currencyVal != 'EUR' or $rate > 0) {
            $amntFixed = $amountVal / $rate;
        }

        echo $amntFixed * ($isEu ? 0.01 : 0.02);

        print "\n";

    } catch (Exception $e) {
        echo "Sorry, didn't find any result for binValue: $binVal";
    }

}

function isEu($c)
{
    $result = false;

    switch ($c) {
        case 'AT':
        case 'BE':
        case 'BG':
        case 'CY':
        case 'CZ':
        case 'DE':
        case 'DK':
        case 'EE':
        case 'ES':
        case 'FI':
        case 'FR':
        case 'GR':
        case 'HR':
        case 'HU':
        case 'IE':
        case 'IT':
        case 'LT':
        case 'LU':
        case 'LV':
        case 'MT':
        case 'NL':
        case 'PO':
        case 'PT':
        case 'RO':
        case 'SE':
        case 'SI':
        case 'SK':
            $result = true;
    }

    return $result;
}
