<div id="bmw-tweets-container">
<?php
	//Loop through tweets
	foreach($tweets as $key=>$tweet){
		//Include our loop template
		include('display-loop.php');
	}
?>
</div>