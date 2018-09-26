<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //using for Dummy Test
        $parser = $this->Sniff();

        return view('home')
            ->with('parser', $parser);
    }

    public function json(Request $request)
    {
        //Parse Json
        $etirm = $this->Sniff();

        foreach ($etirm as $single) {
            //Short Retrive Data
            $step_1 = stristr($single['content']['$t'], "message:");
            //Round Specific Data
            $step_2 = stristr($step_1, ", sentiment:", "sentiment:");
            //Start Array Location Json
            $mapsApi = "https://maps.googleapis.com/maps/api/geocode/json?address=.$step_2[0].&key="
            $request = file_get_contents($mapsApi . env('GOOGLE_MAP_KEY'));
            // Array Search Location in Json File
            $json = json_decode($request, true);
            // URL To Test https://serinc.tech/Task/public/api/map
            return response()->json(["Data" => $json])->setStatusCode(200);
            ;

        }
    }
}
