<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    public const PERMISSION_INDEX_COMMENT = 'index comment';

    public const PERMISSION_EDIT_USER = 'edit user';

    public const PERMISSION_INDEX_USER = 'index user';

    public const PERMISSION_INDEX_CONTENT = 'index content';

    public const PERMISSION_REPLAY_CONTACT = 'replay contact';

    public const PERMISSION_VISIT_REPORT = 'visit report';

    public const PERMISSION_CHANGE_ROLE = 'change role';

    public static $permissions = [
        self::PERMISSION_CHANGE_ROLE,
        self::PERMISSION_EDIT_USER,
        self::PERMISSION_INDEX_COMMENT,
        self::PERMISSION_INDEX_USER,
        self::PERMISSION_REPLAY_CONTACT,
        self::PERMISSION_VISIT_REPORT,
        self::PERMISSION_INDEX_CONTENT,
    ];
}
