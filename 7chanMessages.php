<?php
//Author: Adam Schodde

//Relevant links:
//For sauce, it needs to do the following:
//Take full URL and query for other links.
//Take preconfigured database to match for EXCELLENT links, take all other links and submit for questioning
//Take EXCELLENT links and send to User for download manually. (Cannot download content for them till captcha has been broken)
//Optionals include e-mail, text file, SMS
//This application comes with no warranty and I am not responsible for any wrong doing
//this may have on any user or persons affected by its useage.

//Global Arrays
$linksArray = array();

for($url = 0; $url < count($fullUrlArray); $url++)
{
	$web_page = http_get($target=$fullUrlArray[$url], $referrer="J.A.R.V.I.S. Web Bot");

	$message_excl = parse_array($web_page['FILE'], "<p class=\"message\"", "</p>");

	for($i = 0; $i < count($message_excl); $i++)
	{
		$img_select = return_between($message_excl[$i], "<a href=", "</a>", EXCL);
		$links = strstr($img_select, '>h');
		$linksEnhanced = substr($links, 1);
		$spaceCheck = $linksEnhanced[0];
		if($spaceCheck != "")
		{
			array_push($linksArray, $linksEnhanced);
			echo $linksEnhanced."\n";
		}
	}
}
//print_r($linksArray);
?>
