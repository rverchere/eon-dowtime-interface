
		<h2 class="page-header"><?php echo getLabel("label.users_downtime.title") ?> </h2>
		<div>
			<table>
				<?php
					echo createTableList($path_yaml_app_conf);
				?>
			</table>
				<input type="submit" name="s_add" value="Valider" onclick="if(document.getElementById('a_phone').value!="") document.submit();"/>
		</div>


