 <?php

$pdo = new PDO($dsn, $user, $pass, $options);

$sql = $pdo->prepare("SELECT * FROM `workgroups`");
$sql->execute();
$groups = $sql->fetchAll();

//Remove Core Groups from Displaying
for($i=0;$i<count($groups);$i++)
{
  if($groups[$i]['group_id'] == 1)
  {
    unset($groups[$i]);
  }
}

$groups = array_values($groups);

for($i=0;$i<count($groups);$i++)
{
  if($groups[$i]['group_id'] == 2)
  {
    unset($groups[$i]);
  }
}

$groups = array_values($groups);

?>
 <!-- Main content -->
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Alter a Work Group</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form role="form" action="/modules/Users and Groups/alter_group_process.php" method="post">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="workgroup">Name of Work Group</label>
            <select id="workgroup" name="workgroup" class="select2" data-placeholder="Select a Workgroup" style="width: 100%;">
            <option></option>
            <?php

              for($i=0;$i<count($groups);$i++)
              {
                echo '<option value="'.$groups[$i]['group_id'].'">'.$groups[$i]['group_name']."</option>";
              }

            ?>
          </select>
          </div>
        </div>
        <div class="col-md-6">
          <label for="modules">Allowed Modules</label>
          <select name="modules[]" id="modules" class="select2" multiple="multiple" data-placeholder="Select Modules to Associate" style="width: 100%;">
            <option></option>
          </select>
        </div>
      </div>
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Update</button>
    </div>
  </form>
</div>
<!-- /.card -->
<script>
  
  function updateModuleList(gid)
  {
    $.post( '/modules/Users and Groups/get_modules_for_group.php', { gid : gid })
      .done(function(res) {
          $('#modules').empty().append(res);
      });
  }

  $(function () {
    $('.select2').select2();
  })

  $('#workgroup').on('change', function() {
      updateModuleList(this.value);
  });

</script>