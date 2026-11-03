<?php
	if(has_permission('main.channels.create_channel'))
	{
		echo('
<div id="new_channel_pane" style="display: none; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 3;">
	<span onClick="document.getElementById(\'new_channel_pane\').style.display = \'none\';" style="font-size: 4.5vmax; float: right; margin-right: 2vw; cursor: pointer; position: fixed; top: 3vmax; right: 0;">×</span>
	<div style="background-color: #dae2e6; color: black; width: 30vmax; max-height: 80vh; padding: 1vmax 1vmax; border-radius: 0.2vmax;">
		<h2 style="text-align: center;">'.__("Add new channel").'</h2>
		<br />
		<form method="POST" id="new_channel_form" action="process.php?r=create_channel" enctype="multipart/form-data">
			<input name="channel_setname" class="forminput_2" type="text" placeholder="'.__("Channel name").'*" onChange="validate_data_f();" required/>
			<br />
			<textarea name="channel_description" class="forminput_2" placeholder="'.__("Channel description").'*" onChange="validate_data_f();" required></textarea>
			<br />
			<br />
			'.__("Channel publish time").'*:
			<input name="channel_publish_time" class="forminput_2" type="datetime-local" onChange="validate_data_f();" required/>
			<br />
			<br />
			'.__("Thumbnail").':
			<input name="channel_set_img" id="set_img" class="forminput_2" type="file" onChange="validate_data_f();"/>
			<br />
			<br />
			<input name="channel_isactive" id="isactive" type="checkbox" value="1" onChange="validate_data_f();" checked> 
			<label for="isactive">'.__("Is active?").'</label>
		</form>
		<br />
		<a class="button" id="channel_button_1" onClick="document.getElementById(\'new_channel_form\').submit();" style="display: none; margin-right: 1%; margin-bottom: 1%;"><i class="fa fa-plus"></i>&nbsp;'.__("Add channel").'</a>
		<br style="clear: both;"/>
		<fieldset id="r_i_validation" style="margin-top: 2vmax;">
            <legend>'.__("Data validation").'</legend>
            <p class="data-invalid" id="ch_1">'.__("Fill all required textboxes").' (*)</p>
            <script>
                let f = 0;
                function check_data_f()
                {
                    if(f>0)
                    {
                        document.getElementById(\'channel_button_1\').style.display = \'block\';
                    } else {
                        document.getElementById(\'channel_button_1\').style.display = \'none\';
                    }
                }

                function validate_data_f()
                {
                    f = 0;
                    if(document.querySelector(\'input[name="channel_setname"]\').value.length>0 && document.querySelector(\'textarea[name="channel_description"]\').value.length>0 && document.querySelector(\'input[name="channel_publish_time"]\').value.length>0) 
					{
                        f++;
                        document.getElementById(\'ch_1\').classList.add("data-valid");
                        document.getElementById(\'ch_1\').classList.remove("data-invalid");
                    } else {
                        document.getElementById(\'ch_1\').classList.add("data-invalid");
                        document.getElementById(\'ch_1\').classList.remove("data-valid");
                    }
                    check_data_f();
                }
            </script>
        </fieldset>
		<br style="clear: both;"/>
	</div>
</div>');
	}
?>