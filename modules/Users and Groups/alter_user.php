<?php

$pdo = new PDO($dsn, $user, $pass, $options);

$sql = $pdo->prepare("SELECT * FROM `users`");
$sql->execute();
$users = $sql->fetchAll();

//Remove Core Groups from Displaying
for($i=0;$i<count($users);$i++)
{
  if($users[$i]['group_id'] == 1)
  {
    unset($users[$i]);
  }
}

$users = array_values($users);

for($i=0;$i<count($users);$i++)
{
  if($users[$i]['group_id'] == 2)
  {
    unset($users[$i]);
  }
}

$users = array_values($users);

?>
 <!-- Main content -->
        
<!-- general form elements -->
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Alter User</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form role="form" action="/modules/Users and Groups/alter_user_process.php" method="post">
    <div class="card-body">
      <div class="form-group">
        <label for="user">Select User</label>
        <select name="user" id="user" class="select2" data-placeholder="Select a User" style="width: 100%;">
          <option></option>
          <?php
                for($i=0; $i<count($users); $i++)
                {
                  echo '<option value="'.$users[$i]['user_id'].'">'.$users[$i]['name'].' - '.$users[$i]['uname'].'</option>';
                }
          ?>
        </select>
      </div>
      <div class="form-group">
      <label>Work Group</label>
      <select name="workgroup" id="workgroup" class="select2" data-placeholder="Select a Workgroup" style="width: 100%;">
        <option></option>
      </select>
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

  $('#user').on('change', function() {
      updateModuleList(this.value);
  });

  function updateModuleList(uid)
  {
    $.post( '/modules/Users and Groups/get_group_for_user.php', { uid : uid })
      .done(function(res) {
          $('#workgroup').empty().append(res);
      });
  }

</script>