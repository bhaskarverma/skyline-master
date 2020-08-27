 <?php

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("SELECT * FROM `modules`");
$sql->execute();
$res = $sql->fetchAll();

?>
 <!-- Main content -->
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Add a Work Group</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form role="form" action="/modules/Users and Groups/add_group_process.php" method="post">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="workgroup">Name of Work Group</label>
            <input type="text" class="form-control" id="workgroup" name="workgroup" placeholder="Work Group Name">
          </div>
        </div>
        <div class="col-md-6">
          <label for="modules">Allowed Modules</label>
          <select name="modules[]" class="select2" multiple="multiple" data-placeholder="Select Modules to Associate" style="width: 100%;">
            <option></option>
            <?php
              for($i=0; $i<count($res); $i++)
              {
                echo '<option value="'.$res[$i]['module_id'].'">'.$res[$i]['module_name'].'</option>';
              }
            ?>
          </select>
        </div>
      </div>
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>
<!-- /.card -->
  <script>
  $(function () {
    $('.select2').select2();
  })
</script>