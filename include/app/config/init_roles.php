<?php 
    function load_initial_roles($pdo, $roles_set = null): bool
    {
        $initial_roles = array(
            array(
                'role_name' => 'superadministrator',
                'description' => __('superadministrator').' role with all possible permissions in Your system.',
                'icon' => 'fas fa-globe',
                'color' => '#008109',
                'priority' => 1,
                'permissions' => array(
                    '*'
                )
            ),
            array(
                'role_name' => 'undefined',
                'description' => __('undefined').' role for users that are not logged in.',
                'icon' => 'fas fa-unlock',
                'color' => '#606000',
                'priority' => 999,
                'permissions' => array(
                    'main.register',
                    'main.registration_is_unique',
                    'main.get_content'
                )
            ),
            array(
                'role_name' => 'user',
                'description' => __('user').' role for users that are logged in.',
                'icon' => 'fas fa-user-circle',
                'color' => '#727272',
                'priority' => 90,
                'permissions' => array(
                    'main.send_alg_solution',
                    'main.verify_*',
                    'main.settings_appearance',
                    'main.change_password',
                    'main.get_content',
                    'main.display.dashboard',
                    'main.display.channels',
                    'main.display.mysolutions',
                    'main.display.user_settings',
                    'main.display.algresult.user',
                    'main.display.testresult.user',
                    'main.display.ctfresult.user',
                    'main.display.formresult.user',
                    'main.display.problem',
                    'main.display.channel',
                    'main.social.star_content',
                    'main.social.follow_channel'
                )
            ),
            array(
                'role_name' => 'administrator',
                'description' => __('administrator').' role with most of possible permissions in Your system.',
                'icon' => 'fas fa-wrench',
                'color' => '#002581',
                'priority' => 5,
                'permissions' => array(
                    '*'
                )
            ),
            array(
                'role_name' => 'moderator',
                'description' => __('moderator').' is a person responsible for content/community discourse moderation.',
                'icon' => 'fa fa-institution',
                'color' => '#004d81',
                'priority' => 10,
                'permissions' => array(
                    'main.user_management.*',
                    'main.display.admin.configuration',
                    'main.display.admin.logs',
                    'main.display.all_resources',
                    'main.solutions.exec.*'
                )
            ),
            array(
                'role_name' => 'portal_editor',
                'description' => __('portal_editor').' creates posts and can set up all portal parameters.',
                'icon' => 'fas fa-feather',
                'color' => '#814700',
                'priority' => 15,
                'permissions' => array(
                    'main.portal.*',
                    'main.display.portal.*'
                )
            ),
            array(
                'role_name' => 'content_creator',
                'description' => __('content_creator').' is able to create channels and problems.',
                'icon' => 'fas fa-microphone',
                'color' => '#810000',
                'priority' => 50,
                'permissions' => array(
                    'main.channels.*',
                    'main.display.channels.*',
                    'main.solutions.exec.silent'
                )
            ),
        );

        $roles_set = $roles_set ?? $initial_roles;

        try {

            $pdo->beginTransaction();

            $db_query_roles = $pdo->prepare('INSERT INTO ROLES (role_name, description, icon, color, priority) VALUES (:role_name, :description, :icon, :color, :priority)');
            $db_query_permissions = $pdo->prepare('INSERT INTO PERMISSIONS (role_id, permission) VALUES (:rid, :permission)');

            foreach($roles_set as $role)
            {
                $db_query_roles->execute(['role_name' => $role['role_name'],
                                    'description' => $role['description'],
                                    'icon' => $role['icon'],
                                    'color' => $role['color'],
                                    'priority' => $role['priority']]);
                $role_id = $pdo->lastInsertId();

                foreach($role['permissions'] as $permission) {
                    $db_query_permissions->execute(['rid' => $role_id, 'permission' => $permission]);
                }
            }

            $pdo->commit();
            return True;

        } catch (Throwable $t) {

            if ($pdo->inTransaction()) $pdo->rollBack();
            extended_exception_handler($t);
            return False;

        }
    }

?>