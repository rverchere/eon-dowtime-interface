<?php
include("config.php");
include("functions.php");

if (isset($_POST['dwt_submit']) && $_POST['dwt_submit']) {
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
        if (isset($hosts['services']) && ($hosts['services'])) {
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

if (isset($_POST['dwt_get']) && $_POST['dwt_get']) {
    include_once("header.php");
    $confFile=$_POST['dwt_conf'];
    $yamlFile=yaml_parse_file($path_yaml_app_conf.'/'.$confFile);
    echo '<h2 class="page-header">'.getLabel("label.users_downtime.title").'</h2>';
    echo '<table>';
    echo '<tr class="tr_head">';
    echo '<th class="th_head col-md-1 t_appname">'.getLabel("label.users_downtime.tablehead.app").'</th>';
    echo '<th class="th_head sorting t_desc">'.getLabel("label.users_downtime.tablehead.desc").'</th>';
    echo '<th class="th_head sorting t_endtime">'.getLabel("label.users_downtime.tablehead.entrytime").'</th>';
    echo '<th class="th_head sorting t_starttime">'.getLabel("label.users_downtime.tablehead.starttime").'</th>';
    echo '<th class="th_head sorting t_endtime">'.getLabel("label.users_downtime.tablehead.endtime").'</th>';
    echo '<th></th>';
    echo '<th class="th_head t_actions"></th>';
    echo '</tr>';

    foreach ($yamlFile['app'] as $app) {
        $appHostName=$app['host'];
        $appName=$app['service'];
        $result = thrukGetServiceDowntime($dwt_dest_srv, $appHostName, $appName);
            if ($result==null) {
                echo "Cannot get downtime for application ".$appName." <br/>";
                return -1;
            } else {
                foreach($result as $r) {
                    echo '<tr>';
                    echo '<td class="td_line col-md-1 t_appname">'.$appName.'</td>';
                    echo '<td class="td_line col-md-1 t_comment">'.$r['comment'].'</td>';
                    echo '<td class="td_line col-md-1 t_entrytime">'.epochToDateTime($r['entry_time']).'</td>';
                    echo '<td class="td_line col-md-1 t_starttime">'.epochToDateTime($r['start_time']).'</td>';
                    echo '<td class="td_line col-md-1 t_endtime">'.epochToDateTime($r['end_time']).'</td>';
                    echo '</tr><br/>';
                }
            }
    }

    echo '</table>';

    include_once("footer.php");
}
?>