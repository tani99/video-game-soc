<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rent;
use Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RulesController;

class RentController extends Controller
{

       /**
     * Show a list of all of the application's users.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = "SELECT rentals.id as rentalid, rentals.startdate, rentals.enddate,
                rentals.extensions, duedate, game.name as gamename, game.id as gameid,
                users.name as username, users.id as userid
                from rentals
                left outer join
                users
                on users.id=rentals.iduser
                left outer join currentrentals
                on rentals.id=currentrentals.rentalid
                left outer join game
                on game.id=rentals.idgame
                order by enddate DESC, rentals.startdate DESC";
        $rentals = DB::select($query);
        return view('rentalHistory', compact('rentals'));
    }

    public function createRent(Request $request)
    {
        $id =Auth::id();
        $rentedGames = DB::select("select * from rentals
                                    inner join 
                                    game
                                    on rentals.idgame=game.id
                                    where rentals.iduser={$id} and enddate is null");
        if (sizeof($rentedGames)>=RulesController::getRentGameLimit()) {
            return redirect()->route('error', ['id' => 5]);
        }

        // $hasDamaged = DB::select("select exists(select 1 from damagedrefunds
        //  where damagedrefunds.iduser = {$id} and refunded = false)");
        // if ($hasDamaged == true) {
        //     return redirect()->route('error', ['id' => 5]);
        // }

        $data = $request->all()['data'];
        if (Auth::user()) {
            Rent::create([
                'idgame' => $data['idgame'],
                'iduser' => $id
            ]);
        }
        return redirect()->back();
    }

    public function deleteRent(Request $request)
    {
        $data = $request->all()['data'];
        $id = $data['idrent'];
        if (self::deleteRentById($id)) {
            $gameid = DB::select("select idgame from rentals where rentals.id = {$id}");
            // $damaged = DB::select("select damaged from game where game.id = {$gameid}");
            // if ($damaged) {
            //     $userid = DB::select("select iduser from rentals where rentals.id = {$id}");
            //     DB::table('damagedrefunds')->insert(
            //         ['iduser' => $userid, 'idgame' => $gameid, 'refunded' => 'false']
            //     );
            // }
            return redirect()->back();
        } else {
            return redirect()->route('error', ['id' => 6]);
        }
    }

    public static function deleteRentById($id)
    {
        $rentedGames = DB::select("select * from rentals
        inner join
        game
        on rentals.idgame=game.id
        where rentals.id={$id} and enddate is null");
        if (sizeof($rentedGames) >0) {
            $affected = DB::update("UPDATE rentals SET enddate=NOW() WHERE id = ${id} and enddate is null");
            return true;
        }
        return false;
    }

    public function addExtension($id)
    {
        $extensions = DB::table('rentals')->where('id', $id)->select('extensions')->first();
        if ($extensions->extensions<RulesController::getExtensionLimit()) {
            DB::table('rentals')->where('id', $id)
            ->increment('extensions');
            return redirect()->back();
        } else {
            return redirect()->route('error', ['id' => 9]);
        }
    }

    public function newRentalView(Request $request)
    {
        $users = DB::select("SELECT id, name, email, created_at, updated_at, volunteer, secretary, banned, currentrentals
                            FROM currentusers
                            where currentrentals<2");
        $games = DB::select("SELECT id, name, startdate, enddate, iduser, isavailable 
                            from currentgames 
                            where isavailable=true");

        return view('rent', compact('users', 'games'));
    }

    public function newRentalViewPost(Request $request)
    {
        $data = $request->all();
        Rent::create([
            'idgame' => $data['game'],
            'iduser' => $data['user']
        ]);
        return redirect('rentalhistory');
    }

    public function endRentalViewPost(Request $request)
    {
        $data = $request->all();
        self::deleteRentById($data['id']);
        return redirect()->back();
    }
}
