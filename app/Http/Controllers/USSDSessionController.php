<?php

namespace App\Http\Controllers;

use App\Models\UssdInbox;
use App\Models\USSDSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class USSDSessionController extends Controller
{
    public function acc(Request $request)
    {

        //receiving data from the remote post method (zictaRemoteUtil.php)
        $auth = $request->get('auth');
        $data = $request->get('ussd_request');
        $api_id = "1234";
        $api_key = "1234";

        if ($auth['api_id'] == $api_id && $auth['api_key'] == $api_key) {

            //declaration of variable to be used
            $message_string = "";
            $case_no = 0;
            $step_no = 1;
            $reserved_value = 0;
            $saved_loan_amount = 0;
            $phone = $data['MSISDN'];
            $user_input = $data['MESSAGE'];
            $session_id = $data['SESSION_ID'];
            $lastPart = explode("*", $user_input);
            $parts = count($lastPart);
            $last_part = $lastPart[$parts - 1];
            $request_type = "2"; //continue


            //getting last session info
            $getLastSessionInfo = USSDSession::where('phone_number', $phone)->where('session_id', $session_id)->orderBy('id', 'DESC')->first();

            //checking if there is an active session or not
            if(!empty($getLastSessionInfo)){
                $case_no = $getLastSessionInfo->case_no;
                $step_no = $getLastSessionInfo->step_no;

            }else{
                //save new session record
                $new_session = USSDSession::create([
                    "phone_number" => $phone,
                    "case_no" => 0,
                    "step_no" => 1,
                    "session_id" => $session_id
                ]);
                $new_session->save();

            }
            if($case_no==0 && $step_no==1){
                $message_string="Welcome to ACC. Choose an option:\n 1. Report corruption \n 2. Corruption Awareness \n 3. Request call back \n 4. Register Complaint";
                $request_type = "2";
                //update the session record
                $update_session = USSDSession::where('session_id', $session_id)->update([
                    "case_no" => 1,
                    "step_no" => 2
                ]);
            }
            //Steps Logic

            switch($case_no){
                case '1': //welcome
                        if($case_no == 1 && $step_no == 2 && !empty($last_part) && is_numeric($last_part)){
                            if($last_part==1){
                                $message_string="Please select the form of corruption:\n 1. Bribery and Extortion \n 2. Fraud and Misuse of Public Resources \n 3. Embezzlement and Money Laundering \n 4. Other Forms of Corruption \n 0. for previous menu.";
                                $request_type = "2";
                                //update the session record
                                $update_session = USSDSession::where('session_id', $session_id)->update([
                                    "case_no" => 2,
                                    "step_no" => 1
                                ]);
                            }elseif ($last_part ==2){
                                    //save into the ussd inbox table
                                    $save_inquiry=UssdInbox::create([
                                        'phone_number' => $phone,
                                        'message' => 'The prompt for corruption awareness has been sent successfully'
                                    ]);
                                    $save_inquiry->save();
                                    $formatted_message="Hi, to find out more about corruption please visit our website: https://www.acc.gov.zm/about-corruption/ or call us on our toll-line 5980";
                                    $url_encoded_message = urlencode($formatted_message);
        
                                    $sendSMS = Http::withoutVerifying()
                                        ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                                    $message_string="The prompt for corruption awareness.";
                                    $request_type = "3";

                            }elseif ($last_part ==3){
                                $save_inquiry=UssdInbox::create([
                                    'phone_number' => $phone,
                                    'message' => 'Prompt for requesting a call back has been sent successfully'
                                ]);
                                $save_inquiry->save();
                                $formatted_message="Your request has been sent successfully, you will receive a call soon. Additionally, you can contact us using the following: \n Toll-free line 5980, \n Email info@acc.gov.zm, \n Direct mail/letter P.O Box 50486, Lusaka or\n Visit any one of the ACC offices";
                                $url_encoded_message = urlencode($formatted_message);
    
                                $sendSMS = Http::withoutVerifying()
                                    ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                                $message_string="A request for call back has been sent successfully";
                                $request_type = "3";
                            }elseif ($last_part ==4){
                                $message_string="Select the category of your complaint \n1. Did not receive call back. \n2. Case has not been followed up. \n \n 0 for previous menu.";
                                $request_type = "2";
                                //update the session record
                                $update_session = USSDSession::where('session_id', $session_id)->update([
                                    "case_no" => 4,
                                    "step_no" => 1
                                ]);
                            }
                        }
                    break;
                case '2': //Inquiries
                    if($case_no == 2 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)){
                        if($last_part==1){
                            $message_string="Do you wish to be anonymous \n1. Yes. \n2. No. \n0. for previous menu.";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 3,
                                "step_no" => 1
                            ]);
                        }elseif ($last_part ==2){
                            $message_string="Do you wish to be anonymous \n1. Yes. \n2. No. \n0. for previous menu.";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 3,
                                "step_no" => 1
                            ]);
                        }elseif ($last_part ==3){
                            $message_string="Do you wish to be anonymous \n1. Yes. \n2. No. \n0. for previous menu.";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 3,
                                "step_no" => 1
                            ]);
                        }elseif ($last_part ==4){
                            $message_string="Do you wish to be anonymous \n1. Yes. \n2. No. \n0. for previous menu.";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 3,
                                "step_no" => 1
                            ]);
                        }
                    }elseif ($case_no ==2 && $step_no ==1 && $last_part==0 && is_numeric($last_part)){
                        if($last_part ==0){
                            $message_string="Welcome to ACC. Choose an option:\n 1. Report corruption \n 2. Corruption Awareness \n 3. Request call back \n 4. Register Complaint";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 1,
                                "step_no" => 2
                            ]);
                        }
                    }
                    break;
                case '3': //Register Complaint
                    if ($case_no ==3 && $step_no ==1 && !empty($last_part) && is_numeric($last_part)){
                        if($last_part ==1){
                            $save_inquiry=UssdInbox::create([
                                'phone_number' => $phone,
                                'message' => 'Prompt for requesting a reporting has been sent succesfully'
                            ]);
                            $save_inquiry->save();
                            $formatted_message="Your request has been sent successfully, you will receive a call soon to ascertain the details of your report. Additionally, you can contact us using using our toll-free line 5980 or visit the nearest ACC office to you.";
                            $url_encoded_message = urlencode($formatted_message);

                            $sendSMS = Http::withoutVerifying()
                                ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                            $message_string="A request for reporting has been sent successfully";
                            $request_type = "3";
                        }elseif($last_part ==2){
                            $save_inquiry=UssdInbox::create([
                                'phone_number' => $phone,
                                'message' => 'Prompt for requesting a reporting has been sent succesfully'
                            ]);
                            $save_inquiry->save();
                            $formatted_message="Your request has been sent successfully, you will receive a call soon to ascertain the details of your report. Additionally, you can contact us using using our toll-free line 5980 or visit the nearest ACC office to you.";
                            $url_encoded_message = urlencode($formatted_message);

                            $sendSMS = Http::withoutVerifying()
                                ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                            $message_string="A request for reporting has been sent successfully";
                            $request_type = "3";
                        }
                    }elseif ($case_no ==3 && $step_no ==1 && $last_part ==0 && is_numeric($last_part)){
                        if($last_part ==0){
                            $message_string="Please select the form of corruption:\n 1. Bribery and Extortion \n 2. Fraud and Misuse of Public Resources \n 3. Embezzlement and Money Laundering \n 4. Other Forms of Corruption \n 0. for previous menu.";
                                $request_type = "2";
                                //update the session record
                                $update_session = USSDSession::where('session_id', $session_id)->update([
                                    "case_no" => 2,
                                    "step_no" => 1
                                ]);
                        }
                    }
                    break;
                case '4': //Report Scam
                    if($case_no == 4 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)){
                        if($last_part==1){
                            //save into the ussd inbox table
                           $save_inquiry=UssdInbox::create([
                            'phone_number' => $phone,
                            'message' => 'Response to complaint 1'
                        ]);
                        $save_inquiry->save();
                        $formatted_message="We are sorry for the inconvinience, we will get back to you as soon as possible";
                        $url_encoded_message = urlencode($formatted_message);

                        $sendSMS = Http::withoutVerifying()
                            ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                        $message_string="Prompt for the first complain has been sent successfully.";
                        $request_type = "3";
                        }elseif($last_part==2){
                            //save into the ussd inbox table
                            $save_inquiry=UssdInbox::create([
                                'phone_number' => $phone,
                                'message' => 'Response to complaint 2'
                            ]);
                            $save_inquiry->save();
                        $formatted_message="We are sorry for the inconvinience, we will get back to you as soon as possible";
                        $url_encoded_message = urlencode($formatted_message);

                        $sendSMS = Http::withoutVerifying()
                            ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                        $message_string="Prompt for the second complain has been sent successfully.";
                        $request_type = "3";
                        }
                }elseif ($case_no == 4 && $step_no == 1 && $last_part==0 && is_numeric($last_part)){
                    if($last_part ==0){
                        $message_string="Welcome to ACC. Choose an option:\n 1. Report corruption \n 2. Corruption Awareness \n 3. Request call back \n 4. Register Complaint";
                        $request_type = "2";
                        //update the session record
                        $update_session = USSDSession::where('session_id', $session_id)->update([
                            "case_no" => 1,
                            "step_no" => 2
                        ]);
                    }
                }
                    break;
                    case '5': //Inquiries
                        if($case_no == 5 && $step_no == 1 && !empty($last_part)){
                            if($last_part==$corrupt_individual){
                                $message_string="From which organisation does the individual belong? \n \n 0 for previous menu.";
                                $request_type = "2";
                                //update the session record
                                $update_session = USSDSession::where('session_id', $session_id)->update([
                                    "case_no" => 5,
                                    "step_no" => 2
                                ]);
                            }
                        }elseif ($case_no ==5 && $step_no ==2 && !empty($last_part)){
                            if($last_part ==1){
                                //save into the ussd inbox table
                           $save_inquiry=UssdInbox::create([
                            'phone_number' => $phone,
                            'message' => 'Response to complaint 1'
                        ]);
                        $save_inquiry->save();
                        $formatted_message="We are sorry for the inconvinience, we will get back to you as soon as possible";
                        $url_encoded_message = urlencode($formatted_message);

                        $sendSMS = Http::withoutVerifying()
                            ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                        $message_string="Prompt for the second complaint has been sent successfully.";
                        $request_type = "3";
                            }
                        }
            }
            //request response
            $response = array(
                "ussd_response" => array(
                    "USSD_BODY" => $message_string,
                    "REQUEST_TYPE" => $request_type
                )
            );

            return response()->json($response);

        }
    }
}