<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Client;
use App\Models\Appointments;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\PartnerFacility;
use App\Models\AgeDashboard;
use App\Models\Indicator;
use Auth;
use Carbon\Carbon;

class FinalDashboardController extends Controller
{
    public function dashboard()
    {
        // $dob = Client::select(\DB::raw(" year(curdate()) - year(`tbl_client`.`dob`) as age"))
        //     ->where('status', '=', 'Active')
        //         ->whereNull('hei_no')
        //     ->pluck('age');

           // dd($dob);
        $tonine = AgeDashboard::select('ToNinenonconsented')->sum('ToNinenonconsented');
        dd(json_encode($tonine));
    }
}
