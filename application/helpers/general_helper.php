<?php 

function clean($string) {
   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function sendEmail($recipient,$subject,$message,$cc)
{
    $CI =& get_instance();
        $config = Array(
          'protocol' => 'smtp',
          'smtp_host' => 'smtp.gmail.com',
          'smtp_port' => 465,
          'smtp_user' => 'vg.gonjari@gmail.com', // change it to yours
          'smtp_pass' => 'vinu1909', // change it to yours
          'mailtype' => 'html',
//          'charset' => 'iso-8859-1',
          'smtp_crypto' => 'ssl',
          'wordwrap' => TRUE,
          'starttls'  => true,
          'newline' => '\r\n' //use double quotes to comply with RFC 822 standard
        );

        $CI->load->library('email', $config);
        $CI->email->initialize($config);
        $CI->email->set_newline("\r\n");
        $CI->email->from('vg.gonjari@gmail.com'); // change it to yours
        $CI->email->to($recipient); // change it to yours
        $CI->email->cc($cc); // change it to yours
        $CI->email->subject($subject);
        $CI->email->message($message);
        if($CI->email->send())
        {
            echo 'Email sent successfully !!!';
//            return redirect('MY_Controller/user_dashboard');
        }
        else
        {
            show_error($CI->email->print_debugger());
            return redirect('MY_Controller/user_dashboard');
        }
}




function sendEmail1($recipient,$subject,$message,$cc)
{
    $CI =& get_instance();
        $config = Array(
          'protocol' => 'smtp',
          'smtp_host' => 'ssl://smtp.googlemail.com',
          'smtp_port' => 465,
          'smtp_user' => 'vg.gonjari@gmail.com', // change it to yours
          'smtp_pass' => 'vinu1909', // change it to yours
          'mailtype' => 'html',
          'charset' => 'iso-8859-1',
//          'smtp_crypto' => 'tls',
          'wordwrap' => TRUE,
          'newline' => '\r\n' //use double quotes to comply with RFC 822 standard
        );

        $CI->load->library('email', $config);
        $CI->email->initialize($config);
        $CI->email->set_newline("\r\n");
        $CI->email->from('vg.gonjari@gmail.com'); // change it to yours
        $CI->email->to($recipient); // change it to yours
        $CI->email->cc($cc); // change it to yours
        $CI->email->subject($subject);
        $CI->email->message($message);
        if($CI->email->send())
        {
            echo 'Email sent successfully !!!';
//            return redirect('MY_Controller/user_dashboard');
        }
        else
        {
            show_error($CI->email->print_debugger());
            return redirect('MY_Controller/user_dashboard');
        }
}
function multi_array_search($array, $search)
  {

    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value)
    {

      // Iterate over each search condition
      foreach ($search as $k => $v)
      {

        // If the array element does not meet the search condition then continue to the next element
        if (!isset($value->$k) || $value->$k != $v)
        {
          continue 2;
        }

      }

      // Add the array element's key to the result array
      $result[] = $key;

    }

    // Return the result array
    return $result;

  }
  function multi_arraysearch($array, $search)
  {
    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value)
    {        
      // Iterate over each search condition
      foreach ($search as $k => $v)
      {        
        // If the array element does not meet the search condition then continue to the next element
        if (!isset($value[$k]) || $value[$k] != $v)
        {
          continue 2;
        }

      }
      // Add the array element's key to the result array
      $result[] = $key;

    }

    // Return the result array
    return $result;

  }
  function multi_array_sum($array, $search)
  {

    // Create the result array
    $result = 0;

    // Iterate over each array element
    foreach ($array as $key => $value)
    {

      // Iterate over each search condition
      foreach ($search as $k => $v)
      {

        // If the array element does not meet the search condition then continue to the next element
        if (!isset($value->$k) || $value->$k != $v)
        {
          continue 2;
        }

      }

      // Add the array element's key to the result array
      $result += $array[$key]->allocated_qty;

    }

    // Return the result array
    return $result;

  }
  function find_menu($array, $search)
  {    
    $result = array();   
    foreach ($array as $data)
    {
      foreach ($data['submenu'] as $c_data)
      {
        if($c_data->url==$search){
            $result[] = 1;
        }
      }
    }
    return $result;

  }
  function multiarraysearch($array, $k,$v)
  {
    $result = '';    
    foreach ($array as $key => $value)
    {        
        if (isset($value[$k]) && $value[$k] == $v)
        {
            
            $result = $key;
            break;
        }
    }    
    return $result;
  }
  
  
  