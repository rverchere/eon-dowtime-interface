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

function add_dwtm() {

}

if ($_POST['d_add']) {

}
?>
