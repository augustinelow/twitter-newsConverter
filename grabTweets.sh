#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games
/usr/bin/php /home/ubuntu/twitter-newsConverter/tweetGrabCLI.php > /home/ubuntu/twitter-newsConverter/logs/log_`date +%d-%m-%yT%Hh%Mm`.txt 
