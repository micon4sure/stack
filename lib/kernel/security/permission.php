<?php
namespace stackos\kernel\security;

class Permission {
    /**
     * @var string he type of holder [user/group]
     */
    private $holderType;
    private $holder;
    private $priviledge;

    public function __construct($holder, $priviledge, $holderType) {
        $this->holder = $holder;
        $this->priviledge = $priviledge;
        $this->holderType = $holderType;
    }

    public function getHolder() {
        return $this->holder;
    }
    public function getPriviledge() {
        return $this->priviledge;
    }
    public function getHolderType() {
        return $this->holderType;
    }
}

class Permission_Group extends Permission {
    public function __construct($holder, $priviledge) {
        parent::__construct($holder, $priviledge, Strategy::PERMISSION_HOLDER_TYPE_GROUP);
    }
}
class Permission_User extends Permission {
    public function __construct($holder, $priviledge) {
        parent::__construct($holder, $priviledge, Strategy::PERMISSION_HOLDER_TYPE_USER);
    }
}