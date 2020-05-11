# Report Generator

The brief for this project was to take the data from a bunch of pre-prepared SQL queries and generate a text based interpretation for use with additional reports.  

### Example:

> Production declined substantially for the firm averaging 40k per day last week compared to the previous weeks 179k a loss of 78% shows a downwards trend in comparison to the previous four weeks

### Requirements 

Server with PHP. 

### Installation

Firstly create your own copy of the env_example.php file
 
 `cp env_example.php env.php`

Then in your new `env.php` file configure the connection for your local database: 

```
define('DB_HOST', 'db_host');
define('DB_NAME', 'db_name');
define('DB_USER', 'db_user');
define('DB_PASS', 'db_pass');
define('DB_DRIVER', 'db_driver');
```
DB queries will also needed to be added as env variables as they may contain confidential info. 
```
define('getProductionDataQuery', '...');
define('getCasesOpenedDataQuery', '...');
define('getCasesFiledDataQuery', '...');
define('getInvoiceDataQuery', '...');
define('getProductionRangesQuery', '...');
```
### Create a new report

```new Reports($weeks , $range_threshold , $interest);```

* **$weeks** = INT : The number of weeks to include the report.
* **$range_threshold** = INT : Each business group is set a target across the year and this $range_threshold variable is the percentage above and below this target to create the range.
* **$interest** = INT : What percentage change is required for that business group to be included on the report. I.E if set to 40% only groups with a change greater than 40% will be included. 

### Notes


This project eventually migrated into a Laravel application and in this form remains in a pure PHP state, a loose MVC setup and DB queries executed using PDO.  

Could be improved with Namespacing and a Composer loaded .env file. 
