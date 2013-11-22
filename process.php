<?php
// *make sure you set these*
require_once('settings.php');

// required libarary for image quantization
require_once('color-thief-php.php');


// Grab the latest file added to this directory
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

// use Color Thief to grab dominant colors in the image
$dominantColors = ColorThiefPHP::getPalette($path.$latest_filename, (count($light_ids) - 1));


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

// depending on the $color_mode setting, send the xy value to the Hue lights

switch ($color_mode) {

    case 'multi':
        
        foreach($light_ids as $key => $l){
            $cmd = array('xy' => $xy_arr[$key], 'bri' => $brightness);
            $json_cmd = json_encode($cmd, JSON_NUMERIC_CHECK);
            sendHue('lights/'.$l.'/state', $json_cmd);
            $x++;
        }

        break;
    
    case 'single':

        $cmd = array('xy' => $xy_arr[0], 'bri' => $brightness);
        $json_cmd = json_encode($cmd, JSON_NUMERIC_CHECK);
        sendHue('groups/'.$group_id.'/action', $json_cmd);
        break;

    default:
        // use "multi" as defauly, I suppose
        foreach($light_ids as $key => $l){
            $cmd = array('xy' => $xy_arr[$key], 'bri' => $brightness);
            $json_cmd = json_encode($cmd, JSON_NUMERIC_CHECK);
            sendHue('lights/'.$l.'/state', $json_cmd);
            $x++;
        }

        break;
}

if(isset($debug)){
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

// This is just a thrown together function to make the PUT requests to the Hue bridge
function sendHue($path, $data){

	global $bridge, $hue_key, $debug;
	$chlead = curl_init();
	
	// fixing this so the xy value we pass is formatted properly. 
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

    if(isset($debug)){
        echo "<pre>";
        print_r($chleadresult);
        echo "</pre>";
    }

	curl_close($chlead);

}
?>