@extends('layouts.master')
@section('main-content')
<div class="breadcrumb">
                <h1>Dashboard</h1>
                <ul>
                    <li><a href="">Dashboard</a></li>
                    <li></li>
                </ul>
            </div>

            <div class="separator-breadcrumb border-top"></div>

<div class="col-md-12">

    <form role="form" method="post" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
                <div class="form-group">

                    <select class="form-control filter_partner  input-rounded input-sm select2 step2-select" id="partners" name="partner">
                        <option value="">Please select Partner</option>
                        @foreach ($all_partners as $partner => $value)
                        <option value="{{ $partner }}"> {{ $value }}</option>
                        @endforeach

                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <select class="form-control county  input-rounded input-sm select2 step2-select" id="counties" name="county">
                        <option value="">Please select County:</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                    <select class="form-control subcounty input-rounded input-sm select2" id="subcounties" name="subcounty">
                        <option value="">Please Select Sub County : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;">Loading , Please Wait ...</span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="facilities" name="facility">
                        <option value="">Please select Facility : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">

                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <!-- ICON BG -->
    <div class="col-lg-2 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <div class="content">
                    <p class="text-muted mt-2 mb-0">Target Active Clients</p>

                    <p id="all_target_clients" class="text-primary text-20 line-height-1 mb-2">{{$all_target_clients}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <div class="content">
                    <p class="text-muted mt-2 mb-0">No. of Active Clients</p>
                    <p id="all_clients_number" class="text-primary text-20 line-height-1 mb-2">{{$all_clients_number}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <div class="content">
                    <p class="text-muted mt-2 mb-0">% No. of Active Clients</p>
                    <p id="pec_client_count" class="text-primary text-20 line-height-1 mb-2">{{$pec_client_count}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <div class="content">
                    <p class="text-muted mt-2 mb-0">Consented Clients</p>
                    <p id="all_consented_clients" class="text-primary text-20 line-height-1 mb-2">{{$all_consented_clients}}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-6 col-sm-2">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <div class="content">
                    <p class="text-muted mt-2 mb-0">Future Appointments</p>
                    <p id="all_future_appointments" class="text-primary text-20 line-height-1 mb-2">{{$all_future_appointments}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-6 col-sm-2">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <div class="content">
                    <a class="has-arrow" href="{{route('Reports-active-facilities')}}">
                        <p class="text-muted mt-2 mb-0">No. of Facilities</p>
                    </a>
                    <p id="number_of_facilities" class="text-primary text-20 line-height-1 mb-2">{{$number_of_facilities}}</p>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card mb-4">
            <div class="card-body">

                <div id="mainGraph" class="col" style="height: 450px;margin-top:40px;"></div> <br />

            </div>
        </div>
    </div>
</div>

<div class='loadscreen hidden' id="preloader">

        <div class="loader spinner-bubble spinner-bubble-primary">


        </div>
    </div>


@endsection

@section('page-js')

<script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/themes/high-contrast-light.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/js/bootstrap-select.min.js">
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_dashboard_counties/' + partnerID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="county"]').empty();
                        $('select[name="county"]').append('<option value="">Please County</option>');
                        $.each(data, function(key, value) {
                            $('select[name="county"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="county"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="county"]').on('change', function() {
            var countyID = $(this).val();
            if (countyID) {
                $.ajax({
                    url: '/get_dashboard_sub_counties/' + countyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="subcounty"]').empty();
                        $('select[name="subcounty"]').append('<option value="">Please Select Sub County</option>');
                        $.each(data, function(key, value) {
                            $('select[name="subcounty"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="subcounty"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="subcounty"]').on('change', function() {
            var subcountyID = $(this).val();
            if (subcountyID) {
                $.ajax({
                    url: '/get_dashboard_facilities/' + subcountyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
                        $.each(data, function(key, value) {
                            $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="facility"]').empty();
            }
        });
    });


    $('#dataFilter').on('submit', function(e) {
        e.preventDefault();

        $("#preloader").show();

        let partners = $('#partners').val();
        let counties = $('#counties').val();
        let subcounties = $('#subcounties').val();
        let facilities = $('#facilities').val();


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'GET',
            data: {
                "partners": partners,
                "counties": counties,
                "subcounties": subcounties,
                "facilities": facilities
            },
            url: "{{ route('filter_dashboard') }}",
            success: function(data) {

                    Registered = parseInt(data.registered_clients_count)
                    Consented = parseInt(data.consented_clients_count)


                $("#all_clients_number").html(data.all_clients_number);
                $("#pec_client_count").html(data.pec_client_count);
                $("#all_target_clients").html(data.all_target_clients);
                $("#all_consented_clients").html(data.all_consented_clients);
                $("#all_future_appointments").html(data.all_future_appointments);
                $("#number_of_facilities").html(data.number_of_facilities);


                mainChart.series[0].setData([Registered, Consented]);

                //mainChart.redraw();

               console.log(data.consented_clients_count);

           }
        });

        $("#preloader").hide();
    });



    var RegisteredClients = <?php echo json_encode($registered_clients_count) ?>;
    var ConsentedClients = <?php echo json_encode($consented_clients_count) ?>;

    parseConsented = JSON.parse(ConsentedClients);
    parseRegistered = JSON.parse(RegisteredClients);
    //Registered = JSON.parse(Months);

    //console.log(Months);

    var mainChart = Highcharts.chart('mainGraph', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Number Series'
        },
        xAxis: {
            categories: ['Registered Clients', 'Consented Clients']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Count'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: ( // theme
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || 'gray'
                }
            }
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Total Clients: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
            }
        },
        series: [{

            name: 'Clients Trends',
            data: [parseRegistered, parseConsented]
        }],

    });

    var colors = Highcharts.getOptions().colors;
</script>

@endsection