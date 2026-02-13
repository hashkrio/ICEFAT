<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Control users section of the site.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ]
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'users.list'          => 'Can list users',
        'users.view'          => 'Can view users',
        'users.create'        => 'Can create new non-admin users',
        'users.edit'          => 'Can edit existing non-admin users',
        'users.delete'        => 'Can delete existing non-admin users',
        // 'dashboard.view'      => 'Can view dashboard',        
        // 'material.quantities.list' => 'can list data',
        // 'material.quantities.view' => 'can view data',
        // 'material.quantities.edit' => 'can edit data',
        // 'crate.design.list' => 'can list data',
        // 'crate.design.view' => 'can view data',
        // 'design.regression.list' => 'can list data',
        // 'design.regression.view' => 'can view data',
        // 'design.regression.edit' => 'can edit data',        
        // 'transport.emission.factors.list' => 'can list data',
        // 'transport.emission.factors.view' => 'can view data',
        // 'transport.emission.factors.edit' => 'can edit data',
        // 'crate.type.list' => 'can list data',
        // 'crate.type.edit' => 'can edit data',
        // 'model.option.list' => 'can list data',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*'
        ],
        'admin' => [
            'users.list',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ],
        'user' => [],
    ];
}
