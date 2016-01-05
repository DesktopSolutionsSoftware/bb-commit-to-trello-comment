<?php
/*
 * Run this via command line for a guided approach to creating your config.php!
 *
 * I was hoping to have this run after creating the project with composer but composer forces you through with
 * blank inputs. Either way I thought it would be fun to create an installer so here it is. This will ask you
 * for your Trello API keys then make sure they work before writing config.php. It helps make sure you have
 * things set up right without having to try pushing to your repo. Enjoy! - Andrew
 *
 * ---
 *
 * The MIT License (MIT)
 * Copyright (c) 2016 Desktop Solutions Software
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

if(php_sapi_name() != "cli") {
    die("This can only be run via command line.");
}
require "vendor/autoload.php";
use Trello\Trello;

print "\n\nBitBucket comments to Trello\n";

if(file_exists("config.php")) {
   print "Delete config.php if you want to run this script again.\n";
   exit;
}

$file = fopen("checkWriteAccess.php", "w") or die("Can not write to this directory; please configure manually.");
fclose($file);
unlink("checkWriteAccess.php");

print "We can set up your config.php here if that's cool with you.\n";
print "Do you have your Trello tokens ready? [Y/n]: ";

if(strtolower(trim(fgets(STDIN))) != "y") {
    print "\nOkay. Come back and run this script again when you're ready.";
    print "\nYou can also copy config-example.php to config.php and edit the file yourself.";
    exit;
}

$connected = false;
while(!$connected) {
    $API_KEY    = "";
    $API_TOKEN  = "";

    while (strlen($API_KEY) < 1) {
        print "Enter your API Key: ";
        $API_KEY = trim(fgets(STDIN));
    }

    while (strlen($API_TOKEN) < 1) {
        print "Enter your app's token: ";
        $API_TOKEN = trim(fgets(STDIN));
    }

    print "Trying to connect to Trello...\n";
    $trello = new Trello($API_KEY, null, $API_TOKEN);

    if($trello->members->get('me')) {
        $connected = true;
        print "Authenticated!\n";
    } else {
        print "Authentication failed. Please check your credentials and try again.\n";
    }
}

$file = fopen("config.php", "w") or die("Can not create config.php; please do this manually.");
print "Writing config.php\n";
$txt =
"<?php
define('TRELLO_API_KEY', '{$API_KEY}');
define('TRELLO_API_TOKEN', '{$API_TOKEN}');";

fwrite($file, $txt);
fclose($file);

print "Done! Enjoy using this webhook!\n";