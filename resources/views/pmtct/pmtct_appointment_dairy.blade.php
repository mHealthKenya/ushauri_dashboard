@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection
@section('main-content')

    <div class="separator-breadcrumb border-top"></div>


            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card mb-4">
                    <div class="panel-heading">
                      <i class="icon-table">Booked PMTCT List</i>
                     </div>
                        <div class="card-body">
                        <div class="table-responsive">
                                                <table id="pmtct_booked_table" class="display table table-striped table-bordered" style="width:50%">
                                                <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Clinic Number</th>
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Last Name</th>
                                                <th>Date Booked</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_booked_pmtct_clients) > 0)
                                                @foreach($all_booked_pmtct_clients as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->f_name}}</td>
                                                        <td>  {{$result->m_name}}</td>
                                                        <td>  {{$result->l_name}}</td>
                                                        <td>  {{$result->appntmnt_date}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>

                                      </table>
                                    </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card mb-4">
                    <div class="panel-heading">
                         <i class="icon-table">Scheduled PMTCT List</i>
                          </div>
                        <div class="card-body">
                        <div class="table-responsive">
                                                <table id="pmtct_scheduled_table" class="display table table-striped table-bordered" style="width:50%">
                                                <thead>
                                            <tr>
                                            <th>No</th>
                                            <th>CCC Number</th>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Last Name</th>
                                            <th>Appointment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_schedule_appointment_clients) > 0)
                                                @foreach($all_schedule_appointment_clients as $clients)
                                                    <tr>
                                                    <td> {{ $loop->iteration }}</td>
                                                    <td> {{ ucwords($clients->clinic_number)}}</td>
                                                    <td>  {{$clients->f_name}}</td>
                                                    <td>  {{$clients->m_name}}</td>
                                                    <td>  {{$clients->l_name}}</td>
                                                    <td>  {{$clients->appntmnt_date}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>

                                     </table>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card mb-4">
                    <div class="panel-heading">
                      <i class="icon-table">UnScheduled PMTCT List</i>
                     </div>
                        <div class="card-body">
                        <div class="table-responsive">
                                                <table id="pmtct_unscheduled_table" class="display table table-striped table-bordered" style="width:50%">
                                                <thead>
                                            <tr>
                                            <th>No</th>
                                            <th>CCC Number</th>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Last Name</th>
                                            <th>Appointment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_unschedule_appointment_clients) > 0)
                                                @foreach($all_unschedule_appointment_clients as $clients)
                                                    <tr>
                                                    <td> {{ $loop->iteration }}</td>
                                                    <td> {{ ucwords($clients->clinic_number)}}</td>
                                                    <td>  {{$clients->f_name}}</td>
                                                    <td>  {{$clients->m_name}}</td>
                                                    <td>  {{$clients->m_name}}</td>
                                                    <td>  {{$clients->appntmnt_date}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
     </div>



@endsection

@section('page-js')


     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/js/bootstrap-select.min.js"> </script>
     <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>

     <script type="text/javascript">

$('#pmtct_booked_table').DataTable({
        columnDefs: [{
            targets: [0],
            orderData: [0, 1]
        }, {
            targets: [1],
            orderData: [1, 0]
        }, {
            targets: [4],
            orderData: [4, 0]
        }],
        "paging": true,
        "responsive":true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('#pmtct_scheduled_table').DataTable({
        columnDefs: [{
            targets: [0],
            orderData: [0, 1]
        }, {
            targets: [1],
            orderData: [1, 0]
        }, {
            targets: [4],
            orderData: [4, 0]
        }],
        "paging": true,
        "responsive":true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('#pmtct_unscheduled_table').DataTable({
        columnDefs: [{
            targets: [0],
            orderData: [0, 1]
        }, {
            targets: [1],
            orderData: [1, 0]
        }, {
            targets: [4],
            orderData: [4, 0]
        }],
        "paging": true,
        "responsive":true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
     // multi column ordering

        </script>

@endsection