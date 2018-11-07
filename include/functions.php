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

    echo '<tr class="tr_head">';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.app").'</td>';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.desc").'</td>';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.starttime").'</td>';
    echo '<td class="td_head">'.getLabel("label.users_downtime.tablehead.endtime").'</td>';
    echo '<td class="td_head"></td>';
    echo '</tr>';
    foreach($confPath as $confFile) {
        $yamlFile=yaml_parse_file($confPath.'/'.$confFile);
        echo '<td class=td_line>'.$parsed["app"].'</td>';
        echo '<td class=td_line><input type="text" name="dwt_desc" class="dwt_desc"/></td>';
        echo '<td class=td_line><b>
                <input type="text" name="dwt_starttime" class="dwt_starttime" value="" data-cip-id="dwt_starttime"/>
                <a href="javascript:show cal("dwt_starttime")"/>
            </b></td>';
        echo '<td class=td_line><b>
                <input type="text" name="dwt_endtime" class="dwt_endtime" value=""/>
                <script type="text/javascript">
                $(function datetimepicker() {
                    $("#datetimepicker2").datetimepicker({
                        locale: "'.getLabel("label._lang").'"
                    });
                });
                </script>
            </b></td>';
        echo '<td class=td_line><input type="submit" name="dwt_submit" class="dwt_submit" value="validate"/></td>';
        echo '</tr>';
    }
    echo '</tr>';
}
?>
