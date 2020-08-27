<?php

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("SELECT * FROM `workgroups`");
$sql->execute();
$res = $sql->fetchAll();

?>
 <!-- Main content -->
        
<!-- general form elements -->
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Add a User</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form role="form" action="/modules/Users and Groups/add_user_process.php" method="post">
    <div class="card-body">
      <div class="form-group">
        <label for="user_full_name">Name of User</label>
        <input type="text" class="form-control" id="user_full_name" name="user_full_name" placeholder="Full Name">
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-md-6">
            <label for="user_uname">Username</label>
            <input type="text" class="form-control" id="user_uname" name="user_uname" placeholder="Username">
          </div>
          <div class="col-md-2 mt-4 pt-2">
            <input type="button" class="form-control btn btn-primary" value="Generate Username" onclick="generateUsername()">
          </div>
       </div>
      </div>
      <div class="form-group">
      <label>Work Group</label>
      <select name="workgroup" class="select2" data-placeholder="Select a Workgroup" style="width: 100%;">
        <option></option>
        <?php
              for($i=0; $i<count($res); $i++)
              {
                echo '<option value="'.$res[$i]['group_id'].'">'.$res[$i]['group_name'].'</option>';
              }
        ?>
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

  function generateUsername()
  {
    var name = $("#user_full_name").val();
    $.ajax({
          type: "POST",
          url: "/modules/Users and Groups/generate_username.php",
          data: {
            name: name
            },
          success: function(data){
              $("#user_uname").val(data);
            },
          dataType: "text"
        });
  }
</script>