<?php
	include(__DIR__.'/../../include/app/core.php');

	$allowed_scripts = [
		'register' 		      		=> ['mode' => 'register', 'priority' => 0, 'path' => 'app/user_management/register_new_user.php'],
		'addpost'    		  		=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/portal/addpost_db.php'],
		'getimg'      		  		=> ['mode' => 'non-interactive', 'priority' => 3, 'path' => 'app/portal/tinyupload.php'],
		'deletepost'   		  		=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/portal/deletepost.php', 'required' => ['id']],
		'modifypost'   		  		=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/portal/modifypost_db.php', 'required' => ['id']],
		'send_alg_solution'   		=> ['mode' => 'interactive', 'priority' => 0, 'path' => 'worker/mq_producer.php', 'required' => ['lang', 'pid']],
		'api_get_results'	  		=> ['mode' => 'worker-api', 'priority' => 0, 'path' => 'worker/api/api_get_results.php'],
		'ask_for_inout'	  	  		=> ['mode' => 'worker-api', 'priority' => 0, 'path' => 'worker/api/api_ask_for_inout.php'],
		'registration_is_unique'	=> ['mode' => 'public', 'priority' => 0, 'path' => 'portal/registration_is_unique.php'],
		'add_problem'				=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/problems/add_problem_script.php'],
		'verify_ctf'				=> ['mode' => 'interactive', 'priority' => 0, 'path' => 'app/problems/verify_ctf_script.php'],
		'verify_test'				=> ['mode' => 'interactive', 'priority' => 0, 'path' => 'app/problems/verify_test_anwsers.php'],
		'save_form'					=> ['mode' => 'interactive', 'priority' => 0, 'path' => 'app/problems/save_form_script.php'],
		'check_form'				=> ['mode' => 'interactive', 'priority' => 4, 'path' => 'app/problems/check_form_script.php'],
		'settings_appearance'		=> ['mode' => 'interactive', 'priority' => 0, 'path' => 'app/settings/settings_appearance.php'],
		'modify_content'			=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/portal/modify_content.php'],
		'modify_resources'			=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'portal/modify_portal_resources.php'],
		'modify_user'				=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/user_management/modify_user.php', 'required' => ['uid']],
		'create_user'				=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/user_management/create_user.php'],
		'remove_user'				=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/user_management/remove_user.php', 'required' => ['uid']],
		'modify_config'				=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/config/modify_config.php'],
		'create_channel'			=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/channels/create_channel.php'],
		'archive_channel'			=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/channels/archive_channel.php', 'required' => ['cid']],
		'create_problemset'			=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/channels/create_problemset.php', 'required' => ['cid']],
		'modify_problemset'			=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/channels/modify_problemset.php', 'required' => ['sid']],
		'update_channel_layout'		=> ['mode' => 'non-interactive', 'priority' => 3, 'path' => 'app/channels/update_channel_layout.php', 'required' => ['cid']],
		'download_plugin'			=> ['mode' => 'interactive', 'priority' => 3, 'path' => 'app/plugins/download_plugin.php'],
		'change_password'			=> ['mode' => 'interactive', 'priority' => 0, 'path' => 'app/settings/change_password.php'],
		'diag_server_resources'		=> ['mode' => 'non-interactive', 'priority' => 3, 'path' => 'diagnostics/resources.php'],
		'get_content'				=> ['mode' => 'content-delivery', 'priority' => 0, 'path' => 'app/content_delivery/get_content.php'],
	];

	$current_r = $_GET['r'] ?? 'none';
	if(is_logged_in()) check_session_timeout();
	if (!array_key_exists($current_r, $allowed_scripts)) kick();
	$script_config = $allowed_scripts[$current_r];

	switch($script_config['mode'])
	{
		case 'interactive':
			if(!is_logged_in()) kick();
			if($script_config['priority'] > 0 && !has_a_priority($script_config['priority'])) kick();
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
			if($script_config['priority'] > 0 && !has_a_priority($script_config['priority'])) kick();
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