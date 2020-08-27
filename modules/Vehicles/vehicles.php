<?php

$res = '';

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->query("SELECT * FROM vehicles WHERE breakdown = false");
$res = $sql->fetchAll();

?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Vehicles</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="vehicle-table" class="table table-bordered">
      <thead>                  
        <tr>
          <th>Vehicle No</th>
          <th>Last Known Location</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php

            for($i=0;$i<count($res);$i++)
            {
                echo '<tr>';   
                echo '<td>'.$res[$i]['vehicle_no'].'</td>'; 
                echo '<td>'.$res[$i]['last_known_location'].'</td>';
                echo '<td><button data-toggle="modal" data-target="#statusBreakdownConfirmation" data-vehicle='.$res[$i]['vehicle_no'].' type="button" class="btn btn-primary">Mark as Breakdown</button></td>';
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
<div id="statusBreakdownConfirmation" class="modal pg-show-modal fade">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="breakdown_vehicle_title" class="modal-title">Set Status to Breakdown</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="status">Type of Breakdown</label>
                          <select class="form-control" id="status">
                              <option value=""></option>
                              <option value="Minor Problem">Minor Problem</option>
                              <option value="Major Problem">Major Problem</option>
                              <option value="Electrical Problem">Electrical Problem</option>
                              <option value="Servicing">Servicing</option>
                          </select> 
                    </div>
                    <div class="col-md-6">
                        <label for="details" class="form-group">Details of Breakdown</label>
                        <input type="text" class="mt-n2 form-control" id="details">
                        <input id="breakdown_vehicle" type="hidden" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button onclick="setBreakdown()" type="button" class="btn btn-primary">Mark as Breakdown</button>
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

function setBreakdown()
{
    $("#statusBreakdownConfirmation").modal('toggle');
    var vehicle = $('#breakdown_vehicle').val();
    var details = $('#details').val();
    var status = $('#status').val();

    $.ajax({
          type: "POST",
          url: "/modules/Vehicles/mark_as_breakdown.php",
          data: {
            vehicle_no: vehicle,
            details : details,
            type : status
            },
          dataType: "String"
        });
}

/* Modal OnShow Start */

$('#statusBreakdownConfirmation').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var vehicle_no = button.data('vehicle');
  var modal = $(this);
  modal.find('#breakdown_vehicle_title').html("Set status of " + vehicle_no + " to Breakdown");
  modal.find('#breakdown_vehicle').val(vehicle_no);
});
/* Modal OnShow End */

</script>