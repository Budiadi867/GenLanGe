<?php 
$link1_url = urlencode(base64_encode("https://fbhhhg.naughtymets.com/s/5f54849de4bb0?track=BMKG&subsource=BMKGoneBMKGoneBMKG"));
$link2_url = urlencode(base64_encode("https://fbhhhg.trackingclik.com/s/5fbb6b3e65283?track=Ndey3nng&subsource=Ndey3nng&ext_click_id=Ndey3nng"));
$link3_url = urlencode(base64_encode("https://chatdatlng.biz.id/p/random/public/redirect.php"));
$fpvalue  = $_GET['u']; 
if ($fpvalue == '02') {
    header("Location: i.php?url='.$link1_url.'", true, 302); 
}

else if ($fpvalue == '03') {
    header("Location: i.php?url='.$link2_url.'", true, 302); 
}   

else if ($fpvalue == '04') {
    header("Location: i.php?url='.$link3_url.'", true, 302); 
}?>