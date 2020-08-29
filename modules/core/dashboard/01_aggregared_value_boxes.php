<?php

$first_day_current_month = date('Y-m-d',strtotime("last day of last month"));
$current_day = date('Y-m-d',strtotime("tomorrow"));

//Preparing Connection to fetch various information from DB
$pdo = new PDO($dsn, $user, $pass, $options);

//Total Breakdowns in Current Month
$sql = $pdo->prepare("SELECT * FROM `damage` WHERE `type` != 'Servicing' AND `date_of_breakdown` BETWEEN ? AND ? AND `date_of_repair` != '0000-00-00'");
$sql->execute([$first_day_current_month, $current_day]);
$breakdowns = $sql->fetchAll();

//Total Services in Current Month
$sql = $pdo->prepare("SELECT * FROM `damage` WHERE `type` = 'Servicing' AND `date_of_breakdown` BETWEEN ? AND ? AND `date_of_repair` != '0000-00-00'");
$sql->execute([$first_day_current_month, $current_day]);
$services = $sql->fetchAll();

//Total Freight of Completed Trips
$sql = $pdo->prepare("SELECT SUM(`freight`) as total_freight FROM `trips` WHERE (`trip_start` BETWEEN ? AND ?) AND (`trip_end` BETWEEN ? AND ?)");
$sql->execute([$first_day_current_month, $current_day,$first_day_current_month, $current_day]);
$total_freight = $sql->fetch()['total_freight'];

//Total Fuel in Ltr of Completed Trips
$sql = $pdo->prepare("SELECT SUM(`fuel_ltr`) as total_fuel_ltr FROM `trips` WHERE (`trip_start` BETWEEN ? AND ?) AND (`trip_end` BETWEEN ? AND ?)");
$sql->execute([$first_day_current_month, $current_day,$first_day_current_month, $current_day]);
$total_fuel_ltr = round($sql->fetch()['total_fuel_ltr'],2);

//Total Fuel in Money of Completed Trips
$sql = $pdo->prepare("SELECT SUM(`fuel_money`) as total_fuel_money FROM `trips` WHERE (`trip_start` BETWEEN ? AND ?) AND (`trip_end` BETWEEN ? AND ?)");
$sql->execute([$first_day_current_month, $current_day,$first_day_current_month, $current_day]);
$total_fuel_money = $sql->fetch()['total_fuel_money'];

//Total Breakdown Vehicle Details
$sql = $pdo->prepare("SELECT * FROM `damage` WHERE `date_of_repair` = '0000-00-00'");
$sql->execute();
$total_breakdown_vehicle_details = $sql->fetchAll();
$total_breakdown_vehicles = count($total_breakdown_vehicle_details);

//Total Average of All Vehicles
$sql = $pdo->prepare("SELECT * FROM `trips` WHERE (`trip_start` BETWEEN ? AND ?) AND (`trip_end` BETWEEN ? AND ?)");
$sql->execute([$first_day_current_month, $current_day,$first_day_current_month, $current_day]);
$trips_tot_av = $sql->fetchAll();
$rtrip_arr_av = array();
for($i=0; $i<count($trips_tot_av); $i++)
{
	$sql = $pdo->prepare("SELECT `round_trip_id` FROM `trip_round_trip_xref` WHERE `trip_id` = ?");
	$sql->execute([$trips_tot_av[$i]['trip_id']]);
	$rtid = $sql->fetch()['round_trip_id'];
	array_push($rtrip_arr_av, $rtid);
}
$rtrip_arr_av = array_unique($rtrip_arr_av);
$rtrip_arr_av = array_values($rtrip_arr_av);
$trip_av = array();
for($i=0;$i<count($rtrip_arr_av);$i++)
{

	$sql = $pdo->prepare("SELECT `trip_id` FROM `trip_round_trip_xref` WHERE `round_trip_id` = ?");
	$sql->execute([$rtrip_arr_av[$i]]);
	$tmp_trips = $sql->fetchAll();

	$vehicle = '';
	$fuel = '';

	$sql = $pdo->prepare("SELECT SUM(`km_end` - `km_start`) AS `total_km` FROM `round_trip` WHERE `round_trip_id` = ?");
	$sql->execute([$rtrip_arr_av[$i]]);
	$km = $sql->fetch()['total_km'];

	for($j=0;$j<count($tmp_trips);$j++)
	{
		$sql = $pdo->prepare("SELECT `vehicle`, SUM(`fuel_ltr`) AS `total_fuel_ltr` FROM `trips` WHERE `trip_id` = ?");
		$sql->execute([$rtrip_arr_av[$i]]);
		$tmp_res = $sql->fetch();

		$vehicle = $tmp_res['vehicle'];
		$fuel = $tmp_res['total_fuel_ltr'];
	}

	$tmp = ['vehicle' => $vehicle, 'average' => round($km / $fuel,2)];
	array_push($trip_av, $tmp);
}

//Total KM Ran
$sql = $pdo->prepare("SELECT * FROM `trips` WHERE (`trip_start` BETWEEN ? AND ?) AND (`trip_end` BETWEEN ? AND ?)");
$sql->execute([$first_day_current_month, $current_day,$first_day_current_month, $current_day]);
$trips_tot = $sql->fetchAll();
$rtrip_arr = array();
for($i=0; $i<count($trips_tot); $i++)
{
	$sql = $pdo->prepare("SELECT `round_trip_id` FROM `trip_round_trip_xref` WHERE `trip_id` = ?");
	$sql->execute([$trips_tot[$i]['trip_id']]);
	$rtid = $sql->fetch()['round_trip_id'];
	array_push($rtrip_arr, $rtid);
}
$rtrip_arr = array_unique($rtrip_arr);
$rtrip_arr = array_values($rtrip_arr);
$rtrip_ids = implode(",",$rtrip_arr);
$sql = $pdo->prepare("SELECT SUM(`km_end` - `km_start`) AS `total_km` FROM `round_trip` WHERE `round_trip_id` IN ('".$rtrip_ids."')");
$sql->execute();
$total_km = $sql->fetch()['total_km'];

//Trip Details for Other Singular Entities
$sql = $pdo->prepare("SELECT * FROM `trips` WHERE (`trip_start` BETWEEN ? AND ?) AND (`trip_end` BETWEEN ? AND ?)");
$sql->execute([$first_day_current_month, $current_day,$first_day_current_month, $current_day]);
$trip_details_for_singular_entities = $sql->fetchAll();

//Freight Target
$sql = $pdo->prepare("SELECT `target_value` FROM `targets` WHERE `target_type` = 'Freight'");
$sql->execute();
$freight_target = $sql->fetch()['target_value'];

//Preparing HTML for the Data
$box_html = include 'components/small_box.php';
$current_month_year = date(' M Y ');
$date_today = date('d M Y',strtotime("today"));

//Breakdowns
$breakdown_table_body = '<table id=\'sb-breakdown-count-table\' class="table table-bordered small-box-modal">';
$breakdown_table_body .= '<thead><tr><th>Vehicle</th><th>Date of Breakdown</th><th>Breakdown Type</th><th>Breakdown Description</th></tr></thead>';
$breakdown_table_body .= '<tbody>';

for($i=0;$i<count($breakdowns);$i++)
{
	$breakdown_table_body .= '<tr>';
	$breakdown_table_body .= '<td>'.$breakdowns[$i]['vehicle'].'</td>';
	$breakdown_table_body .= '<td>'.$breakdowns[$i]['date_of_breakdown'].'</td>';
	$breakdown_table_body .= '<td>'.$breakdowns[$i]['type'].'</td>';
	$breakdown_table_body .= '<td>'.$breakdowns[$i]['details'].'</td>';
	$breakdown_table_body .= '</tr>';
}

$breakdown_table_body .= '</tbody></table>';

$breakdown_html = $box_html;
$breakdown_html = str_replace("{{text}}", "Breakdowns This Month", $breakdown_html);
$breakdown_html = str_replace("{{value}}", count($breakdowns), $breakdown_html);
$breakdown_html = str_replace("{{box-type}}", "bg-danger", $breakdown_html);
$breakdown_html = str_replace("{{icon}}", "fa fa-car-crash", $breakdown_html);
$breakdown_html = str_replace("{{view-more-modal-id}}", 'breakdown-count', $breakdown_html);
$breakdown_html = str_replace("{{view-more-modal-title}}", "List Of Breakdowns This Month", $breakdown_html);
$breakdown_html = str_replace("{{view-more-modal-body}}", $breakdown_table_body, $breakdown_html);
$breakdown_html = str_replace("{{name-of-report}}", 'Total Breakdowns in '.$current_month_year.' as of '.$date_today, $breakdown_html);
$breakdown_html = str_replace("{{view-more-modal-table}}", 'breakdown-count-table', $breakdown_html);


//Services
$services_table_body = '<table id=\'sb-services-count-table\' class="table table-bordered small-box-modal">';
$services_table_body .= '<thead><tr><th>Vehicle</th><th>Date of Service</th><th>Description</th></tr></thead>';
$services_table_body .= '<tbody>';

for($i=0;$i<count($services);$i++)
{
	$services_table_body .= '<tr>';
	$services_table_body .= '<td>'.$services[$i]['vehicle'].'</td>';
	$services_table_body .= '<td>'.$services[$i]['date_of_breakdown'].'</td>';
	$services_table_body .= '<td>'.$services[$i]['details'].'</td>';
	$services_table_body .= '</tr>';
}

$services_table_body .= '</tbody></table>';

$services_html = $box_html;
$services_html = str_replace("{{text}}", "Services This Month", $services_html);
$services_html = str_replace("{{value}}", count($services), $services_html);
$services_html = str_replace("{{box-type}}", "bg-warning", $services_html);
$services_html = str_replace("{{icon}}", "fa fa-cogs", $services_html);
$services_html = str_replace("{{view-more-modal-id}}", 'services-count', $services_html);
$services_html = str_replace("{{view-more-modal-title}}", "List Of Services This Month", $services_html);
$services_html = str_replace("{{view-more-modal-body}}", $services_table_body, $services_html);
$services_html = str_replace("{{name-of-report}}", 'Total Services in '.$current_month_year.' as of '.$date_today, $services_html);
$services_html = str_replace("{{view-more-modal-table}}", 'services-count-table', $services_html);

//Total Freight
$total_freight_table_body = '<table id=\'sb-total-freight-table\' class="table table-bordered small-box-modal">';
$total_freight_table_body .= '<thead><tr><th>Vehicle</th><th>Source</th><th>Destination</th><th>Freight</th></tr></thead>';
$total_freight_table_body .= '<tbody>';

for($i=0;$i<count($trip_details_for_singular_entities);$i++)
{
	$total_freight_table_body .= '<tr>';
	$total_freight_table_body .= '<td>'.$trip_details_for_singular_entities[$i]['vehicle'].'</td>';
	$total_freight_table_body .= '<td>'.$trip_details_for_singular_entities[$i]['trip_from'].'</td>';
	$total_freight_table_body .= '<td>'.$trip_details_for_singular_entities[$i]['trip_to'].'</td>';
	$total_freight_table_body .= '<td>'.'&#x20B9;'.$trip_details_for_singular_entities[$i]['freight'].'</td>';
	$total_freight_table_body .= '</tr>';
}

$total_freight_table_body .= '</tbody></table>';

$total_freight_html = $box_html;
$total_freight_html = str_replace("{{text}}", "Total Freight This Month", $total_freight_html);
$total_freight_html = str_replace("{{value}}", '&#x20B9;'.$total_freight, $total_freight_html);
$total_freight_html = str_replace("{{box-type}}", "bg-success", $total_freight_html);
$total_freight_html = str_replace("{{icon}}", "fa fa-rupee-sign", $total_freight_html);
$total_freight_html = str_replace("{{view-more-modal-id}}", 'total-freight', $total_freight_html);
$total_freight_html = str_replace("{{view-more-modal-title}}", "Trips This Month With Their Freight", $total_freight_html);
$total_freight_html = str_replace("{{view-more-modal-body}}", $total_freight_table_body, $total_freight_html);
$total_freight_html = str_replace("{{name-of-report}}", 'Freight Details in '.$current_month_year.' as of '.$date_today, $total_freight_html);
$total_freight_html = str_replace("{{view-more-modal-table}}", 'total-freight-table', $total_freight_html);

//Total Fuel
$total_fuel_table_body = '<table id=\'sb-total-fuel-table\' class="table table-bordered small-box-modal">';
$total_fuel_table_body .= '<thead><tr><th>Vehicle</th><th>Source</th><th>Destination</th><th>Fuel (Ltr)</th><th>Fuel (&#x20B9;)</th></tr></thead>';
$total_fuel_table_body .= '<tbody>';

for($i=0;$i<count($trip_details_for_singular_entities);$i++)
{
	$total_fuel_table_body .= '<tr>';
	$total_fuel_table_body .= '<td>'.$trip_details_for_singular_entities[$i]['vehicle'].'</td>';
	$total_fuel_table_body .= '<td>'.$trip_details_for_singular_entities[$i]['trip_from'].'</td>';
	$total_fuel_table_body .= '<td>'.$trip_details_for_singular_entities[$i]['trip_to'].'</td>';
	$total_fuel_table_body .= '<td>'.$trip_details_for_singular_entities[$i]['fuel_ltr'].'</td>';
	$total_fuel_table_body .= '<td>'.'&#x20B9;'.$trip_details_for_singular_entities[$i]['fuel_money'].'</td>';
	$total_fuel_table_body .= '</tr>';
}

$total_fuel_table_body .= '</tbody></table>';

$total_fuel_html = $box_html;
$total_fuel_html = str_replace("{{text}}", "Total Fuel Consumption This Month", $total_fuel_html);
$total_fuel_html = str_replace("{{value}}", '&#x20B9;'.$total_fuel_money.'<br />'.$total_fuel_ltr.' Ltr', $total_fuel_html);
$total_fuel_html = str_replace("{{box-type}}", "bg-warning", $total_fuel_html);
$total_fuel_html = str_replace("{{icon}}", "fa fa-gas-pump", $total_fuel_html);
$total_fuel_html = str_replace("{{view-more-modal-id}}", 'total-fuel', $total_fuel_html);
$total_fuel_html = str_replace("{{view-more-modal-title}}", "Fuel Costs For This Month", $total_fuel_html);
$total_fuel_html = str_replace("{{view-more-modal-body}}", $total_fuel_table_body, $total_fuel_html);
$total_fuel_html = str_replace("{{name-of-report}}", 'Fuel Details in '.$current_month_year.' as of '.$date_today, $total_fuel_html);
$total_fuel_html = str_replace("{{view-more-modal-table}}", 'total-fuel-table', $total_fuel_html);


//Current Breakdown Vehicles
$total_breakdown_table_body = '<table id=\'sb-current-breakdown-vehicles-table\' class="table table-bordered small-box-modal">';
$total_breakdown_table_body .= '<thead><tr><th>Vehicle</th><th>Date of Breakdown</th><th>Breakdown Type</th><th>Breakdown Description</th></tr></thead>';
$total_breakdown_table_body .= '<tbody>';

for($i=0;$i<count($total_breakdown_vehicle_details);$i++)
{
	$total_breakdown_table_body .= '<tr>';
	$total_breakdown_table_body .= '<td>'.$total_breakdown_vehicle_details[$i]['vehicle'].'</td>';
	$total_breakdown_table_body .= '<td>'.$total_breakdown_vehicle_details[$i]['date_of_breakdown'].'</td>';
	$total_breakdown_table_body .= '<td>'.$total_breakdown_vehicle_details[$i]['type'].'</td>';
	$total_breakdown_table_body .= '<td>'.$total_breakdown_vehicle_details[$i]['details'].'</td>';
	$total_breakdown_table_body .= '</tr>';
}

$total_breakdown_table_body .= '</tbody></table>';

$total_breakdown_vehicles_html = $box_html;
$total_breakdown_vehicles_html = str_replace("{{text}}", "Vehicles Currently Breakdown", $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{value}}", $total_breakdown_vehicles, $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{box-type}}", "bg-danger", $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{icon}}", "fa fa-car-crash", $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{view-more-modal-id}}", 'total-breakdown-count', $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{view-more-modal-title}}", "Vehicles Currently Broken / In Servicing", $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{view-more-modal-body}}", $total_breakdown_table_body, $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{name-of-report}}", 'Vehicles Currently in Breakdown State as of '.$date_today, $total_breakdown_vehicles_html);
$total_breakdown_vehicles_html = str_replace("{{view-more-modal-table}}", 'current-breakdown-vehicles-table', $total_breakdown_vehicles_html);

//Total KM
$total_km_table_body = '<table id=\'sb-total-km-table\' class="table table-bordered small-box-modal">';
$total_km_table_body .= '<thead><tr><th>Vehicle</th><th>Source</th><th>Destination</th></tr></thead>';
$total_km_table_body .= '<tbody>';

for($i=0;$i<count($trips_tot);$i++)
{
	$total_km_table_body .= '<tr>';
	$total_km_table_body .= '<td>'.$trips_tot[$i]['vehicle'].'</td>';
	$total_km_table_body .= '<td>'.$trips_tot[$i]['trip_from'].'</td>';
	$total_km_table_body .= '<td>'.$trips_tot[$i]['trip_to'].'</td>';
	$total_km_table_body .= '</tr>';
}

$total_km_table_body .= '</tbody></table>';

$total_km_html = $box_html;
$total_km_html = str_replace("{{text}}", "Total KM Running This Month", $total_km_html);
$total_km_html = str_replace("{{value}}", $total_km.' KM', $total_km_html);
$total_km_html = str_replace("{{box-type}}", "bg-success", $total_km_html);
$total_km_html = str_replace("{{icon}}", "fa fa-truck", $total_km_html);
$total_km_html = str_replace("{{view-more-modal-id}}", 'total-km', $total_km_html);
$total_km_html = str_replace("{{view-more-modal-title}}", "Trip Details Accounting For Total KM Value", $total_km_html);
$total_km_html = str_replace("{{view-more-modal-body}}", $total_km_table_body, $total_km_html);
$total_km_html = str_replace("{{name-of-report}}", 'KM Summary ('.$current_month_year.') as of '.$date_today, $total_km_html);
$total_km_html = str_replace("{{view-more-modal-table}}", 'total-km-table', $total_km_html);

//Average of All Vehicles
$average_all_vehicles_table_body = '<table id=\'sb-total-km-table\' class="table table-bordered small-box-modal">';
$average_all_vehicles_table_body .= '<thead><tr><th>Vehicle</th><th>Average</th></tr></thead>';
$average_all_vehicles_table_body .= '<tbody>';

for($i=0;$i<count($trip_av);$i++)
{
	$average_all_vehicles_table_body .= '<tr>';
	$average_all_vehicles_table_body .= '<td>'.$trip_av[$i]['vehicle'].'</td>';
	$average_all_vehicles_table_body .= '<td>'.$trip_av[$i]['average'].'</td>';
	$average_all_vehicles_table_body .= '</tr>';
}

$average_all_vehicles_table_body .= '</tbody></table>';

$average_all_vehicles_html = $box_html;
$average_all_vehicles_html = str_replace("{{text}}", "Total Average", $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{value}}", round($total_km / $total_fuel_ltr, 2), $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{box-type}}", "bg-success", $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{icon}}", "fa fa-tint", $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{view-more-modal-id}}", 'average-km-tot', $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{view-more-modal-title}}", "Total Average", $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{view-more-modal-body}}", $average_all_vehicles_table_body, $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{name-of-report}}", 'Average Total as of '.$date_today, $average_all_vehicles_html);
$average_all_vehicles_html = str_replace("{{view-more-modal-table}}", 'average-km-tot-table', $average_all_vehicles_html);


//Freight Target Calc
$freight_target_achieved_html = $box_html;
$freight_target_achieved_html = str_replace("{{text}}", "Freight Target Achieved", $freight_target_achieved_html);
$freight_target_achieve_perc = round((($total_freight / $freight_target) * 100),2);
$freight_target_achieved_html = str_replace("{{value}}", $freight_target_achieve_perc.'%', $freight_target_achieved_html);
$freight_target_achieved_html = str_replace("{{icon}}", "fa fa-bullseye", $freight_target_achieved_html);

$freight_target_achieve_type = '';
if($freight_target_achieve_perc > 80)
{
	$freight_target_achieve_type = 'bg-success';
}
else if($freight_target_achieve_perc > 40)
{
	$freight_target_achieve_type = 'bg-warning';
}
else
{
	$freight_target_achieve_type = 'bg-danger';
}

$freight_target_achieved_html = str_replace("{{box-type}}", $freight_target_achieve_type, $freight_target_achieved_html);

?>

<div class="row">
	<div class="col-lg-3 col-6">
		<?php echo $breakdown_html; ?>
	</div>
	<div class="col-lg-3 col-6">
		<?php echo $services_html; ?>
	</div>
	<div class="col-lg-3 col-6">
		<?php echo $total_freight_html; ?>
	</div>
	<div class="col-lg-3 col-6">
		<?php echo $total_fuel_html; ?>
	</div>
	<div class="col-lg-3 col-6">
		<?php echo $total_breakdown_vehicles_html; ?>
	</div>
	<div class="col-lg-3 col-6">
		<?php echo $total_km_html; ?>
	</div>
	<div class="col-lg-3 col-6">
		<?php echo $average_all_vehicles_html; ?>
	</div>
	<div class="col-lg-3 col-6">
		<?php echo $freight_target_achieved_html; ?>
	</div>
</div>