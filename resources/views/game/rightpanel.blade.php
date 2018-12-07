<div>
    @member
    <div class="card" style="margin-bottom:8px;">
        <div class="card-body">
            @if($renting) @useridequals($renting[0]->idmember==Auth::id())
            <dt>Rented by:</dt>
            <dd> <a style="color:black;" href="/account">
                            You</a></dd>
            @else
            <dt>Rented by:</dt>
            <dd>

                @volunteer <a style="color:black;" href="/account/{{$renting[0]->idmember}}">@endvolunteer
                         {{$renting[0]->username}}
                         @volunteer </a>@endvolunteer
            </dd>
            @enduseridequals @endif @member @if($isavailable)
            <form style="display: inline-block;" method="POST" action="{{ route('rentgame', ['data' => array('idgame'=>$game->id)] ) }}">
                @csrf
                <input class="btn btn-outline-success" {{(!$isavailable? "disabled": "")}} type='submit' value="Rent it!" />
            </form>
            @else @useridequals($renting[0]->idmember)
            <form style="display: inline-block;" onSubmit="return confirm('Are you sure you want to return this item?');" method="POST"
                action="{{ route('unrentgame', ['data' => array('idgame'=>$game->id)] ) }}">
                @csrf
                <input class="btn btn-outline-danger" type='submit' value="Send Back!" />
            </form>
            @enduseridequals @endif @endmember
        </div>
    </div>
    @endmember

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Rental History</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rentalhistory as $r)
                    <tr>
                        <td> {{$r->username}}</td>
                        <td>
                            <?php 
                                    $timestamp = strtotime($r->startdate);
                                        echo date("Y/m/d - H:i", $timestamp); ?> </td>
                        <td>
                            <?php 
                                    if($r->enddate)  {
                                        $timestamp = strtotime($r->enddate);
                                    echo date("d-m-Y", $timestamp);
                                    }  else {
                                        echo "Currently Renting";
                                    }
                                 ?> </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>