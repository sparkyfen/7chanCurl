This php application will query 7chan.org and in theory, after the user's input for a board name, will query the front page, download the thread URLs, query the thread URLs and download all the images in each thread.

Afterwards, it will save them to their own folder within this applications folder.

Requirements:
sudo apt-get/yum install php5-curl php5-cli

Webbots Spiders and Screen Scrapers Library (http://webbotsspidersscreenscrapers.com/DSP_download.php)

How to run:

Edit 7chanDL.php for directory of library folder

Command: php 7chanDL.php

Enter board name

The application takes a while to build the array and parse it out, escpecially if the pages has a ton of pictures, give it some time.

Things to do:

Fix issue when user breaks application mid-way through download and after restarting application, it will skip over all missing images in the folder it was halted on.

[FIXED] Fix PHP warning when querying every odd index (odd indexes are null after array_unique call).

[FIXED] Add functionality for ALL boards.

Error check user input for board names.

[FIXED] Correct coding to verify, with each board, that the thread ID number is the correct length.

[FIXED] Query comments of users for relevant links.

Requery pages every n seconds.

Parse URLs to database to requery.

Query other pages other than the front page.

Integration with other chans may take place, or each maybe seperate.
