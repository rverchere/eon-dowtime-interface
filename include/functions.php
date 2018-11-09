<?php
include_once("classes/Translator.class.php");
include_once("config.php");

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

function createTableList($yamlConfPath){
    $confPath = preg_grep('/^([^.])/', scandir($yamlConfPath));
    $fileCount=1;
    $pickerCount=1;
    echo '<tr class="tr_head">';
    echo '<th class="th_head col-md-1 t_appname">'.getLabel("label.users_downtime.tablehead.app").'</th>';
    echo '<th class="th_head sorting t_desc">'.getLabel("label.users_downtime.tablehead.desc").'</th>';
    echo '<th class="th_head sorting t_starttime">'.getLabel("label.users_downtime.tablehead.starttime").'</th>';
    echo '<th class="th_head sorting t_endtime">'.getLabel("label.users_downtime.tablehead.endtime").'</th>';
    echo '<th></th>';
    echo '<th class="th_head t_actions"></th>';
    echo '</tr>';
    foreach($confPath as $confFile) {
        $yamlFile=yaml_parse_file($yamlConfPath.'/'.$confFile);
        echo '<td class="td_line col-md-1 t_appname">'.$yamlFile["displayname"].'</td>';
        echo '<td class="td_line sorting t_desc"><input type="text" name="dwt_desc" class="form-control"/></td>';
        echo '<td class="td_line sorting t_starttime"><b>
                <div class="input-group date startdate" id="datetimepicker'.$fileCount.$pickerCount.'">
                    <input type="text" class="form-control" name="startdate" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    <script type="text/javascript">
                        $(function () { $("#datetimepicker'.$fileCount.$pickerCount.'").datetimepicker(); });
                    </script>
                </div>
            </b></td>';
        $pickerCount++;
        echo '<td class="td_line sorting t_endtime"><b>
                <div class="input-group date enddate" id="datetimepicker'.$fileCount.$pickerCount.'">
                    <input type="text" class="form-control" name="enddate" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    <script type="text/javascript">
                        $(function () { $("#datetimepicker'.$fileCount.$pickerCount.'").datetimepicker(); });
                    </script>
                </div>
            </b></td>';
        echo '<td><input type="hidden" class="inp_hidden" name="dwt_conf" value="'.$confFile.'"/></td>';
        echo '<td class="td_line  t_actions">';
        echo '<input type="submit" name="dwt_submit" class="btn btn-sm btn-primary dwt_button" value="'.getLabel("label.users_downtime.button.action.valid").'"/>';
        echo '<input type="submit" name="dwt_get" class="btn btn-sm btn-primary dwt_button" value="'.getLabel("label.users_downtime.button.action.get").'"/>';
        echo '<input type="submit" name="dwt_config" class="btn btn-sm btn-primary dwt_button" value="'.getLabel("label.users_downtime.button.action.config").'"/>';
        echo '</td>';
        echo '</tr>';
        $fileCount++;
    }
    echo '</tr>';
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
    $dt = new DateTime("@$epoch");
    return $dt->format('Y-m-d H:i:s');
}

?>
