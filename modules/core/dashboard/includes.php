<?php

//List Modules for Dashboard
$modules_for_dashboard = scandir('./modules/core/dashboard');

//Filter Modules and Remove current directory
foreach($modules_for_dashboard as $module)
{
	if($module == "." || $module == ".." || substr($module, -4) != '.php' || $module == 'includes.php')
	{
		$key = array_search($module, $modules_for_dashboard);
		unset($modules_for_dashboard[$key]);
	}
}

//Reindex Array
$modules_for_dashboard = array_values($modules_for_dashboard);

foreach($modules_for_dashboard as $module)
{
	include('./modules/core/dashboard/'.$module);
}

?>