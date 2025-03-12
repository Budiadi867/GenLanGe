<?php

header('location: '.base64_decode(urldecode($_GET["url"])));
die();
?>