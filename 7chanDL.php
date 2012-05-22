//Author: Adam Schodde
//This application comes with no warranty and I am not responsible for any wrong doing
//this may have on any user or persons affected by its useage.


<?php
//This application is going to start with a base_url then ask the user for the board.
//Once the board has been placed, it will query the site every n seconds for new threads.
//Next it will take the thread ID numbers and send them to a database.
//Then, it will take the thread ID numbers and append them to the full url (i.e. base_url + board + ID)
//It will query the full URL and download the images on the site.
//Optional:
//It will then append the information after requerying the database.

//Include Libraries
include("./library/LIB_parse.php");
include("./library/LIB_http.php");
include("./library/LIB_simple_spider.php");
include("./library/LIB_resolve_addresses.php");
include("./library/LIB_download_images.php");

//Base Chan URL we are querying.
$base_url = "https://7chan.org/";
$short_url = "7chan";

//Array for ID
$IDarray = array();
$image_array = array();
$filename_array = array();
echo "Please give the board name that you want:\n";
//This opens the Inputstream for reading and takes in the board value from the user.
$boardInput = fopen('php://stdin', 'r');
$board = trim(fgets($boardInput));

$boardList = array("777", "b", "fl", "gfx", "fail", "class", "co", "eh", "fit", "halp", "lit", "phi", "pr", "rnb", "sci", "tg", "w", "zom", "a", "hi", "me", "rx", "vg", "wp", "x", "be", "cake", "cd", "d", "di", "elit", "fag", "fur", "gif", "h", "men", "pco", "s", "sm", "ss", "unf", "v");

$web_page = http_get($target=$base_url.$board."/", $referrer="");

$img_excl = parse_array($web_page['FILE'], "<div", ">");


for($x=0; $x < count($img_excl); $x++)
{
	//Grab div ID tag
	$name = get_attribute($img_excl[$x], $attribute="id");
	//ID=thread_12345_eh
	//Look for the word "thread_" from the ID
        $frontend = strstr($name, 'thread_');
	$backtest = strrchr($frontend, $board);
	//For each board, we need to cut off "_eh" from the end of the ID, then grab the sub string with the entire string by starting after the word thread_ (6chars long)
	if($backtest == "b")
	{
	        $backend = strstr($frontend, $backtest, true);
                $ID = substr($backend, 7, 6);
		array_push($IDarray, $ID);
	}
	else if($board == $backtest && $backtest != "b")
        {
                $backend = strstr($frontend, $backtest, true);
                $ID = substr($backend, 7, 5);
		array_push($IDarray, $ID);
	}
}
for($y = 0; $y < count($IDarray); $y++)
{
	//Full URL to query(i.e. https://7chan.org/eh/res/12433.html)
	$full_url = $base_url.$board."/res/".$IDarray[$y].".html";
//Download full URL page
  $full_web_page = http_get($target=$full_url, $referrer="");
//Locate images within page.
  $img_tag_array = parse_array($full_web_page['FILE'], "<a", "</a>");

	if(count($img_tag_array) == 0)
        {
	        echo "No images found at $target\n";
        }
	else
	{
		for($z = 0; $z < count($img_tag_array); $z++)
			{
			        $final_name = get_attribute($img_tag_array[$z], $attribute="href");
				if(stristr($final_name, ".jpg") || stristr($final_name, ".gif") || stristr($final_name, ".png"))
				{
				        $short_image = strstr($final_name, '/src/');
					$shorter_image = substr($short_image, 5);
					array_push($filename_array, $shorter_image);
					array_push($image_array, $final_name);
	                                $image_unique = array_unique($image_array);
					$filename_unique = array_unique($filename_array);
				}
			}
	}
}
print_r($image_unique);
$download_path = "./".$short_url."/".$board."/".$ID."/";
// clear cache to get accurate directory status
clearstatcache();
        //Saving content:
        //Folder should look like:
        //base_url/board/ID/.img.gif.jpg
if(!is_dir($download_path))
	mkpath($download_path);

echo "Directory to save: ". $download_path."\n";
for($q = 0; $q < count($image_unique); $q++)
{
	if(!file_exists($download_path.$filename_unique) && !is_null($image_unique[$q])) 
	{
	//Echo out the filename
	echo "Saving: ".$image_unique[$q]."\n";
	//Download the image, report image size
	$this_image_file =  download_binary_file($image_unique[$q], $ref="");
        echo "Size: ".strlen($this_image_file);
	//Put file into directory
	$fp = fopen($download_path.$filename_unique[$q], "w+");
	//file_put_contents($this_image_file);
	fwrite($fp, $this_image_file);
	fclose($fp);
	echo "\n";
	}
}

///Relevant links:
//For sauce, it needs to do the following:
//Take full URL and query for other links.
//Take preconfigured database to match for EXCELLENT links, take all other links and submit for questioning
//Take EXCELLENT links and send to User for download manually. (Cannot download content for them till captcha has been broken)
//Optionals include e-mail, text file, SMS

//Side job:
//Querying relevant links if they do not have captcha. (i.e. some URL that has pics on them).
//Need to train bot to query new URL and download images of that site, saving them in same format as above. (base_url/images)
?>
