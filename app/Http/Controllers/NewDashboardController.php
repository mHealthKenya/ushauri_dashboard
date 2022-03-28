<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Appointments;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use Auth;

class NewDashboardController extends Controller
{
    public function dashboard()
    {

        // showing all the active clients, all appointments, missed appointments
        if (Auth::user()->access_level == 'Facility') {
            $client = Client::where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $missed_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '!=', 'Booked')
                ->orwhere('tbl_appointment.app_status', '!=', 'Notified')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $missed_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '!=', 'Booked')
                ->orwhere('tbl_appointment.app_status', '!=', 'Notified')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client = Client::where('status', '=', 'Active')
                ->count();
            $appointment = Appointments::select('id')
                ->count();
            $missed_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '!=', 'Booked')
                ->orwhere('tbl_appointment.app_status', '!=', 'Notified')
                ->count();
        }

        // showing all consented clients
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_consented = Client::where('smsenable', '=', 'Yes')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_consented = Client::where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }

        // showing all non consented clients
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_nonconsented = Client::where('smsenable', '!=', 'Yes')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_nonconsented = Client::where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
    }

    public function client_charts()
    {

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            // active clients by gender
            $clients_male = Client::where('gender', '=', '2')
                ->where('status', '=', 'Active')
                ->count();

            $clients_female = Client::where('gender', '=', '1')
                ->where('status', '=', 'Active')
                ->count();

            // active clients by age distribution
            $client_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();

            $client_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();

            $client_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();

            $client_to_twentyfive_above = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 25) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();
            // consented clients by gender

            $client_consented_male = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '2')
                ->count();
            $client_consented_female = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '1')
                ->count();
            // non consented clients by gender
            $client_nonconsented_male = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '2')
                ->count();
            $client_nonconsented_female = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '1')
                ->count();
            // consented clients by age distribution
            $client_consented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
            $client_consented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
            $client_consented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
            $client_consented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
            $client_nonconsented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
            $client_nonconsented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
            $client_nonconsented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $clients_male = Client::where('gender', '=', '2')
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $clients_female = Client::where('gender', '=', '1')
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            // active clients by age distribution

            $client_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $client_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $client_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $client_to_twentyfive_above = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 25) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            // consented clients by gender
            $client_consented_male = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '2')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_female = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '1')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            // non consented clients by gender
            $client_nonconsented_male = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '2')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_female = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '1')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            // consented clients by age distribution
            $client_consented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // active clients by age distribution

            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
    }

    public function appointment_chart()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            // main appointments
            $appointment = Appointments::select('id')
                ->count();
            $appointment_honoured = Appointments::where('date_attended', '=', 'appntmnt_date')
                ->count();
            $appointment_not_honoured = Appointments::where('date_attended', '!=', 'appntmnt_date')
                ->count();
            // appointment by gender
            $appointment_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '2')
                ->count();
            $appointment_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '1')
                ->count();
            // appointment by age
            $appointment_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->count();
            $appointment_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->count();
            $appointment_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->count();
            $appointment_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->count();
            $appointment_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
                ->count();
            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();
            // appointment honored by age
            $appointment_honored_to_nine = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_honored_to_fourteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_honored_to_nineteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_honored_to_twentyfour = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_honored_to_twentyfive_above = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->count();
            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_not_honored_to_fourteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_not_honored_to_nineteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_not_honored_to_twentyfour = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->count();
            $appointment_not_honored_to_twentyfive_above = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('date_attended', '!=', 'appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            // appointment by gender
            $appointment_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            // appointment by age
            $appointment_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            // appointment honored by age
            $appointment_honored_to_nine = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_honored_to_fourteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_honored_to_nineteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_honored_to_twentyfour = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_honored_to_twentyfive_above = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_not_honored_to_fourteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_not_honored_to_nineteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_not_honored_to_twentyfour = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment_not_honored_to_twentyfive_above = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('date_attended', '!=', 'appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // appointment by gender
            $appointment_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // appointment by age
            $appointment_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // appointment honored by age
            $appointment_honored_to_nine = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_honored_to_fourteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_honored_to_nineteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_honored_to_twentyfour = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_honored_to_twentyfive_above = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_not_honored_to_fourteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_not_honored_to_nineteen = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_not_honored_to_twentyfour = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment_not_honored_to_twentyfive_above = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
    }
    public function missed_appointment_chart()
    {
        // main dashboard all
        $appointment_total_missed = Appointments::where('date_attended', '!=', 'appntmnt_date')
            ->count();
        $appointment_missed = Appointments::where('app_status', '=', 'Missed')
            ->count();
        $appointment_defaulted = Appointments::where('app_status', '=', 'Defaulted')
            ->count();
        $appointment_lftu = Appointments::where('app_status', '=', 'LTFU')
            ->count();

        // Total missed appointment by gender
        $appointment_total_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
            ->where('tbl_client.gender', '=', '1')
            ->count();
        $appointment_total_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
            ->where('tbl_client.gender', '=', '2')
            ->count();

        // Total missed appointment by age
        $appointment_total_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
            ->count();
        $appointment_total_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
            ->count();
        $appointment_total_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
            ->count();
        $appointment_total_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
            ->count();
        $appointment_total_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
            ->count();

        // missed appointment by gender
        $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->where('tbl_client.gender', '=', '1')
            ->count();
        $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->where('tbl_client.gender', '=', '1')
            ->count();

        // missed appointment by age
        $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->count();
        $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->count();
        $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->count();
        $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->count();
        $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->count();

        // defaulted appointment by gender
        $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->where('tbl_client.gender', '=', '1')
            ->count();
        $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->where('tbl_client.gender', '=', '2')
            ->count();

        // defaulted appointment by age
        $appointment_defaulted_to_nine = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->count();
        $appointment_defaulted_to_fourteen = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->count();
        $appointment_defaulted_to_nineteen = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->count();
        $appointment_defaulted_to_twentyfour = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->count();
        $appointment_defaulted_to_twentyfive_above = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->count();

        // ltfu appointment by gender
        $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->where('tbl_client.gender', '=', '1')
            ->count();
        $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->where('tbl_client.gender', '=', '2')
            ->count();

        // ltfu appointment by age
        $appointment_ltfu_to_nine = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->count();
        $appointment_ltfu_to_fourteen = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->count();
        $appointment_ltfu_to_nineteen = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->count();
        $appointment_ltfu_to_twentyfour = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->count();
        $appointment_ltfu_to_twentyfive_above = join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->count();
    }
}
