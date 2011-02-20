<?php
/**
 * Switch your config to a different campaign
 *
 * @author Mitchell Amihod
 */

$UI = new UI(getenv('DIALOG'));

$retval = $api->campaigns();
$oopsy->go($api->errorCode, $api->errorMessage, 'Unable to get list of Campaign!');

//pull out campaign info, prep it for TM 
$collector = array();
foreach($retval['data'] as $campaign){
    $temp = '{title="%s";campaign_id="%s";}';
    $collector[] = sprintf($temp, $campaign['title'], $campaign['id']);
}

$response = $UI->menu($collector);

if(empty($response)) {
    exit( 'Cancelled.');
}

$xml = new SimpleXMLElement($response);

for ($i=0; $i < count($xml->dict->key); $i++) { 
    if('campaign_id' == $xml->dict->key[$i]) {
        $config->campaign_id = $xml->dict->string[$i];
    }
}

$config->save();