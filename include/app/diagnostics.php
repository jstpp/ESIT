<?php 
    include(__DIR__.'/../../include/diagnostics/resources.php');
    include(__DIR__.'/../../include/diagnostics/configuration.php');
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
        background: rgb(0, 179, 255);
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
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<center>
	<h1>Panel diagnostyczny</h1>
</center>
<div class="window">
	<h2 class="window_title">Zasoby systemowe</h2>
    <p>System operacyjny: <?php echo(php_uname()); ?></p>
    <p>Procesor CPU: <?php echo(getNumOfCPUs()); ?> rdzeni</p>
    <p>Pamięć RAM: <?php print(round(getMemory()['MemTotal']/1024/1024,2)); ?> GB</p>
    <div style="display: flex; width: 90%; margin-left: 5%; gap: 5%;">
        <div style="width: 50%;">
            <h3 style="margin: 0;">Zużycie pamięci [%]</h3>
            <br />
            <canvas id="memory_usage_chart"></canvas>
        </div>
        <div style="width: 50%;">
            <h3 style="margin: 0;">Zużycie procesora [%]</h3>
            <br />
            <canvas id="cpu_usage_chart"></canvas>
        </div>
    </div>
    <br />
    <script>
        cpu_usage_data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        cpu_usage_chart = new Chart(document.getElementById('cpu_usage_chart'), {
        type: "line",
        data: {
            labels: [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
            datasets: [{ 
            data: cpu_usage_data,
            borderColor: "rgb(0, 179, 255)",
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
            borderColor: "rgb(0, 179, 255)",
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
	<h2 class="window_title">Podsumowanie</h2>
    <p><small>Podsumowanie przedstawia wyniki powierzchownych testów.</p>
    <h3 class="window_title">Konfiguracja</h3>
    <?php
        $count = 0;
        foreach(configuration_diagnostics() as $row)
        {
            $count++;
            echo('<div class="diagnostics_feedback diag_'.htmlentities($row['category']).'" style="display: flex; flex-direction: row;">
                <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">'.$row['content'].'</p>
            </div>');
        }
        if($count==0)
        {
            echo('<div class="diagnostics_feedback diag_info" style="flex-direction: row;">
                <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">Hurra! Nie ma zdarzeń wartych Twojej uwagi.</p>
            </div>');
        }
    ?>
    <br />
    <h3 class="window_title">Dziennik zdarzeń</h3>
    <?php
        $db_query = $pdo->prepare('SELECT * FROM LOGS WHERE category="fatal" OR category="error" OR category="exception" ORDER BY time DESC LIMIT 3');
        $db_query->execute();

        $count = 0;
        while($row = $db_query->fetch())
        {
            $count++;
            echo('<div class="diagnostics_feedback diag_'.htmlentities($row['category']).'">
                <div style="display: flex; flex-direction: row; width: 100%; align-items: center;">
                    <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">'.$row['content'].'</p>
                </div>
                <code class="diagnostics_details">
                '.htmlentities($row['time']).' 
                '.htmlentities($row['details']).'
                </code>
            </div>');
        }
        if($count==0)
        {
            echo('<div class="diagnostics_feedback diag_info" style="flex-direction: row;">
                <div class="circle pulse" style="width: 1vw; height: 1vw;"></div><p style="margin-left: 3%;">Hurra! Nie ma zdarzeń wartych Twojej uwagi.</p>
            </div>');
        }
    ?>
    <br />
    <a class="button" style="margin-right: 5%;" href="index.php?p=logs">Zobacz wszystkie</a>
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