<style> 
	:root {
		--notifications-menu-bg: rgba(49,49,54,0.8);
		--notifications-container-bg: #3e4145;
		--notifications-menu-text: #dae2e6;
	}

	[data-theme="light"] {
		--notifications-menu-bg: rgba(200,200,200,0.8);
		--notifications-container-bg:rgb(229, 229, 229);
		--notifications-menu-text: #313136;
	}

	[data-theme="dark"] {
		--notifications-menu-bg: rgba(49,49,54,0.8);
		--notifications-container-bg: #3e4145;
		--notifications-menu-text: #dae2e6;
	}
	#notifications_menu {
		margin: 0;
		height: 100vh;
		width: 20vw;
		top: 0;
		right: 0;
		display: none;

		z-index: 4;
		position: fixed;
		overflow: auto;

		background-color: var(--notifications-menu-bg);
		animation: notification 1s;
	}

	#notifications_menu a {
		text-decoration: none;
		color: #00b3ff;
		padding: 0.3vmax;
		cursor: pointer;
		transition: 0.2s;
	}
	#notifications_menu a:hover {
		color: white;
		background-color: color: #00b3ff;
	}

	#notifications_menu .category_title {
		margin-top: 2vw;
		margin-left: 1vw;
		user-select: none;

		font-weight: bold;
	}
	
	.notification {
		background-color: var(--notifications-container-bg);
		color: var(--notifications-menu-text);
		border-radius: 0.2vmax;
		padding: 0.5vmax 0.5vmax;
		margin: 0.5vmax;
	}

	.notification span {
		color:rgb(139, 144, 146);
		font-size: 0.7vw;
	}

	.info {
		border-left: 0.3vmax solid rgb(0, 98, 255);
	}
	.success {
		border-left: 0.3vmax solid #00d10a;
	}
	.warning {
		border-left: 0.3vmax solid #ffc117;
	}
	.error {
		border-left: 0.3vmax solid #ff3d6e;
	}

	@keyframes notification {
		from {right: -20vw;}
		to {right: 0vw;}
	}
</style>
<div id="notifications_menu">
	<span style="float: right; font-size: 3vmax; margin-top: 3vw; margin-right: 1vmax; cursor: pointer" onClick="document.getElementById('notifications_menu').style.display = 'none';">×</span>
	<p style="margin-left: 1vw; margin-top: 5vw;">
		<b>Powiadomienia</b>
		<br />
		<span style="font-size: 0.8vw;">użytkownika <?php echo($_SESSION['AUTH_USERNAME']) ?></span>
	</p>
	<br />
	<?php
		$db_query = $pdo->prepare('SELECT * FROM NOTIFICATIONS WHERE user_id=:uid ORDER BY time DESC, is_delivered LIMIT 4');
		$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

		while($notification = $db_query->fetch())
		{
			if($notification['is_delivered']==0) $notification['content'] = '<b>'.$notification['content'].'</b>';
			if($notification['type']=='success')
			{
				echo('<div class="notification success">
				<span>'.$notification['time'].'</span>
				<p>'.$notification['content'].'</p>
				</div>');
			} else if($notification['type']=='warn')
			{
				echo('<div class="notification warning">
				<span>'.$notification['time'].'</span>
				<p>'.$notification['content'].'</p>
				</div>');
			} else if($notification['type']=='error')
			{
				echo('<div class="notification error">
				<span>'.$notification['time'].'</span>
				<p>'.$notification['content'].'</p>
				</div>');
			} else {
				echo('<div class="notification info">
				<span>'.$notification['time'].'</span>
				<p>'.$notification['content'].'</p>
				</div>');
			}
		}

		$db_query = $pdo->prepare('UPDATE NOTIFICATIONS SET is_delivered=1 WHERE user_id=:uid AND is_delivered=0;');
		$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);
	?>
</div>