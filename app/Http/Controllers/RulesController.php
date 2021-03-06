<?php

namespace App\Http\Controllers;

use App\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class RulesController extends Controller
{
    public function index()
    {
        $rules = DB::select("SELECT * from rules");
       
        return view('rules', ['rules' => $rules[0]]);
    }

    public function edit(Request $request)
    {
        $data = $request->all();
        //Log::info(print_r($data));
        
        $rules = Rules::first();

        $rentalPeriod = $data['rentalperiod']. " " . $data['rentalperiodtime'];
        $rulevioperiod = $data['rulevioperiod']. " " . $data['rulevioperiodtime'];
        $banperiod = $data['banperiod']. " " . $data['banperiodtime'];
         
        DB::update("update rules set rentalperiod = '{$rentalPeriod}', rulevioperiod = '{$rulevioperiod}', banperiod = '{$banperiod}', 
        
        rentgamelimit = {$data['rentgamelimit']}, 
        extensionlimit= {$data['extensionlimit']},
        ruleviolimitperperiod= {$data['ruleviolimitperperiod']}");
     
        return redirect()->route('rules');
    }

    public static function getRentGameLimit()
    {
        return DB::select("SELECT rentgamelimit from rules")[0]->rentgamelimit;
    }

    public static function getExtensionLimit()
    {
        return DB::select("SELECT extensionlimit from rules")[0]->extensionlimit;
    }

    public static function getViolationLimii()
    {
        return DB::select("SELECT ruleviolimitperperiod from rules")[0]->ruleviolimitperperiod;
    }
}
