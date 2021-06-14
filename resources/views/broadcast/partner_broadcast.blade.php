<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

@extends('layouts.master')

@section('main-content')
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title mb-3 text-center">New Broadcast To Partner Clients</div>
                            <form role="form" method="post"action="{{route('send-broadcast')}}">
                                {{ csrf_field() }}
                                <div class="row">

                                    <div class="col-md-12 form-group mb-3">
                                        <label>Select Facility</label>
                                        <select class="selectpicker form-control" data-width="100%" id="mfl_code" name="mfl_code" multiple data-actions-box="true">
                                            @if (count($facilities) > 0)
                                                @foreach($facilities as $facility)
                                                <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                                                    @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label>Select Group</label>
                                        <select class="selectpicker form-control" data-width="100%" id="groups" name="groups" multiple data-actions-box="true">
                                            @if (count($groups) > 0)
                                                @foreach($groups as $group)
                                                <option value="{{$group->id }}">{{ ucwords($group->name) }}</option>
                                                    @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label>Select Gender</label>
                                        <select class="selectpicker form-control" data-width="100%" id="genders" name="genders" multiple data-actions-box="true">
                                            @if (count($genders) > 0)
                                                @foreach($genders as $gender)
                                                <option value="{{$gender->id }}">{{ ucwords($gender->name) }}</option>
                                                    @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label for="message">Message</label>
                                        <textarea class="form-control" rows="3" id="message" name="message" placeholder="message"> </textarea>
                                    </div>


                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

@endsection