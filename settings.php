<?php 
$color_mode = 'multi'; // "multi" or "single" - multi uses a different color for each light, single uses the most dominant color for a group defined in $group_id
$light_ids = array( 1, 2, 3 ); // ids of your Hue lights that you want to use with color-scan
$group_id = 0; // set this if using single color mode;
$path = "/path/to/your/images/";
$bridge = 'x.x.x.x'; // ip address of your internal bridge
$hue_key = 'hue-user'; // valid api user
//$debug = 'debug'; // uncomment this for some debug info
?>