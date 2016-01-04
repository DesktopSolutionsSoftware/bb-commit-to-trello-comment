<?php
/*
 * BitBucket Commit Message to Trello Card Comments
 * @author Andrew Natoli
 * @version 1.0
 *
 * Put this on your server and create a "push" webhook for it on BitBucket. When you push a commit containing the
 * full URL of a Trello card you can comment on, the commit message, URL, and author will be posted as a comment on
 * that card.
 *
 * Remember to copy config-example.php to config.php and populate the values with your own application keys.
 *
 * This was built for a specific use case. If you wish to extend any functionality of this program whether it
 * be improving the comment messages, enhancing security, etc. please fork the repo and submit a pull request!
 *
 * The MIT License (MIT)
 * Copyright (c) 2016 Andrew Natoli, Desktop Solutions Software
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
require "config.php";
require "vendor/autoload.php";

use \Trello\Trello;

// Step One: Check bitbucket params
$req_body = file_get_contents('php://input');
if(!strlen($req_body)) {
   die("No input");
}

$trello = new Trello(TRELLO_API_KEY, null, TRELLO_API_TOKEN);
$find = "https://trello.com/c/";
$payload = json_decode($req_body);

if(!isset($payload->push->changes[0]->commits)) {
    die("Invalid payload.");
}

$total_commits_used = 0;
$total_cards_used = 0;

// Step two: Go through each commit and create the cards for each
foreach ($payload->push->changes[0]->commits as $commit) {

    // Check the commit message for card references. Should be full URLs to card or the share URL.
    $msg = $commit->message;
    $cards = array();
    while(stristr($msg, $find)) {
        $msg = substr($msg, strpos($msg, $find));
        $a = explode($find, $msg);
        $i = (empty($a[0])) ? 1: 0; // index of a to use
        if(strstr($a[$i], "/")) {
            $c = explode("/", $a[$i]);
            $cards[] = $c[0];
            $msg = substr($msg, strpos($msg, $c[0]));
        } else if(strstr($a[$i], " ")) {
            $c = explode(" ", $a[$i]);
            $cards[] = $c[0];
            $msg = substr($msg, strpos($msg, $c[0]));
        }
    }

    if(count($cards) > 1) {
        $total_commits_used++;
        $comment = "{$commit->type} {$commit->hash} by {$commit->author->user->display_name}\n------\n{$commit->message}\n------\n{$commit->links->html->href}";
        foreach ($cards as $card) {
            $trello->post("cards/{$card}/actions/comments", array("text"=>$comment));
            $total_cards_used++;
        }
    }
}

die("Updated {$total_cards_used} from {$total_commits_used} commits.");