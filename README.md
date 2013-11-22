### What is this?

color-scan grabs the dominant color(s) from images and passes the color(s) to [Philips Hue lights](https://www.meethue.com/en-US). You can decide if you want to send the main color to a group of lights, or individual colors to individual lights. It also works with scanned photos, of course, or with any jpeg that you drop in the directory. You don't actually _need_ the scanner.

You can can decide whether you want the main dominant color on all your lights `$color_mode = 'single';`, or if you'd like to grab a palette of colors to send to individual bulbs `$color_mode = 'multi';`. The example in the video demonstrates using the "single" color mode.

### How does it work?

When the process script is called, it grabs the latest image from a directory. Color Thief handles the color quantization and provides RGB colors. RGB is converted to the the xy color space, then sent to your Hue bridge.

### Where can I get this?

Please [fork it](https://github.com/alexpgates/color-scan) from GitHub!

### How do the settings work?

    :::php
    $color_mode = 'multi'; // "multi" or "single"
    // "multi" uses different colors for each light (defined in $light_ids), 
    // "single" uses the most dominant color found in the image for a Hue group (defined in $group_id)

    $light_ids = array( 1, 2, 3 ); 
    // ids of your Hue lights that you want to use with color-scan
    // Grab these from your bridge. http://developers.meethue.com/1_lightsapi.html

    $brightness = 150;
    // 0 - 255 http://developers.meethue.com/1_lightsapi.html

    $group_id = 0; 
    // must be set  if using single color mode;
    // Grab this from your bridge. http://developers.meethue.com/2_groupsapi.html

    $path = "/path/to/your/images/";
    // directory where you want to look for an image to process

    $bridge = 'x.x.x.x'; 
    // ip address of your internal bridge

    $hue_key = 'hue-user'; 
    // valid api user

    //$debug = 'debug'; // uncomment this for some debug info

<small>*Note: color-scan doesn't handle registering a user on your bridge. You'll need to handle that [on your own](http://developers.meethue.com/4_configurationapi.html).</small>

### How do I use this?

After adjusting the settings to fit your Hue setup, drop a jpeg in the directory you defined in `$path`. Then run process.php.

### Why did you make this (totally useless) thing?

For fun, of course! I wanted to build something that would allow me to scan an image (or a piece of paper with some color) and have my lights change to match the dominant color(s) in the scanned image.

I pulled that all together with color-scan + the addition of a folder action in OS X that runs process.php when a new file is added to the directory I defined in $path. I have a profile in the Fujitsu settings that dumps new images in this directory, so it's all triggered by pressing the button on my scanner.

!['OS X Folder Action setup'](https://dl.dropboxusercontent.com/u/2227623/blog-images/XNMDVF-Screen_Shot_2013-11-22_at_3.03.28_PM.png)

### Can't you already do this with digital images and the Hue app?

Yes. But I just got this fancy new scanner and I wanted to hook it up to my light bulbs!

### Dependencies

- This uses a [color-thief-php](https://github.com/ksubileau/color-thief-php) by [Kevin Subileau](http://kevinsubileau.fr), which is a port of [Color Thief](https://github.com/lokesh/color-thief, "Color Thief") written by [Lokesh Dhakar](http://lokeshdhakar.com/)
- PHP (Only tested in 5.3.6) with cURL


### License

color-scan is MIT licensed.

Color Thief has it's own license, so check [here](https://github.com/lokesh/color-thief) and [here](https://github.com/ksubileau/color-thief-php).