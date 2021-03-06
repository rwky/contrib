#!/usr/bin/php
# Author Andrey Kozhokaru <andrey@kozhokaru.com>
# Plugin to monitor Rackspace CloudFile storage usage
#
# Parameters:
#
# 	config   (required)
#
#
#%# family=manual

<?php
$x_auth_user='###NAME';
$x_auth_key='###KEY';
$api_url='https://auth.api.rackspacecloud.com/v1.0/';

function SplitTwice($content,$first,$second) {
        $s1=split($first,$content);
        $tokens=split($second,$s1[1]);
        return trim($tokens[0]);
    }


if ($argv[1]=='config'){
    print "graph_title Rackspace CDN storage usage\n";
    print "graph_vlabel CDN storage usage\n";
    print "graph_category cloud\n";
    print "usage.label storage usage\n";
    print "graph_args --base 1024\n";

    exit;
    }

$header_auth = array("X-Auth-User:$x_auth_user","X-Auth-Key:$x_auth_key");

//Authenticate
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $api_url);
   curl_setopt($ch, CURLOPT_HEADER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $header_auth);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
   $data = curl_exec($ch);
   curl_close($ch);


$cdn_url= SplitTwice($data,'X-Storage-Url: ','Cache');
$token= SplitTwice ($data,'X-Auth-Token:','X-Storage-Token:');


$header_cdn = array ("X-Auth-Token:$token");

//Get data
   $ch1 = curl_init();
   curl_setopt($ch1, CURLOPT_URL, $cdn_url);
   curl_setopt($ch1, CURLOPT_HEADER, true);
   curl_setopt($ch1, CURLOPT_HTTPHEADER, $header_cdn);
   curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 30);
   $data1 = curl_exec($ch1);
   curl_close($ch1);

$objects_bytes_used = SplitTwice ($data1,'X-Account-Bytes-Used:','X-Account-Container-Count:');

echo 'usage.value '.$objects_bytes_used;

?>
