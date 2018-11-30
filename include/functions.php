<?php
include("classes/Translator.class.php");

function getLabel($reference){
    global $dictionnary;
    global $path_messages;
    global $path_messages_custom;
    global $t;
    // Load dictionnary if not isset
    if(!isset($t)) {
            $t = new Translator();
            $t->initFile($path_messages,$path_messages_custom);
            $dictionnary = $t->createPHPDictionnary();
    }
    // Display dictionnary reference if isset or reference
    if(isset($dictionnary[$reference])) {
            $label = $dictionnary[$reference];
    }
    else {
            $label = $reference;
    }
    return $label;
}

function thrukCurl($ch) {
    global $eon_cookies;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIE, $eon_cookies);
    $output = curl_exec($ch);
    $rcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($rcode == 200) {
        return json_decode($output, true);
    }
}

function thrukGetHost($server, $hostname) {
    $ch = curl_init('https://'.$server.'/thruk/r/hosts/'.$hostname);
    return thrukCurl($ch);
}

function thrukGetService($server, $hostname, $service) {
    $ch = curl_init('https://'.$server.'/thruk/r/services/'.$hostname.'/'.$service);
    return thrukCurl($ch);
}

function thrukGetDowntimes($server) {
    $ch = curl_init('https://'.$server.'/thruk/r/downtimes');
    return thrukCurl($ch);
}

function thrukGetServiceDowntime($server, $servername, $servicename) {
    $ch = curl_init('https://'.$server.'/thruk/r/downtimes');
    $results = thrukCurl($ch);
    $services = [];
    foreach ($results as $result) {
        if (($result['host_name'] == $servername) && ($result['service_description']) == $servicename) {
            array_push($services, $result);
        }
    }
    return($services);
}

function thrukGetHostDowntime($server, $servername) {
    return thrukGetServiceDowntime($server, $servername, '');
}

function thrukSetDowntime($server, $hostname, $servicename, $details) {
    /*
    * Details are downtime values:
    * - comment_data: downtime comment (required)
    * - start_time (optionnal)
    * - end_time (optionnal)
    * - fixed (optionnal)
    * - triggered_by (optionnal)
    * - duration (optionnal)
    * - comment_author (optionnal)
    */
    if($details == '') {
        $details = [
            'comment_data' => 'PHP Test',
            'comment_author' => 'Me'
        ];
    }

    if ($servicename != '') {
        // if service name is defined, set downtime for this service
        $url = 'https://'.$server.'/thruk/r/services/'.$hostname.'/'.$servicename.'/cmd/schedule_svc_downtime';
    } else {
        // if not, set downtime to the server itself
        $url = 'https://'.$server.'/thruk/r/hosts/'.$hostname.'/cmd/schedule_host_downtime';
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $details);
    return thrukCurl($ch);
}

function eonGetDowntimes($server, $filters = []) {
    /*
    EON API REQUEST
    POST https://localhost/eonapi/listNagiosObjects?&username=admin&apiKey=xxxx
    {
    "object": "downtimes",
    "columns": ["host_name", "service_description", "comment", "entry_time", "start_time", "end_time"],
    "backendid": "0",
    "filters": [
    "host_name = Applications_Building",
    "service_description = Test-int-downtime"
    ]
    }
     */
    global $eon_username;
    global $eon_apikey;
    $details = array(
        "object" => "downtimes",
        "columns" => ["host_name",
                    "service_description",
                    "comment",
                    "entry_time",
                    "start_time",
                    "end_time"],
        "backendid"=> "0",
        "filters" => $filters
    );
    $url ='https://'.$server.'/eonapi/listNagiosObjects?&username='.$eon_username.'&apiKey='.$eon_apikey;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($details));
    $output = curl_exec($ch);
    $rcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($rcode == 200) {
        return json_decode($output, true);
    }
}

function eonGetServerDowntime($server, $servername) {
    $filters = [
        "host_name = ".$servername.""
    ];
    return eonGetDowntimes($server, $filters);
}

function eonGetServiceDowntime($server, $servername, $servicename) {
    $filters = [
        "host_name = ".$servername."",
        "service_description = ".$servicename.""
    ];
    return eonGetDowntimes($server, $filters);
}

function epochToDateTime($epoch) {
    $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
    $dt->setTimeZone(new DateTimeZone($_ENV["TZ"]));
    return $dt->format('Y-m-d H:i:s O'); // output = 2017-01-01 00:00:00
}

function createTableHead($type,$tableID) {
    echo '<tr class="tr_head">';
    if ($type == 'app')
    {
        echo '<th class="th_head col-md-1 t_appname th_col-start">'.getLabel("label.users_downtime.tablehead.app").'</th>';
        echo '<th class="th_head sorting t_desc">'.getLabel("label.users_downtime.tablehead.desc").'</th>';
        switch ($tableID)
        {
            case 'front':
                echo '<th class="th_head sorting t_starttime">'.getLabel("label.users_downtime.tablehead.starttime").'</th>';
                echo '<th class="th_head sorting t_endtime">'.getLabel("label.users_downtime.tablehead.endtime").'</th>';
                echo '<th></th>';
                echo '<th class="th_head t_actions th_col-end"></th>';
                break;
            case 'submit':
                echo '<th class="th_head sorting t_starttime">'.getLabel("label.users_downtime.tablehead.starttime").'</th>';
                echo '<th class="th_head sorting t_endtime">'.getLabel("label.users_downtime.tablehead.endtime").'</th>';
                echo '<th class="th_head sorting ts_status th_col-end">'.getLabel("label.users_downtime.tablehead.status").'</th>';
                break;
            case 'get':
                echo '<th class="th_head sorting tv_entrytime">'.getLabel("label.users_downtime.tablehead.entrytime").'</th>';
                echo '<th class="th_head sorting tv_starttime">'.getLabel("label.users_downtime.tablehead.starttime").'</th>';
                echo '<th class="th_head sorting tv_endtime th_col-end">'.getLabel("label.users_downtime.tablehead.endtime").'</th>';
                break;
        }
    }
    if ($type == 'host')
    {
        echo '<th class="th_head col-md-1 tv_host th_col-start">'.getLabel("label.users_downtime.tablehead.host").'</th>';
        echo '<th class="th_head sorting tv_service">'.getLabel("label.users_downtime.tablehead.service").'</th>';
        echo '<th class="th_head sorting tv_desc">'.getLabel("label.users_downtime.tablehead.desc").'</th>';
        echo '<th class="th_head sorting tv_starttime">'.getLabel("label.users_downtime.tablehead.starttime").'</th>';
        echo '<th class="th_head sorting tv_endtime">'.getLabel("label.users_downtime.tablehead.endtime").'</th>';
        echo '<th class="th_head sorting tv_status th_col-end">'.getLabel("label.users_downtime.tablehead.status").'</th>';
    }
    if ($tableID == 'configApp' OR $tableID == 'configHosts' )
    {
        echo '<th class="th_head sorting t_host th_col-start">'.getLabel("label.users_downtime.tablehead.host").'</th>';
        echo '<th class="th_head sorting t_service th_col-end">'.getLabel("label.users_downtime.tablehead.service").'</th>';
        if ($tableID == 'configHosts')
        {
            echo '<th class="th_head sorting t_child th_col-end">'.getLabel("label.users_downtime.tablehead.child").'</th>';
        }
    }

    echo '</tr>';
}

?>
