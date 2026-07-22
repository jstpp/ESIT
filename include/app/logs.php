<style>
	.window #results {
		width: 90%;
		margin-left: 5%;
		user-select: none;
	}
	.window #results td {
		border-top: 0.1vw solid gray;
		padding: 0.5vw 0.5vw;
	}
	.window #results td a {
		font-weight: bold;
		text-align: center;
		transition: 0.3s;
		cursor: pointer;
	}
	.window #results tr {
		transition: 0.2s;
		cursor: default;
	}
	.window #results tr:hover {
		background-color: #2a2c2e;
	}

	.window .forminput, .window .forminput_a {
		border: 0; 
		padding: 1vw 1.5vw; 
		color: var(--text); 
		background-color: var(--container-hover-bg-textbox); 
		font-family: inherit;
		transition: 0.4s;
		outline: none;
	}
	.window .forminput_a:hover {
		background-color: var(--container-hover-bg); 
		cursor: pointer;
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
</style>

<center>
	<h1>Dziennik zdarzeń</h1>
</center>
<div class="window">
	<h2 class="window_title">Dziennik zdarzeń</h2>
	<?php
        $db_query = $pdo->prepare('SELECT * FROM LOGS WHERE category="fatal" OR category="error" OR category="exception" ORDER BY time DESC ');
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
</div>
<br />
<br />