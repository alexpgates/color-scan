# What is this?

color-scan grabs the dominant color(s) from images and passes the color(s) to Philips Hue bulbs. You can decide if you want to send the main color to a group of lights, or individual colors to individual lights.

# How does it work?

When the process script is called, it'll grab the latest image in the $path you set in the settings. Then it will grab the top 3 dominant colors from the image, convert RGB to the xy color space, then send the color(s) to your Hue bridge.

# How do the settings work?

There are a few things you need to set up in process.php. 

    $multi = 'false'; // true = use top 3 colors for lights 1, 2, and 3 respectively, or send the top color to group 0
    $path = "/path/to/whatever/directory/"; // path to where your scanned images are stored.
    $bridge = 'x.x.x.x'; // ip address of your internal bridge
    $hue_key = 'your-hue-dev-user'; // valid api user
    $debug = 'true'; // Display debug info (and RGB colors) on the screen

**Note: color-scan doesn't handle registering a dev user on your bridge. You'll need to handle that on your own.** (<http://developers.meethue.com/4_configurationapi.html>)

# How do I use this?

After adjusting the settings to fit your needs, drop a jpeg in the directory you defined in $path. Then execute process.php.

# Dependencies

- This uses a port of Color Thief written by Lokesh Dhakar (<https://github.com/lokesh/color-thief>)
- PHP (Only tested in 5.3.6) with cURL

# Why did you make this?

I wanted to build something that would allow me to scan an image (or a piece of paper with some color) and have my lights change to match the dominant color(s) in the scanned image. Pointless, I know, but the thought occurred to me and I wanted to try it out.

I pulled that all together with color-scan + the addition of a folder action in OS X that triggers process.php when a new image is added to the directory defined in $path.

# Disclaimer

- This isn't really written to be very flexible off the shelf. I built it around what I have (just a set of 3 lights), but it _could_ be extended to work with more than three. (You should totally do this.)
- The function to pass off the commands to the Hue bridge is just a little function written with cURL. I wanted to keep things lightweight, so I avoided using any REST libraries, for better or for worse.
- <http://i.imgur.com/oGhAXTM.jpg>

# License

color-scan is MIT licensed, so go nuts.

Color Thief has it's own license, so check here: <https://github.com/lokesh/color-thief>