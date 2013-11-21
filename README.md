# What is this?

color-scan grabs the dominant color(s) from images and passes the color(s) to Philips Hue bulbs. You can decide if you want to send the main color to a group of lights, or individual colors to individual lights.

# How does it work?

When the process script is called, it grabs the latest image from a directory. Color Thief handles the color quantization and provides RGB colors. RGB is converted the the xy color space, then sent to your Hue bridge.

# How do the settings work?

    $color_mode = 'multi'; // "multi" or "single"
    // "multi" uses different colors for each light (defined in $light_ids), 
    // "single" uses the most dominant color found in the image for a Hue group (defined in $group_id)

    $light_ids = array( 1, 2, 3 ); 
    // ids of your Hue lights that you want to use with color-scan
    // Grab these from your bridge. http://developers.meethue.com/1_lightsapi.html

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

**Note: color-scan doesn't handle registering a dev user on your bridge. You'll need to handle that on your own.** (<http://developers.meethue.com/4_configurationapi.html>)

# How do I use this?

After adjusting the settings to fit your needs, drop a jpeg in the directory you defined in $path. Then execute process.php.

# Dependencies

- This uses a port of Color Thief written by Lokesh Dhakar (<https://github.com/lokesh/color-thief>)
- PHP (Only tested in 5.3.6) with cURL

# Why did you make this?

I wanted to build something that would allow me to scan an image (or a piece of paper with some color) and have my lights change to match the dominant color(s) in the scanned image.

I pulled that all together with color-scan + the addition of a folder action in OS X that runs process.php. The folder action is set on the file I defined in $path, and made with an automator workflow that just executes a shell script when files are added to the directory.
[inotify](http://en.wikipedia.org/wiki/Inotify) would probably work on linux as an alternative to os x and folder actions.


# License

color-scan is MIT licensed.

Color Thief has it's own license, so check here: <https://github.com/lokesh/color-thief>