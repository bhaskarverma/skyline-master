<?php

$res = '';

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->query("SELECT vehicles.*, MAX(damage.damage_id) AS d_id FROM `vehicles` JOIN `damage` ON vehicles.vehicle_no = damage.vehicle WHERE `vehicles`.`breakdown` = true GROUP BY vehicles.vehicle_no");
$res = $sql->fetchAll();

?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Broken Vehicles</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="vehicle-table" class="table table-bordered">
      <thead>                  
        <tr>
          <th>Vehicle No</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php

            for($i=0;$i<count($res);$i++)
            {
                echo '<tr>';   
                echo '<td>'.$res[$i]['vehicle_no'].'</td>'; 
                echo '<td><button data-toggle="modal" data-target="#statusRepairedConfirmation" data-vehicle='.$res[$i]['vehicle_no'].' data-damage='.$res[$i]['d_id'].' type="button" class="btn btn-primary">Mark as Repaired</button></td>';
                echo '</tr>';
            }
        ?>
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->

<!-- Modals Start -->
<div id="statusRepairedConfirmation" class="modal pg-show-modal fade">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="repair_vehicle_title" class="modal-title">Set Status to Repaired</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="repair" class="form-group">Cost of Repair</label>
                        <input type="text" class="mt-n2 form-control" id="repair">
                        <input id="damage_id" type="hidden" value="">
                        <input id="vehicle_no" type="hidden" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button onclick="setRepaired()" type="button" class="btn btn-primary">Mark as Repaired</button>
            </div>
        </div>
    </div>
</div>
<!-- Modals End -->
<script>

$(function () {
  $('#vehicle-table').DataTable({
    "responsive": true,
    "autoWidth": false,
  });
});

function setRepaired()
{
    $("#statusRepairedConfirmation").modal('toggle');
    var damage = $('#damage_id').val();
    var cost = $('#repair').val();
    var vno = $('#vehicle_no').val();

    $.ajax({
          type: "POST",
          url: "/modules/Broken Vehicles/mark_as_repaired.php",
          data: {
            damage : damage,
            cost : cost,
            vno : vno
            },
          dataType: "String"
        });
}

/* Modal OnShow Start */
$('#statusRepairedConfirmation').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var damage = button.data('damage');
  var vehicle_no = button.data('vehicle');
  var modal = $(this);
  modal.find('#repair_vehicle_title').html("Set status of " + vehicle_no + " to Repaired");
  modal.find('#damage_id').val(damage);
  modal.find('#vehicle_no').val(vehicle_no);
});
/* Modal OnShow End */

</script>