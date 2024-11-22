<?php

namespace App\Lib\Permission\cases;

use App\Lib\Permission\Permission;
use App\Lib\Permission\PermissionManager;

class AdministratorPermission extends PermissionManager
{

    public function __construct()
    {
        parent::__construct();
        $p1 = new Permission("*", "member", "會員權限節點", [
            new Permission("", "view", "檢視", []),
            new Permission("", "create", "建立", []),
            new Permission("", "remove", "刪除", []),
            new Permission("", "update", "更新", []),
        ]);
        $this->addPermissions($this->PermissTreeParser($p1));
        //(new Utils())->pinv($this->getPermissions(), "getPermissions");
        //(new Utils())->pinv($this->hasPermission("member_page_profile_permissions_edit_page_5"), "hasPermission");
    }
}
