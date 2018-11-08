<?php
include("config.php");
include("functions.php");

if ($_POST['dwt_submit']) {
    $desc=$_POST['dwt_desc'];
    $startdate=$_POST['startdate'];
    $enddate=$_POST['enddate'];
    $confFile=$_POST['dwt_conf'];

    $yamlFile=yaml_parse_file($path_yaml_app_conf.'/'.$confFile);

    $details = [
        'comment_data' => "$desc",
        'start_time' => strtotime($startdate),
        'end_time' => strtotime($enddate),
        'fixed' => 1,
        'comment_author' => $dwt_author
    ];

    foreach ($yamlFile['app'] as $app) {
        $appHostName=$app['host'];
        $appName=$app['service'];
        $result = thrukSetDowntime($dwt_dest_srv, $appHostName, $appName, $details);
            if ($result==null) {
                echo "Cannot set downtime for application ".$appName." <br/>";
                return -1;
            } else {
                echo "Downtime set for application ".$appName." <br/>";
            }
    }

    foreach ($yamlFile['hosts'] as $hosts) {
        $hostname=$hosts['host'];
        if ($hosts['services']) {
            $servicename=$hosts['services']; //Array here
            foreach ($hosts['services'] as $service) {
                $result = thrukSetDowntime($dwt_dest_srv, $hostname, $service, $details);
                if ($result==null) {
                    echo "Cannot set downtime for ".$hostname."/".$service." <br/>";
                    return -1;
                } else {
                    echo "Downtime set for ".$hostname."/".$service." <br/>";
                }
            }
        }
        unset($service);
        $result = thrukSetDowntime($dwt_dest_srv, $hostname, '', $details);
        if ($result==null) {
            echo "Cannot set downtime for ".$hostname." <br/>";
            return -1;
        } else {
            echo "Downtime set for ".$hostname." <br/>";
        }
    }
}

if($_POST['dwt_get']) {
    $confFile=$_POST['dwt_conf'];
    $yamlFile=yaml_parse_file($path_yaml_app_conf.'/'.$confFile);

    foreach ($yamlFile['app'] as $app) {
        $appHostName=$app['host'];
        $appName=$app['service'];
        $result = thrukGetServiceDowntime($dwt_dest_srv, $appHostName, $appName, $details);
            if ($result==null) {
                echo "Cannot get downtime for application ".$appName." <br/>";
                return -1;
            } else {
                echo "Downtime get for application ".$appName." <br/>";
                print_r($result);
            }
    }
}
?>