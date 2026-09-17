<?php
	include(__DIR__.'/../../include/app/core.php');

	$allowed_scripts = [
		'register' 		      		=> ['mode' => 'register', 'permission' => 'main.register', 'path' => 'app/user_management/register_new_user.php'],
		'addpost'    		  		=> ['mode' => 'interactive', 'permission' => 'main.portal.addpost', 'path' => 'app/portal/addpost_db.php'],
		'getimg'      		  		=> ['mode' => 'non-interactive', 'permission' => 'main.portal.tinyupload', 'path' => 'app/portal/tinyupload.php'],
		'deletepost'   		  		=> ['mode' => 'interactive', 'permission' => 'main.portal.deletepost', 'path' => 'app/portal/deletepost.php', 'required' => ['id']],
		'modifypost'   		  		=> ['mode' => 'interactive', 'permission' => 'main.portal.modifypost', 'path' => 'app/portal/modifypost_db.php', 'required' => ['id']],
		'send_alg_solution'   		=> ['mode' => 'interactive', 'permission' => 'main.send_alg_solution', 'path' => 'worker/mq_producer.php', 'required' => ['lang', 'pid']],
		'api_get_results'	  		=> ['mode' => 'worker-api', 'permission' => 'main.api_get_results', 'path' => 'worker/api/api_get_results.php'],
		'ask_for_inout'	  	  		=> ['mode' => 'worker-api', 'permission' => 'main.ask_for_inout', 'path' => 'worker/api/api_ask_for_inout.php'],
		'registration_is_unique'	=> ['mode' => 'public', 'permission' => 'main.registration_is_unique', 'path' => '../public/include/registration_is_unique.php'],
		'add_problem'				=> ['mode' => 'interactive', 'permission' => 'main.channels.add_problem', 'path' => 'app/problems/add_problem_script.php'],
		'verify_ctf'				=> ['mode' => 'interactive', 'permission' => 'main.verify_ctf', 'path' => 'app/problems/verify_ctf_script.php'],
		'verify_test'				=> ['mode' => 'interactive', 'permission' => 'main.verify_test', 'path' => 'app/problems/verify_test_anwsers.php'],
		'save_form'					=> ['mode' => 'interactive', 'permission' => 'main.save_form', 'path' => 'app/problems/save_form_script.php'],
		'check_form'				=> ['mode' => 'interactive', 'permission' => 'main.channels.check_form', 'path' => 'app/problems/check_form_script.php'],
		'settings_appearance'		=> ['mode' => 'interactive', 'permission' => 'main.settings_appearance', 'path' => 'app/settings/settings_appearance.php'],
		'modify_content'			=> ['mode' => 'interactive', 'permission' => 'main.portal.modify_content', 'path' => 'app/portal/modify_content.php', 'required' => ['page']],
		'modify_resources'			=> ['mode' => 'interactive', 'permission' => 'main.portal.modify_resources', 'path' => 'app/portal/modify_portal_resources.php'],
		'modify_user'				=> ['mode' => 'interactive', 'permission' => 'main.user_management.modify_user', 'path' => 'app/user_management/modify_user.php', 'required' => ['uid']],
		'create_user'				=> ['mode' => 'interactive', 'permission' => 'main.user_management.create_user', 'path' => 'app/user_management/create_user.php'],
		'remove_user'				=> ['mode' => 'interactive', 'permission' => 'main.user_management.remove_user', 'path' => 'app/user_management/remove_user.php', 'required' => ['uid']],
		'modify_config'				=> ['mode' => 'interactive', 'permission' => 'main.admin.modify_config', 'path' => 'app/config/modify_config.php'],
		'create_channel'			=> ['mode' => 'interactive', 'permission' => 'main.channels.create_channel', 'path' => 'app/channels/create_channel.php'],
		'archive_channel'			=> ['mode' => 'interactive', 'permission' => 'main.channels.archive_channel', 'path' => 'app/channels/archive_channel.php', 'required' => ['cid']],
		'create_problemset'			=> ['mode' => 'interactive', 'permission' => 'main.channels.create_problemset', 'path' => 'app/channels/create_problemset.php', 'required' => ['cid']],
		'modify_problemset'			=> ['mode' => 'interactive', 'permission' => 'main.channels.modify_problemset', 'path' => 'app/channels/modify_problemset.php', 'required' => ['sid']],
		'update_channel_layout'		=> ['mode' => 'non-interactive', 'permission' => 'main.channels.update_channel_layout', 'path' => 'app/channels/update_channel_layout.php', 'required' => ['cid']],
		'download_plugin'			=> ['mode' => 'interactive', 'permission' => 'main.download_plugin', 'path' => 'app/plugins/download_plugin.php'],
		'change_password'			=> ['mode' => 'interactive', 'permission' => 'main.change_password', 'path' => 'app/settings/change_password.php'],
		'diag_server_resources'		=> ['mode' => 'non-interactive', 'permission' => 'main.diag_server_resources', 'path' => 'diagnostics/resources.php'],
		'get_content'				=> ['mode' => 'content-delivery', 'permission' => 'main.get_content', 'path' => 'app/content_delivery/get_content.php'],
	];

	$current_r = $_GET['r'] ?? 'none';
	if(is_logged_in()) check_session_timeout();
	if (!array_key_exists($current_r, $allowed_scripts)) kick();
	$script_config = $allowed_scripts[$current_r];

	switch($script_config['mode'])
	{
		case 'interactive':
			if(!is_logged_in()) kick();
			if(!has_permission($script_config['permission'])) kick();
			print('
				<style>
					html {
						background-color: rgba(39, 55, 71, 1);
					}
				</style>'
			);
			break;
		case 'non-interactive':
			if(!is_logged_in()) kick();
			if(!has_permission($script_config['permission'])) kick();
			break;
		case 'content-delivery':
			break;
		case 'public':
			if(!api_rate_limit_tick(120, 600)) {
				header('HTTP/1.1 429 Too Many Requests');
				die;
			}
			break;
		case 'register':
			if(is_logged_in()) kick();
			break;
		case 'worker-api':
			if(!net_check_if_trusted()) kick();
			break;
		default:
			kick();
			break;
	}

	if (isset($script_config['required'])) {
        foreach ($script_config['required'] as $req) {
            if (!isset($_GET[$req]) && !isset($_POST[$req])) {
                kick();
            }
        }
    }

	include(__DIR__.'/../../include/'.$script_config['path']);
?>