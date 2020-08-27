<?php
$res = '';

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

<!-- Scripts -->
<script>

 function trip_start(trip_id)
          {
            // alert(trip_id);
            window.location = "?<?php echo 'module=Trips&page=Trip New' ?>&trip_id=" + trip_id;
          }   

</script>

<!-- Card -->
<a href="#" onclick="trip_start('new');" type="button" class="btn btn-primary mb-3 ml-1">New Trip</a>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Trips</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="table_trip_select" class="table table-bordered">
      <thead>                  
        <tr>
          <th>Vehicle No.</th> 
          <th>From</th> 
          <th>To</th> 
          <th>Start Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
               for($i=0;$i<count($res);$i++)
                {
                    echo '<tr>';   
                    echo '<td>'.$res[$i]['vehicle'].'</td>'; 
                    echo '<td>'.$res[$i]['trip_from'].'</td>';
                    echo '<td>'.$res[$i]['trip_to'].'</td>';
                    echo '<td>'.$res[$i]['trip_start'].'</td>';
                    echo '<td><a href="#" onclick="trip_start('.$res[$i]['trip_id'].')" type="button" class="btn btn-primary">Select</a></td>';
                    echo '</tr>';
                }
        ?>
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->
<script>
  $(function () {
    $("#table_trip_select").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
</script>