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

function createTableList($yamlConfPath){
    $confPath = preg_grep('/^([^.])/', scandir($yamlConfPath));
    $fileCount=1;
    $pickerCount=1;
    echo '<tr class="tr_head">';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.app").'</td>';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.desc").'</td>';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.starttime").'</td>';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.endtime").'</td>';
    echo '<td class="td_head"></td>';
    echo '</tr>';
    foreach($confPath as $confFile) {
        $yamlFile=yaml_parse_file($yamlConfPath.'/'.$confFile);
        echo '<td class=td_line>'.$yamlFile["displayname"].'</td>';
        echo '<td class=td_line><input type="text" name="dwt_desc" class="dwt_desc"/></td>';
        echo '<td class=td_line><b>
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
        echo '<td class=td_line><b>
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
        echo '<td class=td_line><input type="submit" name="dwt_submit" class="dwt_submit" value="validate"/></td>';
        echo '<td class=td_line><input type="submit" name="dwt_get" class="dwt_get" value="get"/></td>';
        echo '</tr>';
        $fileCount++;
    }
    echo '</tr>';
}

function thrukCurl($ch) {
    $cookies = "user_name=admin; session_id=169014757; user_id=1; group_id=1; user_limitation=0";
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
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

?>
