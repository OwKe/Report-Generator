<?php

require_once 'libraries/Database.php';

class Queries
{
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    /**
     * Coverts an Array of Objects ot an Array of Arrays
     * Helps to standardise the code between the PHP and Laravel versions.
     *
     * @param $objects
     * @return array
     */
    private function objectToArray($objects) {

        $array = [];

        foreach ($objects as $object) {
            $array[] = (array) $object;
        }

       return $array;
    }

    /**
     * @param int $weeks
     * @return mixed
     */
    public function getProductionData($weeks){

        $this->db->query(getProductionDataQuery);
        $this->db->bind(':weeks', $weeks+1);

        return $this->objectToArray($this->db->resultSet());
    }

    /**
     * @param int $weeks
     * @return mixed
     */
    public function getCasesOpenedData($weeks){

        $this->db->query(getCasesOpenedDataQuery);
        $this->db->bind(':weeks', $weeks+1);

        return $this->objectToArray($this->db->resultSet());
    }

    /**
     * @param int $weeks
     * @return mixed
     */
    public function getCasesFiledData($weeks){

        $this->db->query(getCasesFiledDataQuery);
        $this->db->bind(':weeks', $weeks+1);

        return $this->objectToArray($this->db->resultSet());
    }

    /**
     * @return mixed
     */
    public function getInvoiceData(){

        $this->db->query(getInvoiceDataQuery);

        return $this->objectToArray($this->db->resultSet());
    }

    /**
     * Gets the Budget for the year for each Business Group
     *
     * @param $measurement
     * @return array
     */
    public function getProductionRanges($measurement) {

        // Weeks = 254 because that is estimated number of working days in a year.

        $range_sets = [
            'Weeks' => 254, 'Months' => 12, // 'Years' => 1,
        ];

        $results = [];

        $this->db->query(getProductionRangesQuery);
        $this->db->bind(':time_frame', $range_sets[$measurement]);

        // Create an array item for each BusinessGroup and an Overall that tallies up all totals.

        foreach ($this->objectToArray($this->db->resultSet()) as $item)  {
            $results[$item['BusinessGroup']] = 0 + $item['Total'];
            $results['Overall'] += $item['Total'];
        }

        return $results;
    }

}