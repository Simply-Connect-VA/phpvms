<?php

namespace Modules\DisposableTools\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Acars;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Models\User;
use App\Models\UserFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;

class DisposableToolsController extends Controller
{
  // Admin Page
  public function admin(Request $request) {

    if($request->input('action') === 'whazzup') {
      $network_selection = $request->input('network');
      if ($network_selection === 'IVAO') {
        $key_fn = 'dtools.whazzup_ivao_fieldname';
        $key_ri = 'dtools.whazzup_ivao_refresh';
      } elseif ($network_selection === 'VATSIM') {
        $key_fn = 'dtools.whazzup_vatsim_fieldname';
        $key_ri = 'dtools.whazzup_vatsim_refresh';
      }
      $field_name = $request->input('field_name');
      $refresh_interval = $request->input('refresh_interval');
      $this->ChangeDisposableSettings($key_fn, $field_name);
      $this->ChangeDisposableSettings($key_ri, $refresh_interval);
      Flash::success($network_selection.' WhazzUp Widget Settings Updated');
    }

    return view('DisposableTools::admin');
  }

  public function dbcheck() {

    // Build Arrays from what we have
    $current_users = User::pluck('id')->toArray();
    $current_airports = Airport::pluck('id')->toArray();
    $current_pireps = Pirep::pluck('id')->toArray();
    $current_airlines = Airline::pluck('id')->toArray();
    $current_aircraft = Aircraft::pluck('id')->toArray();
    // Check Pireps
    $pirep_user = Pirep::whereNotIn('user_id', $current_users)->pluck('id')->toArray();
    $pirep_comp = Pirep::whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
    $pirep_orig = Pirep::whereNotIn('dpt_airport_id', $current_airports)->pluck('id')->toArray();
    $pirep_dest = Pirep::whereNotIn('arr_airport_id', $current_airports)->pluck('id')->toArray();
    $pirep_acft = Pirep::whereNotIn('aircraft_id', $current_aircraft)->pluck('id')->toArray();
    // Check Acars Table
    $acars_pirep = Acars::whereNotIn('pirep_id', $current_pireps)->pluck('id')->toArray();
    // Check Subfleets
    $fleet_comp = Subfleet::whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
    // Check Flights
    $flight_comp = Flight::whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
    $flight_orig = Flight::whereNotIn('dpt_airport_id', $current_airports)->pluck('id')->toArray();
    $flight_dest = Flight::whereNotIn('arr_airport_id', $current_airports)->pluck('id')->toArray();
    // Check Users
    $users_comp = User::whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
    $users_field = UserFieldValue::whereNotIn('user_id', $current_users)->pluck('id')->toArray();

    return view('DisposableTools::dbcheck',
      [
        'acars_pirep' => $acars_pirep,
        'fleet_comp'  => $fleet_comp,
        'flight_comp' => $flight_comp,
        'flight_orig' => $flight_orig,
        'flight_dest' => $flight_dest,
        'pirep_user'  => $pirep_user,
        'pirep_comp'  => $pirep_comp,
        'pirep_orig'  => $pirep_orig,
        'pirep_dest'  => $pirep_dest,
        'pirep_acft'  => $pirep_acft,
        'users_comp'  => $users_comp,
        'users_field' => $users_field,
      ]);
  }

  public function ChangeDisposableSettings($key_name, $key_value) {
    DB::table('disposable_settings')->upsert([['key' => $key_name, 'value'=> $key_value]],['key'], ['value']);
  }

}
