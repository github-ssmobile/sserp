<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Rest {
    function request($url, $method = "GET", $data = NULL, $api = 0, $auth = NULL, $image = 0) {
         $header=array('Ocp-Apim-Subscription-Key: '.$api, 'Content-Type:application/json','Access-Control-Allow-Origin : *');
        if($auth!=NULL){
          $header= array_merge($header,$auth);
        }
        switch ($method) {
            case "GET" :
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                if ($api) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);       
                } else {
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                }
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;
                break;

            case "PUT" :
                $fields_string = "";
                if ($data) {
                    foreach ($data as $key => $value) {
                        $fields_string .= $key . '=' . $value . '&';
                    }
                }
                rtrim($fields_string, '&');
                //die($fields_string);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($api) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);       
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;
                break;

            case "POST" :                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);       
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;

                break;
            
              case "POST_login" :         
                
                $header=array('Content-Type:application/json');
               
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);   
                //curl_setopt($ch, CURLOPT_PORT, 443);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                           
                $data = curl_exec($ch);
//                  $err = curl_error($ch);
//			     $errno = curl_errno($ch);
//			print_r($err);
//			print_r($errno);
                curl_close($ch);
                return $data;

                break;
            
             case "GET_Template" :
                // print_r($auth);die;
                //$header=array("Authentication: Bearer : $auth[0]");
                 $headers = [];
                 $headers = array(
                "Authorization: Bearer {$auth[0]}",
                );
                // print_r($headers);die;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                if ($api) {
//                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//                } else {
//                    curl_setopt($ch, CURLOPT_HEADER, 0);
//                }
                $data = curl_exec($ch);
               
                curl_close($ch);
                return $data;
                break;
            
             case "POST_Template" :                
                $headers = array(
                "Authorization: Bearer {$auth[0]}",
                "Content-Type:application/json"
                // "HTTP_X_MERCHANT_CODE: DOC",        
                );
                
              //  print_r($url);die;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);   
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                           
                $data = curl_exec($ch);
              //  print_r($data);die;
                curl_close($ch);
                return $data;

                break;
            
            case "POST_ACCESSORIES" :                
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);   
                
                //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                           
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;

                break;

            case "DELETE" :
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                if ($api) {
                   curl_setopt($ch, CURLOPT_HTTPHEADER, $header);       
                } else {
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                }
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;
                break;

            case "OTHER" :
                //$fields_string = "";

                $feilds = array();
                if ($data) {
                    foreach ($data as $key => $value) {
                        //$fields_string .= $key.'='.$value.'&'; 
                        $feilds[$key] = $value;
                    }
                }

                $ch = curl_init($url);

                $boundary = " ---------------------" . md5(mt_rand() . microtime());
                $headers = array();
                if ($image) {
                    //die("image set");
                    $filename = $_FILES[$image]['name'];
                    $filedata = $_FILES[$image]['tmp_name'];
                    $filesize = $_FILES[$image]['size'];
                    $headers[] = "Ocp-Apim-Subscription-Key: $api";
                    //$headers[] = "Expect: 100-continue";					 
                    $headers[] = "Content-Type: multipart/form-data; boundary={$boundary}";

                    //$fields_string .= 'image='.'@' . $_FILES['file']['tmp_name'][0]; 
                    //die(print_r($_FILES));
                    $cfile = new CURLFile($_FILES[$image]['tmp_name'], $_FILES[$image]['type'], $_FILES[$image]['name']);
                    $feilds[$image] = $cfile; // '@' . $_FILES[$image]['tmp_name'].";filename=" . $_FILES[$image]['name'];
                }
                unset($_FILES[$image]);
                //die(print_r($feilds));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, count($feilds));

                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                //curl_setopt($ch, CURLOPT_HTTPHEADER , $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $feilds);
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;

                break;

            case "MULTIIMAGE" :
                $feilds = array();

                foreach ($data as $key => $value) {
                    //$fields_string .= $key.'='.$value.'&'; 
                    $feilds[$key] = $value;
                }

                $ch = curl_init($url);

                $boundary = " ---------------------" . md5(mt_rand() . microtime());
                $headers = array();
                if (count($image)) {
                    //die(print_r($image));
                    foreach ($image as $img) {
                        if ($img) {

                            $cfile = new CURLFile($_FILES[$img]['tmp_name'], $_FILES[$img]['type'], $_FILES[$img]['name']);
                            $feilds[$img] = $cfile; //'@' . $_FILES[$img]['tmp_name'].";filename=" . $_FILES[$img]['name'];
                            unset($_FILES[$img]);
                        }
                    }
                }

                //die(print_r($feilds));
                //curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, count($feilds));

                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                //curl_setopt($ch, CURLOPT_HTTPHEADER , $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $feilds);
                $data = curl_exec($ch);
                curl_close($ch);
                //die($data);
                return $data;

                break;
        }
    }
}

?>