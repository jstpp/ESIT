<?php
	include(__DIR__.'/../../include/app/core.php');

	if(isset($_GET['r']))
	{
		if($_GET['r']=="register")
		{
			if(is_logged_in()) kick();
			include(__DIR__.'/../../include/app/user_management/register_new_user.php');
		} else if($_GET['r']=="addpost")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/portal/addpost_db.php');
		} else if($_GET['r']=="getimg")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/portal/tinyupload.php');
		} else if($_GET['r']=="deletepost" and isset($_GET['id']))
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/portal/deletepost.php');
		} else if($_GET['r']=="modifypost" and isset($_GET['id']))
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/portal/modifypost_db.php');
		} else if($_GET['r']=="send_alg_solution" and isset($_POST['lang']) and isset($_GET['pid']))
		{
			if(!is_logged_in()) force_to_login();
			include(__DIR__.'/../../include/worker/mq_producer.php');
		} else if($_GET['r']=="api_get_results")
		{
			if(!net_check_if_trusted()) kick();
			include(__DIR__.'/../../include/worker/api/api_get_results.php');
		} else if($_GET['r']=="ask_for_inout")
		{
			if(!net_check_if_trusted()) kick();
			include(__DIR__.'/../../include/worker/api/api_ask_for_inout.php');
		} else if($_GET['r']=="registration_is_unique")
		{
			include(__DIR__.'/../../include/portal/registration_is_unique.php');
		} else if($_GET['r']=="add_problem")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/problems/add_problem_script.php');
		} else if($_GET['r']=="verify_ctf")
		{
			if(!is_logged_in()) force_to_login();
			include(__DIR__.'/../../include/app/problems/verify_ctf_script.php');
		} else if($_GET['r']=="verify_test")
		{
			if(!is_logged_in()) force_to_login();
			include(__DIR__.'/../../include/app/problems/verify_test_anwsers.php');
		} else if($_GET['r']=="save_form")
		{
			if(!is_logged_in()) force_to_login();
			include(__DIR__.'/../../include/app/problems/save_form_script.php');
		} else if($_GET['r']=="check_form")
		{
			if(!is_logged_in()) force_to_login();
			include(__DIR__.'/../../include/app/problems/check_form_script.php');
		} else if($_GET['r']=="settings_appearance")
		{
			if(!is_logged_in()) force_to_login();
			include(__DIR__.'/../../include/app/settings/settings_appearance.php');
		} else if($_GET['r']=="modify_content")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/portal/modify_content.php');
		} else if($_GET['r']=="modify_resources")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/portal/modify_portal_resources.php');
		} else if($_GET['r']=="modify_user")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/user_management/modify_user.php');
		} else if($_GET['r']=="create_user")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/user_management/create_user.php');
		} else if($_GET['r']=="remove_user")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/user_management/remove_user.php');
		} else if($_GET['r']=="modify_config")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/config/modify_config.php');
		} else if($_GET['r']=="create_set")
		{
			if(!is_logged_in()) force_to_login();
			if(!has_a_priority(3)) kick();
			include(__DIR__.'/../../include/app/sets/create_set.php');
		} else if($_GET['r']=="change_password")
		{
			if(!is_logged_in()) force_to_login();
			include(__DIR__.'/../../include/app/settings/change_password.php');
		} else if($_GET['r']=="get_content")
		{
			include(__DIR__.'/../../include/app/content_delivery/get_content.php');
		} else {
			echo("Not found.");
			kick();
		}

		if(is_logged_in()) check_session_timeout();
	}
?>