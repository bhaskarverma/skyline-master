<?php

//Fetching Installed Modules List
$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("SELECT `module_name` FROM `modules`");
$sql->execute();
$modules_in_db = $sql->fetchAll();

//Fetching all the available modules in File System
$modules_in_fs = array_filter(glob('modules/*'), 'is_dir');

//Filtering the values and get the actual directory names of Modules
foreach ($modules_in_fs as &$f) {
    $f = substr($f, strpos($f,"/")+1);
}

//Removing the Core Modules from the Modules List
if (($key = array_search('core', $modules_in_fs)) !== false) {
    unset($modules_in_fs[$key]);
}

//Reformatting modules_in_db array
$tmp_mid = array();

for($i=0; $i<count($modules_in_db); $i++)
{
  array_push($tmp_mid, $modules_in_db[$i]['module_name']);
}

$modules_in_db = $tmp_mid;
unset($tmp_mid);

$modules_not_in_db = array_values(array_diff($modules_in_fs, $modules_in_db));

?>

<script>

	function reloadPage()
	{
		location.reload(true);
	}

	function add_module_to_db(module)
	{
		$.ajax({
          type: "POST",
          url: "/modules/core/module_control/add_to_db.php",
          data: {module: module},
          success: reloadPage,
          dataType: "String"
        });
	}
	
</script>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Recently Added Modules</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="vehicle-table" class="table table-bordered">
      <thead>                  
        <tr>
          <th>Recently Added Modules</th>
          <th>Module Version</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
            for($i=0;$i<count($modules_not_in_db);$i++)
            {
                $module_details = include 'modules/'.$modules_not_in_db[$i].'/manifest.php';
                echo '<tr>';   
                echo '<td data-vehicle='.$modules_not_in_db[$i].'>'.$modules_not_in_db[$i].'</td>'; 
                echo '<td>'.$module_details['version'].'</td>';
                echo '<td><button type="button" onclick="add_module_to_db(\''.$modules_not_in_db[$i].'\')" class="btn btn-primary">Add to Database</button></td>';
                echo '</tr>';
            }
        ?>
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->