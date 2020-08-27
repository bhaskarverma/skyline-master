<?php

$trip_id = $_GET['trip_id'];

if($trip_id != "new")
{
  $pdo = new PDO($dsn, $user, $pass, $options);
  $sql = $pdo->prepare("SELECT round_trip_id FROM trip_round_trip_xref WHERE trip_id = ?");
  $sql->execute([$trip_id]);
  $round_trip_id = $sql->fetch()['round_trip_id'];
}
else
{
  $round_trip_id = "new";
}
?>

 <!-- Main content -->
<!-- general form elements -->
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">
      <?php

          if($trip_id == "new")
          {
            echo 'Start a New Trip';
          }
          else
          {
            echo 'Continue Trip';
          }

          ?>
    </h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form role="form" method="post" action="/modules/Trips/add_new_trip.php">
    <div class="card-body">
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label for="trip_from">Trip From</label>
            <input type="text" class="form-control" name="trip_from" id="trip_from" placeholder="Trip From">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label for="trip_to">Trip To</label>
            <input type="text" class="form-control" name="trip_to" id="trip_to" placeholder="Trip To">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label for="material">Material</label>
            <input type="text" class="form-control" name="material" id="material" placeholder="Material">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label for="vehicle">Vehicle</label>
            <input type="text" class="form-control" id="vehicle" name="vehicle" placeholder="Vehicle">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label for="driver">Driver</label>
            <input type="text" class="form-control" id="driver" name="driver" placeholder="Driver">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label for="quantity">Material Quantity</label>
            <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Material Quantity">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label for="rate">Rate</label>
            <input type="text" class="form-control" id="rate" name="rate" placeholder="Rate">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label for="fuel_money">Fuel (Money)</label>
            <input type="text" class="form-control" id="fuel_money" name="fuel_money" placeholder="Fuel (Money)">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label for="fuel_ltr">Fuel (Ltr)</label>
            <input type="text" class="form-control" id="fuel_ltr" name="fuel_ltr" placeholder="Fuel (Ltr)">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label for="freight">Freight</label>
            <input type="text" class="form-control" id="freight" name="freight" placeholder="Freight">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label for="km_start">KM (Start)</label>
            <input type="text" class="form-control" id="km_start" name="km_start" placeholder="KM (Start)">
            <?php
                    echo '<input type="hidden" name="round_trip_id" value="'.$round_trip_id.'">';
             ?>
          </div>
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
  //Calculation of Total Freight Start
 $("#rate").on("keyup", function() {
            var rate = $("#rate").val();
            var quantity = $("#quantity").val();

            if(rate == "")
            {
              rate = 0;
            }

            if(quantity == "")
            {
              quantity = 0;
            }

            if(!$.isNumeric(rate))
            {
              alert("Please Enter Only Integers as Rate");
              $("#rate").val("");
              return;
            }

            if(!$.isNumeric(quantity))
            {
              alert("Please Enter Only Integers as Material Quantity");
              $("#quantity").val("");
              return;
            }

            $("#freight").val(rate * quantity);            
          });

 $("#quantity").on("keyup", function() {
            var rate = $("#rate").val();
            var quantity = $("#quantity").val();

            if(rate == "")
            {
              rate = 0;
            }

            if(quantity == "")
            {
              quantity = 0;
            }

            if(!$.isNumeric(rate))
            {
              alert("Please Enter Only Integers as Rate");
              $("#rate").val("");
              return;
            }

            if(!$.isNumeric(quantity))
            {
              alert("Please Enter Only Integers as Material Quantity");
              $("#quantity").val("");
              return;
            }

            $("#freight").val(rate * quantity);
          });
 //Calculation of Total Freight End
</script>