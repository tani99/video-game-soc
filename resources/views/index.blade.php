@extends('layouts.app') 
@section('content') @if ($games)

<style>
    .link-button-container {
        flex: 1;
        margin-right: 4px;
        margin-left: 4px;
    }

    .link-button-button {
        width: 100%;
    }

    .button-container {}

    @media all and (min-width: 0px) and (max-width: 1024px) {
        .button-container {
            flex-direction: column !important;
        }
        .link-button-container {
            padding-top: 4px;
            padding-bottom: 4px;

        }
    }
</style>


<form class="input-group mb-3" method="POST" action="{{ route('indexFilter') }}">
    @csrf
  <input name="filter" id="filter" type="text" class="form-control" placeholder="Recipient's username" aria-label="Search Games ... " aria-describedby="button-addon2">
  <div class="input-group-append">
    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
  </div>

</form>

@foreach ($games as $g) @if($loop->index%3==0) @if($loop->index!=0)
</div>
@endif
<div class="card-columns">
    @endif
    <?php
    $buttonStyle =  $g->isavailable? "btn-outline-light" : "btn-outline-dark";
    ?>
        <div class="card">
            {{-- <img class="card-img-top" src=".../100px180/" alt="Card image cap"> --}}
            <div class="card-body" style="padding-bottom: 8px;">
                <h5 class="card-title"> {{ $g->name }}</h5>
                <div style="position: absolute; top: 4px; right: 4px;">
                    <h6>
                        @if ($g->isavailable)
                        <span class="badge badge-success">Available</span> @else
                        <span class="badge badge-secondary">Not Available</span> @endif
                    </h6>
                </div>
                {{--
                <p class="card-text">

                </p> --}}

            </div>
            <div class="card-footer @if ($g->isavailable) bg-success text-white @endif" style="padding-right: 8px; padding-left: 8px;">
                {{--
                <h6>
                    @if ($g->isavailable) Available @else Not available @endif
                </h6> --}}
                <div class="d-flex flex-row justify-content-around button-container">

                    <a class="link-button-container" href={{ "/game/{$g->id}"}}>
                            <button type="button" class="btn {{$buttonStyle }} link-button-button"  >More Information</button>
                    </a> @member @if($g->isavailable)
                    <form class="link-button-container" method="POST" action="{{ route('rentgame', ['data' => array('idgame'=>$g->id)] ) }}">
                        @csrf
                        <input type="submit" class="btn {{$buttonStyle }} link-button-button" value="Rent it!" />
                    </form>
                    @endif @endmember @volunteer
                    <a class="link-button-container" class="align-items-stretch" href={{ "/game/{$g->id}/edit"}}>
                        <button type="button" class="btn {{$buttonStyle }} link-button-button"  >Edit</button>
                    </a> @endvolunteer
                </div>

            </div>
        </div>
        @if($loop->remaining==0)
</div>@endif @endforeach @else No Games Found. @endif
@endsection