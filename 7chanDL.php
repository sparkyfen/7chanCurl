<?php
//Author: Adam Schodde
//This application comes with no warranty and I am not responsible for any wrong doing
//this may have on any user or persons affected by its useage.

//This application is going to start with a base_url then ask the user for the board.
//Once the board has been placed, it will query the site every n seconds for new threads.
//Next it will take the thread ID numbers and send them to a database.
//Then, it will take the thread ID numbers and append them to the full url (i.e. base_url + board + ID)
//It will query the full URL and download the images on the site.
//Optional:
//It will then append the information after requerying the database.

//Include Libraries
include("../library/LIB_parse.php");
include("../library/LIB_http.php");
include("../library/LIB_simple_spider.php");
include("../library/LIB_resolve_addresses.php");
include("../library/LIB_download_images.php");


//Base Chan URL we are querying.
$base_url = "https://7chan.org/";
$short_url = "7chan";

//Global Arrays
$IDarray = array();
$image_array = array();
$filename_array = array();
$filenamesFinal = array();
$imagesFinal = array();
$downloadPath_array = array();
$tempFileNameMerged = array();
$fullUrlArray = array();

echo "
 _____ ____ _                 
|___  / ___| |__   __ _ _ __  
   / / |   | '_ \ / _` | '_ \ 
  / /| |___| | | | (_| | | | |
 /_/  \____|_| |_|\__,_|_| |_|
                              
 ____                      _                 _           
|  _ \  _____      ___ __ | | ___   __ _  __| | ___ _ __ 
| | | |/ _ \ \ /\ / / '_ \| |/ _ \ / _` |/ _` |/ _ \ '__|
| |_| | (_) \ V  V /| | | | | (_) | (_| | (_| |  __/ |   
|____/ \___/ \_/\_/ |_| |_|_|\___/ \__,_|\__,_|\___|_|   "."\n\n";
echo "Please give the board name that you want:\n";
//This opens the Inputstream for reading and takes in the board value from the user.
$boardInput = fopen('php://stdin', 'r');
$board = trim(fgets($boardInput));

$boardList = array("777", "b", "fl", "gfx", "fail", "class", "co", "eh", "fit", "halp", "lit", "phi", "pr", "rnb", "sci", "tg", "w", "zom", "a", "hi", "me", "rx", "vg", "wp", "x", "be", "cake", "cd", "d", "di", "elit", "fag", "fur", "gif", "h", "men", "pco", "s", "sm", "ss", "unf", "v");

$web_page = http_get($target=$base_url.$board."/", $referrer="J.A.R.V.I.S. Web Bot");

$img_excl = parse_array($web_page['FILE'], "<div", ">");


for($x=0; $x < count($img_excl); $x++)
{
	//Grab div ID tag
	$name = get_attribute($img_excl[$x], $attribute="id");
	//ID=thread_12345_eh
	//Look for the word "thread_" from the ID
	$frontend = strstr($name, 'thread_');
	$underscore_check = $frontend[12];
	$controls_check = $frontend[7];
	if($underscore_check == "_")
	{
		if($controls_check != "c")
		{
			$ID = substr($frontend, 7, 5);
		}
		else
		{
			$ID = substr($frontend, 7, 4);
		}
	if($ID != false)
	array_push($IDarray, $ID);
	}
}
for($y = 0; $y < count($IDarray); $y++)
{
	//Full URL to query(i.e. https://7chan.org/eh/res/12433.html)
	$full_url = $base_url.$board."/res/".$IDarray[$y].".html";
	array_push($fullUrlArray, $full_url);
	//Download full URL page
	$full_web_page = http_get($target=$full_url, $referrer="J.A.R.V.I.S. Web Bot");
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
					//Merge the array together to get rid of odd indexes where array_unique removed them
					$tempFileNameMerged = array_merge_recursive($filename_unique);
					$tempImageMerged = array_merge_recursive($image_unique);
				}
			}
	}
		
	$download_path = "./".$short_url."/".$board."/".$IDarray[$y]."/";
	// clear cache to get accurate directory status
	clearstatcache();
        //Saving content:
        //Folder should look like:
        //base_url/board/ID/.img.gif.jpg
	if(!is_dir($download_path))
	{
		mkpath($download_path);
		array_push($downloadPath_array, $download_path);
	}
echo "Directory to save: ". $download_path."\n\n";
for($f = 0; $f < count($downloadPath_array); $f++)
{
	for($q = 0; $q < count($tempImageMerged); $q++)
	{
		echo "File Name: ".$download_path.$tempFileNameMerged[$q]."\n";

		if(file_exists($download_path.$tempFileNameMerged[$q]) == false && is_null($tempImageMerged[$q]) == false) 
		{
		//Echo out the filename
		echo "Saving: ".$tempImageMerged[$q]."\n";
		//Download the image, report image size
		$this_image_file =  download_binary_file($tempImageMerged[$q], $ref="J.A.R.V.I.S. Web Bot");
	        echo "Size: ".strlen($this_image_file);
		//Put file into directory
		$fp = fopen($download_path.$tempFileNameMerged[$q], "w+");
		//file_put_contents($this_image_file);
		fwrite($fp, $this_image_file);
		fclose($fp);
		echo "\n";
		}
	}
}
unset($filename_array);
unset($image_array);
$filename_array = array();
$image_array = array();
}

//Relevant links:
//For sauce, it needs to do the following:
//Take full URL and query for other links.
//Take preconfigured database to match for EXCELLENT links, take all other links and submit for questioning
//Take EXCELLENT links and send to User for download manually. (Cannot download content for them till captcha has been broken)
//Optionals include e-mail, text file, SMS

echo "Interesting Links: \n";
require_once("7chanMessages.php");

//Side job:
//Querying relevant links if they do not have captcha. (i.e. some URL that has pics on them).
//Need to train bot to query new URL and download images of that site, saving them in same format as above. (base_url/images)
?>
