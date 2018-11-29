<?php
include_once("header.php");

echo '<h1 class="page-header">'.getLabel("label.users_downtime.title").'</h1>';

if (isset($_POST['dwt_submit']) && $_POST['dwt_submit']) {
    $lineID=$_POST['cb'];
    $desc=$_POST['dwt_desc'.$lineID];
    $startdate=$_POST['startdate'.$lineID];
    $enddate=$_POST['enddate'.$lineID];
    $confFile=unserialize($_POST['dwt_conf']);
    $yamlFile=$confFile[$lineID];

    $details = [
        'comment_data' => "$desc",
        'start_time' => strtotime($startdate.' '.$_ENV["TZ"]),
        'end_time' => strtotime($enddate.' '.$_ENV["TZ"]),
        'fixed' => 1,
        'comment_author' => $dwt_author
    ];

    foreach ($yamlFile['app'] as $app) {
        echo '<h2>'.getLabel("label.users_downtime.set.app.title").'</h2>';
        echo '<table>';
        createTableHead('app','submit');
        $appHostName=$app['host'];
        $appName=$app['service'];
        echo '<tr>';
        echo '<td class="td_line col-md-1 ts_appname"><h4>'.$appName.'</h4></td>';
        echo '<td class="td_line sorting ts_desc">'.$details["comment_data"].'</td>';
        echo '<td class="td_line sorting ts_starttime">'.$startdate.'</td>';
        echo '<td class="td_line sorting ts_endtime">'.$enddate.'</td>';

        $result = thrukSetDowntime($dwt_dest_srv, $appHostName, $appName, $details);
        if ($result==null) {
            $status=getLabel("label.users_downtime.set.app.status.fail");
            $state='failed';
        } else {
            $status=getLabel("label.users_downtime.set.app.status.success");
            $state='succeed';
        }
        echo '<td class="td_line sorting ts_status">'.$status.'</td>';
        echo '</tr>';
        echo '</table>';
        if ($status=='failed') { return -1; }
    }

    echo '<h2>'.getLabel("label.users_downtime.set.hosts.title").'</h2>';
    echo '<table>';
    createTableHead('host','');


    foreach ($yamlFile['hosts'] as $hosts) {
        $appHostName=$app['host'];
        $appName=$app['service'];
        echo '<tr>';
        $hostname=$hosts['host'];
        if (isset($hosts['services']) && ($hosts['services'])) {
            $servicename=$hosts['services']; //Array here
            foreach ($hosts['services'] as $service) {
                $result = thrukSetDowntime($dwt_dest_srv, $hostname, $service, $details);
                if ($result==null) {
                    $status=getLabel("label.users_downtime.set.app.status.fail");
                    $state='failed';
                } else {
                    $status=getLabel("label.users_downtime.set.app.status.success");
                    $state='success';
                }
                echo '<td class="td_line col-md-1 tv_host">'.$hostname.'</td>';
                echo '<td class="td_line col-md-1 tv_service">'.$service.'</td>';
                echo '<td class="td_line sorting tv_desc">'.$details["comment_data"].'</td>';
                echo '<td class="td_line sorting tv_starttime">'.$startdate.'</td>';
                echo '<td class="td_line sorting tv_endtime">'.$enddate.'</td>';
                echo '<td class="td_line sorting ts_status">'.$status.'</td>';
                echo '</tr>';
                $svcSet='yes';
                if ($status=='failed') { return -1; }
            }
        }
        unset($service);
        $result = thrukSetDowntime($dwt_dest_srv, $hostname, '', $details);
        if ($result==null) {
            $status=getLabel("label.users_downtime.set.app.status.fail");
            $state='failed';
        } else {
            $status=getLabel("label.users_downtime.set.app.status.success");
            $state='success';
        }
        if ( $svcSet != 'yes' ) {
            echo '<tr>';
        }
        echo '<td class="td_line col-md-1 tv_host">'.$hostname.'</td>';
        echo '<td class="td_line sorting tv_service">-</td>';
        echo '<td class="td_line sorting tv_desc">'.$details["comment_data"].'</td>';
        echo '<td class="td_line sorting tv_starttime">'.$startdate.'</td>';
        echo '<td class="td_line sorting tv_endtime">'.$enddate.'</td>';
        echo '<td class="td_line sorting ts_status">'.$status.'</td>';
        echo '</tr>';
        if ($status=='failed') { return -1; }
        $endSet='yes';
    }
    if ( $endSet == 'yes' ) { echo '</table>'; }
}

if (isset($_POST['dwt_get']) && $_POST['dwt_get']) {
    $lineID=$_POST['cb'];
    $confFile=unserialize($_POST['dwt_conf']);
    $yamlFile=$confFile[$lineID];
    echo '<table>';
    createTableHead('app','get');


    foreach ($yamlFile['app'] as $app) {
        $appHostName=$app['host'];
        $appName=$app['service'];
        $result = eonGetServiceDowntime($dwt_dest_srv, $appHostName, $appName);
        if ($result==null) {
            echo '<h3>'.getLabel("label.users_downtime.status.nodwt").' : '.$appName.'<h3/><br/>';
            return -1;
        } else {
            foreach($result['result']['default'] as $r) {
                echo '<tr>';
                echo '<td class="td_line col-md-1 tv_appname"><h4>'.$appName.'</h4></td>';
                echo '<td class="td_line col-md-1 tv_comment">'.$r['comment'].'</td>';
                echo '<td class="td_line col-md-1 tv_entrytime">'.epochToDateTime($r['entry_time']).' UTC </td>';
                echo '<td class="td_line col-md-1 tv_starttime">'.epochToDateTime($r['start_time']).' UTC </td>';
                echo '<td class="td_line col-md-1 tv_endtime">'.epochToDateTime($r['end_time']).' UTC </td>';
                echo '</tr><br/>';
            }
        }
    }
    echo '</table>';
}

if (isset($_POST['dwt_config']) && $_POST['dwt_config']) {

    $lineID=$_POST['cb'];
    $confFile=unserialize($_POST['dwt_conf']);
    $yamlFile=$confFile[$lineID];
    echo '<h3 class="page-header">'.$yamlFile['displayname'].'</h3>';

    // list applications (yaml app part)
    echo '<div>';
    echo '<h4 class="page-header">'.getLabel("label.users_downtime.tablehead.app").'</h4>';
    echo '<table>';
    createTableHead('','configApp');

    foreach ($yamlFile['app'] as $app) {
        $host=$app['host'];
        $service=$app['service'];
        echo '<tr>';
        echo '<td class="td_line col-md-1 t_host">'.$host.'</td>';
        echo '<td class="td_line col-md-1 t_service">'.$service.'</td>';
        echo '</tr><br/>';
    }
    echo '</table>';
    echo '</div>';

    // list servers (yaml app part)
    echo '<div>';
    echo '<h4 class="page-header">'.getLabel("label.users_downtime.tablehead.hosts").'</h4>';
    echo '<table>';
    createTableHead('','configHosts');

    foreach ($yamlFile['hosts'] as $hosts) {
        $host=$hosts['host'];
        if (isset($hosts['services'])) {
            $service=implode(", ", $hosts['services']);
        } else {
            $service='-';
        }

        if($hosts['propagation_childs']){
            $child='1';
        } else {
            $child='0';
        }

        echo '<tr>';
        echo '<td class="td_line col-md-1 t_host">'.$host.'</td>';
        echo '<td class="td_line col-md-1 t_service">'.$service.'</td>';
        echo '<td class="td_line col-md-1 t_child">'.$child.'</td>';
        echo '</tr><br/>';
    }
    echo '</table>';
    echo '</div>';
}
include_once("footer.php");

?>
