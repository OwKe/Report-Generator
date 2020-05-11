<?php

require_once 'env.php';
require_once 'models/Queries.php';
require_once 'helpers/helpers.php';


/**
 * Class Reports
 */
class Reports
{
    private $queries;

    private $weeks;
    private $threshold;
    private $interest;

    public $raw_data;
    public $content;

    /**
     * Reports constructor.
     * @param int $weeks
     * @param int $range_threshold
     * @param int $interest
     */
    public function __construct($weeks, $range_threshold, $interest){

        $this->queries = new Queries();

        $this->weeks     = $weeks;
        $this->threshold = $range_threshold;
        $this->interest  = $interest;

        // Each item in this array is a section of the report.
        // New reports could easily be added by adding the name and measurement here and the DB query's to match

        $reports = [
            'production'    => ['measurement' =>  'Weeks',  'query' => 'getProductionData', 'range_query' => 'getProductionRanges'],
            'cases_opened'  => ['measurement' =>  'Weeks',  'query' => 'getCasesOpenedData','range_query' => null],
            'cases_filed'   => ['measurement' =>  'Weeks',  'query' => 'getCasesFiledData', 'range_query' => null],
            'invoicing'     => ['measurement' =>  'Months', 'query' => 'getInvoiceData',    'range_query' => null]
        ];

        // Create a report for each item in the array

        foreach ($reports as $key => $report) {
            $this->createReport($report['measurement'],$report['query'], $key, $report['range_query']);
        }

        // After all reports have been created render a view
        // Passing in all the data from each report and the number of weeks in a nice format.
        // Save the rendered view to a class variable.

        $this->content = renderPHP('views/content.php', $this->raw_data, numberToText($this->weeks));
    }


    /**
     * @param string $measurement
     * @param string $query
     * @param string $report_title
     * @param string $range_query
     */
    public function createReport($measurement, $query, $report_title, $range_query) {

        // Run the query specified in the report array i.e getCasesOpenedData() which fetches the data.
        // Passing across the time frame of the report.

        $data   = $this->queries->$query($this->weeks);

        // Run the range query if specified in the report array.

        $ranges = $range_query ? $this->queries->$range_query($measurement) : null;

        // Take the raw query data and make apply some logic to it.
        // Save all report data to a class variable.
        // This is needed as the raw data of each report is saved to to DB also.

        $this->raw_data[$report_title] = $this->processData($measurement, $data, $ranges);
    }


    /**
     * @param string $measurement
     * @param array $data
     * @param array $ranges
     * @return array
     */
    public function processData($measurement, $data, $ranges) {

        // --------------------------------------------------------------------------
        //  CREATE OVERALL REPORT
        // --------------------------------------------------------------------------

        // Group data by the specified measurement.

        $grouped_data = groupBy($measurement, $data);

        // From this grouped data produce a nicely formatted array.

        $data_set = $this->createArray($grouped_data, $ranges['Overall']);

        // --------------------------------------------------------------------------
        //  CREATE REPORT FOR EACH BUSINESS GROUP
        // --------------------------------------------------------------------------

        $business_groups = array_unique(array_column($data, 'BusinessGroup'));
        sort($business_groups);

        foreach ($business_groups as $business_title) {

            $business_grouped_data = groupBy($measurement, $data, $business_title);

            $business_data_set = $this->createArray($business_grouped_data, $ranges[$business_title]);

            // Only include this individual business groups report into the Overall $data_set
            // If the degree of change is above the interest level set on class instantiation.
            // IF true then report is nested within the Overall $data_set

            if(abs($business_data_set['last_vs_av']['percentage_change']) > $this->interest AND $business_data_set['av'] > 0) {
                $data_set['BusinessGroups'][$business_title] = $business_data_set;
            }
        }

        return $data_set;
    }

    /**
     * @param array $data
     * @param int $range
     * @return array
     */
    public function createArray($data, $range) {

        // The data is sorted oldest to newest

        end($data); $lastKey = key($data); // Gets the Key of the last/most recent item in array.

        $last_av = $data[$lastKey];
        $prev_av = $data[$lastKey-1];

        // Calculate the average across the date range but.
        // Dont include the last week in the average calculations.

        unset($data[$lastKey]);
        $av = array_sum($data) / count($data);

        // Generate the percentage change between the last and the previous week &
        // the change between last week against the date range average.

        $pc_prev = percentageChange($last_av, $prev_av);
        $pc_av = percentageChange($last_av, $av);

        // Use the percentage changes to form an array of written interpretations of what has occurred.

        $last_vs_prev = interpretation($pc_prev);
        $last_vs_av = interpretation($pc_av);

        // When combining the above interpretations sometimes they imply different things such as:
        //      "A fall against the previous week {{ BUT }} a rise against the four weekly average"
        // Or Sometimes they imply the same thing:
        //      "Rose dramatically against the previous week {{ AND }} " increased against the four weekly average"
        // So this joining statement might be useful.
        $prev_vs_av_join = $last_vs_prev['trend'] == $last_vs_av['trend'] ? 'and' : 'but';

        return [
            'last_av' => numberConvert($last_av),
            'prev_av' => numberConvert($prev_av),
            'av' => numberConvert($av),
            'last_vs_prev' =>  $last_vs_prev,
            'last_vs_av' =>  $last_vs_av,
            'prev_vs_av_join' => $prev_vs_av_join,
            'range' => $range ? rangeFinder( $range, $last_av, $this->threshold) : null,
            'weeks_data' => $data,
        ];

    }

}