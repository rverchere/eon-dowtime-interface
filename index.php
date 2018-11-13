<?php
include_once("include/header.php");
?>
		<h1 class="page-header"><?php echo getLabel("label.users_downtime.title") ?> </h1>
			<form action="include/downtime.php" method="post">
				<table>
					<?php
						    $confPath = preg_grep('/^([^.])/', scandir($path_yaml_app_conf));
							$fileCount=1;
							$pickerCount=1;
							echo '<table>';
							createTableHead('app','front');
							foreach($confPath as $confFile) {
								$yamlFile=yaml_parse_file($path_yaml_app_conf.'/'.$confFile);
								echo '<td class="td_line col-md-1 t_appname"><h4>'.$yamlFile["displayname"].'</h4></td>';
								echo '<td class="td_line sorting t_desc"><input type="text" name="dwt_desc" class="form-control"/></td>';
								echo '<td class="td_line sorting t_starttime"><b>
										<div class="input-group date startdate" id="datetimepicker'.$fileCount.$pickerCount.'">
											<input type="text" class="form-control" name="startdate" />
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span>
											<script type="text/javascript">
												$(function () { $("#datetimepicker'.$fileCount.$pickerCount.'").datetimepicker(); });
											</script>
										</div>
									</b></td>';
								$pickerCount++;
								echo '<td class="td_line sorting t_endtime"><b>
										<div class="input-group date enddate" id="datetimepicker'.$fileCount.$pickerCount.'">
											<input type="text" class="form-control" name="enddate" />
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span>
											<script type="text/javascript">
												$(function () { $("#datetimepicker'.$fileCount.$pickerCount.'").datetimepicker(); });
											</script>
										</div>
									</b></td>';
								echo '<td><input type="hidden" class="inp_hidden" name="dwt_conf" value="'.$confFile.'"/></td>';
								echo '<td class="td_line  t_actions">';
								echo '<input type="submit" name="dwt_submit" class="btn btn-sm btn-primary dwt_button" value="'.getLabel("label.users_downtime.button.action.valid").'"/>';
								echo '<input type="submit" name="dwt_get" class="btn btn-sm btn-primary dwt_button" value="'.getLabel("label.users_downtime.button.action.get").'"/>';
								echo '<input type="submit" name="dwt_config" class="btn btn-sm btn-primary dwt_button" value="'.getLabel("label.users_downtime.button.action.config").'"/>';
								echo '</td>';
								echo '</tr>';
								$fileCount++;
							}
							echo '</tr>';
							echo '</table>';
					?>
				</table>
			</form>

<?php
include_once("include/footer.php");
?>

