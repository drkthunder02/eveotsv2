<?php

namespace Custom\Session;

class Sessions {
    //The database object in order to store the session data in a mysql database
    private $db;
    
    public function __construct(){
        $config = parse_ini_file('/../database/config.ini');
        
        //Setup our db object
        $this->db = new \Simplon\Mysql\Mysql(
            $config['server'],
            $config['username'],
            $config['password'],
            $config['database']
        );
        
        // Set handler to overide SESSION
        session_set_save_handler(
            array($this, "_open"),
            array($this, "_close"),
            array($this, "_read"),
            array($this, "_write"),
            array($this, "_destroy"),
            array($this, "_gc")
        );
        
        session_start();
    }
    
    public function _open() {
        if($this->db) {
            return true;
        } else {
            return false;
        }
    }
    
    public function _close() {
        if($this->db->close()) {
            return true;
        } else {
            return false;
        }
        
    }
    
    public function _read($id) {
        $row = $this->db->fetchRow('SELECT data FROM sessions WHERE id= :id', array('id' => $id));
        
        if($row) {
            return $row;
        } else {
            return '';
        }
    }
    
    public function _write($id, $data){
        // Create time stamp
        $access = time();
        
        $result = $this->db->replace('sessions', array('id' => $id, 'access' => $access, 'data' => $data));
        
        if($result) {
            return true;
        } else {
            return false;
        }
    }
    
    public function _destroy($id){
        $result = $this->db->delete('sessions', array('id' => $id));
    
        if($result) {
            return true;
        } else {
            return false;
        }
    }    
    
    public function _gc($max) {
        $old = time() - $max;
        
        // custom conditions query
        $condsQuery = 'access < :old';
        $result = $this->db->delete('sessions', array('old' => $old), $condsQuery);
        
        if($result) {
            return true;
        } else {
            return false;
        }
       
    }
    
}
