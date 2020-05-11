<?php

require_once 'controllers/Reports.php';

$reports = new Reports(4, 7.5, 40);
$show_data = true;

?>

<?php

echo $reports->content;

if($show_data) {
    echo "<br><hr><pre>" . var_export($reports->raw_data, true) . "</pre>";
}
?>
