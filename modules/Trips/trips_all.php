<?php

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->query("SELECT round_trip_id FROM round_trip WHERE on_road = true");
$all_on_road_round_trips = $sql->fetchAll();

$res = array();

foreach($all_on_road_round_trips AS $trip)
{
    $sql = $pdo->prepare("SELECT MAX(trip_id) AS tid FROM trip_round_trip_xref WHERE round_trip_id = ? GROUP BY round_trip_id");
    $sql->execute([$trip['round_trip_id']]);
    $trip_id = $sql->fetch()['tid'];

    $sql = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $sql->execute([$trip_id]);
    array_push($res,$sql->fetch());
}

function anyBreakdown($res)
{
    for($i=0; $i<count($res); $i++)
    {
        if($res[$i]['breakdown'] == 1)
        {
            return true;
        }
    }

    return false;
}

?>

<!-- Card -->
<a href="<?php echo '?module=Trips&page=Trip Select' ?>" type="button" class="btn btn-primary mb-3 ml-1">Start Trip</a>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Vehicles</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="trips_all_tbl" class="table table-bordered">
      <thead>                  
        <tr>
          <th>Vehicle No.</th> 
          <th>Driver</th>
          <th>From</th> 
          <th>To</th> 
          <th>Start Date</th>
          <th>Current Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php

                for($i=0;$i<count($res);$i++)
                {
                    echo '<tr>';   
                    echo '<td>'.$res[$i]['vehicle'].'</td>'; 
                    echo '<td>'.$res[$i]['driver'].'</td>';
                    echo '<td>'.$res[$i]['trip_from'].'</td>';
                    echo '<td>'.$res[$i]['trip_to'].'</td>';
                    echo '<td>'.$res[$i]['trip_start'].'</td>';
                    echo '<td>'.$res[$i]['current_status'].'</td>';
                    echo '<td><a href="#" data-toggle="modal" data-target="#updateModal" data-trip='.$res[$i]['trip_id'].' data-vehicle='.$res[$i]['vehicle'].' type="button" class="btn btn-primary">Update</a><a href="#" data-toggle="modal" data-target="#endTripModal" data-vehicle='.$res[$i]['vehicle'].' data-trip='.$res[$i]['trip_id'].' type="button" class="ml-3 btn btn-primary">End Trip</a></td>';
                    echo '</tr>';
                }
                ?>
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
<!-- /.card -->
<!-- Modals -->
<div id="updateModal" class="modal pg-show-modal fade">
    <div class="modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modal_title" class="modal-title">Update Trip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <label for="driver" class="form-group">Driver</label>
                  <input type="text" class="form-control" id="driver">
                  <div class="form-group"> 
                      <label for="status">Status</label>
                      <select class="form-control" id="status">
                          <option value=""></option>
                          <option value="Status 1">Status 1</option>
                          <option value="Status 2">Status 2</option>
                          <option value="Status 3">Status 3</option>
                          <option value="Status 4">Status 4</option>
                      </select> 
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="fuel_ltr" class="form-group">Fuel (Ltr)</label>
                  <input type="text" class="form-control" id="fuel_ltr" value="0">
                  <label for="fuel_money" class="form-group">Fuel (Money)</label>
                  <input type="text" class="form-control" id="fuel_money" value="0">
                  <input type="hidden" id="trip_id" value="">
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button onclick="updateTrip()" type="button" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>

<div id="endTripModal" class="modal pg-show-modal fade">
    <div class="modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="end_modal_title" class="modal-title">End Trip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
              <label for="km_end" class="form-group">KM End</label>
              <input type="text" class="form-control" id="km_end">
              <input type="hidden" id="end_trip_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button onclick="endTrip()" type="button" class="btn btn-primary">End Trip</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>

  function updateTrip()
  {
    var trip_id = $("#trip_id").val();
    var driver = $("#driver").val();
    var status = $("#status").val();
    var fuel_ltr = $("#fuel_ltr").val();
    var fuel_money = $("#fuel_money").val();

    $.ajax({
          type: "POST",
          url: "/modules/Trips/update_trip.php",
          data: {
            trip_id : trip_id,
            driver : driver,
            status : status,
            fuel_ltr : fuel_ltr,
            fuel_money : fuel_money
          },
          success: function(data){
             location.reload();
          },
          dataType: "String"
        });
  }

  function endTrip()
  {
    var trip_id = $("#end_trip_id").val();
    var km_end = $("#km_end").val();

    $.ajax({
          type: "POST",
          url: "/modules/Trips/end_trip.php",
          data: {
            trip_id : trip_id,
            km_end : km_end
          },
          success: function(data){
             location.reload();
          },
          dataType: "String"
        });
  }

  $(function () {
    $("#trips_all_tbl").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });

  $('#updateModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var vehicle_no = button.data('vehicle');
    var trip_id = button.data('trip');
    var modal = $(this);
    modal.find('#modal_title').html("Update Trip for " + vehicle_no);
    modal.find('#trip_id').val(trip_id);
  });

  $('#endTripModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var vehicle_no = button.data('vehicle');
    var trip_id = button.data('trip');
    var modal = $(this);
    modal.find('#end_modal_title').html("End Trip for " + vehicle_no);
    modal.find('#end_trip_id').val(trip_id);
  });

  $("#fuel_ltr").on("keyup", function() {
    var fuel_ltr = $("#fuel_ltr").val();

    if(!$.isNumeric(fuel_ltr))
    {
      alert("Please Enter Only Integers as Fuel (Ltr)");
      $("#fuel_ltr").val("0");
      return;
    }          
  });

  $("#fuel_money").on("keyup", function() {
    var fuel_money = $("#fuel_money").val();

    if(!$.isNumeric(fuel_money))
    {
      alert("Please Enter Only Integers as Fuel (Money)");
      $("#fuel_money").val("0");
      return;
    }          
  });

</script>