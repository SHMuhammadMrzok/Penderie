<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Rebuild_encryption_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*public function rebuild_encryption($encrypted_data)
    {
        foreach($encrypted_data as $table=>$fields)
        {
            $this->db->select(implode(',', $fields).',id,unix_time');
            $table_data = $this->db->get($table);
            
            if($table_data)
            {
                $table_data = $table_data->result();
                
                foreach($table_data as $row)
                {
                    foreach($fields as $field)
                    {
                        $decrypted_data   = $this->decrypt_table_data_with_old_key($row->$field, $row->unix_time);
                        
                        $data_array       = array(
                                                   'data'            => $decrypted_data ,
                                                   'data_id'         => $row->id        ,
                                                   'table'           => $table          ,
                                                   'field'           => $field          ,
                                                   'field_unix_time' => $row->unix_time ,
                                                   'unix_time'       => time()
                                                );
                                           
                        $this->db->insert('rebuild_encryption',$data_array);
                        
                    }
                }
            }  
        }
        
        $this->encrypt_fields_with_new_key();
    }*/
    
    public function decrypt_table_data_with_old_key($encrypted_data,$unix_time)
    {
        $secret_iv        = md5($unix_time);
        $old_key          = $this->config->item('old_encryption_key');
        $decrypted_data   = $this->encryption->decrypt($encrypted_data,$old_key,$secret_iv);
        
        return $decrypted_data;
    }
    
    
    public function decrypt_rows($table, $fields, $limit)
    {
            $this->db->select(implode(',', $fields).',id,unix_time');
            $this->db->where('decrypt_value','0');
            $this->db->where('encrypt_value','0');
            $table_data = $this->db->get($table, $limit);
            
            if($table_data)
            {
                $table_data = $table_data->result();
                
                foreach($table_data as $row)
                {
                    foreach($fields as $field)
                    {
                        $decrypted_data = $this->decrypt_table_data_with_old_key($row->$field,$row->unix_time);
                        
                        if($decrypted_data)
                        { 
                            $insert_data    = array(
                                                     'data'            => $decrypted_data ,
                                                     'data_id'         => $row->id,
                                                     'table'           => $table,
                                                     'field'           => $field ,
                                                     'field_unix_time' => $row->unix_time ,
                                                     'unix_time'       => time()
                                                   ); 
                            $this->insert_rebuild_encryption($insert_data);
                                                   
                            //$this->db->insert('rebuild_encryption',$insert_data);
                            
                            //update decrypt_value to be 1
                            $update_decrypt_value = array('decrypt_value'=>'1');
                            
                            $this->db->where('id',$row->id);
                            $this->db->update($table,$update_decrypt_value);
                        }
                    }
                    
                }
                
                
                return true; 
            }
            else
            {
                return false;
            }
    }
    
    public function encrypt_fields_with_new_key($encrypted_fields, $limit)
    {
        foreach($encrypted_fields as $table=>$fields)
        {
            $count_unencrypted_rows = $this->count_table_unencrypted_rows($table);
            
            if($count_unencrypted_rows > 0)
            {
                $this->db->where('encrypt_value', 0);
                $table_data = $this->db->get($table, $limit);
                
                if($table_data)
                {
                    foreach($table_data->result() as $table_row)
                    {
                        $this->db->where('table', $table);
                        $this->db->where('data_id', $table_row->id);
                        $rebuild_data = $this->db->get('rebuild_encryption');
                        
                        if($rebuild_data)
                        {
                            $rebuild_data = $rebuild_data->result();
                            
                            foreach($rebuild_data as $row)
                            {
                                $new_key    = $this->config->item('new_encryption_key');
                                $secret_iv  = md5($row->field_unix_time);    
                                $encrypted_data_with_new_key = $this->encryption->encrypt($row->data,$new_key,$secret_iv);
                                
                                $data = array($row->field => $encrypted_data_with_new_key);
                                
                                $where = array('id' => $row->data_id);
                                $this->update_table($row->table, $data, $where);
                                
                                $where = array('id'=>$row->id);
                                $this->delete_table('rebuild_encryption', $where);   
                            }
                        }
                        
                        //update encrypt_value to be 1
                        $data  = array('encrypt_value'=>'1');
                        $where = array('id'=>$row->data_id);
                        $this->update_table($row->table, $data, $where);
                    }
                }  
                  
                return true;
            }
            else
            {
                return false;
            }
            
        }
        
        //$rebuild_data = $this->db->get('rebuild_encryption',$limit);
        
        
    }
    
    public function insert_rebuild_encryption($data_array)
    {
        $this->db->insert('rebuild_encryption',$data_array);
        return true;
    }
    
    public function count_rebuild_table_rows()
    {
        return $this->db->count_all('rebuild_encryption');
    }
    
    public function check_count($field,$value,$table)
    {
        $this->db->where($field,$value);
        return $this->db->count_all_results($table);
        
    }
    
    public function reset_encryption_values($encrypted_fields)
    {
        foreach($encrypted_fields as $table => $fields)
        {
            $data = array('decrypt_value' => 0 , 'encrypt_value' => 0 );
            $this->db->update($table,$data);
        }
    }
    
    public function get_all_rows_count($encrypted_fields)
    {
        $count = 0;
        
        foreach($encrypted_fields as $table=>$fields)
        {
            $count += $this->db->count_all($table);
        }
        
        return $count;
    }
    
    public function get_decrepted_rows($encrypted_fields) 
    {
        $count = 0;
        
        foreach($encrypted_fields as $table=>$fields)
        {
            $this->db->where('decrypt_value','1');
            $count += $this->db->count_all_results($table);
        }
        
        return $count;
    }
    
    public function count_all_encrypted_rows($encrypted_fields)
    {
        $count = 0;
        
        foreach($encrypted_fields as $table=>$fields)
        {
            $this->db->where('encrypt_value',1);
            $count += $this->db->count_all_results($table);
        }
        
        return $count;
    }
    
    public function count_table_unencrypted_rows($table)
    {
        $this->db->where('encrypt_value', 0);
        return $this->db->count_all_results($table);
    }
    
    public function update_table($table, $data=array(), $where=array())
    {
        foreach($where as $field=>$value)
        {
            $this->db->where($field , $value);
        }
        
        return $this->db->update($table, $data);
    }
    
    public function delete_table($table, $where)
    {
        foreach($where as $field=>$value)
        {
            $this->db->where($field , $value);
        }
    
        return $this->db->delete($table);
    }
    
    public function test_decryption($encryption_fields)
    {
        foreach($encryption_fields as $table=>$fields)
        {
            $query = $this->db->get($table, 1);
            
            if($query)
            {
                $row       = $query->row();
                $key       = $this->config->item('old_encryption_key');
                $secret_iv = md5($row->unix_time);
                
                $output = $this->encryption->decrypt($row->$fields[0], $key, $secret_iv);

                if(!empty($output))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
            
        }
    }
    
/****************************************************************/
}