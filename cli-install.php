<?php
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

print "We can try and set up your config.php here if that's cool with you.\n";
print "Do you have your Trello tokens ready? [Y/n]: ";

if(strtolower(trim(fgets(STDIN))) != "y") {
    print "\nOkay. You can copy config-example.php to config.php and set up the API tokens when you're ready.";
    print "\nYou can also run this cli-install.php script again.";
    sleep(3);
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