<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1 p-0">Online Pilots | {{ $network }} <i class="fas fa-user-friends float-right"></i></h5>
  </div>
  @if(isset($pilots))
    <div class="card-body p-0">
      @if($pilots->count() > 0)
        <table class="table table-borderless table-sm table-striped mb-0 text-center">
          <tr>
            <th class="text-left">Name</th>
            <th>Callsign</th>
            <th>{{ $network }} ID</th>
            <th>Server</th>
            <th class="text-right">Time Online</th>
          </tr>
          @foreach($pilots as $pilot)
            <tr>
              <td class="text-left">
                @if(isset($pilot['user_id']))
                  <a href="{{ route('frontend.profile.show', [$pilot['user_id']]) }}" target="_blank">{{ $pilot['name_private'] }}</a>
                @else 
                  Deleted User
                @endif
              </td>
              <td><span title="{{ $pilot['flightplan'] ?? 'ATC Not Available' }}">{{ $pilot['callsign'] }}</span></td>
              <td>{{ $pilot['network_id'] }}</td>
              <td>{{ $pilot['server_name'] }}</td>
              <td class="text-right">
                @if($checks)
                  @if(!$pilot['airline']) <i class="fas fa-exclamation-circle mr-1 ml-1" title="Airline Not Found !" style="color: darkred;"></i>@endif
                  @if(!$pilot['pirep'])
                    <i class="fas fa-clipboard mr-1 ml-1" title="Pirep Not Found !" style="color: darkred;"></i>
                  @elseif($pilot['pirep'])
                    <i class="fas fa-clipboard-check mr-1 ml-1" title="{{ $pilot['pirep']->aircraft->icao.' | '.$pilot['pirep']->dpt_airport_id.' > '.$pilot['pirep']->arr_airport_id }}" style="color: darkgreen;"></i>
                  @endif
                @endif
                {{ Dispo_TimeConvert($pilot['online_time']) }}
              </td>
            </tr>
          @endforeach
        </table>
      @else
        <span class="text-danger mr-1 ml-1">No {{ $network }} Online Flights Found</span>
      @endif
    </div>
  @elseif(isset($error))
    <div class="card-body p-0">
      <span class="text-danger mr-1 ml-1">{{ $error }}</span>
    </div>
  @endif
  @if(isset($dltime))
    <div class="card-footer p-0 text-right">
      @if($checks)<span class="ml-1 float-left" title="Admin Checks Enabled"><b>*</b></span>@endif
      <span class="mr-1 ml-1">{{ $dltime->diffForHumans() }}</span>
    </div>
  @endif
</div>
