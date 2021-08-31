<?php
/* ********************* Store Log query to Database ********************* */
// Start Database configuration 
$db_host = '4you.hitenstore.com';
$db_user = 'hitensto_hitenst';
$db_pass = 'n,{!o%@.,a)f';
$db_name = 'hitensto_new_4you';
$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
mysqli_query($connection,"SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
// End Database configuration

$radius_row = mysqli_fetch_array(mysqli_query($connection,"SELECT * FROM settings where ID='2'"));
$radius = $radius_row['locator_max_distance'];
echo 'radius:'. $radius;
?>
