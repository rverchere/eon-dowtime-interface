<?php
include("classes/Translator.class.php")

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

function createTableList($yamlConfPath) {
    $confPath = scandir($yamlConfPath);

    echo '<tr class=tr_head>'
    echo '<td>'.getLabel(label.users_downtime.tablehead.app).'</td>'
    echo '<td>'.getLabel(label.users_downtime.tablehead.desc).'</td>'
    echo '<td>'.getLabel(label.users_downtime.tablehead.starttime).'</td>'
    echo '<td>'.getLabel(label.users_downtime.tablehead.endtime).'</td>'
    echo '<td></td>'
    echo '</tr>'
}


function thrukGetDowntimes($server) {
    $cookies = "user_name=admin; session_id=169014757; user_id=1; group_id=1; user_limitation=0";
    $ch = curl_init('https://'.$server.'/thruk/r/downtimes');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $output = curl_exec($ch);
    $rcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($rcode == 200) {
        return $output;
    } else {
        return"ERROR\n";
    }
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
    if($details =='') {
        $fields = [
            'comment_data' => 'PHP Test',
            'comment_author' => 'Me'
        ];
    }

    $cookies = "user_name=admin; session_id=169014757; user_id=1; group_id=1; user_limitation=0";
    $url = 'https://'.$server.'/thruk/r/services/'.$hostname.'/'.$servicename.'/cmd/schedule_svc_downtime';
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);

    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $output = curl_exec($ch);
    $rcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($rcode == 200) {
        return $output;
    } else {
        return"ERROR\n";
    }
}

?>
