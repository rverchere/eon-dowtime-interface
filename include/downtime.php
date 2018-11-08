<?php
if ($_POST[dwt_submit]) {
    $hostname='Test-int-downtime2';
    $servicename='processor';
    $desc=$_POST[dwt_desc];
    $startdate=$_POST[startdate];
    $enddate=$_POST[enddate];

    $details = [
        'comment_data' => $desc,
        'start_time' => $startdate,
        'end_time' => $enddate,
        'fixed' => 1,
        'comment_author' => $dwt_author
    ];

    $result = thrukSetDowntime($dwt_dest_srv, $hostname, $servicename, $details);
    if ($result==null) {
        echo "Cannot set downtime for ".$hostname."\n";
        return -1;
    } else {
        print_r($result);
    }
}
?>