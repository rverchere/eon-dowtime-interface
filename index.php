<?php
include("include/header.php");
?>
		<h2 class="page-header"><?php echo getLabel("label.users_downtime.title") ?> </h2>
			<form action="include/downtime.php" method="post">
				<table>
					<?php
						echo createTableList($path_yaml_app_conf);
					?>
				</table>
			</form>

<?php
include("include/footer.php");
?>

