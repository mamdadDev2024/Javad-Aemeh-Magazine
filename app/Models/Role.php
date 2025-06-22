<?php

namespace App\Models;

use Spatie\Permission\Models\Role as ModelsRole;
use App\Models\Permission;

class Role extends ModelsRole
{
    public const USER_ROLE = 'user';
    public const SUPER_ADMIN_ROLE = 'super admin';
    public const ADMIN_ROLE = 'admin';

    public static $roles = [
        self::USER_ROLE => [
            Permission::PERMISSION_EDIT_USER,
        ],
        self::ADMIN_ROLE => [
            Permission::PERMISSION_INDEX_COMMENT,
            Permission::PERMISSION_REPLAY_CONTACT,
            Permission::PERMISSION_VISIT_REPORT,
        ],
        self::SUPER_ADMIN_ROLE => [
            Permission::PERMISSION_INDEX_CONTENT,
            Permission::PERMISSION_INDEX_USER,
            Permission::PERMISSION_INDEX_COMMENT,
            Permission::PERMISSION_REPLAY_CONTACT,
            Permission::PERMISSION_VISIT_REPORT,
        ]
    ];
}
