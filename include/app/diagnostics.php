<?php
    include(__DIR__ . '/../../include/diagnostics/resources.php');
    include(__DIR__ . '/../../include/diagnostics/configuration.php');
?>
<style>
    .window p, h2, h3 {
        margin-left: 5%;
    }
    .pulse {
        animation: pulse-animation 1.8s infinite;
    }
    @keyframes pulse-animation {
        0% {
            box-shadow: 0 0 0 0vw rgba(0, 0, 0, 0.2);
        }
        100% {
            box-shadow: 0 0 0.2vw 0.8vw rgba(0, 0, 0, 0);
        }
    }
    .circle {
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        box-shadow: 0px 0px 1px 1px #0000001a;
    }

    .diag_error .circle, .diag_fatal .circle {
        background: red;
    }
    .diag_warning .circle, .diag_exception .circle {
        background: orange;
    }
    .diag_info .circle {
        background: var(--highlight-color);
    }
    
    .diagnostics_feedback {
        width: 86%;
        background: var(--container-hover-bg);
        margin-left: 5%;
        margin-top: 0.5vw;
        padding: 1% 2%;
        align-items: center;
        justify-content: left;
        display: flex;
        flex-direction: column;
        border-radius: 10px;
    }

    .diagnostics_details {
        background: var(--text);
        color: var(--bg);
        width: calc(100% - 1vmax);
        padding: 1vmax;
        border-radius: 10px;
    }

    #workers_list {
        display: grid;
        grid-template-columns: 33% 33% 33%;
        margin-left: calc(5% - 0.5vw);
        width: calc(90% + 1vw);
    }
    #workers_list .workers_instance {
        background: var(--container-hover-bg);
        padding: 1vw 1vw;
        border-radius: 10px;
        margin: 0.5vw;
    }
    .display_extension {
        padding: 0.1vmax 0.5vmax;
        margin: 0.3vmax;
        border-radius: 0.5vmax;
        border: 0.1vmax solid var(--text);
        line-height: 1.75;
        user-select: none;
    }

    .network_health_device_active {
        background-color: var(--container-hover-bg);
        border-radius: 0.5vmax;
        border-top: 0.5vmax solid #009b3b;
        padding: 0.5vmax 1vmax;
    }

    .network_health_device_inactive {
        background-color: var(--container-hover-bg);
        border-radius: 0.5vmax;
        border-top: 0.5vmax solid #545554;
        padding: 0.5vmax 1vmax;
    }
</style>
<script src="/include/js/chart.js/chart.js" type="text/javascript"></script>

<center>
    <h1><?php echo(__("Diagnostics panel")); ?></h1>
</center>
<div class="window">
    <h2 class="window_title"><?php echo(__("System resources")); ?></h2>
    <div>
        <p><?php echo(__("OS details")); ?>: <?php echo(php_uname()); ?></p>
        <p>CPU: <?php echo(getNumOfCPUs()); ?> <?php echo(__("cores")); ?></p>
        <p>RAM: <?php print(round(getMemory()['MemTotal'] / 1024 / 1024, 2)); ?> GB</p>
        <br />
    </div>
    <div style="display: flex; width: 90%; margin-left: 5%; gap: 5%;">
        <div style="width: 50%;">
            <h3 style="margin: 0;"><?php echo(__("RAM memory usage")); ?> [%]</h3>
            <br />
            <canvas id="memory_usage_chart"></canvas>
        </div>
        <div style="width: 50%;">
            <h3 style="margin: 0;"><?php echo(__("CPU usage")); ?> [%]</h3>
            <br />
            <canvas id="cpu_usage_chart"></canvas>
        </div>
    </div>
    <br />
    <br />
    <p><?php echo(__("Loaded PHP extensions")); ?>:
        <div style="width: 90%; margin-left: 5%; display: flex; flex-wrap: wrap;">
            <?php
            foreach (get_loaded_extensions() as $extension) {
                print("<span class='display_extension'>" . htmlentities($extension) . "</span>");
            }
            ?>
        </div>
    </p>
    <script>
        cpu_usage_data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        cpu_usage_chart = new Chart(document.getElementById('cpu_usage_chart'), {
        type: "line",
        data: {
            labels: [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
            datasets: [{ 
            data: cpu_usage_data,
            borderColor: "rgba(0, 179, 255, 1)",
            pointRadius: 2,
            tension: 0.4,
            fill: true,
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            y: {
                suggestedMin: 0,
                suggestedMax: (<?php echo(getCPUUsage()); ?>+10)
            }
        }
        });

        memory_usage_data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        memory_usage_chart = new Chart(document.getElementById('memory_usage_chart'), {
        type: "line",
        data: {
            labels: [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
            datasets: [{ 
            data: memory_usage_data,
            borderColor: "rgba(0, 179, 255, 1)",
            pointRadius: 2,
            tension: 0.4,
            fill: true,
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
        });
    </script>
</div>
<script>
    async function update_stats() {
        const response = await fetch('process.php?r=diag_server_resources&call=getall');
        const data = await response.json();
        for (let i = 0; i < 10; i++)
        {
            memory_usage_data[i] = memory_usage_data[i+1];
            cpu_usage_data[i] = cpu_usage_data[i+1];
        }
        memory_usage_data[10] = Math.round((parseFloat(data.value.memory.MemTotal) - parseFloat(data.value.memory.MemFree))/parseFloat(data.value.memory.MemTotal) * 1000)/10;
        memory_usage_chart.update();
        cpu_usage_data[10] = parseInt(data.value.cpuusage);
        cpu_usage_chart.update();
    }
    update_stats();
    setInterval(update_stats, 1000);
</script>
<div class="window">
    <h2 class="window_title"><?php echo(__("Network health")); ?></h2>
    <?php
        $db_query = $pdo->query("SELECT DISTINCT ip_address FROM NETWORK_LOG ORDER BY ip_address ASC");
        while ($row = $db_query->fetch()) {
            echo('<h3>' . htmlentities($row['ip_address']) . '</h3>');

            $sql = "
                    SELECT n.device_name, n.device_type,
                        n.cpu_total, n.cpu_free,
                        n.mem_total, n.mem_free,
                        n.heartbeat,
                        (n.timestamp > NOW() - INTERVAL (n.heartbeat + 5) SECOND) AS is_active
                    FROM NETWORK_LOG n
                    INNER JOIN (
                        SELECT device_name, device_type, MAX(LOG_ID) AS max_id
                        FROM NETWORK_LOG
                        WHERE ip_address = :ip_addr
                        GROUP BY device_name, device_type
                    ) latest ON n.LOG_ID = latest.max_id
                    ORDER BY n.device_name ASC
                ";
            $xdb_query = $pdo->prepare($sql);
            $xdb_query->execute(['ip_addr' => $row['ip_address']]);

            $active = [];
            $inactive = [];
            while ($xrow = $xdb_query->fetch()) {
                if ($xrow['is_active']) {
                    $active[] = $xrow;
                } else {
                    $inactive[] = $xrow;
                }
            }

            echo('<div style="display: grid; gap: 0.5vmax; margin-left: 5%; width: 90%; grid-template-columns: auto auto auto;">');
            foreach ($active as $xrow) {
                echo("<div class='network_health_device network_health_device_active'>");
                echo("<b>" . htmlentities($xrow['device_name']) . "</b><br />");
                echo("<small>" . htmlentities($xrow['device_type']) . "</small><br />");
                echo("<br />");
                echo("<small>CPUs: " . htmlentities($xrow['cpu_total']) . " (" . htmlentities(round($xrow['cpu_free'] / $xrow['cpu_total'] * 100, 2)) . "% free)</small><br />");
                echo("<small>Memory: " . htmlentities($xrow['mem_total']) . "MB (" . htmlentities($xrow['mem_free']) . "MB free)</small><br />");
                echo("<small>Heartbeat: " . htmlentities($xrow['heartbeat']) . " seconds</small><br />");
                echo("</div>");
            }
            echo('</div>');

            echo('<div style="display: grid; gap: 0.5vmax; margin-top: 0.5vmax; margin-left: 5%; width: 90%; grid-template-columns: auto auto auto;">');
            foreach ($inactive as $xrow) {
                echo("<div class='network_health_device network_health_device_inactive'>");
                echo("<b>" . htmlentities($xrow['device_name']) . "</b><br />");
                echo("<small>" . htmlentities($xrow['device_type']) . "</small><br />");
                echo("</div>");
            }
            echo('</div>');
        }
    ?>
    <br style="clear: both;" />
</div>
<div class="window">
    <h2 class="window_title"><?php echo(__("Summary")); ?></h2>
    <p><small><?php echo(__("The summary shows the results of superficial tests.")); ?></small></p>
    <h3 class="window_title"><?php echo(__("Configuration")); ?></h3>
    <?php
        $count = 0;
    foreach (configuration_diagnostics() as $row) {
        $count++;
        echo('<div class="diagnostics_feedback diag_' . htmlentities($row['category']) . '" style="display: flex; flex-direction: row;">
                <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">' . $row['content'] . '</p>
            </div>');
    }
    if ($count == 0) {
        echo('<div class="diagnostics_feedback diag_info" style="flex-direction: row;">
                <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">' . __("Yay! There is no issues at the moment!") . '</p>
            </div>');
    }
    ?>
    <br />
    <h3 class="window_title"><?php echo(__("Event logs")); ?></h3>
    <?php
        $db_query = $pdo->prepare('SELECT * FROM LOGS WHERE category="fatal" OR category="error" OR category="exception" ORDER BY time DESC LIMIT 3');
        $db_query->execute();

        $count = 0;
    while ($row = $db_query->fetch()) {
        $count++;
        echo('<div class="diagnostics_feedback diag_' . htmlentities($row['category']) . '">
                <div style="display: flex; flex-direction: row; width: 100%; align-items: center;">
                    <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">' . $row['content'] . '</p>
                </div>
                <code class="diagnostics_details">
                ' . htmlentities($row['time']) . ' 
                ' . htmlentities($row['details']) . '
                </code>
            </div>');
    }
    if ($count == 0) {
        echo('<div class="diagnostics_feedback diag_info" style="flex-direction: row;">
                <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">' . __("Yay! There is no issues at the moment!") . '</p>
            </div>');
    }
    ?>
    <br />
    <a class="button" style="margin-right: 5%;" href="index.php?p=logs"><?php echo(__("Display all")); ?></a>
    <br style="clear: both;"/>
    <br />
    <br />
</div>
<?php
    include_plugins_for("diagnostics");
?>
<br />
<br />
<br />