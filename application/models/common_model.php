<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class common_model extends CI_Model {
    /* Function For Getting Records From Table Start */

    public function getRecords($table, $fields = '', $condition = '', $order_by = '', $limit = '', $debug = 0) {
        $str_sql = '';
        if (is_array($fields)) {  #$fields passed as array
            $str_sql .= implode(",", $fields);
        } elseif ($fields != "") {   #$fields passed as string
            $str_sql .= $fields;
        } else {
            $str_sql .= '*';  #$fields passed blank
        }
        
        $this->db->select($str_sql, FALSE);
        if (is_array($condition)) {  #$condition passed as array
            if (count($condition) > 0) {
                foreach ($condition as $field_name => $field_value) {
                    if ($field_name != '' && $field_value != '') {
                        $this->db->where($field_name, $field_value);
                    }
                }
            }
        } else if ($condition != "") { #$condition passed as string
            $this->db->where($condition);
        }
        if ($limit != "")
            $this->db->limit($limit);#limit is not blank

        if (is_array($order_by)) {
            $this->db->order_by($order_by[0], $order_by[1]);  #$order_by is not blank
        } else if ($order_by != "") {
            $this->db->order_by($order_by);  #$order_by is not blank
        }
        $this->db->from($table);
        $query = $this->db->get();
        if ($debug) {
            die($this->db->last_query());
        }
        return $query->result_array();
    }

    /* Function For Getting Records From Table End */


    public function getRecordswithoffset($table, $fields = '', $condition = '', $order_by = '', $limit = '', $debug = 0,$offset='',$search=array()) {
        $str_sql = '';
        if (is_array($fields)) {  #$fields passed as array
            $str_sql .= implode(",", $fields);
        } elseif ($fields != "") {   #$fields passed as string
            $str_sql .= $fields;
        } else {
            $str_sql .= '*';  #$fields passed blank
        }
        $this->db->select($str_sql, FALSE);
        if (is_array($condition)) {  #$condition passed as array
            if (count($condition) > 0) {
                foreach ($condition as $field_name => $field_value) {
                    if ($field_name != '' && $field_value != '') {
                        $this->db->where($field_name, $field_value);
                    }
                }
            }
        } else if ($condition != "") { #$condition passed as string
            $this->db->where($condition);
        }
        if(!empty($search)){
            foreach ($search as $field_name => $field_value) {
                if ($field_name != '' && $field_value != '') {
                    $this->db->like($field_name, $field_value);
                }
            }
        }
        if ($limit != "")
            $this->db->limit($limit,$offset);#limit is not blank

        if (is_array($order_by)) {
            $this->db->order_by($order_by[0], $order_by[1]);  #$order_by is not blank
        } else if ($order_by != "") {
            $this->db->order_by($order_by);  #$order_by is not blank
        }
        $this->db->from($table);
        $query = $this->db->get();
        if ($debug) {
            die($this->db->last_query());
        }
        return $query->result_array();
    }

    /* Function For Inserting Record In Table Start */
    public function insertRow($insert_data, $table_name) {
        $this->db->insert($table_name, $insert_data);
        return $this->db->insert_id();
    }

    /* Function For Update Record In Table Start */

    public function updateRow($table_name, $update_data, $condition) {
        if (is_array($condition)) {
            if (count($condition) > 0) {
                foreach ($condition as $field_name => $field_value) {
                    if ($field_name != '' && $field_value != '') {
                        $this->db->where($field_name, $field_value);
                    }
                }
            }
        } else if ($condition != "") {
            $this->db->where($condition);
        }
        $this->db->update($table_name, $update_data);

        return TRUE;
    }

    /* Function For Delete Record In Table Start */
    public function deleteRows($arr_delete_array, $table_name, $field_name) {
        if (count($arr_delete_array) > 0) {
            foreach ($arr_delete_array as $id) {
                $this->db->where($field_name, $id);
                $query = $this->db->delete($table_name);
            }
        }
        return true;
    }



    /* Function For Abosulute Path  Start */

    public function absolutePath($path = '') {
        $abs_path = str_replace('system/', $path, BASEPATH);
        $abs_path = preg_replace("#([^/])/*$#", "\\1/", $abs_path);
        return $abs_path;
    }

    /* Function For Abosulute Path End */

    function daysAgo($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function encrypt_decrypt($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'Company_secret_key';
        $secret_iv = 'A';
        $key = hash('sha256', $secret_key);

        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    public function getSingleRow($tablename, $condition,$select_array='*')
    {
        $this->db->select($select_array)
        ->from($tablename);
        if($condition!='')
        {
            $this->db->where($condition);
        }
        $userDetails=$this->db->get();
        return $userDetails->row_array();
    }

}
?>
