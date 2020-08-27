<?php

require('components/to_do_list.php');

$pdo = new PDO($dsn, $user, $pass, $options);

$sql = $pdo->prepare("SELECT * FROM `global_todo` WHERE `is_completed` = false");
$sql->execute();
$todo = $sql->fetchAll();

$list = new ToDoList();

$now = strtotime('now');

for($i=0;$i<count($todo);$i++)
{
	$day = strtotime($todo[$i]['expected_finish_datetime']);
	$diff = $day - $now;
	$remain = '';
	$t_days = '';
	$t_hours = '';
	$t_mins = '';
	$color = '';

	if($diff > 21600)
	{
		$color = 'success';
	}
	else if($diff > 10800)
	{
		$color = 'warning';
	}
	else
	{
		$color = 'danger';
	}

	$attach = ' to go';

	if($diff >= 86400) //Checking if Time is Greater Than 1 Day
	{
		$t_days = floor($diff/86400);
		if($t_days == 1)
		{
			$remain = $t_days.' Day';
		}
		else
		{
			$remain = $t_days.' Days';
		}
		$diff = $diff - ($t_days * 86400);
	}

	if($diff >= 3600) //Checking if Remaining Time is Greater Than 1 Hour
	{
		$t_hours = floor($diff/3600);
		if($t_hours == 1)
		{
			if($t_days != '')
			{
				$remain .= ' ';
			}

			$remain .= $t_hours.' Hour';
		}
		else
		{
			if($t_days != '')
			{
				$remain .= ' ';
			}

			$remain .= $t_hours.' Hours';
		}

		$diff = $diff - ($t_hours * 3600);
	}

	if($diff >= 60) //Checking if Remaining Time is Greater Than 1 Hour
	{
		$t_mins = floor($diff/60);
		if($t_mins == 1)
		{
			if($t_days != '' || $t_hours != '')
			{
				$remain .= ' ';
			}
			
			$remain .= $t_mins.' min';
		}
		else
		{
			if($t_days != '' || $t_hours != '')
			{
				$remain .= ' ';
			}

			$remain .= $t_mins.' mins';
		}
	}

	if($diff < 0)
	{
		$remain = 'Overdue';
		$attach = '';
	}

	$list->addItem('toDo-item-'.$todo[$i]['item_id'], $todo[$i]['text'], $color, $remain.$attach);
}

echo $list->getHTML();

?>