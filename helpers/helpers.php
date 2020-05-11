<?php

/**
 * Take number add appropriate suffix
 *
 * @param int $number
 * @return string
 */
function numberConvert($number) {

    // Ensure number is not a negative value

    $number = abs($number);

    $number_text = number_format($number);

    if($number < 1000) {
        $number_text = number_format($number); // 100
    } else if ($number < 1000000) {
        $number_text = strtok(number_format($number), ',').'k'; // 140K
    } else if ($number < 1000000000) {
        $number_text = substr(number_format($number, 2, '.', '.'), 0, 4).' million';  // 1.25 million
    }
    return $number_text;
}

/**
 * Group Rows by Business Group
 *
 * The $data returned from the DB query lists a row for each business group and for each week in the weeks range.
 * Each business group needs their rows for each week separating out into its own array.
 *
 * This >
 *
    Electronics	    14	47481.84
    Life Sciences 	14	7140.00
    Electronics     15	23419.82
    Life Sciences	15	2099.84
 *
 * Into >
 *
    'Electronics' => [
        0 => ['week' => 14, 'total' => 47481.84],
        1 => ['week' => 15, 'total' => 23419.82]
    ],
    'Life Sciences' => [
        0 => ['week' => 14, 'total' => 7140.00],
        1 => ['week' => 15, 'total' => 2099.84]
    ],
 *
 * @param string $key
 * @param array $data
 * @param string $business_group
 * @return array
 */
function groupBy($key, $data, $business_group = null) {

    $results = [];

    $key_groups = array_unique(array_column($data, $key));

    // Ensure all data sets are created even if no data is recorded during this period.

    foreach ($key_groups as $key_group) {
        $results[$key_group] = 0; // $results[15] = 0
    }

    foreach ($data as $val) {

        // If a business_group is set only add data to array it is relevant to that particular business_group
        // Otherwise skip onto next iteration

        if($business_group AND $val['BusinessGroup'] !== $business_group) {
            continue;
        }

        if( isset($results[ $val[$key] ]) ){  // $val[$key] = $results[14] - 14 being the week added.
            $results[ $val[$key] ] += round(($val['Total']));
        } else {
            $results[ $val[$key] ] = round(($val['Total']));
        }
    }

    ksort($results);
    return $results;
}

/**
 * Calculate Percentage Change
 *
 * @param int $number
 * @param int $baseNumber
 * @return float|int
 */
function percentageChange($number, $baseNumber) {

    // Avoid Division by zero error if the $baseNumber is equal to 0.

    $base = $baseNumber == 0 ? 1 : $baseNumber;

    // Calculate and return the percentage change between the base and sample number.

    return number_format(($number - $base) / $base * 100,0);
}

/**
 * Find a values percentage location in Array.
 *
 * @param int $targetNumber
 * @param int $array_size
 * @return int
 */
function theClosestItemKey($targetNumber, $array_size) {

    // Create array of the percentage values for each fraction of the array

    $percentages_array = [];
    for ($x = 0; $x < $array_size; $x++) {
        $percentages_array[] = (100 / $array_size * $x);
    }

    // Use this percentages array and find the item that sits closest to our target value.

    $closestItem = null;
    foreach ($percentages_array as $key => $item) {
        if ($closestItem === null || abs($targetNumber - $closestItem) > abs($item - $targetNumber)) {
            $closestItem = $item;
        }
    }

    // Return the key for the closest item.

    return array_search($closestItem, $percentages_array);
}

/**
 * Define the direction of a change
 *
 * @param $percentage_change
 * @return string[]
 */
function getChangeDirectionText($percentage_change) {

    // Create a multidimensional array containing two sets of trend descriptions.
    // Each sub item defined must have an alternate past and present value that will work in any given sentence.

    $direction_array = [
        'positive' => [
            0 => ['present' => 'increase',   'past' => 'increased'],
            1 => ['present' => 'incline',    'past' => 'inclined'],
            2 => ['present' => 'rise',       'past' => 'rose'],
            3 => ['present' => 'climb',      'past' => 'climbed']
        ],
        'negative' => [
            0 => ['present' => 'decrease',   'past' => 'decreased'],
            1 => ['present' => 'decline',    'past' => 'declined'],
            2 => ['present' => 'fall',       'past' => 'fell'],
            3 => ['present' => 'drop',       'past' => 'dropped']
        ]
    ];

    // Define the trend - Positive or Negative.

    $trend_direction = $percentage_change  > 0 ? 'positive' : 'negative';

    // Select this trend from $direction_array and then select and return a random item from its sub array.

    $direction_key = array_rand($direction_array[$trend_direction]);

    return $direction_array[$trend_direction][$direction_key];

}

/**
 * Define description of a change
 *
 * @param int $percentage_change
 * @return string[]
 */
function getChangeDescriptionText($percentage_change) {

    // Create a multidimensional array containing descriptions increasing in scale.

    $descriptions_array = [
        0 => ['present' => 'no change',       'past' => 'remained consistent'],   // 0.0
        1 => ['present' => 'a slight',        'past' => 'slightly'],            // 12.5
        2 => ['present' => 'a modest',        'past' => 'modestly'],            // 25.0
        3 => ['present' => 'a steady',        'past' => 'steadily'],            // 37.5
        4 => ['present' => 'a significant',   'past' => 'significantly'],       // 50.0
        5 => ['present' => 'a considerable',  'past' => 'considerably'],        // 62.5
        6 => ['present' => 'a substantial',   'past' => 'substantially'],       // 75.0
        7 => ['present' => 'a dramatic',      'past' => 'dramatically']         // 87.5
    ];

    // Use the array key returned from the theClosestItemKey function to select.
    // and return the corresponding description sub array.

    $key = theClosestItemKey(abs($percentage_change), count($descriptions_array));

    return  $descriptions_array[$key];

}

/**
 * Form an interpretation by combining a description and direction
 * e.g. "a significant" + "rise"
 *
 * @param int $percentage_change
 * @return array
 */
function interpretation($percentage_change) {

    $description = getChangeDescriptionText($percentage_change);
    $direction = getChangeDirectionText($percentage_change);

    $combined_text = [];

    // If $description key is 0 indicating no change, then only include the $description.

    if($description['present'] === 'no change') {
        $combined_text['past'] = $description['past'];
        $combined_text['present'] = $description['present'];
        $combined_text['trend'] = 'consistent';
        $combined_text['gain_loss'] = $percentage_change  > 0 ? 'very slight gain' : 'very slight loss';
    }

    // Else include both direction and description in past or present order.

    else {
        $combined_text['past'] = $direction['past'].' '.$description['past'];
        $combined_text['present'] = $description['present'].' '.$direction['present'];
        $combined_text['trend'] = $percentage_change  > 0 ? 'up' : 'down';
        $combined_text['gain_loss'] = $percentage_change  > 0 ? 'gain' : 'loss';
        $combined_text['icon'] = $percentage_change  > 0 ? '<span style="color: green">&uarr;</span>' : '<span style="color: red">&darr;</span>';
    }

    $combined_text['percentage_change'] = abs($percentage_change);

    return $combined_text;
}

/**
 * Generate Range Info
 *
 * @param int $daily_target generated by getRanges() query
 * @param int $value current daily average
 * @param int $percent
 * @return array
 */
function rangeFinder($daily_target, $value, $percent) {

    // Takes a calculated daily target and creates a high and low threshold based upon a given percentage.

    $low = $daily_target * ((100-$percent) / 100);
    $high = $daily_target * ((100+$percent) / 100);

    // Then calculates an appropriate description.

    if($value  >= $low AND $value  <= $high ) {
        $description = 'inline with';
    } elseif ($value  > $high) {
        $description = 'above the';
    } else {
        $description = 'below the';
    }

    return [
        'target' => numberConvert($daily_target),
        'margin' => $percent.'%',
        'low' => numberConvert($low),
        'high' => numberConvert($high),
        'text' => $description
    ];

}

/**
 * Render a PHP file to a string using output buffering
 *
 * @param string $path location of view file
 * @param array $data raw data used to populate the view
 * @param string $weeks number of weeks as a word
 * @return false|string
 */
function renderPHP($path, $data, $weeks)
{
    ob_start();
    include($path);
    $page = ob_get_contents();
    ob_end_clean();
    return $page;
}

/**
 * Turns a given number into a word
 *
 * @param int $number
 * @return false|string
 */
function numberToText($number) {

    $words = [
        1 => "one", 2 => "two", 3 => "three", 4 => "four", 5 => "five",
        6 => "six", 7 => "seven", 8 => "eight", 9 => "nine", 10 => "ten",
    ];

    if($number > count($words)) {
        return $number;
    }

    return $words[$number];

}