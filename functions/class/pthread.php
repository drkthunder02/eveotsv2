<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

namespace EVEOTS\Threads;

class BuildAllianceNames extends Thread {
    private $first;
    private $last;
    
    public function __construct($f, $l) {
        $this->first = $f;
        $this->last = $l;
    }
    
    public function run() {
        
    }    
}

class BuildCorporationIds extends Thread {
    private $first;
    private $last;
    
    public function __construct($f, $l) {
        $this->first = $f;
        $this->last = $l;
    }
    
    public function run() {
        
    }
}

class BuildCorporationNames extends Thread {
    private $first;
    private $last;
    
    public function __construct($f, $l) {
        $this->first = $f;
        $this->last = $l;
    }
    
    public function run() {
        
    }
}

class CheckAlliances extends Thread {
    private $first;
    private $last;
    
    public function __construct($f, $l) {
        $this->first = $f;
        $this->last = $l;
    }
    
    public function run() {
        
    }
}

class CheckCorporations extends Thread {
    private $first;
    private $last;
    
    public function __construct($f, $l) {
        $this->first = $f;
        $this->last = $l;
    }
    
    public function run() {
        
    }
}

class CheckCharacters extends Thread {
    private $first;
    private $last;
    
    public function __construct($f, $l) {
        $this->first = $f;
        $this->last = $l;
    }
    
    public function run() {
        
    }
}

