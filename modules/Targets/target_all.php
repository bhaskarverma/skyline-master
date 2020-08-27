<?php

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->query("SELECT * FROM targets");
$targets = $sql->fetchAll();

?>

<!-- Card -->
<a href="#" data-toggle="modal" data-target="#addTarget" type="button" class="btn btn-primary mb-3 ml-1">New Target</a>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Targets</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="targets_all_tbl" class="table table-bordered">
      <thead>                  
        <tr>
          <th>Target Type</th> 
          <th>Value</th> 
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php

                for($i=0;$i<count($targets);$i++)
                {
                    echo '<tr>';   
                    echo '<td>'.$targets[$i]['target_type'].'</td>'; 
                    echo '<td>'.$targets[$i]['target_value'].'</td>';
                    echo '<td><a href="#" data-target-type='.$targets[$i]['target_type'].' data-toggle="modal" data-target="#updateTarget" type="button" class="btn btn-primary">Update</a></td>';
                    echo '</tr>';
                }
                ?>
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
<!-- /.card -->
<!-- Modals -->
<div id="addTarget" class="modal pg-show-modal fade">
    <div class="modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modal_title" class="modal-title">Add Target</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <label for="target_type_add" class="form-group">Target Type</label>
                  <input type="text" class="form-control" id="target_type_add">
                </div>
                <div class="col-md-6">
                  <label for="target_value_add" class="form-group">Target Value</label>
                  <input type="number" class="form-control" id="target_value_add" value="0">
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button onclick="addTarget()" type="button" class="btn btn-primary">Add Target</button>
            </div>
        </div>
    </div>
</div>

<div id="updateTarget" class="modal pg-show-modal fade">
    <div class="modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modal_title" class="modal-title">Update Target</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <label for="target_type_update" class="form-group">Target Type</label>
                  <input type="text" class="form-control" id="target_type_update">
                </div>
                <div class="col-md-6">
                  <label for="target_value_update" class="form-group">Target Value</label>
                  <input type="number" class="form-control" id="target_value_update" value="0">
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button onclick="updateTarget()" type="button" class="btn btn-primary">Update Target</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>

  function addTarget()
  {
    $('#addTarget').modal('toggle');
    var target_type = $("#target_type_add").val();
    var target_value = $("#target_value_add").val();

    $.ajax({
          type: "POST",
          url: "/modules/Targets/add_new_target.php",
          data: {
            target_type : target_type,
            target_value : target_value
          },
          success: function(data){
             location.reload();
          },
          dataType: "String"
        });
  }

  function updateTarget()
  {
    $('#updateTarget').modal('toggle');
    var target_type = $("#target_type_update").val();
    var target_value = $("#target_value_update").val();

    $.ajax({
          type: "POST",
          url: "/modules/Targets/update_target.php",
          data: {
            target_type : target_type,
            target_value : target_value
          },
          success: function(data){
             location.reload();
          },
          dataType: "String"
        });
  }

  $('#updateTarget').on('show.bs.modal', function(e) {
      var target_type = $(e.relatedTarget).data('target-type');
      $(e.currentTarget).find('#target_type_update').val(target_type);
  });

  $(function () {
    $("#targets_all_tbl").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });

</script>