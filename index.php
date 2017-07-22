<?php
require_once("kanban_logic.php");

$kanban = new kanban("kanban");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<style type="text/css">
	div.single_task {
		width:35%;
		cursor:move;
	}
	
	div.single_task_head {
		min-height:20px;
		font-weight:bold;
		text-aling:center;
		background-color:black;
		opacity:0.5;
		padding:5px;
		width:100%
	}
	
	div.single_task_body {
		height:100px;
		width:100%;
		padding:5px;
	}
	
	td#col_todo, td#col_doing, td#col_done {
		width:33%;	
	}
</style>

<title>KANBAN Board</title>
</head>

<body>
<div class="container">
	<div class="row">
    	<div class="col-lg-12 text-center">
			<span class="lead">Oh, look at your tasks!</span>
    	</div>
    </div>
    <div class="row">
    	<div class="col-lg-12" id="kanban">
        	<?php
				echo $kanban->create();
			?>
        </div>
    </div>
</div>
<script>
	$(function () {
		$(".single_task").draggable();
		$( ".kanban_col" ).droppable({
			hoverClass: "drop-hover",
      		drop: function( event, ui ) {
				$.post('kanban_logic.php', {
						ch_id: $(ui.draggable).attr("id"),
						ch_to: $(this).attr("id")
				}, function(data) {
					$(ui.draggable).remove();
					var t = data.split("|");
					$("#"+t[0]).html(t[1]);
				});
      		}});
	});
</script>
</body>
</html>
