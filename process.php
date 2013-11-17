<?php
// Settings. Edit these.

$multi = 'false'; // true = use top 3 colors for lights 1, 2, and 3. false = use top color for Group 0
$path = "/path/to/whatever/directory/"; // path to where your scanned images are stored
$bridge = 'x.x.x.x'; // ip address of your internal bridge
$hue_key = 'your-hue-dev-user'; // valid api user
$debug = 'true'; // uncomment this for some debug info

// end Settings


// required libarary for image quantization
require_once('color-thief-php.php');


// This part grabs the latest image added to $path
$latest_ctime = 0;
$latest_filename = '';    

$d = dir($path);
while (false !== ($entry = $d->read())) {
	if($entry == '.' || $entry == '..' || $entry == '.DS_Store'){
    	continue;
  	}
  	$filepath = "{$path}/{$entry}";
  	if (is_file($filepath) && filectime($filepath) > $latest_ctime) {
    	$latest_ctime = filectime($filepath);
    	$latest_filename = $entry;
  	}
}

// get a palette of 3 colors for the latest image
$dominantColors = ColorThiefPHP::getPalette($path.$latest_filename, 2);


// Create arrays to hold the RGB of the image and the XYZ transaltions 
$xy_arr = array();
$rgb_arr = array();

// calculate xy (what Hue needs) for each color, and stuff RGB in an array, too, for debug / display in page
foreach($dominantColors as $key => $color){
	
	// Calculate RGB - hat tip, https://github.com/chr1s1/siriproxy-hue/commit/770de6157f8fd3dc3177c6bdd17175707488c0cd
    $big_x = 1.076450 * $color[0] - 0.237662 * $color[1] + 0.161212 * $color[2];
    $big_y = 0.410964 * $color[0] + 0.554342 * $color[1] + 0.034694 * $color[2];
    $big_z = -0.010954 * $color[0] - 0.013389 * $color[1] + 1.024343 * $color[2];

    $x = number_format($big_x / ($big_x + $big_y + $big_z), 5);
    $y = number_format($big_y / ($big_x + $big_y + $big_z), 5);

    $xy_arr[$key] = '['.$x.','.$y.']';
    $rgb_arr[$key] = $color[0].','.$color[1].','.$color[2];
}

$x = 1;
if($multi == 'true'){
	// Set lights 1, 2, 3 with the top three dominant colors
    foreach($xy_arr as $key => $xy){
        $cmd = array();
        $cmd['xy'] = $xy;
        $json_cmd = json_encode($cmd, JSON_NUMERIC_CHECK);
        sendHue('lights/'.$x.'/state', $json_cmd);
        $x++;
    }
}else{
	// set all the lights in Group 0 to the most dominant color
    $cmd = array();
    $cmd['xy'] = $xy_arr[0];
    $json_cmd = json_encode($cmd, JSON_NUMERIC_CHECK);
    sendHue('groups/0/action', $json_cmd);
}

if($debug == 'true'){
    echo 'Processing file: '.$latest_filename;
    echo "<pre>xy_arr";
    print_r($xy_arr);
    echo "</pre>";

    echo "<pre>rgb_arr";
    print_r($rgb_arr);
    echo "</pre>";
	
    foreach($rgb_arr as $key => $rgb){
        echo '<div style="height:200px;witdh:100%;background-color:rgb('.$rgb.');"></div>';
	}
}

function sendHue($path, $data){
	global $bridge, $hue_key, $debug;
	$chlead = curl_init();
	
	// fixing this so the xy value we pass is formatted properly. I know the json could be encoded better, but http://i.imgur.com/oGhAXTM.jpg
    $data = str_replace(']"', ']', $data);
    $data = str_replace('"[', '[', $data);
	
	curl_setopt($chlead, CURLOPT_URL, 'http://'.$bridge.'/api/'.$hue_key.'/'.$path);
	curl_setopt($chlead, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
	curl_setopt($chlead, CURLOPT_VERBOSE, 1);
	curl_setopt($chlead, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($chlead, CURLOPT_CUSTOMREQUEST, "PUT"); 
	curl_setopt($chlead, CURLOPT_POSTFIELDS,$data);
	curl_setopt($chlead, CURLOPT_SSL_VERIFYPEER, 0);
	$chleadresult = curl_exec($chlead);
	$chleadapierr = curl_errno($chlead);
	$chleaderrmsg = curl_error($chlead);

    if($debug == 'true'){
        echo "<pre>";
        print_r($chleadresult);
        echo "</pre>";
    }

	curl_close($chlead);
}
?>