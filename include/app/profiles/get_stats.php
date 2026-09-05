<?php
	$db_query = $pdo->prepare('SELECT AVG(score_percentage) AS avg_score, COUNT(*) AS submissions_total FROM SUBMISSIONS WHERE user_id=:uid');
	$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

	while($row = $db_query->fetch()) {
		$avg_score = isset($row['avg_score']) ? round($row['avg_score'], 1) : 0;
        $submissions_total = isset($row['submissions_total']) ? round($row['submissions_total'], 1) : 0;
	}

	$db_query = $pdo->prepare('SELECT
		DATE(submission_time) as day,
		SUM(score) as daily_points
		FROM SUBMISSIONS
		WHERE user_id = :uid
		AND submission_time >= CURDATE() - INTERVAL 30 DAY
		GROUP BY day
		ORDER BY day ASC;');
	$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);
	$data = $db_query->fetchAll();

	$labels = [];
	$points = [];
    $map = [];
	$current_sum = 0;
	$period = new DatePeriod(
		new DateTime("-30 days"),
		new DateInterval("P1D"),
		new DateTime("+0 day")
	);

	foreach ($data as $row) {
		$map[$row['day']] = $row['daily_points'];
	}

	foreach ($period as $date) {
		$day = $date->format("Y-m-d");
		if (isset($map[$day])) {
			$current_sum += $map[$day];
		}
		$labels[] = $date->format("j M");
		$points[] = $current_sum;
	}
?>