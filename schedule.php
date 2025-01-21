@extends('layouts.customerLayout')

@section('styles')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{\config('app.url')}}/assets/vendor/MDTimePicker-master/dist/mdtimepicker.css" />
<link rel="stylesheet" type="text/css" href="{{\config('app.url')}}/assets/vendor/MDTimePicker-master/dist/mdtimepicker-theme.css">
<link rel="stylesheet" type="text/css" href="{{\config('app.url')}}/dist/css/select2.min.css">
<style>
.meeting_id_button {
            width:90px;
            height: 40px;
            background-color:#1bcc8d;
            color: white; cursor:pointer;
            border-radius:0.375rem;
    font-weight: 500; outline:none; border:none;
            position: absolute; right:3%; top:4.7%;
        }
        .meeting_id_button:hover {background-color:#11a973; border-radius:0.375rem;  cursor:pointer;}
        </style>
@endsection
<?php

use App\Models\user_settings;

?>
@section('content')
<div class="grid grid-cols-12 gap-6">

    <!-- END: General Report -->
    <!-- BEGIN: Weekly Top Products -->
    <div class="col-span-12 mt-6">
        <div class="intro-y block sm:flex items-center h-10">
            <h2 class="text-lg font-medium truncate mr-5">
            {{  __('messages.schedule_a_new_meeting')}}
            </h2>

        </div>
        <!--  @include('notification.notify')-->
        <div class="intro-y col-span-12 lg:col-span-6">
            <!-- BEGIN: Form Validation -->





            <div class="" id="form-validation">
                <div class="preview">

                    <form class="validate-form1" id="meetingfrm" name="meetingfrm" action="/meetings" method="post" enctype="multipart/form-data" >
                        {{ csrf_field() }}
                        <input type="hidden" id="subscription_id" name="subscription_id" value="{{$subscription_details->id}}" />


                        <div class="grid grid-cols-12 gap-6">



                            <div class="col-span-12 lg:col-span-6">

                                <div class="intro-y box p-5">
                                <div class="input-form">

                                        <label class="flex flex-col sm:flex-row">Meeting ID <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600"></span> </label>
                                        <span id="smeeting_id_text">{{$meeting_id_custom}} </span> <?php if($edit_meeting_id==1) { ?> <a id="smeeting_id_text_link" class="button text-white bg-theme-1 shadow-md mb-2 ml-5" onclick="editmeetingid();">Edit</a> <?php } 
                                        else { ?>  
                                            <a  id="editmeetingidalert" class="button text-white bg-theme-1 shadow-md mb-2 ml-5" onclick="editmeetingidalert();">Edit</a>
                                            <?php } ?>


                                      <div>  <span id="smeeting_id_field" style="display:none;"><input type="text" id="meeting_id"  class="input w-full border mt-2" placeholder="Meeting ID" minlength="6" maxlength="20" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" onkeyup="checkmeetingid(event)" value="{{$meeting_id_custom}}" onchange="checkmeetingid();">
                                        <input class="meeting_id_button" onclick="hidemeetingbutton();" type="button" value="Save" id="meeting_id_button"></input>   </span>  </div> 

                                       <span id="meeting_id_text"  style="color:red;display:none;" >Meeting ID unavailable. Choose a different Meeting ID.</span>
                                       <span id="meeting_id_error_text"  style="color:red;display:none;" >Meeting ID must have 6 characters</span>
                                    </div>

                                    <div class="input-form mt-2">
                                        <label class="flex flex-col sm:flex-row">{{  __('messages.meetingname')}} <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">{{  __('messages.required_at_least_2_characters')}}</span> </label>
                                       <input type="hidden" id="meetingid" name="meeting_id" value={{$meeting_id_custom}}>
                                        <input type="text" name="topic" class="input w-full border mt-2" placeholder="Meeting Name" minlength="2" required autocomplete="new-password">
                                    </div>
                                    <div class="input-form mt-3">
                                        <label class="flex flex-col sm:flex-row">{{  __('messages.meeting_description')}}  <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">{{  __('messages.optional')}} </span> </label>
                                        <input type="textbox" name="agenda" class="input w-full border mt-2" placeholder="Meeting Description" autocomplete="new-password">
                                    </div>

                                    <div class="input-form mt-3">
                                        <label class="flex flex-col sm:flex-row">{{  __('messages.meeting_passcode')}}<span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">{{  __('messages.required_at_least_6_characters')}} </span> </label>
                                        <input type="password" name="passcode" id="id_password" class="input w-full border mt-2" placeholder="Passcode" minlength="6" required autocomplete="new-password"><img src="{{\config('app.url')}}/dist/images/eye-off.svg" id="togglePassword"> <!--<i data-feather="eye" id="togglePassword" style="margin-left: -30px; cursor: pointer; display:inline;"></i>-->
                                    </div>
                                    <div class="grid grid-cols-12">
                                        <div class="intro-y col-span-12 lg:col-span-6">
                                            <div class="input-form">
                                                <label class="flex flex-col sm:flex-row">{{  __('messages.meeting_date')}} </label>

                                            </div>

                                            <div class="mt-2  relative text-gray-700 dark:text-gray-300">

                                                <div class="absolute rounded-l w-10 h-full flex items-center justify-center bg-gray-100 border text-gray-600 dark:bg-dark-1 dark:border-dark-4"> <i data-feather="calendar" class="w-4 h-4"></i> </div>


                                                <input type="text" name="meeting_date" class="datepicker input  pl-12 border" data-single-mode="true" style="padding-left: 45px;" readonly>


                                            </div>
                                        </div>

                                        <div class="intro-y col-span-12 lg:col-span-6">

                                            <div class="input-form">
                                                <label class="flex flex-col sm:flex-row">{{  __('messages.meeting_time')}} </label>

                                            </div>
                                            <div class="mt-2 relative text-gray-700 dark:text-gray-300">
                                                <div class="absolute rounded-l w-10 h-full flex items-center justify-center bg-gray-100 border text-gray-600 dark:bg-dark-1 dark:border-dark-4"> <i data-feather="clock" class="w-4 h-4"></i> </div>
                                                <input type="text" name="meeting_time" id="timepicker" placeholder="Pick a time." class="input pl-12 border" value="" style="padding-left: 45px;" />
                                            </div>

                                        </div>
                                        <div class="col-span-12 lg:col-span-6 mt-3" id="duration-hr-div">
                                            <div class="input-form">
                                                <label class="flex flex-col sm:flex-row">{{  __('messages.duration')}} </label>
                                                <select name="duration_hr" id="duration_hr" class="tail-select" style="margin-top:.5rem; width:75px; margin-right:6px;">
                                                    <option value="0">0</option>
                                                    <option value="1" selected>1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                    <option value="24">24</option>
                                                </select> <label class="sm:w-20 sm:text-right sm:ml-2 mr-5" style="line-height:57px">{{  __('messages.hr')}}</label>

                                                <select name="duration_min" id="duration_min" class="tail-select " style="margin-top:.5rem; width:75px; margin-right:6px;">
                                                    <option selected value="0">0</option>
                                                    <option value="15">15</option>
                                                    <option value="30">30</option>
                                                    <option value="45">45</option>

                                                </select> <label class="w-full sm:w-20 sm:text-right sm:ml-2 mr-5" style="line-height:57px">{{  __('messages.min')}}</label>

                                            </div>
                                        </div>
                                        <div class="col-span-12 lg:col-span-6 mt-3">
                                            <label>{{  __('messages.time_zone')}}</label>
                                            <div class="mt-2" id="tail-select-full-width">
                                                <select style="width:75px; margin-right:6px;" data-search="true" name="timezone" class="tail-select w-full">
                                                    <?php if (count($timezones)) {
                                                        for ($i = 0; $i < count($timezones); $i++) { ?>
                                                            <option value="<?php echo $timezones[$i]['value']; ?>" <?php if ($timezones[$i]['value'] == $user->timezone) { ?> selected <?php } ?>><?php echo $timezones[$i]['name']; ?></option>

                                                    <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-span-12 lg:col-span-6 mt-3">
                                            <label>{{  __('messages.subscription')}}</label>
                                            <div class="mt-2" id="tail-select-full-width">
                                                {{ (isset($subscription_details->isowner) && $subscription_details->isowner==1?'Personal':(isset($subscription_details->organization) && !empty($subscription_details->organization)?$subscription_details->organization:$subscription_details->owner_name . "'s")) . ' (' . $subscription_details->subscription_plan_name . ' ' . ($subscription_details->isowner==1?'Subscription':'Host Account')  . ')'}}
                                            </div>
                                        </div>
                                        <div class="col-span-12 lg:col-span-12">

                                            <div class="input-form mt-5">

                                                <div class="dropdown-mul-2 mt-5"> <label class=""> {{  __('messages.meeting_attendees')}} </label>
                                                    <a href="javascript:;" data-toggle="modal" data-target="#new-contact-modal" class="button text-white bg-theme-1 shadow-md mb-2" style="float:right;">+ {{  __('messages.add_new_contact')}}</a>
                                                    <select id="sel_attendees" name="attendees" onchange=" addToAttdList(this); " class="w-full" style="height:30px; width:100%;">
                                                        <option value="0">{{  __('messages.select_attendees')}} </option>
                                                        <?php
                                                        foreach ($contacts as $contact) {
                                                        ?>
                                                            <option value="<?php echo $contact->id; ?>"><?php echo $contact->email; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div id="attendees">
                                                    <ul id="attendees1">

                                                    </ul>
                                                </div>
                                            </div>


                                            <div class="input-form mt-3">
                                                <label class="flex flex-col sm:flex-row"> {{  __('messages.pick_moderator')}}<span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600"></span> </label>
                                                <table class="table" id="pick_moderator">
                                                    <thead>
                                                        <th>#</th>

                                                        <th> {{  __('messages.email')}}</th>
                                                    </thead>
                                                    <tbody id="attendees2">
                                                        <tr>
                                                            <td><input type="checkbox" checked disabled style="width: 16px; height: 16px;"></td>

                                                            <td>{{ $user->email }} ({{  __('messages.organiser')}})</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-span-12 mt-3">
                                                <label>{{  __('messages.meeting_groups')}} </label>
                                                <div class="mt-2" id="multi-select">
                                                    <div class="preview">
                                                        <select name=groups[] data-placeholder="Select Meeting Groups" data-search="true" class="tail-select w-full" multiple>
                                                            <?php if (count($contact_groups) > 0) { ?>
                                                                @foreach($contact_groups as $contact_group)
                                                                <option value="<?php echo $contact_group->id; ?>"><?php echo $contact_group->name; ?></option>

                                                                @endforeach
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-span-12 mt-3">
                                                <label>{{  __('messages.general_options')}} </label>
                                                <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                                    <input type="checkbox" name="options[]" value="ALLOW_GUEST" class="input border mr-2" id="vertical-checkbox-guest-user" checked>
                                                    <label class="cursor-pointer select-none" for="vertical-checkbox-guest-user">{{  __('messages.option_1')}}</label>
                                                </div>
                                                <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                                    <input type="checkbox" name="options[]" value="JOIN_ANYTIME" class="input border mr-2" id="vertical-checkbox-allow-anytime" checked onclick="if(($(this).is(':checked'))) { $('#allow_join_before_sec').hide(); } else {$('#allow_join_before_sec').show();} ">
                                                    <label class="cursor-pointer select-none" for="vertical-checkbox-allow-anytime">{{  __('messages.option_2')}}</label>
                                                </div>
                                                <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                                    <input type="checkbox" name="options[]" value="ENABLE_LOBBY" class="input border mr-2" id="vertical-checkbox-enable-lobby" checked>
                                                    <label class="cursor-pointer select-none" for="vertical-checkbox-enable-lobby">{{  __('messages.enable_lobby')}}</label>
                                                </div>
                                                @if(isset($subscription_feature['livepad']) && isset($subscription_feature['livepad']->is_available) && $subscription_feature['livepad']->is_available==1)
                                                <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                                    <input type="checkbox" name="options[]" value="LIVEPAD" class="input border mr-2" id="vertical-checkbox-live-pad" checked>
                                                    <label class="cursor-pointer select-none" for="vertical-checkbox-live-pad">{{  __('messages.livepad')}}</label>
                                                </div>
                                                @endif
                                                @if(isset($subscription_feature['whiteboard']) && isset($subscription_feature['whiteboard']->is_available) && $subscription_feature['whiteboard']->is_available==1)
                                                <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                                    <input type="checkbox" name="options[]" value="WHITE_BOARD" class="input border mr-2" id="vertical-checkbox-WhiteBoard" checked>
                                                    <label class="cursor-pointer select-none" for="vertical-checkbox-WhiteBoard">{{  __('messages.genericiframe')}}</label>
                                                </div>
                                                @endif


                                            </div>
                                            <br>





                                            <div class="input-form mt-3" id="allow_join_before_sec" style="display:none;">
                                                <label class="flex flex-col sm:flex-row">{{  __('messages.schedule_text_1')}} </label>
                                                <input type="number" name="allow_join_before" id="allow_join_before" class="input w-full border mt-2" placeholder="Minutes" style="width:50%"> <label class="w-full sm:w-20 sm:text-right sm:mr-5" style="display:inline;">{{  __('messages.minutes')}}</label>
                                            </div>




                                        </div>





                                    </div>

                                </div>
                            </div>
                            <div class="col-span-12 lg:col-span-6">


                                <div class="intro-y box p-5">

                                    <div class="col-span-12 lg:col-span-12">
                                        <div class="input-form">

                                            <label style="font-size:18px;">{{  __('messages.schedule_text_2')}}  &nbsp;&nbsp;</label> <input class="input input--switch border mr-2" style="vertical-align:middle;" name="is_recurring" type="checkbox" id="myCheck" value="1" onclick="myFunction()">



                                        </div>


                                        <div class="mt-3  relative text-gray-700 dark:text-gray-300">


                                            <div id="text" style="display:none">
                                                <div class="grid grid-cols-12 gap-3" id="horizontal-form">

                                                    <div class="col-span-12 lg:col-span-6">
                                                        <label class="w-full sm:w-20 sm:text-left">{{  __('messages.schedule_text_3')}}</label>
                                                        <select id="recurring_type" name="recurring_type" class="tail-select" onchange="onRecurringTypeChange(this);">
                                                            <option selected value="daily">{{  __('messages.schedule_text_4')}}</option>
                                                            <option value="weekly">{{  __('messages.schedule_text_5')}}</option>
                                                            <option value="monthly">{{  __('messages.monthly')}}</option>
                                                            <?php // <option value="no-fixed-time">No Fixed Time</option>
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div id="repeat_interval_div" class="col-span-12 lg:col-span-6">
                                                        <label class="w-full sm:w-20 sm:text-left">{{  __('messages.schedule_text_6')}} </label>
                                                        <select name="repeat_interval" id="repeat_interval" class="tail-select ">
                                                            <option selected value="1">{{  __('messages.schedule_text_7')}}</option>
                                                            <option value="2">{{  __('messages.schedule_text_8')}}</option>
                                                            <option value="3">{{  __('messages.schedule_text_9')}}</option>
                                                            <option value="4">{{  __('messages.schedule_text_10')}}</option>
                                                            <option value="5">{{  __('messages.schedule_text_11')}}</option>
                                                            <option value="6">{{  __('messages.schedule_text_12')}}</option>
                                                            <option value="7">{{  __('messages.schedule_text_13')}}</option>
                                                            <option value="8">{{  __('messages.schedule_text_14')}}</option>
                                                            <option value="9">{{  __('messages.schedule_text_15')}}</option>
                                                            <option value="10">{{  __('messages.schedule_text_16')}}</option>
                                                            <option value="11">{{  __('messages.schedule_text_17')}}</option>
                                                            <option value="12">{{  __('messages.schedule_text_18')}}</option>
                                                            <option value="13">{{  __('messages.schedule_text_19')}}</option>
                                                            <option value="14">{{  __('messages.schedule_text_20')}}</option>
                                                            <option value="15">{{  __('messages.schedule_text_21')}}</option>
                                                        </select>&nbsp;<label class="w-full sm:w-10 sm:text-right sm:mr-5" ID='repeat_interval_lbl'>{{  __('messages.schedule_text_22')}}</label>
                                                    </div>

                                                    <div id="weeklyWeekDays" class="col-span-12 lg:col-span-12 mt-3" style="display:none;">
                                                        <label class="w-full sm:w-20 sm:text-left">Occurs on</label>
                                                        <input class="input border mr-2" type="checkbox" name="weeklyWeekDays[]" value="1" aria-label="Occurs on Sunday">{{  __('messages.schedule_text_23')}}&nbsp;&nbsp;
                                                        <input class="input border mr-2" type="checkbox" name="weeklyWeekDays[]" checked value="2" aria-label="Occurs on Monday">{{  __('messages.schedule_text_24')}}&nbsp;&nbsp;
                                                        <input class="input border mr-2" type="checkbox" name="weeklyWeekDays[]" value="3" aria-label="Occurs on Tuesday">{{  __('messages.schedule_text_25')}}&nbsp;&nbsp;
                                                        <input class="input border mr-2" type="checkbox" name="weeklyWeekDays[]" value="4" aria-label="Occurs on Wednesday">{{  __('messages.schedule_text_26')}}&nbsp;&nbsp;
                                                        <input class="input border mr-2" type="checkbox" name="weeklyWeekDays[]" value="5" aria-label="Occurs on Thursday">{{  __('messages.schedule_text_27')}}&nbsp;&nbsp;
                                                        <input class="input border mr-2" type="checkbox" name="weeklyWeekDays[]" value="6" aria-label="Occurs on Friday">{{  __('messages.schedule_text_28')}}&nbsp;&nbsp;
                                                        <input class="input border mr-2" type="checkbox" name="weeklyWeekDays[]" value="7" aria-label="Occurs on Saturday">{{  __('messages.schedule_text_29')}}
                                                    </div>

                                                    <div id="month_div" class="flex flex-col sm:flex-row items-center col-span-12 lg:col-span-12" style="display:none;">

                                                        <label class="w-full sm:w-20 sm:text-left">{{  __('messages.schedule_text_30')}}</label>
                                                        <input type="radio" name="monthlyBy" value="BYMONTHDAY" checked="checked" aria-label=""> &nbsp;
                                                        <select name="monthlyByDay" class="tail-select">
                                                            <option selected value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                            <option value="9">9</option>
                                                            <option value="10">10</option>
                                                            <option value="11">11</option>
                                                            <option value="12">12</option>
                                                            <option value="13">13</option>
                                                            <option value="14">14</option>
                                                            <option value="15">15</option>
                                                            <option value="16">16</option>
                                                            <option value="17">17</option>
                                                            <option value="18">18</option>
                                                            <option value="19">19</option>
                                                            <option value="20">20</option>
                                                            <option value="21">21</option>
                                                            <option value="22">22</option>
                                                            <option value="23">23</option>
                                                            <option value="24">24</option>
                                                            <option value="25">25</option>
                                                            <option value="26">26</option>
                                                            <option value="27">27</option>
                                                            <option value="28">28</option>
                                                            <option value="29">29</option>
                                                            <option value="30">30</option>
                                                            <option value="31">31</option>
                                                        </select> <label class="w-full sm:w-20 sm:text-right sm:mr-5">{{  __('messages.schedule_text_31')}}</label>
                                                        <br />
                                                        <div class="flex flex-col sm:flex-row items-center mt-3" style="clear:both;">
                                                            <input type="radio" name="monthlyBy" value="BYDAY" aria-label="End by specified date time">
                                                            <select name="montlyByWeekdayIndex" class="tail-select">
                                                                <option selected value="first">{{  __('messages.schedule_text_32')}}</option>
                                                                <option value="second">{{  __('messages.schedule_text_33')}}</option>
                                                                <option value="third">{{  __('messages.schedule_text_34')}}</option>
                                                                <option value="fourth">{{  __('messages.schedule_text_35')}}</option>
                                                                <option value="last">{{  __('messages.schedule_text_36')}}</option>
                                                            </select>
                                                            <select name="montlyByWeekday" class="tail-select">
                                                                <option selected value="sunday">{{ __('messages.schedule_text_37')}}</option>
                                                                <option value="monday">{{ __('messages.schedule_text_38')}}</option>
                                                                <option value="tuesday">{{ __('messages.schedule_text_39')}}</option>
                                                                <option value="wednesday">{{ __('messages.schedule_text_40')}}</option>
                                                                <option value="thursday">{{ __('messages.schedule_text_41')}}</option>
                                                                <option value="friday">{{ __('messages.schedule_text_42')}}</option>
                                                                <option value="saturday">{{ __('messages.schedule_text_43')}}</option>
                                                            </select> <label class="w-full sm:w-20 sm:text-right sm:mr-5">{{ __('messages.schedule_text_44')}} </label>

                                                        </div>
                                                    </div>

                                                    <div id="end_date_div" class="flex flex-col sm:flex-row items-center col-span-12 lg:col-span-12">


                                                        <input type="radio" name="endBy" value="END_DATETIME" checked="checked" aria-label="End by specified date time"> &nbsp;<label class="w-full sm:w-20 sm:text-left">{{ __('messages.schedule_text_45')}}</label>
                                                        <div style="position: relative;    padding: 10px;" class="rounded-l w-10 flex items-center justify-center bg-gray-100 border text-gray-600 dark:bg-dark-1 dark:border-dark-4"> <i data-feather="calendar" class="w-4 h-4"></i> </div>

                                                        <input type="text" name="end_date_time" style="width:110px;" id="end_date_time" readonly class="datepicker input pl-12 border mr-5" data-single-mode="true"> &nbsp; &nbsp;
                                                        <label class=" sm:w-12 sm:text-left"><strong>(OR)</strong></label> &nbsp; &nbsp;
                                                        <input type="radio" name="endBy" value="END_TIMES" aria-label="End after specified occurrences"> &nbsp;<label class="w-full sm:w-20 sm:text-left">{{ __('messages.schedule_text_46')}}</label>
                                                        <select name="end_times" class="tail-select mr-5">
                                                            <option selected value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                            <option value="9">9</option>
                                                            <option value="10">10</option>
                                                            <option value="11">11</option>
                                                            <option value="12">12</option>
                                                            <option value="13">13</option>
                                                            <option value="14">14</option>
                                                            <option value="15">15</option>
                                                        </select> <label class="sm:w-8 sm:text-right sm:mr-5 mr-5">{{ __('messages.schedule_text_47')}}</label>




                                                    </div>


                                                </div>

                                            </div>
                                        </div>

                                    </div>





                                    <div class="grid grid-cols-12 gap-6 mt-3">
                                        <div class="col-span-12 lg:col-span-6" >
                                        <label>{{ __('messages.schedule_text_48')}}</label>
                                        <?php $is_disable = 1; ?>
                                        @if((isset($subscription_feature['dropbox-recording']) && isset($subscription_feature['dropbox-recording']->is_available) && $subscription_feature['dropbox-recording']->is_available==1) || (isset($subscription_feature['meet-hour-recording']) && isset($subscription_feature['meet-hour-recording']->is_available) && $subscription_feature['meet-hour-recording']->is_available==1) || (isset($subscription_feature['custom-aws-s3']) && isset($subscription_feature['custom-aws-s3']->is_available) && $subscription_feature['custom-aws-s3']->is_available==1))
                                        <?php $is_disable = 0; ?>
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="ENABLE_RECORDING" class="input border mr-2" id="ENABLE_RECORDING_id" checked onclick="uncheckautostartrecording();">
                                            <label class="cursor-pointer select-none" for="ENABLE_RECORDING_id">{{ __('messages.enable_recording')}}</label>
                                        </div>

                                        @if(isset($subscription_feature['auto-start-recording']) && isset($subscription_feature['auto-start-recording']->is_available) && $subscription_feature['auto-start-recording']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="AUTO_START_RECORDING" class="input border mr-2" id="AUTO_START_RECORDING_id" onclick="checkrecording();">
                                            <label class="cursor-pointer select-none" for="AUTO_START_RECORDING_id">{{ __('messages.auto_start_recording')}}</label>
                                        </div>
                                        @endif
                                        @endif
                                        @if(isset($subscription_feature['mute-audio-video']) && isset($subscription_feature['mute-audio-video']->is_available) && $subscription_feature['mute-audio-video']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="MUTE_PARTICIPANTS" class="input border mr-2" id="vertical-checkbox-mute-upon-entry">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-mute-upon-entry">{{ __('messages.mute_participants_upon_entry')}}</label>
                                        </div>

                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="VIDEO_MUTE_PARTICIPANTS" class="input border mr-2" id="vertical-checkbox-video-mute-upon-entry">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-video-mute-upon-entry">{{ __('messages.video_mute_participants_upon_entry')}}</label>
                                        </div>
                                        @endif
                                        @if(isset($subscription_feature['force-audio-video-mute-participants']) && isset($subscription_feature['force-audio-video-mute-participants']->is_available) && $subscription_feature['force-audio-video-mute-participants']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="FORCE_MUTE_PARTICIPANTS" class="input border mr-2" id="vertical-checkbox-mute-participants">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-mute-participants">{{ __('messages.force_mute_participants')}}</label>
                                        </div>
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="FORCE_VIDEO_MUTE_PARTICIPANTS" class="input border mr-2" id="vertical-checkbox-video-mute-participants">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-video-mute-participants">{{ __('messages.force_video_mute_participants')}}</label>
                                        </div>
                                        @endif
                                        @if(isset($subscription_feature['youtube-live-stram']) && isset($subscription_feature['youtube-live-stram']->is_available) && $subscription_feature['youtube-live-stram']->is_available==1)

                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input id="id_ENABLE_LIVESTREAM" type="checkbox" name="options[]" value="ENABLE_LIVESTREAM" class="input border mr-2" checked onclick="uncheckautostartlivestream();">
                                            <label class="cursor-pointer select-none" for="id_ENABLE_LIVESTREAM">{{ __('messages.enable_livestream')}}</label>
                                        </div>


                                        @endif

                                        @if(isset($subscription_feature['auto-start-live-streaming']) && isset($subscription_feature['auto-start-live-streaming']->is_available) && $subscription_feature['auto-start-live-streaming']->is_available==1)

                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input id="id_AUTO_START_LIVESTREAMING" type="checkbox" name="options[]" value="AUTO_START_LIVESTREAMING" class="input border mr-2" onclick="checklivestreamsettings();">
                                            <label class="cursor-pointer select-none" for="id_AUTO_START_LIVESTREAMING">{{ __('messages.auto_start_live_streaming')}} + {{ __('messages.recording')}} <!--(<a href="/customer/liveStreamingSetting" target="_blank" style="color:#2D68BB">Settings</a>)--></label>
                                        </div>
                                        @endif

                                        @if(isset($subscription_feature['embed-meeting']) && isset($subscription_feature['embed-meeting']->is_available) && $subscription_feature['embed-meeting']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="ENABLE_EMBEED_MEETING" class="input border mr-2" id="vertical-checkbox-enable-embed-meeting" checked>
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-enable-embed-meeting">{{ __('messages.enable_embed_meeting')}}</label>
                                        </div>
                                        @endif
                                        <!--<div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                        <input type="checkbox" name="options[]" value="ENABLE_LOBBY" class="input border mr-2" id="vertical-checkbox-daniel-craig" checked>
                        <label class="cursor-pointer select-none" for="vertical-checkbox-automatic-recording">Enable Lobby</label>
                    </div>-->
                                        @if(isset($subscription_feature['donor-box-donation']) && isset($subscription_feature['donor-box-donation']->is_available) && $subscription_feature['donor-box-donation']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="DONOR_BOX" class="input border mr-2" id="vertical-checkbox-donorbox" checked>
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-donorbox">{{ __('messages.donorbox_visibilty')}}</label>
                                        </div>
                                        @endif
                                        @if(isset($subscription_feature['click-pledge-live-donation']) && isset($subscription_feature['click-pledge-live-donation']->is_available) && $subscription_feature['click-pledge-live-donation']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="CP_CONNECT" class="input border mr-2" id="vertical-checkbox-click-pledge" checked>
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-click-pledge">{{ __('messages.clickpledge_Connect')}}</label>
                                        </div>
                                        @endif


                                        @if(isset($subscription_feature['disable-screen-sharing-for-guest']) && isset($subscription_feature['disable-screen-sharing-for-guest']->is_available) && $subscription_feature['disable-screen-sharing-for-guest']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="DISABLE_SCREEN_SHARING_FOR_GUEST" class="input border mr-2" id="vertical-checkbox-disable-screen-sharing-guest">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-disable-screen-sharing-guest">{{ __('messages.disable_screen_sharing_for_guest')}}</label>
                                        </div>
                                        @endif

                                        @if(isset($subscription_feature['enable-pre-registration']) && isset($subscription_feature['enable-pre-registration']->is_available) && $subscription_feature['enable-pre-registration']->is_available==1)
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="DISABLE_JOIN_LEAVE_NOTIFICATIONS" class="input border mr-2" id="vertical-checkbox-disable-toast-for-participant-entry-and-exit">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-disable-toast-for-participant-entry-and-exit">{{ __('messages.disable_toast_for_participant_entry_and_exit')}}</label>
                                        </div>
                                        @endif

                                        </div>
                                        
                                        <div class="col-span-12 lg:col-span-6" >


                                        @if(isset($subscription_feature['notification-sound-in-conference-on-off']) && isset($subscription_feature['notification-sound-in-conference-on-off']->is_available) && $subscription_feature['notification-sound-in-conference-on-off']->is_available==1)
                                        <label>Sound Controls</label>
                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="PARTICIPANT_JOINED_SOUND_ID" class="input border mr-2" id="vertical-checkbox-participant-joined-sound-id">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-participant-joined-sound-id">{{ __('messages.participant_joined_sound_id')}}</label>
                                        </div>

                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="PARTICIPANT_LEFT_SOUND_ID" class="input border mr-2" id="vertical-checkbox-participant-left-sound-id">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-participant-left-sound-id">{{ __('messages.participant_left_sound_id')}}</label>
                                        </div>

                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="INCOMING_USER_REQ_SOUND_ID" class="input border mr-2" id="vertical-checkbox-incoming-user-req-sound-id">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-incoming-user-req-sound-id">{{ __('messages.incoming_user_req_sound_id')}}</label>
                                        </div>

                                        <div class="flex items-center text-gray-700 dark:text-gray-500 mt-2">
                                            <input type="checkbox" name="options[]" value="USER_WAITING_REGISTER" class="input border mr-2" id="vertical-checkbox-user-waiting-register">
                                            <label class="cursor-pointer select-none" for="vertical-checkbox-user-waiting-register">{{ __('messages.user_waiting_register')}}</label>
                                        </div>

                                        @endif
                                        </div>

                                    </div>

                                    <div class="col-span-12 lg:col-span-12 mt-10">
                                        <label>{{ __('messages.set_recording_storage')}} <?php if ($is_disable == 1) { ?> <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600" style="float:right;">{{ __('messages.schedule_text_49')}}</span> <?php } ?></label>
                                        <div class="mt-2" id="tail-select-full-width">
                                            <?php $default_recording_storage = user_settings::settings('default_recording_storage', $subscription_details->owner_id); ?>
                                            <select data-search="true" name="default_recording_storage" class="tail-select w-full" <?php if ($is_disable == 1) { ?> disabled <?php } ?>>
                                                <option value="">{{ __('messages.schedule_text_50')}} </option>
                                                @if((isset($subscription_feature['meet-hour-recording']) && isset($subscription_feature['meet-hour-recording']->is_available) && $subscription_feature['meet-hour-recording']->is_available==1))
                                                <option value="Local" {{ $default_recording_storage=="Local" ? "selected":'' }}>{{ __('messages.meet_hour_recording')}}</option>
                                                @endif
                                                @if((isset($subscription_feature['dropbox-recording']) && isset($subscription_feature['dropbox-recording']->is_available) && $subscription_feature['dropbox-recording']->is_available==1))
                                                <option value="Dropbox" {{ $default_recording_storage=="Dropbox" ? "selected":'' }}>{{ __('messages.dropbox')}}</option>
                                                @endif
                                                @if((isset($subscription_feature['custom-aws-s3']) && isset($subscription_feature['custom-aws-s3']->is_available) && $subscription_feature['custom-aws-s3']->is_available==1))
                                                <option value="Custom" {{ $default_recording_storage=="Custom" ? "selected":'' }}>{{ __('messages.custom_S3')}}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @if(isset($subscription_feature['youtube-live-stram']) && isset($subscription_feature['youtube-live-stram']->is_available) && $subscription_feature['youtube-live-stram']->is_available==1)
                                    <div class="col-span-12 lg:col-span-12 mt-12">
                                        <label>{{ __('messages.schedule_text_51')}} + {{ __('messages.record_settings')}}</label>
                                        <div class="mt-2" id="tail-select-full-width">


                                            <div class="flex sm:inline-flex md:block lg:hidden xl:flex">
                                                <div class="text-gray-700 text-center   py-2 m-2"><a href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview"><i data-feather="plus-circle"></i> {{ __('messages.add_destination')}} </a></div>

                                                <div id="icon_recording" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_recording_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Meet Hour Recording" alt="Meet Hour Recording"><svg height="20" width="20" viewBox="0 0 32 32">
                                                            <path d="M16 32C7.163 32 0 24.837 0 16S7.163 0 16 0s16 7.163 16 16-7.163 16-16 16zm2.167-13.556h-3.456v-1.851h3.261v-1.26h-3.261v-1.777h3.456v-1.361h-5.052v7.61h5.052v-1.361zM23.087 20c1.839 0 3.181-1.134 3.313-2.779h-1.554c-.153.838-.84 1.376-1.754 1.376-1.205 0-1.95-.997-1.95-2.6s.745-2.595 1.945-2.595c.909 0 1.601.575 1.754 1.45h1.554c-.116-1.656-1.501-2.853-3.308-2.853-2.214 0-3.578 1.524-3.578 3.997 0 2.479 1.369 4.003 3.578 4.003zM7.196 17.047h1.305l1.422 2.758h1.807l-1.607-3.006c.877-.348 1.385-1.192 1.385-2.162 0-1.514-1.03-2.442-2.706-2.442H5.6v7.61h1.596v-2.758zm0-3.565v2.341h1.4c.803 0 1.274-.432 1.274-1.165 0-.722-.497-1.176-1.289-1.176H7.196z" fill="#FF1010"></path>
                                                        </svg></a></div><br />
                                                <div id="icon_facebook2" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_facebook_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Facebook Live Stream" alt="Facebook Live Stream"><svg height="20" width="20" viewBox="0 0 512 512">
                                                            <rect height="512" rx="64" ry="64" width="512" fill="#3b5998"></rect>
                                                            <path d="M286.968 456V273.538h61.244l9.17-71.103h-70.413V157.04c0-20.588 5.72-34.62 35.235-34.62l37.656-.01V58.807C353.35 57.934 331.003 56 304.992 56c-54.288 0-91.45 33.146-91.45 93.998v52.437H152.14v71.103h61.4V456h73.428z" fill="#fff"></path>
                                                        </svg></a></div><br />
                                                <div id="icon_youtube2" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_youtube_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Youtube Live Stream" alt="Youtube Live Stream"><svg height="20" width="20" viewBox="0 0 32 32">
                                                            <path d="M31.67 9.179s-.312-2.353-1.271-3.389c-1.217-1.358-2.58-1.366-3.205-1.443C22.717 4 16.002 4 16.002 4h-.015s-6.715 0-11.191.347c-.625.077-1.987.085-3.205 1.443C.633 6.826.32 9.179.32 9.179S0 11.94 0 14.701v2.588c0 2.763.32 5.523.32 5.523s.312 2.352 1.271 3.386c1.218 1.358 2.815 1.317 3.527 1.459 2.559.262 10.877.343 10.877.343s6.722-.012 11.199-.355c.625-.08 1.988-.088 3.205-1.446.958-1.034 1.271-3.386 1.271-3.386s.32-2.761.32-5.523v-2.588c0-2.762-.32-5.523-.32-5.523z" fill="#E02F2F"></path>
                                                            <path fill="#FFF" d="M12 10v12l10-6z"></path>
                                                        </svg></a></div><br />
                                                <div id="icon_twitch" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_twitch_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Twitch Live Stream" alt="Twitch Live Stream"><svg height="20" width="20" viewBox="0 0 50 50">
                                                            <path d="M45 1H5C2.8 1 1 2.8 1 5v40c0 2.2 1.8 4 4 4h40c2.2 0 4-1.8 4-4V5c0-2.2-1.8-4-4-4z" fill="#6441A5"></path>
                                                            <g fill="#FFF">
                                                                <path d="M31 36h-6l-3 4h-4v-4h-7V15.1l2-5.1h26v18l-8 8zm5-9V13H15v19h6v4l4-4h6l5-5z"></path>
                                                                <path d="M28 18h3v8h-3zM22 18h3v8h-3z"></path>
                                                            </g>
                                                        </svg></a></div>
                                                <div id="icon_linkedin" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_linkedin_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Linkedin Live Stream" alt="Linkedin Live Stream"><svg height="20" width="20" viewBox="0 0 50 50">
                                                            <path d="M41 4H9C6.24 4 4 6.24 4 9v32c0 2.76 2.24 5 5 5h32c2.76 0 5-2.24 5-5V9c0-2.76-2.24-5-5-5zM17 20v19h-6V20h6zm-6-5.53c0-1.4 1.2-2.47 3-2.47s2.93 1.07 3 2.47c0 1.4-1.12 2.53-3 2.53-1.8 0-3-1.13-3-2.53zM39 39h-6V29c0-2-1-4-3.5-4.04h-.08C27 24.96 26 27.02 26 29v10h-6V20h6v2.56S27.93 20 31.81 20c3.97 0 7.19 2.73 7.19 8.26V39z"></path>
                                                        </svg></a></div>
                                                <div id="icon_instagram" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_instagram_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Instagram Live Stream" alt="Instagram Live Stream"><svg height="20" width="20" data-name="Google alt" viewBox="0 0 420 419.997">
                                                            <path d="M342.818 100.279a24.3 24.3 0 11-24.295-24.295 24.3 24.3 0 0124.295 24.295zM420 209.999l-.005.306-1.38 88.105a121.58 121.58 0 01-120.2 120.2L210 419.999l-.306-.006-88.105-1.376a121.586 121.586 0 01-120.206-120.2L0 209.999l.006-.306 1.376-88.108a121.59 121.59 0 01120.206-120.2L210-.001l.306.006 88.105 1.376a121.584 121.584 0 01120.2 120.2zm-39.112 0l-1.374-87.8A82.654 82.654 0 00297.8 40.484L210 39.113l-87.8 1.371a82.658 82.658 0 00-81.716 81.715l-1.371 87.8 1.371 87.8a82.655 82.655 0 0081.716 81.715l87.8 1.371 87.8-1.371a82.651 82.651 0 0081.714-81.715zm-63.048 0A107.841 107.841 0 11210 102.158a107.962 107.962 0 01107.84 107.841zm-39.107 0A68.734 68.734 0 10210 278.733a68.812 68.812 0 0068.732-68.734z"></path>
                                                        </svg></a></div>
                                                <div id="icon_rtmp" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_rtmp_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Custom RTMP" alt="Custom RTMP"><svg height="20" width="20" viewBox="0 0 256 256">
                                                            <path d="M40.59 49.24c-1.28 0-33.08 24.32-33.08 76 0 53.23 30.89 81.28 32.64 81.49s16.43-13.58 16.43-14.68-26.95-20.59-26.95-65.28 26.73-61.33 26.73-62.85c0-.92-12.26-14.68-15.77-14.68z" fill="#191919"></path>
                                                            <path d="M40.29 214.77a9.09 9.09 0 01-1.14-.07c-1.64-.2-4.12-.51-10.79-7.93a101.48 101.48 0 01-12.11-16.87C8.62 176.73-.49 154.83-.49 125.26c0-28.86 9.43-49.51 17.34-61.74a89.8 89.8 0 0112.44-15.39c7-6.88 9.5-6.88 11.3-6.88 3.49 0 7.2 1.57 14.9 9.4 8.87 9 8.87 10.89 8.87 13.28 0 3.72-2.18 5.75-4.29 7.71-5.55 5.17-22.44 20.89-22.44 55.16 0 23.42 7.81 43 23.21 58.11 1.75 1.72 3.74 3.67 3.74 7.18 0 2.89 0 4.81-12.22 15.59-5.72 5.04-8.69 7.09-12.07 7.09zM41.13 59c-7.46 7.05-25.62 28.17-25.62 66.26 0 39.53 17.7 63 25.27 71.28 1.54-1.33 3.28-2.92 4.85-4.4-15.92-17.6-24-39.56-24-65.35 0-25.52 8.06-46.81 24-63.36-1.48-1.54-3.1-3.16-4.5-4.43z" fill="#191919"></path>
                                                            <path d="M74.77 78.6c-1.55 0-21.69 12.71-22.13 46.88-.45 35.05 19.72 52.36 21 52.58s14.24-13.36 14.24-14.46-15.48-11.84-14.68-35.49c.88-25.85 14.9-34.61 14.9-35.71S76.74 78.6 74.77 78.6z" fill="#191919"></path>
                                                            <path d="M73.73 186.06a8.38 8.38 0 01-1.38-.12c-7.35-1.22-16.66-16.9-16.75-17.05-5.14-8.72-11.22-23.36-11-43.52.26-19.94 6.73-33.19 12.12-40.78 3.43-4.84 11.62-14 18-14 3.75 0 6.73 2 13.27 9 7.33 7.85 8.1 9.92 8.1 12.77 0 3.51-2.09 5.59-3.47 7-3 3-10.82 10.79-11.43 29a36.5 36.5 0 0011.21 28.19c1.56 1.61 3.51 3.61 3.51 7s-1.46 5.38-6.84 11.14a116.63 116.63 0 01-8.33 8.18c-1.34 1.19-3.74 3.19-7.01 3.19zm.68-97.53c-4.83 4.5-13.5 15.57-13.77 37-.28 21.97 8.26 35.61 13.36 41.79 1.12-1.12 2.3-2.35 3.38-3.5a51.81 51.81 0 01-12.14-36c.64-18.82 7.56-30 12.45-35.83-1.06-1.16-2.22-2.39-3.28-3.46zM215.41 49.24c1.28 0 33.08 24.32 33.08 76 0 53.23-30.89 81.28-32.64 81.49s-16.43-13.58-16.43-14.68 26.95-20.59 26.95-65.28-26.73-61.36-26.73-62.87c0-.9 12.26-14.66 15.77-14.66z" fill="#191919"></path>
                                                            <path d="M215.7 214.77c-3.38 0-6.34-2-12.06-7.1-12.22-10.79-12.22-12.7-12.22-15.59 0-3.5 2-5.45 3.74-7.18 15.4-15.14 23.21-34.69 23.21-58.11 0-34.27-16.89-50-22.44-55.16-2.11-2-4.29-4-4.29-7.71 0-2.39 0-4.27 8.87-13.28 7.71-7.83 11.41-9.4 14.9-9.4 1.79 0 4.25 0 11.3 6.88a89.8 89.8 0 0112.44 15.39c7.91 12.24 17.34 32.88 17.34 61.74 0 29.57-9.1 51.47-16.74 64.64a101.48 101.48 0 01-12.11 16.86c-6.67 7.41-9.15 7.72-10.8 7.93a9 9 0 01-1.14.09zm-5.33-22.62c1.56 1.48 3.31 3.07 4.85 4.4 7.57-8.23 25.27-31.75 25.27-71.28 0-38.09-18.16-59.21-25.62-66.26-1.4 1.27-3 2.89-4.48 4.43 15.91 16.55 24 37.84 24 63.36-.03 25.78-8.09 47.74-24.02 65.34z" fill="#191919"></path>
                                                            <path d="M181.23 78.6c1.55 0 21.69 12.71 22.13 46.88.45 35.05-19.72 52.36-21 52.58s-14.24-13.36-14.24-14.46 15.48-11.84 14.68-35.49c-.88-25.85-14.9-34.61-14.9-35.71s11.36-13.8 13.33-13.8z" fill="#191919"></path>
                                                            <path d="M182.27 186.06c-3.3 0-5.66-2-7-3.15a116.63 116.63 0 01-8.33-8.18c-5.38-5.77-6.84-7.8-6.84-11.14s1.95-5.44 3.51-7a36.5 36.5 0 0011.17-28.17c-.62-18.22-8.47-26.06-11.43-29-1.38-1.38-3.47-3.46-3.47-7 0-2.85.77-4.92 8.1-12.77 6.54-7 9.52-9 13.27-9 6.39 0 14.57 9.16 18 14 5.39 7.6 11.86 20.84 12.12 40.78.26 20.16-5.82 34.79-11 43.52-.09.16-9.41 15.83-16.75 17.05a8.38 8.38 0 01-1.35.06zm-3.65-22.25c1.08 1.15 2.26 2.38 3.38 3.5 5.11-6.17 13.64-19.82 13.36-41.74-.27-21.48-8.94-32.54-13.77-37-1.06 1.07-2.22 2.3-3.28 3.47 4.9 5.87 11.81 17 12.45 35.83a51.81 51.81 0 01-12.14 35.95zM151.07 128.11c0 15.17-10.17 28.47-26.79 27.47-14.77-.89-26.79-12.3-26.79-27.47s10.76-27.07 26.79-27.47c15.83-.39 26.79 12.3 26.79 27.47z" fill="#191919"></path>
                                                            <path d="M126.06 163.63q-1.12 0-2.27-.07c-19.56-1.18-34.31-16.42-34.31-35.45 0-20 14.55-35 34.59-35.47a33.24 33.24 0 0124.35 9.36 36.5 36.5 0 0110.65 26.09c0 10.56-4 20.4-11 27a31.37 31.37 0 01-22.01 8.54zm-1.07-55h-.52c-11.18.28-19 8.29-19 19.47 0 10.44 8.29 18.82 19.27 19.48a15.85 15.85 0 0012.33-4.15c3.8-3.58 6-9.17 6-15.34a20.43 20.43 0 00-5.82-14.64 17.14 17.14 0 00-12.25-4.82z" fill="#191919"></path>
                                                            <path d="M40.59 49.24c-1.28 0-33.08 24.32-33.08 76 0 53.23 30.89 81.28 32.64 81.49s16.43-13.58 16.43-14.68-26.95-20.59-26.95-65.28 26.73-61.33 26.73-62.85c0-.92-12.26-14.68-15.77-14.68z" fill="#fff"></path>
                                                            <path d="M74.77 78.6c-1.55 0-21.69 12.71-22.13 46.88-.45 35.05 19.72 52.36 21 52.58s14.24-13.36 14.24-14.46-15.48-11.84-14.68-35.49c.88-25.85 14.9-34.61 14.9-35.71S76.74 78.6 74.77 78.6zM215.41 49.24c1.28 0 33.08 24.32 33.08 76 0 53.23-30.89 81.28-32.64 81.49s-16.43-13.58-16.43-14.68 26.95-20.59 26.95-65.28-26.73-61.36-26.73-62.87c0-.9 12.26-14.66 15.77-14.66z" fill="#fff"></path>
                                                            <path d="M181.23 78.6c1.55 0 21.69 12.71 22.13 46.88.45 35.05-19.72 52.36-21 52.58s-14.24-13.36-14.24-14.46 15.48-11.84 14.68-35.49c-.88-25.85-14.9-34.61-14.9-35.71s11.36-13.8 13.33-13.8z" fill="#fff"></path>
                                                            <path d="M151.07 128.11c0 15.17-10.17 28.47-26.79 27.47-14.77-.89-26.79-12.3-26.79-27.47s10.76-27.07 26.79-27.47c15.83-.39 26.79 12.3 26.79 27.47z" fill="#e83a2a"></path>
                                                        </svg></a></div>
                                                <div id="icon_rtmps" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_rtmps_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Custom RTMPS" alt="Custom RTMPS"><svg height="20" width="20" viewBox="0 0 256 256">
                                                            <path d="M40.59 49.24c-1.28 0-33.08 24.32-33.08 76 0 53.23 30.89 81.28 32.64 81.49s16.43-13.58 16.43-14.68-26.95-20.59-26.95-65.28 26.73-61.33 26.73-62.85c0-.92-12.26-14.68-15.77-14.68z" fill="#191919"></path>
                                                            <path d="M40.29 214.77a9.09 9.09 0 01-1.14-.07c-1.64-.2-4.12-.51-10.79-7.93a101.48 101.48 0 01-12.11-16.87C8.62 176.73-.49 154.83-.49 125.26c0-28.86 9.43-49.51 17.34-61.74a89.8 89.8 0 0112.44-15.39c7-6.88 9.5-6.88 11.3-6.88 3.49 0 7.2 1.57 14.9 9.4 8.87 9 8.87 10.89 8.87 13.28 0 3.72-2.18 5.75-4.29 7.71-5.55 5.17-22.44 20.89-22.44 55.16 0 23.42 7.81 43 23.21 58.11 1.75 1.72 3.74 3.67 3.74 7.18 0 2.89 0 4.81-12.22 15.59-5.72 5.04-8.69 7.09-12.07 7.09zM41.13 59c-7.46 7.05-25.62 28.17-25.62 66.26 0 39.53 17.7 63 25.27 71.28 1.54-1.33 3.28-2.92 4.85-4.4-15.92-17.6-24-39.56-24-65.35 0-25.52 8.06-46.81 24-63.36-1.48-1.54-3.1-3.16-4.5-4.43z" fill="#191919"></path>
                                                            <path d="M74.77 78.6c-1.55 0-21.69 12.71-22.13 46.88-.45 35.05 19.72 52.36 21 52.58s14.24-13.36 14.24-14.46-15.48-11.84-14.68-35.49c.88-25.85 14.9-34.61 14.9-35.71S76.74 78.6 74.77 78.6z" fill="#191919"></path>
                                                            <path d="M73.73 186.06a8.38 8.38 0 01-1.38-.12c-7.35-1.22-16.66-16.9-16.75-17.05-5.14-8.72-11.22-23.36-11-43.52.26-19.94 6.73-33.19 12.12-40.78 3.43-4.84 11.62-14 18-14 3.75 0 6.73 2 13.27 9 7.33 7.85 8.1 9.92 8.1 12.77 0 3.51-2.09 5.59-3.47 7-3 3-10.82 10.79-11.43 29a36.5 36.5 0 0011.21 28.19c1.56 1.61 3.51 3.61 3.51 7s-1.46 5.38-6.84 11.14a116.63 116.63 0 01-8.33 8.18c-1.34 1.19-3.74 3.19-7.01 3.19zm.68-97.53c-4.83 4.5-13.5 15.57-13.77 37-.28 21.97 8.26 35.61 13.36 41.79 1.12-1.12 2.3-2.35 3.38-3.5a51.81 51.81 0 01-12.14-36c.64-18.82 7.56-30 12.45-35.83-1.06-1.16-2.22-2.39-3.28-3.46zM215.41 49.24c1.28 0 33.08 24.32 33.08 76 0 53.23-30.89 81.28-32.64 81.49s-16.43-13.58-16.43-14.68 26.95-20.59 26.95-65.28-26.73-61.36-26.73-62.87c0-.9 12.26-14.66 15.77-14.66z" fill="#191919"></path>
                                                            <path d="M215.7 214.77c-3.38 0-6.34-2-12.06-7.1-12.22-10.79-12.22-12.7-12.22-15.59 0-3.5 2-5.45 3.74-7.18 15.4-15.14 23.21-34.69 23.21-58.11 0-34.27-16.89-50-22.44-55.16-2.11-2-4.29-4-4.29-7.71 0-2.39 0-4.27 8.87-13.28 7.71-7.83 11.41-9.4 14.9-9.4 1.79 0 4.25 0 11.3 6.88a89.8 89.8 0 0112.44 15.39c7.91 12.24 17.34 32.88 17.34 61.74 0 29.57-9.1 51.47-16.74 64.64a101.48 101.48 0 01-12.11 16.86c-6.67 7.41-9.15 7.72-10.8 7.93a9 9 0 01-1.14.09zm-5.33-22.62c1.56 1.48 3.31 3.07 4.85 4.4 7.57-8.23 25.27-31.75 25.27-71.28 0-38.09-18.16-59.21-25.62-66.26-1.4 1.27-3 2.89-4.48 4.43 15.91 16.55 24 37.84 24 63.36-.03 25.78-8.09 47.74-24.02 65.34z" fill="#191919"></path>
                                                            <path d="M181.23 78.6c1.55 0 21.69 12.71 22.13 46.88.45 35.05-19.72 52.36-21 52.58s-14.24-13.36-14.24-14.46 15.48-11.84 14.68-35.49c-.88-25.85-14.9-34.61-14.9-35.71s11.36-13.8 13.33-13.8z" fill="#191919"></path>
                                                            <path d="M182.27 186.06c-3.3 0-5.66-2-7-3.15a116.63 116.63 0 01-8.33-8.18c-5.38-5.77-6.84-7.8-6.84-11.14s1.95-5.44 3.51-7a36.5 36.5 0 0011.17-28.17c-.62-18.22-8.47-26.06-11.43-29-1.38-1.38-3.47-3.46-3.47-7 0-2.85.77-4.92 8.1-12.77 6.54-7 9.52-9 13.27-9 6.39 0 14.57 9.16 18 14 5.39 7.6 11.86 20.84 12.12 40.78.26 20.16-5.82 34.79-11 43.52-.09.16-9.41 15.83-16.75 17.05a8.38 8.38 0 01-1.35.06zm-3.65-22.25c1.08 1.15 2.26 2.38 3.38 3.5 5.11-6.17 13.64-19.82 13.36-41.74-.27-21.48-8.94-32.54-13.77-37-1.06 1.07-2.22 2.3-3.28 3.47 4.9 5.87 11.81 17 12.45 35.83a51.81 51.81 0 01-12.14 35.95zM151.07 128.11c0 15.17-10.17 28.47-26.79 27.47-14.77-.89-26.79-12.3-26.79-27.47s10.76-27.07 26.79-27.47c15.83-.39 26.79 12.3 26.79 27.47z" fill="#191919"></path>
                                                            <path d="M126.06 163.63q-1.12 0-2.27-.07c-19.56-1.18-34.31-16.42-34.31-35.45 0-20 14.55-35 34.59-35.47a33.24 33.24 0 0124.35 9.36 36.5 36.5 0 0110.65 26.09c0 10.56-4 20.4-11 27a31.37 31.37 0 01-22.01 8.54zm-1.07-55h-.52c-11.18.28-19 8.29-19 19.47 0 10.44 8.29 18.82 19.27 19.48a15.85 15.85 0 0012.33-4.15c3.8-3.58 6-9.17 6-15.34a20.43 20.43 0 00-5.82-14.64 17.14 17.14 0 00-12.25-4.82z" fill="#191919"></path>
                                                            <path d="M40.59 49.24c-1.28 0-33.08 24.32-33.08 76 0 53.23 30.89 81.28 32.64 81.49s16.43-13.58 16.43-14.68-26.95-20.59-26.95-65.28 26.73-61.33 26.73-62.85c0-.92-12.26-14.68-15.77-14.68z" fill="#fff"></path>
                                                            <path d="M74.77 78.6c-1.55 0-21.69 12.71-22.13 46.88-.45 35.05 19.72 52.36 21 52.58s14.24-13.36 14.24-14.46-15.48-11.84-14.68-35.49c.88-25.85 14.9-34.61 14.9-35.71S76.74 78.6 74.77 78.6zM215.41 49.24c1.28 0 33.08 24.32 33.08 76 0 53.23-30.89 81.28-32.64 81.49s-16.43-13.58-16.43-14.68 26.95-20.59 26.95-65.28-26.73-61.36-26.73-62.87c0-.9 12.26-14.66 15.77-14.66z" fill="#fff"></path>
                                                            <path d="M181.23 78.6c1.55 0 21.69 12.71 22.13 46.88.45 35.05-19.72 52.36-21 52.58s-14.24-13.36-14.24-14.46 15.48-11.84 14.68-35.49c-.88-25.85-14.9-34.61-14.9-35.71s11.36-13.8 13.33-13.8z" fill="#fff"></path>
                                                            <path d="M151.07 128.11c0 15.17-10.17 28.47-26.79 27.47-14.77-.89-26.79-12.3-26.79-27.47s10.76-27.07 26.79-27.47c15.83-.39 26.79 12.3 26.79 27.47z" fill="#e83a2a"></path>
                                                        </svg></a></div>
                                                <div id="icon_vimeo" class="text-gray-700 text-center  px-4 py-2 m-2 hidden"><a id="a_viemo_id" href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" title="Vimeo Live Stream" alt="Vimeo Live Stream"><svg height="20" width="20" viewBox="0 0 512 512">
                                                            <circle cx="256" cy="256" r="256" fill="#65a2d9"></circle>
                                                            <path d="M388.741 143.1c-14.305 27.281-24.457 56.289-35.357 85.626-15.398-17.534-27.851-31.056-27.851-31.056h-42.696l-56.832-56.83 31.923 119.672-91.57-67.722-49.145 27.405L259.958 362.94c-8.945 3.024-18.642 5.491-28.691 8.697L352.711 493.08c90.474-36.945 155.064-124.159 159.07-226.94L388.741 143.1z" fill="#3a7ca5"></path>
                                                            <path d="M390.401 144.762c-7.096-7.646-17.368-11.717-30.527-12.104-37.152-1.2-63.426 18.566-78.091 58.733a5.965 5.965 0 001.226 6.106 5.928 5.928 0 005.947 1.71c4.22-1.16 8.401-1.746 12.435-1.746 5.413 0 12.621.995 16.208 5.735 2.75 3.641 3.125 9.097 1.11 16.219-1.698 6.011-10.449 22.097-21.28 39.117-13.195 20.739-20.482 30.036-22.585 30.036-6.959 0-32.116-111.547-32.809-113.993-7.213-25.566-10.836-38.398-34.299-38.398-17.884 0-57.768 32.658-90.517 61.109-2.015 1.746-3.71 3.22-5.022 4.346a7.786 7.786 0 00-1.088 10.702l6.103 7.859a7.78 7.78 0 0010.68 1.576l.336-.241c10.593-7.537 20.604-14.655 28.16-15.048 8.209-.402 15.334 12.428 23.828 42.951 16.175 59.289 40.755 129.96 65.977 129.96 26.357 0 57.804-22.344 98.451-75.242 36.788-47.875 56.489-85.845 57.751-113.473.951-20.859-3.085-36.311-11.994-45.914z" fill="#fff"></path>
                                                            <path d="M390.401 144.762c-7.096-7.646-17.368-11.717-30.527-12.104-37.152-1.2-63.426 18.566-78.091 58.733a5.965 5.965 0 001.226 6.106 5.928 5.928 0 005.947 1.71c4.22-1.16 8.401-1.746 12.435-1.746 5.413 0 12.621.995 16.208 5.735 2.75 3.641 3.125 9.097 1.11 16.219-1.698 6.011-10.449 22.097-21.28 39.117-13.195 20.739-20.482 30.036-22.585 30.036-3.587 0-12.007-29.625-19.42-58.675v148.599c24.426-4.613 53.212-27.479 89.219-74.342 36.788-47.875 56.489-85.845 57.751-113.473.952-20.86-3.084-36.312-11.993-45.915z" fill="#d1d1d1"></path>
                                                        </svg></a></div>
                                            </div>


                                            <input type="hidden" name="meethour_recording" id="meethour_recording_id" value="" />
                                            <input type="hidden" name="facebook_stream_key" id="facebook_stream_key_id" value="" />
                                            <input type="hidden" name="youtube_stream_key" id="youtube_stream_key_id" value="" />
                                            <input type="hidden" name="twitch_stream_key" id="twitch_stream_key_id" value="" />
                                            <input type="hidden" name="linkedin_stream_key" id="linkedin_stream_key_id" value="" />
                                            <input type="hidden" name="instagram_stream_key" id="instagram_stream_key_id" value="" />
                                            <input type="hidden" name="rtmp_stream_key" id="rtmp_stream_key_id" value="" />
                                            <input type="hidden" name="rtmps_stream_key" id="rtmps_stream_key_id" value="" />
                                            <input type="hidden" name="vimeo_stream_key" id="vimeo_stream_key_id" value="" />
                                        </div>
                                    </div>
                                    @endif
                                    <!--
                    <div class="input-form mt-3">
                        <label class="flex flex-col sm:flex-row"> Meeting URL <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">Optional, URL format</span> </label>
                        <input style="text-align:left" type="url" name="url" class="input  w-full border mt-2" placeholder="http://meethour.io/teamlead2021">
                    </div>
                -->




                                    <div class="input-form  mt-3">
                                        <label class="flex flex-col sm:flex-row"> {{ __('messages.instructions')}} <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">{{ __('messages.schedule_text_52')}}</span> </label>
                                        <textarea class="input w-full border mt-2" name="comment" placeholder="Type your Instructions" minlength="10" rows="4"></textarea>
                                    </div>


                                    <div class="input-form mt-3">

                                        <label style="font-size:18px" class="flex flex-col sm:flex-row"> {{ __('messages.enable_webinar_mode')}}  &nbsp;&nbsp;<input id="enable_pre_registration" type="checkbox" class="input input--switch border" name="enable_pre_registration" value="true" onchange="check_pre_registration_feature();"> </label>


                                    </div>

                                    <?php
                                    if (isset($subscription_feature['enable-pre-registration']) && isset($subscription_feature['enable-pre-registration']->is_available) && $subscription_feature['enable-pre-registration']->is_available == 1) { ?>
                                        <div id="pre_registration_content" style="display:none">
                                            <div class="input-form mt-3">
                                                <label class="flex flex-col sm:flex-row">{{ __('messages.meeting_topic')}} </label>
                                                <input type="text" name="meeting_topic" class="input w-full border mt-2" placeholder="Meeting Topic" minlength="2">
                                            </div>

                                            <div class="input-form mt-3">
                                                <label class="flex flex-col sm:flex-row">{{ __('messages.meeting_agenda')}}  </label>

                                                <textarea class="editor" name="meeting_agenda">
                            </textarea>

                                            </div>
                                            <div class="input-form mt-3"> <label class="w-full sm:w-40 sm:text-right sm:mr-5">{{ __('messages.meeting_image')}} </label>
                                                <input name="meeting_image" type="file">
                                            </div>


                                            <div class="input-form  mt-3">
                                        <label class="flex flex-col sm:flex-row"> <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600"> <a style="color:#4B6790;text-decoration: underline;cursor:pointer;" onclick="callwebinarfields();">Additional Fields</a></span> </label>

                                       </div>
                            
                            <div id="additional_webinar_fields" style="display:none;"  >         
                            <?php for($i=0; $i<2;$i++) { ?>          
                            <div class="grid grid-cols-12 gap-3"> 

                            <div class="intro-y col-span-12 lg:col-span-6">
                            <div class="input-form">
                                <label class="flex flex-col sm:flex-row">Label {{$i+1}}</label>

                            </div>

                            <input type="text" name="additional_label[]" class="input w-full border mt-2" placeholder="Label {{$i+1}}" minlength="2">
                            
                            </div>

                            <div class="intro-y col-span-12 lg:col-span-6">
                                            <label>{{  __('messages.time_zone')}}</label>
                                            <div class="preview">
                                                <select style="width:75px; margin-right:6px;"  name="additional_input[]" class="tail-select w-full">
                                                  <option value="text">Text</option>
                                                  <option value="number">Number</option>
                                                </select>
                                            </div>
                                        </div>
                            </div>
                            <br/>
                            <?php } ?>
                            </div>

                                           
                                            <div class="input-form mt-3">  
                                            <?php
                                            $participant_count=0; 
                                            if (isset($subscription_feature['host-participants']) && isset($subscription_feature['host-participants']->is_available) && $subscription_feature['host-participants']->is_available == 1) { 
                                             $participant_count=$subscription_feature['host-participants']['feature_value'];
                                          }  ?>     
                                            Note: Registrations are unlimited but maximum of {{$participant_count}} participants can join in a single meeting based on your plan. Contact support if you want to increase this limit
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>


                            </div>



                            <div class="col-span-12 lg:col-span-12 text-center ">
                                <div class="intro-y box p-5"><?php if ($is_admin == 0) { ?>
                                        <button  type="submit" class="button bg-theme-1 text-white">{{ __('messages.schedule_a_meeting')}}</button> &nbsp;&nbsp; <!-- <button type="submit" class="button w-24 mr-1 mb-2 border text-gray-700 dark:bg-dark-5 dark:text-gray-300">Cancel</button>-->
                                    <?php } else {  ?>
                                        <br /> <a href="javascript:;" data-toggle="modal" data-target="#admin_alert" class="button bg-theme-1 text-white mt-5">{{ __('messages.schedule_a_meeting')}}</a>

                                    <?php } ?>

                                </div>
                            </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- END: Form Validation -->
</div>
</div>
<!-- END: Weekly Top Products -->
</div>

<div class="modal" id="header-footer-modal-preview">
    <div class="modal__content">
        <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">{{ __('messages.live_stream')}} + {{ __('messages.record_settings')}}</h2>
        </div>
        <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
            <input type="hidden" id="max_livestream_settings" value="<?php echo $max_livestream_settings; ?>">
            <div class="col-span-12 sm:col-span-12">
                <div id="error_message" style="display:none;" class="rounded-md flex items-center px-5 py-4 mb-2 bg-theme-31 text-theme-6"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-octagon w-6 h-6 mr-2">
                        <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg> Awesome alert with icon </div>
            </div>
            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <input type="checkbox" name="sream[]" value="1" class="input input--switch border" id="meethour_id" onclick="checkval('meethour_id' , '');"> &nbsp;{{ __('messages.addon_type_recording')}}
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="meethour_id1"> {{ __('messages.schedule_text_54')}} <a href="/customer/recordingSettings" target="_blank" style="color:#2D68BB">{{ __('messages.record_settings')}}  </a>
                    </div>
                </div>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <input type="checkbox" name="sream[]" value="2" class="input input--switch border" id="facebook_id" onclick="checkval('facebook_id', 'facebook_stream_key');">&nbsp;{{ __('messages.facebook')}}
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="facebook_id1">
                        <input type="text" name="facebook_stream_key" id="facebook_stream_key" class="input w-full border mt-2" placeholder="Enter Stream URL -  (Example - rtmps://live-api-s.facebook.com:443/rtmp/STREAMKEY)" minlength="2">
                    </div>
                </div>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                            <input type="checkbox" name="sream[]" value="3" class="input input--switch border" id="youtube_id" onclick="checkval('youtube_id', 'youtube_stream_key');">&nbsp;{{ __('messages.youtube')}}
                        </label>
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="youtube_id1">
                        <input type="text" name="youtube_stream_key" id="youtube_stream_key" class="input w-full border mt-2" placeholder="Enter Stream URL - (Example - rtmp://a.rtmp.youtube.com/live2/STREAMKEY)" minlength="2">
                    </div>
                </div>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <input type="checkbox" name="sream[]" value="4" class="input input--switch border" id="twich_id" onclick="checkval('twich_id', 'twich_stream_key');">&nbsp;{{ __('messages.twitch')}}
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="twich_id1">
                        <input type="text" name="twich_stream_key" id="twich_stream_key" class="input w-full border mt-2" placeholder="Enter Stream URL - (Example - rtmp://ymq03.contribute.live-video.net/app/STREAMKEY)" minlength="2">
                    </div>
                </div>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <input type="checkbox" name="sream[]" value="5" class="input input--switch border" id="linkedin_id" onclick="checkval('linkedin_id', 'linkedin_stream_key');">&nbsp;{{ __('messages.linkedin')}}
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="linkedin_id1">
                        <input type="text" name="linkedin_stream_key" id="linkedin_stream_key" class="input w-full border mt-2" placeholder="Enter Stream URL - (Example - rtmps://71bf35ba56fb3b7a0b.channel.media.azure.net:2935/live/8c7b49f8afe79f04f67/STREAMKEY" minlength="2">
                    </div>
                </div>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <input type="checkbox" name="sream[]" value="6" class="input input--switch border" id="instagram_id" onclick="checkval('instagram_id', 'instagram_stream_key');">&nbsp;{{ __('messages.instagram')}}
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="instagram_id1">
                        <input type="text" name="instagram_stream_key" id="instagram_stream_key" class="input w-full border mt-2" placeholder="Enter Stream URL - (Example - rtmps://live-upload.instagram.com:443/rtmp/STREAMKEY)" minlength="2">
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <input type="checkbox" name="sream[]" value="9" class="input input--switch border" id="viemo_id" onclick="checkval('viemo_id', 'viemo_stream_key');">&nbsp;{{ __('messages.vimeo')}}
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="viemo_id1">
                        <input type="text" name="viemo_stream_key" id="viemo_stream_key" class="input w-full border mt-2" placeholder="Enter  Stream URL - (Example - rtmps://rtmp-global.cloud.vimeo.com:443/live/STREAMKEY)" minlength="2">
                    </div>
                </div>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                            <input type="checkbox" name="sream[]" value="7" class="input input--switch border" id="customrtmp_id" onclick="checkval('customrtmp_id', 'customrtmp_stream_key');">&nbsp;{{ __('messages.custom_rtmp')}}
                        </label>
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="customrtmp_id1">
                        <input type="text" name="customrtmp_stream_key" id="customrtmp_stream_key" class="input w-full border mt-2" placeholder="Enter Custom RTMP URL - (Example - rmtp://live.domain.com/STREAMKEY)" minlength="2">
                    </div>
                </div>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label class="w-full sm:w-60 sm:text-right sm:mr-5">
                        <input type="checkbox" name="sream[]" value="8" class="input input--switch border" id="customrtmps_id" onclick="checkval('customrtmps_id', 'customrtmp1_stream_key');">&nbsp;{{ __('messages.custom_rtmps')}}
                    </label>
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <div style="display:none;" id="customrtmps_id1">
                        <input type="text" name="customrtmp1_stream_key" id="customrtmp1_stream_key" class="input w-full border mt-2" placeholder="Enter Custom RTMPS URL - (Example - rmtps://live.domain.com/STREAMKEY)" minlength="2">
                    </div>
                </div>
            </div>
        </div>
        <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5"> <button type="button" data-dismiss="modal" class="button w-20 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1">{{ __('messages.cancel')}}</button> <button type="button" class="button bg-theme-1 text-white" onclick="checkdata();">{{ __('messages.schedule_text_55')}}</button> </div>
    </div>
</div>

<div id="new-contact-modal" class="modal" data-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body px-5 py-5">
                <div class="flex items-center border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto">
                    {{ __('messages.new_contact')}}
                    </h2>
                </div>
                <form name="frmcontact" id="frmcontact" method="post" action="{{route('customer.savecontact')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-6">
                            <label>{{ __('messages.first_name')}} *</label>
                            <input type="text" name="firstname" required class="input w-full border mt-2 flex-1" placeholder="First Name">
                        </div>
                        <div class="col-span-6">
                            <label>{{ __('messages.last_name')}} </label>
                            <input type="text" name="lastname" class="input w-full border mt-2 flex-1" placeholder="Last Name">
                        </div>
                        <div class="col-span-12">
                            <label>{{ __('messages.email')}} *</label>
                            <input type="text" name="email" required class="input w-full border mt-2 flex-1" placeholder="Email"><br />
                            <span style="color:red;display:none;" id="contact_exists_text"> {{ __('messages.schedule_text_56')}}</span>
                            <span style="color:red;display:none;" id="contactemail_valid_text">  {{ __('messages.invalid_email')}} </span>

                        </div>
                        <div class="col-span-12">
                            <label>{{ __('messages.mobile')}}</label>
                            <input type="text" name="phone" id="phone" class="input w-full border mt-2 flex-1" placeholder="Mobile">
                        </div>
                        <div class="col-span-12">
                            <label>{{ __('messages.photo')}}</label>
                            <input type="file" name="image" class="input w-full border mt-2 flex-1" placeholder="Image">
                        </div>
                    </div>
                    <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                        <input type='hidden' name='is_ajax' value=1 />
                        <button type="button" id="close_button_id" data-dismiss="modal" class="button w-32 border dark:border-dark-5 text-gray-700 dark:text-gray-300 mr-1">{{ __('messages.cancel')}}</button>
                        <button type="submit" id="modal_submit_button_id" class="button w-32 bg-theme-1 text-white">{{ __('messages.save')}} & {{ __('messages.invite')}}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php if (Session::has('flash_success')) {
    if (Session::get('flash_success') == 'SM') { 
        if(isset($meeting->id) && !empty($meeting->id))   { 
        ?>
        @include('meeting.meetingsuccess')
        <a href="javascript:;" style="display:none;" data-toggle="modal" data-target="#meeting-details" id="meeting-details2" class="button bg-theme-1 text-white mt-5">Schedule Meeting</a>
<?php } }
} ?>




<div class="modal" id="checklivestreamsettingsalert" data-backdrop="static">

    <div class="modal__content">


        <div class="p-5 text-center">

            <div class="text-2xl mt-5"></div>
            <div class="text-gray-600 mt-2">{{ __('messages.schedule_text_57')}}</div>
        </div>
        <div class="px-5 pb-8 text-center"> <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1">{{ __('messages.close')}}</button> </div>

    </div>

</div>
<a href="javascript:;" style="display:none;" data-toggle="modal" data-target="#checklivestreamsettingsalert" id="checklivestreamsettingsalertbutton" class="button bg-theme-1 text-white mt-5"></a>



<div class="modal" id="checkauostarstramalert" data-backdrop="static">

    <div class="modal__content">


        <div class="p-5 text-center">

            <div class="text-2xl mt-5"></div>
            <div class="text-gray-600 mt-2">{{ __('messages.schedule_text_58')}}</div>
        </div>
        <div class="px-5 pb-8 text-center"> <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1">{{ __('messages.close')}}</button> </div>

    </div>

</div>
<a href="javascript:;" style="display:none;" data-toggle="modal" data-target="#checkauostarstramalert" id="checkauostarstramalertbutton" class="button bg-theme-1 text-white mt-5"></a>







<div class="modal" id="checkpreregistration" data-backdrop="static">

    <div class="modal__content">


        <div class="p-5 text-center">

            <div class="text-2xl mt-5"></div>
            <div class="text-gray-600 mt-2">{{ __('messages.profile_text_1')}}</div>
        </div>
        <div class="px-5 pb-8 text-center"> <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1">{{ __('messages.close')}}</button> </div>

    </div>

</div>
<a href="javascript:;" style="display:none;" data-toggle="modal" data-target="#checkpreregistration" id="checkpreregistrationalert" class="button bg-theme-1 text-white mt-5"></a>




@endsection
@section('scripts')
<script>
    function myFunction() {
        // Get the checkbox
        var checkBox = document.getElementById("myCheck");
        // Get the output text
        var text = document.getElementById("text");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true) {
            text.style.display = "block";
        } else {
            text.style.display = "none";
        }
    }
</script>


<script src="https://cpwebassets.codepen.io/assets/common/stopExecutionOnTimeout-157cd5b220a5c80d4ff8e0e70ac069bffd87a61252088146915e8726e5d9f147.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script src="{{\config('app.url')}}/assets/vendor/MDTimePicker-master/dist/mdtimepicker.js"></script>

<script src="{{\config('app.url')}}/dist/js/select2.min.js"></script>
<script id="rendered-js">
    $("#frmcontact").submit(function() {
        $("#modal_submit_button_id").prop("disabled", true);
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            data: formData,
            async: false,
            success: function(data) {
                if (data.error_code == 1 || data.error_code == 11) {
                    if (data.error_code == 1) {
                        $("#contact_exists_text").show();
                        $("#contactemail_valid_text").hide();
                        $("#modal_submit_button_id").prop("disabled", false);
                        setTimeout(function() {
                            document.querySelector('#load-bar').style.display = "none";
                        }, 1000);
                    }
                    if (data.error_code == 11) {
                        $("#contactemail_valid_text").show();
                        $("#contact_exists_text").hide();
                        $("#modal_submit_button_id").prop("disabled", false);
                        setTimeout(function() {
                            document.querySelector('#load-bar').style.display = "none";
                        }, 1000);
                    }
                } else {
                    $('#attendees1').append('<li><input type=hidden name=attend[] value=' + data.id + '>' + data.email + '<span class="contact_close" attend_id="' + data.id + '">&times;</span></li>'); //

                    $('#attendees2').append('<tr id="pick_' + data.id + '"><td><input class="input border mr-2" type=checkbox name=hostusers[] value=' + data.id + '></td><td>' + data.email + '</td></tr>');
                    document.getElementById("frmcontact").reset();
                    $("#close_button_id").trigger('click');
                    $("#modal_submit_button_id").prop("disabled", false);
                    $("#contact_exists_text").hide();
                    $("#contactemail_valid_text").hide();

                    var closebtns = document.getElementsByClassName("contact_close");
                    var i;

                    for (i = 0; i < closebtns.length; i++) {
                        closebtns[i].addEventListener("click", function() {
                            this.parentElement.remove();

                            $('table#pick_moderator tr#pick_' + $(this).attr('attend_id')).remove();

                        });
                    }

                    setTimeout(function() {
                        document.querySelector('#load-bar').style.display = "none";
                    }, 1000);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    });

    //test for getting url value from attr
    // var img1 = $('.test').attr("data-thumbnail");
    // console.log(img1);

    //test for iterating over child elements
    var langArray = [];
    $('.vodiapicker option').each(function() {
        var img = $(this).attr("data-thumbnail");
        var text = this.innerText;
        var value = $(this).val();
        var item = '<li><img src="' + img + '" alt="" value="' + value + '"/><span>' + text + '</span></li>';
        langArray.push(item);
    });

    $('#a').html(langArray);

    //Set the button value to the first el of the array
    $('.btn-select').html(langArray[0]);
    $('.btn-select').attr('value', 'en');

    //change button stuff on click
    $('#a li').click(function() {
        var img = $(this).find('img').attr("src");
        var value = $(this).find('img').attr('value');
        var text = this.innerText;
        var item = '<li><img src="' + img + '" alt="" /><span>' + text + '</span></li>';
        $('.btn-select').html(item);
        $('.btn-select').attr('value', value);
        $(".b").toggle();
        //console.log(value);
    });

    $(".btn-select").click(function() {
        $(".b").toggle();
    });

    //check local storage for the lang
    var sessionLang = localStorage.getItem('lang');
    if (sessionLang) {
        //find an item with value of sessionLang
        var langIndex = langArray.indexOf(sessionLang);
        $('.btn-select').html(langArray[langIndex]);
        $('.btn-select').attr('value', sessionLang);
    } else {
        var langIndex = langArray.indexOf('ch');
        console.log(langIndex);
        $('.btn-select').html(langArray[langIndex]);
        //$('.btn-select').attr('value', 'en');
    }
    //# sourceURL=pen.js





    /*  $('.dropdown-mul-2').dropdown({
      readOnly: true,
      input: '<input type="text" maxLength="20" placeholder="Search">'
    });
*/
    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function formatState(contact) {

        if (!contact.id) {
            return contact.text;
        }

        if (contact.photo == undefined) {
            contact.photo = "/assets/img/empty-profile-image.jpg";
        }
        var $contact = $(
            '<span><img src=" ' + (contact.photo == "" ? "/assets/img/empty-profile-image.jpg" : contact.photo) + '" style="width:30px; height:30px; display:inline;" /> &nbsp; &nbsp; ' + contact.text + '</span>'
        );
        console.log(contact);
        return $contact;
    };

    $("#sel_attendees").select2({
        tags: true,
        tokenSeparators: [',', ' ', ';'],
        placeholder: "Select a User",

        ajax: {
            url: '/customer/getContactSearch',
            dataType: 'json',
            delay: 250,
            data: function(data) {
                return {
                    q: data.term // search term
                };
            },
            processResults: function(response) {
                var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                txt = [];

                response.forEach(function(value, index, array) {


                    if (value.email.match(validRegex)) {
                        txt.push(value)
                    }

                });

                return {
                    results: txt
                };
            },
            cache: true
        },
        templateResult: formatState,
        createTag: function(term, data) {
            var value = term.term;
            if (validateEmail(value)) {
                return {
                    id: value,
                    text: value
                };
            }
            return null;
        }

    });

    function showModelWindow(contact_id) {

        $('#contact_id').val(contact_id);
    }

    function onRecurringTypeChange(obj) {
        if (obj.value == 'daily') {
            $('#repeat_interval_div').show();
            $('#weeklyWeekDays').hide();
            $('#month_div').hide();
            $('#end_date_div').show();
            $('#repeat_interval_lbl').html('Day');
        } else if (obj.value == 'weekly') {
            $('#repeat_interval_div').show();
            $('#weeklyWeekDays').show();
            $('#month_div').hide();
            $('#end_date_div').show();
            $('#repeat_interval_lbl').html('Week');
        } else if (obj.value == 'monthly') {
            $('#repeat_interval_div').show();
            $('#weeklyWeekDays').hide();
            $('#month_div').show();
            $('#end_date_div').show();
            $('#repeat_interval_lbl').html('Month');
        } else if (obj.value == 'no-fixed-time') {
            $('#repeat_interval_div').hide();
            $('#weeklyWeekDays').hide();
            $('#month_div').hide();
            $('#end_date_div').hide();
        }
    }

    var closebtns = document.getElementsByClassName("contact_close");
    var i;

    for (i = 0; i < closebtns.length; i++) {
        closebtns[i].addEventListener("click", function() {

            this.parentElement.style.display = 'none';
        });
    }
    //"<img src='https://lh3.googleusercontent.com/ogw/ADea4I4p4AYBh029G7SqPhb6-Mwj_maO1O8iVdPqLK8_=s32-c-mo' />" +
    function addToAttdList(obj) {
        if ($(obj).val() == 0)
            return 1;
        var values = $("input[name='attend[]']")
            .map(function() {
                return $(this).val();
            }).get();
        values = values + '';
        if (values != '') {
            val_array = values.split(',');

            if (val_array.indexOf($(obj).val()) > -1) {
                return 1;
            }
        }
        X = $(obj).val();
        $('#attendees1').append('<li><input type=hidden name=attend[] value=' + $(obj).val() + '>' + $("#sel_attendees option:selected").text() + '<span class="contact_close" attend_id="' + X.replace(/[^a-zA-Z0-9]/g, '_') + '">&times;</span></li>'); //

        $('#attendees2').append('<tr id=pick_' + X.replace(/[^a-zA-Z0-9]/g, '_') + '><td><input class="input border mr-2" type=checkbox name=hostusers[] value=' + X + '></td><td>' + $("#sel_attendees option:selected").text() + '</td></tr>');
        $('#sel_attendees option[value=0]').prop('selected', 'selected').change();
        var closebtns = document.getElementsByClassName("contact_close");
        var i;


        for (i = 0; i < closebtns.length; i++) {
            closebtns[i].addEventListener("click", function() {
                this.parentElement.remove();

                $('table#pick_moderator tr#pick_' + $(this).attr('attend_id')).remove();

            });
        }
    }


    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#id_password');

    togglePassword.addEventListener('click', function(e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        if (type == 'password') {
            $('#togglePassword').attr('src', '<?php echo \config('app.url') . "/dist/images/eye-off.svg"; ?>');
        }

        if (type == 'text') {
            $('#togglePassword').attr('src', '<?php echo \config('app.url') . "/dist/images/eye-on.svg"; ?>');
        }
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });
</script>


<script>
    <?php if (Session::has('flash_success')) {
        if (Session::get('flash_success') == 'SM') { ?>
            //$("#meeting-details2").trigger('click');
            document.getElementById("meeting-details2").click();

            $('#meeting_id').val('');
            $('#smeeting_id_text').hide();
            $('#smeeting_id_text_link').hide();
            $('#editmeetingidalert').hide();
    <?php     }
    } ?>


<?php if (Session::has('flash_error')) {
        if (Session::get('flash_error') == 'SME') { ?>
            alert('Meeting ID unavailable. Choose a different Meeting ID.');

            $('#meeting_id').val('');
    <?php     }
    } ?>


    function formatAMPM(date) {
        var hours = date.getHours();
        //console.log(hours);
        var minutes = date.getMinutes();
        //console.log(minutes);
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        //console.log(strTime);
        return strTime;
    }

    $(document).ready(function() {
        mdtimepicker.defaults({
            theme: 'blue',
            hourPadding: true,
            clearBtn: true
        });
        mdtimepicker('#timepicker', 'setValue', formatAMPM(new Date()))
    });


    function uncheckautostartrecording() {
        if ($('#ENABLE_RECORDING_id').is(':checked') == false) {
            $('#AUTO_START_RECORDING_id').prop('checked', false);
        }

    }

    function uncheckautostartlivestream() {
        if ($('#id_ENABLE_LIVESTREAM').is(':checked') == false) {
            $('#id_AUTO_START_LIVESTREAMING').prop('checked', false);
        }

    }

    function checkrecording() {
        if ($('#AUTO_START_RECORDING_id').is(':checked') == true) {
            $('#ENABLE_RECORDING_id').prop('checked', true);
        }

        if ($('#id_AUTO_START_LIVESTREAMING').is(':checked') == true) {
            document.getElementById("checkauostarstramalertbutton").click();
            $('#AUTO_START_RECORDING_id').prop('checked', false);
            return false;
        }

    }



    function checklivestreamsettings() {

        if ($('#AUTO_START_RECORDING_id').is(':checked') == true) {
            document.getElementById("checkauostarstramalertbutton").click();
            $('#id_AUTO_START_LIVESTREAMING').prop('checked', false);
            return false;
        }

        $.ajax({
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}"
            },
            url: '/customer/checklivestreamsettings',
            success: function(data) {

                if (data == 1) {
                    if ($('#id_AUTO_START_LIVESTREAMING').is(':checked') == true) {
                        $('#id_ENABLE_LIVESTREAM').prop('checked', true);
                    }
                } else {
                    if ($('#id_AUTO_START_LIVESTREAMING').is(':checked') == true) {
                        $('#id_ENABLE_LIVESTREAM').prop('checked', true);
                    }
                    //$('#id_AUTO_START_LIVESTREAMING').prop('checked',false);
                    //document.getElementById("checklivestreamsettingsalertbutton").click();

                    cash('#header-footer-modal-preview').modal('show');

                }

            }
        });

    }



    function check_pre_registration_feature() {

        if ($('#enable_pre_registration').is(':checked') == true) {
            <?php
            if (isset($subscription_feature['enable-pre-registration']) && isset($subscription_feature['enable-pre-registration']->is_available) && $subscription_feature['enable-pre-registration']->is_available == 1) { ?>
                $('#pre_registration_content').show();
            <?php } else { ?>
                document.getElementById("checkpreregistrationalert").click();
                $('#enable_pre_registration').prop('checked', false);

            <?php } ?>
        } else {
            $('#pre_registration_content').hide();
        }


    }

    function checkval(id, X) { //
        var numberOfChecked = $('input[name=\'sream[]\']:checked').length;
        var max_livestream_settings = $('#max_livestream_settings').val();

        if (id == 'meethour_id' && $('#meethour_id').prop('checked')) {
            $('#heading_text').html('Live Streaming + Recording Settings');
        } else {
            if ($('#meethour_id').prop('checked')) {
                $('#heading_text').html('Live Streaming + Recording Settings');
            } else {
                $('#heading_text').html('Live Streaming Settings');
            }
        }

        if (numberOfChecked > max_livestream_settings) {
            str = 'Only ' + max_livestream_settings + ' Live Streamings allowed at a time';
            $('#error_message').show();
            $('#error_message').html(str);

            $('#' + id).prop('checked', false);
            $('#' + id + '1').hide();
        } else {
            $('#error_message').hide();
        }
        if ($('#' + id).prop('checked')) {
            $('#' + id + '1').show();
        } else {
            $('#' + id + '1').hide();
            if (X != '')
                $('#' + X).val('');


        }

    }

    function checkdata() {
        $is_set = 0;

        if ($('#meethour_id').prop('checked')) {
            $('#meethour_recording_id').val(1);
            $('#icon_recording').show();
            $is_set = 1;
        } else {
            $('#meethour_recording_id').val("");
            $('#icon_recording').hide();
        }
        if ($('#facebook_id').prop('checked')) {
            facebook_stream_key = $('#facebook_stream_key').val();
            if (facebook_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter Facebook Stream Key');
                return false;
            }
            $('#icon_facebook2').show();
            $('#facebook_stream_key_id').val(facebook_stream_key);
            $('#a_facebook_id').attr("title", facebook_stream_key);
            $('#a_facebook_id').attr("alt", facebook_stream_key);
            $is_set = 1;
        } else {
            $('#facebook_stream_key').val("");
            $('#icon_facebook2').hide();
            $('#a_facebook_id').attr("title", "Facebook Live Stream");
            $('#a_facebook_id').attr("alt", "Facebook Live Stream");
        }

        if ($('#youtube_id').prop('checked')) {
            youtube_stream_key = $('#youtube_stream_key').val();
            if (youtube_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter YouTube Stream Key');
                return false;
            }
            $('#icon_youtube2').show();
            $('#youtube_stream_key_id').val(youtube_stream_key);
            $('#a_youtube_id').attr("title", youtube_stream_key);
            $('#a_youtube_id').attr("alt", youtube_stream_key);
            $is_set = 1;
        } else {
            $('#youtube_stream_key_id').val("");
            $('#icon_youtube2').hide();
            $('#a_youtube_id').attr("title", "Youtube Live Stream");
            $('#a_youtube_id').attr("alt", "Youtube Live Stream");
        }

        if ($('#twich_id').prop('checked')) {
            twich_stream_key = $('#twich_stream_key').val();
            if (twich_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter Twitch Stream Key');
                return false;
            }
            $('#icon_twitch').show();
            $('#twitch_stream_key_id').val(twich_stream_key);
            $('#a_twitch_id').attr("title", twich_stream_key);
            $('#a_twitch_id').attr("alt", twich_stream_key);
            $is_set = 1;
        } else {
            $('#twitch_stream_key_id').val("");
            $('#icon_twitch').hide();
            $('#a_twitch_id').attr("title", "Twitch Live Stream");
            $('#a_twitch_id').attr("alt", "Twitch Live Stream");
        }

        if ($('#linkedin_id').prop('checked')) {
            linkedin_stream_key = $('#linkedin_stream_key').val();
            if (linkedin_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter LinkedIn Stream Key');
                return false;
            }
            $('#icon_linkedin').show();
            $('#linkedin_stream_key_id').val(linkedin_stream_key);
            $('#a_linkedin_id').attr("title", linkedin_stream_key);
            $('#a_linkedin_id').attr("alt", linkedin_stream_key);
            $is_set = 1;
        } else {
            $('#linkedin_stream_key_id').val("");
            $('#icon_linkedin').hide();
            $('#a_linkedin_id').attr("title", "Linkedin Live Stream");
            $('#a_linkedin_id').attr("alt", "Linkedin Live Stream");
        }

        if ($('#instagram_id').prop('checked')) {
            instagram_stream_key = $('#instagram_stream_key').val();
            if (instagram_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter Instagram Stream Key');
                return false;
            }
            $('#icon_instagram').show();
            $('#instagram_stream_key_id').val(instagram_stream_key);
            $('#a_instagram_id').attr("title", instagram_stream_key);
            $('#a_instagram_id').attr("alt", instagram_stream_key);
            $is_set = 1;
        } else {
            $('#instagram_stream_key_id').val("");
            $('#icon_instagram').hide();
            $('#a_instagram_id').attr("title", "Instagram Live Stream");
            $('#a_instagram_id').attr("alt", "Instagram Live Stream");
        }

        if ($('#customrtmp_id').prop('checked')) {
            customrtmp_stream_key = $('#customrtmp_stream_key').val();
            if (customrtmp_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter Custom RTMP Stream Key');
                return false;
            }
            $('#icon_rtmp').show();
            $('#rtmp_stream_key_id').val(customrtmp_stream_key);
            $('#a_rtmp_id').attr("title", customrtmp_stream_key);
            $('#a_rtmp_id').attr("alt", customrtmp_stream_key);
            $is_set = 1;
        } else {
            $('#rtmp_stream_key_id').val("");
            $('#icon_rtmp').hide();
            $('#a_rtmp_id').attr("title", "Custom RTMP Live Stream");
            $('#a_rtmp_id').attr("alt", "Custom RTMP Live Stream");
        }

        if ($('#customrtmps_id').prop('checked')) {
            customrtmp1_stream_key = $('#customrtmp1_stream_key').val();
            if (customrtmp1_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter Custom RTMPS Stream Key');
                return false;
            }
            $('#icon_rtmps').show();
            $('#rtmps_stream_key_id').val(customrtmp1_stream_key);
            $('#a_rtmps_id').attr("title", customrtmp1_stream_key);
            $('#a_rtmps_id').attr("alt", customrtmp1_stream_key);
            $is_set = 1;
        } else {
            $('#rtmps_stream_key_id').val("");
            $('#icon_rtmps').hide();
            $('#a_rtmps_id').attr("title", "Custom RTMPS Live Stream");
            $('#a_rtmps_id').attr("alt", "Custom RTMPS Live Stream");
        }

        if ($('#viemo_id').prop('checked')) {
            viemo_stream_key = $('#viemo_stream_key').val();
            if (viemo_stream_key == '') {
                $('#error_message').show();
                $('#error_message').html('Please Enter Vimeo Stream Key');
                return false;
            }
            $('#icon_vimeo').show();
            $('#vimeo_stream_key_id').val(viemo_stream_key);
            $('#a_viemo_id').attr("title", viemo_stream_key);
            $('#a_viemo_id').attr("alt", viemo_stream_key);
            $is_set = 1;
        } else {
            $('#vimeo_stream_key_id').val("");
            $('#icon_vimeo').hide();
            $('#a_viemo_id').attr("title", "Viemo Live Stream");
            $('#a_viemo_id').attr("alt", "Viemo Live Stream");
        }
        if ($is_set == 0) {
            $('#error_message').show();
            $('#error_message').html('Please select at least one live stream channel.');
        }

        cash('#header-footer-modal-preview').modal('hide');
    }


    $(document).ready(function() {
        <?php
        $mr = user_settings::settings('Meethour_Recording', $subscription_details->owner_id);
        if (isset($mr) && !empty($mr)) { ?>
            $('#heading_text').html('Live Streaming + Recording Settings');
        <?php   } else { ?>
            $('#heading_text').html('Live Streaming Settings');
        <?php   } ?>

    });
</script>


<script>

function loadJS(FILE_URL, async = true) {
  let scriptEle = document.createElement("script");

  scriptEle.setAttribute("src", FILE_URL);
  scriptEle.setAttribute("type", "text/javascript");
  scriptEle.setAttribute("async", async);

  document.body.appendChild(scriptEle);

  // success event 
  scriptEle.addEventListener("load", () => {
    console.log("File loaded")
  });
   // error event
  scriptEle.addEventListener("error", (ev) => {
    console.log("Error on loading file", ev);
  });
}
document.addEventListener('DOMContentLoaded', function() {
loadJS("/js/atc.js", true);
//console.log('DOM fully loaded and parsed padmanabha');
});

  function checkmeetingid(event=0)
  {
    meeting_id=$('#meeting_id').val();

    if(meeting_id.length<6)
    {
        $('#meeting_id_text').hide();
        $('#meeting_id_error_text').show(); 
        $('#meeting_id').focus();
        $('#meetingid').val(meeting_id);
        return 1;
    }
   
    if(event != 0)
    {
            if (event.keyCode >= 37 && event.keyCode <= 40) 
            {
                return 1;
            }
    }
    $.ajax({
            type: "POST",
            data: {"_token":"{{ csrf_token() }}", "meeting_id":meeting_id},
            url: '/customer/meetingidcheck',
            success: function(data) {
             if(data==2)
             {
                $('#meeting_id_text').hide(); 
                $('#meeting_id_error_text').show(); 
                $('#meeting_id').focus();
                $('#meetingid').val(meeting_id);
                
             }    
             if(data==1)
             {
                $('#meeting_id_text').show(); 
                $('#meeting_id_error_text').hide(); 
                $('#meeting_id').focus();
                $('#meetingid').val(meeting_id);
                
             }

             if(data==0){
                $('#meeting_id_text').hide();  
                $('#meeting_id_error_text').hide(); 
                $('#meetingid').val(meeting_id);
             }

            }

      });
  }  



  function editmeetingid()
  {
     $('#smeeting_id_text').hide();
     $('#smeeting_id_text_link').hide();
     $('#smeeting_id_field').show();
  }

  function hidemeetingbutton()
  {
    meeting_id=$('#meeting_id').val();

    if(meeting_id.length<6)
    {
        $('#meeting_id_text').hide();
        $('#meeting_id_error_text').show(); 
        $('#meeting_id').focus();
        return 1;
    }

    $.ajax({
            type: "POST",
            data: {"_token":"{{ csrf_token() }}", "meeting_id":meeting_id},
            url: '/customer/meetingidcheck',
            success: function(data) {
                if(data==1 || data==2)
                {
                    $('#smeeting_id_field').show();
                    
                }
                else{
                    $('#smeeting_id_text').show();
                    $('#smeeting_id_text_link').show();
                    $('#smeeting_id_field').hide();
                    $('#meetingid').val(meeting_id);
                    $('#smeeting_id_text').html(meeting_id);
                    $('#meeting_id_text').hide(); 
                    $('#meeting_id_error_text').hide(); 

                }
            }

       });

    
     
  }

  function editmeetingidalert()
  {
    document.getElementById("checkpreregistrationalert").click();

  }



  function callwebinarfields()
  {

    $('#additional_webinar_fields').show();
  }
  

</script>

@endsection