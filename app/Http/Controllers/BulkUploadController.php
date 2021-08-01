<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Excel;
use Carbon\Carbon;
use App\Models\Client;
use Auth;

class BulkUploadController extends Controller
{
    public function uploadClientForm(){
        return view('clients.upload-clients-form');
    }


    public function importClients(Request $request){

        $file = request()->file('file');

        if (!empty($file)) {
            $receivedArr = $this->csvToArray($file);
            for ($i = 0; $i < count($receivedArr); $i++) {
                $gender_value = trim(strtolower($receivedArr[$i]['Gender']));

                if($gender_value == 'm'){
                    $gender = 2;
                }
                elseif ($gender_value=='f') {
                    $gender =1;
                }else {
                    $gender= 5;
                }

                $marital_value = trim(strtolower($receivedArr[$i]['MaritalStatus']));

                if($marital_value == 'divorced'){
                    $marital = 3;
                }elseif ($marital_value == 'living with partner') {
                    $marital = 5;
                }elseif ($marital_value == 'married') {
                    $marital = 2;
                }elseif ($marital_value == 'never married') {
                    $marital = 1;
                }
                elseif ($marital_value == 'polygamous') {
                    $marital = 8;
                }elseif ($marital_value == 'widowed') {
                    $marital = 4;
                }else{
                    $marital = 6;
                }

                $dob_value = trim(strtolower($receivedArr[$i]['DOB']));

                $dob = Carbon::Parse($dob_value)->format('Y-m-d');

                $art_value = trim(strtolower($receivedArr[$i]['ARTStartDate']));

                $art_start_date = Carbon::Parse($art_value)->format('Y-m-d');

                $enrollment_value = trim(strtolower($receivedArr[$i]['enroll_date']));

                $enrollment_date = Carbon::Parse($enrollment_value)->format('Y-m-d');


                $age_value  = (float)$receivedArr[$i]['ageInYears'];
                if($age_value >=20){
                    $group_id = 1;
                }elseif ($age_value >=13) {
                    $group_id = 2;
                }else{
                    $group_id = 4;
                }

                $first_name = trim($receivedArr[$i]['GivenName']);
                $middle_name = trim($receivedArr[$i]['MiddleName']);
                $last_name = trim($receivedArr[$i]['FamilyName']);
                $clinic_number = trim($receivedArr[$i]['CCC_Number']);
                $phone_number = trim($receivedArr[$i]['MobileNumber']);
                $facility_id = trim($receivedArr[$i]['MFL']);
                $mfl_code = trim($receivedArr[$i]['MFL']);
                $partner_id = trim($receivedArr[$i]['PartnerID']);
                $status = "Active";
                $client_status = "Art";
                $clinic_id = 1;
                $text_frequency = 168;
                $text_time = 7;
                $wellness = "Yes";
                $motivational = "Yes";
                $smsenable = "Yes";
                $language = 2;

                $client = new Client;

                $client->f_name = $first_name;
                $client->m_name = $middle_name;
                $client->l_name = $last_name;
                $client->clinic_number = $clinic_number;
                $client->phone_no = $phone_number;
                $client->group_id = $group_id;
                $client->language_id = $language;
                $client->facility_id = $facility_id;
                $client->mfl_code = $mfl_code;
                $client->gender = $gender;
                $client->marital = $marital;
                $client->dob = $dob;
                $client->art_date = $art_start_date;
                $client->enrollment_date = $enrollment_date;
                $client->partner_id = $partner_id;
                $client->status = $status;
                $client->client_status = $client_status;
                $client->clinic_id = $clinic_id;
                $client->txt_frequency = $text_frequency;
                $client->txt_time = $text_time;
                $client->wellness_enable = $wellness;
                $client->motivational_enable = $motivational;
                $client->created_by =  Auth::user()->id;
                $client->updated_by = Auth::user()->id;

                $existing  = Client::where('clinic_number', $clinic_number)->first();

                if($existing){
                    echo ('Client' . $clinic_number  . ' already exists in the system <br>');

                }elseif($clinic_number.length < 10 || $clinic_number.length > 10){
                    echo ('Client' . $clinic_number  . ' has less or more than 10 digit ccc number <br>');
                }else{

                    if ($client->save()) {
                        echo ('Insert Client Record successfully for client.' . $clinic_number. '<br>');
                    }else{
                        echo ('Could not insert record for client.' . $clinic_number. '<br>');
                    }
                }


            }
        }
        echo  "Done";
    }


    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = [];
        $data = array();

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 5000, $delimiter)) !== false) {
                if (!$header) {
                    // print_r("header not empty");
                    $header = $row;
                } else {
                    // print_r("header empty");
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

}
