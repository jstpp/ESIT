<?php 
	include_once(__DIR__.'/../../../include/app/profiles/get_stats.php');
?>
<style>
    #profile_background_pane {
		position: fixed;
		top: 0;
		left: 0;
		margin: 0;
		padding: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		background-color: rgba(0,0,0,0.5);
		z-index: 3;
		width: 100%;
		height: 100vh;
	}
	#profile_content_pane {
		width: 30vmax;
		padding: 1vmax;
		background: var(--bg);
		color: var(--text);
		border-radius: 0.5vmax;
	}
	.profile_stats_box {
		display: flex;
		margin-top: 2vmax;
	}
	.profile_stats_box > div {
		flex: 1;
		width: 33.3%;
		text-align: center;
	}
	.profile_stats_box > div h2 {
		margin-top: 0.5vmax;
	}
</style>
<div id="profile_background_pane" style="display: none;" onClick="this.style.display = 'none';">
	<div id="profile_content_pane">
		<br />
		<center>
			<img src="https://api.dicebear.com/10.x/identicon/svg?seed=<?php echo($_SESSION['AUTH_USERNAME']) ?>" style="width: 5vmax; background-color: var(--text); border-radius: 2.5vmax;" />
			<h3 style="text-align: center; margin-bottom: 0;"><?php echo($_SESSION['AUTH_NAME']) ?> <?php echo($_SESSION['AUTH_SURNAME']) ?></h3>
			<small style="text-align: center;">@<?php echo($_SESSION['AUTH_USERNAME']) ?></small>
		</center>
		<div class="profile_stats_box">
			<div>
				<?php echo(__("Accuracy")); ?>
				<h2><?php echo($avg_score); ?>%</h2>
			</div>
			<div>
				<?php echo(__("Solutions amount")); ?>
				<h2><?php echo($submissions_total); ?></h2>
			</div>
			<div>
				<?php echo(__("Total points")); ?>
				<h2><?php echo($current_sum); ?></h2>
			</div>
		</div>
	</div>
</div>