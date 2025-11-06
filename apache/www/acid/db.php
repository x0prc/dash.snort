<?php
require_once 'acid_config.php';
require_once ADODB_PATH . 'adodb.inc.php';

function getAlerts() {
    $db = ADONewConnection('mysqli');
    $db->Connect(DBHOST, DBUSER, DBPASS, DBNAME);
    $results = $db->Execute('SELECT timestamp, signature, src_ip, dst_ip FROM event ORDER BY timestamp DESC LIMIT 50');
    $alerts = [];
    while(!$results->EOF) {
        $alerts[] = $results->fields;
        $results->MoveNext();
    }
    return $alerts;
}
?>
