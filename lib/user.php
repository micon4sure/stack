<?php
namespace enork;

class User {
    private $uname;
    private $home;
    private $uber = false;
    private $groups = array();

    public function __construct($uname, array $groups, $home = null) {
        $this->uname = $uname;
        if($home === null)
            $home = "/home/$uname";
        $this->groups = $groups;
        $this->home = $home;
    }

    public function getUname() {
        return $this->uname;
    }

    public function getGroups() {
        return $this->groups;
    }

    public function getHome() {
        return $this->home;
    }
    public function setHome($home) {
        $this->home = $home;
        return $this;
    }

    public function getUber() {
        return $this->uber;
    }
    public function setUber($uber) {
        $this->uber = $uber;
        return $this;
    }
}