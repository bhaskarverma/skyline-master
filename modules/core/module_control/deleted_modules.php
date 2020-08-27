<?php

//Fetching Installed Modules List
$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("SELECT `module_name` FROM `modules`");
$sql->execute();
$modules_in_db = $sql->fetchAll();

//Reformatting modules_in_db array
$tmp_mid = array();

for($i=0; $i<count($modules_in_db); $i++)
{
  array_push($tmp_mid, $modules_in_db[$i]['module_name']);
}

$modules_in_db = $tmp_mid;
unset($tmp_mid);

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

$modules_not_in_fs = array_values(array_diff($modules_in_db, $modules_in_fs));

?>

</script>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Deleted Modules</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="vehicle-table" class="table table-bordered">
      <thead>                  
        <tr>
          <th>Recently Deleted Modules</th>
        </tr>
      </thead>
      <tbody>
        <?php

            for($i=0;$i<count($modules_not_in_db);$i++)
            {
                echo '<tr>';   
                echo '<td data-vehicle='.$modules_not_in_db[$i].'>'.$modules_not_in_db[$i].'</td>';
                echo '</tr>';
            }
        ?>
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->