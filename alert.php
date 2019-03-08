<?php  
ignore_user_abort(); // run script in background
    set_time_limit(0); // run script forever
    $interval=60*15; // do every 15 minutes...

    do{
       // add the script that has to be ran every 15 minutes here
       // ...
       sleep($interval); // wait 15 minutes
    } while(true);