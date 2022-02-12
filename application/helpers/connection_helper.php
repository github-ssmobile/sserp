<?php
function is_connected()
{
    $connected = @fsockopen("www.google.com", 80); 
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;
}
