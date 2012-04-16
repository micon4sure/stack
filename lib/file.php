<?php
namespace enork;

class File {
    private $path;
    private $owner;
    private $permissions;

    public function __construct($path, $owner, array $permissions) {
        $this->path = $path;
        $this->owner = $owner;
        $this->permissions = $permissions;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getPath() {
        return $this->path;
    }

    public function getPermissions() {
        return $this->permissions;
    }
}