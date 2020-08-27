<?php

//Nav Sidebar list HTML
$type_single_name_start_to_link =  '<li class="nav-item"><a href="';
$type_single_name_link_continued = '" class="nav-link"><i class="nav-icon fas fa-th"></i><p>';
$type_single_name_end = '</p></a></li>';

$type_multi_name_start = '<li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-th"></i><p>';
$type_multi_name_end = '<i class="fas fa-angle-left right"></i></p></a><ul class="nav nav-treeview">';
$type_multi_list_start_to_link = '<li class="nav-item"><a href="';
$type_multi_list_link_continued = '" class="nav-link"><i class="far fa-circle nav-icon"></i><p>';
$type_multi_list_end = '</p></a></li>';
$type_multi_end = '</ul></li>';

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

//Creating Common Modules Var to Populate Values
$modules = array();
for($i=0; $i<count($modules_in_db); $i++)
{
  array_push($modules, $modules_in_db[$i]);
}

//Checking for Difference in Module Lists
$modules_not_in_db = array(); //Array to contain the list of Recently Added Modules
$modules_not_in_fs = array(); //Array to contain the list of Recently Deleted Modules

$modules_not_in_db = array_values(array_diff($modules_in_fs, $modules_in_db));
$modules_not_in_fs = array_values(array_diff($modules_in_db, $modules_in_fs));

//Flags for Module Difference
$modules_added = false;
$modules_deleted = false;

if(!empty($modules_not_in_db))
{
  $modules_added = true;
}

if(!empty($modules_not_in_fs))
{
  $modules_deleted = true;
}

//Debug
// print_r($modules);

?>

<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="Skyline Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Skyline Logistics</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['user_data']['name']; ?>&size=128" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['user_data']['name']; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview menu-open">
            <a href="/" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <?php

            foreach($modules as $module)
            {
              if(in_array($module, $modules_not_in_fs))
              {
                continue;
              }
              if(in_array($module,$_SESSION['user_access']))
              {
                $module_details = include 'modules/'.$module.'/manifest.php';

                if($module_details['list_type'] == 'single')
                {
                  $path = '';
                  foreach($module_details['pages_to_list'] as $page_name => $page_path)
                  {
                    $path = $page_path;
                  }
                  $path = "?module=".$module."&page=".$page_name;
                  echo $type_single_name_start_to_link.$path.$type_single_name_link_continued.$module.$type_single_name_end;
                }
                else
                {
                  echo $type_multi_name_start.$module.$type_multi_name_end;
                  foreach($module_details['pages_to_list'] as $page_name => $path)
                  {
                    $path = "?module=".$module."&page=".$page_name;
                    echo $type_multi_list_start_to_link.$path.$type_multi_list_link_continued.$page_name.$type_multi_list_end;
                  }
                  echo $type_multi_end;
                }
              }
            }

            if($_SESSION['user_data']['gid'] == 1 || $_SESSION['user_data']['gid'] == 2)
            {
              if($modules_added || $modules_deleted)
              {
                $path_unknown = "?module=core/module_control&page=Unknown Modules";
                $path_deleted = "?module=core/module_control&page=Deleted Modules";
                echo $type_multi_name_start."Modules Changed".$type_multi_name_end;
                if($modules_added)
                {
                  echo $type_multi_list_start_to_link.$path_unknown.$type_multi_list_link_continued."Unknown Modules".$type_multi_list_end;
                }
                if($modules_deleted)
                {
                  echo $type_multi_list_start_to_link.$path_deleted.$type_multi_list_link_continued."Deleted Modules".$type_multi_list_end;
                }
                echo $type_multi_end;
              }
            }
          ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
