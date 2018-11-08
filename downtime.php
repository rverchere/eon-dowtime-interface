<?php
    include("include/functions.php");

    /*
     * just for tests:
     * $argv[0]: php script
     * $argv[1]: EON server
     * $argv[2]: comment
     * $argv[3]: start_time
     * $argv[4]: end_time
     * $argv[5]: servername
     * $argv[6]: servicename
     */

    // Set EON Server
    if(isset($argv[1])) {
        $server = $argv[1];
    } else {
        echo "EON Server not defined\n";
        return -1;
    }

    // if only eon server as parameter, get downtimes
    if($argc < 3) {
        $result = thrukGetDowntimes($argv[1]);
        if ($result==null) {
            echo "Cannot get downtimes\n";
            return -1;
        } else {
            echo $result;
        }
        return 0;
    }

    // to set downtimes, check all params
    if($argc < 6) {
        echo "Missing parameters to set downtimes\n";
        return -1;
    }

    $hostname = $argv[5];


    if(isset($argv[6])) {
        $servicename = $argv[6];
    } else {
        $servicename = '';
    }

    $details = [
        'comment_data' => $argv[2],
        'start_time' => $argv[3],
        'end_time' => $argv[4],
        'fixed' => 1,
        'comment_author' => 'Mr. Robot'
    ];

    $result = thrukSetDowntime($server, $hostname, $servicename, $details);
    if ($result==null) {
        echo "Cannot set downtime for ".$hostname."\n";
        return -1;
    } else {
        echo $result;
    }
    return 0;

?>
