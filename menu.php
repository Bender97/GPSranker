



<table id="menu_table">
	<tr>
		<td>
			<?php
				echo "<span id='greeting'>Ciao ".$_SESSION['name']."!</span>";
			?>
		</td>
		<td rowspan="2">
		</td>
		<td rowspan="2">
			<button type="button" onclick="getLocation(init, undefined);" class="cancelbtn">Update</button>
		</td>
	</tr>
	<tr>
		<td>
			<a id="logout" href='logout.php'>Logout</a>
		</td>
	</tr>
</table>

