<?php

namespace App\Http\Controllers;

use App\Models\UssdInbox;
use App\Models\USSDSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class USSDSessionController extends Controller
{
    public function geepay(Request $request)
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
                $message_string="Welcome to Anti-corruption Commission. Please select from the following options:\n 1. Report corruption \n 2. Corruption Awareness \n 3. Request call back \n 4. Case Tracking \n 3. Register Complaint";
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
                                $message_string="Please select the form of corruption:\n 1. Bribery and Extortion \n 2. Fraud and Misuse of Public Resources \n 3. Embezzlement and Money Laundering \n 4. Other Forms of Corruption \n 0 for previous menu.";
                                $request_type = "2";
                                //update the session record
                                $update_session = USSDSession::where('session_id', $session_id)->update([
                                    "case_no" => 2,
                                    "step_no" => 1
                                ]);
                            }elseif ($last_part ==2){
                                $message_string="Select one of the following complaint category\n 1. Sim related. \n 2. License. \n 3. Consumer Protection. \n \n 0 for previous menu.";
                                $request_type = "2";
                                //update the session record
                                $update_session = USSDSession::where('session_id', $session_id)->update([
                                    "case_no" => 3,
                                    "step_no" => 1
                                ]);
                            }elseif ($last_part ==3){
                                $message_string="Select one of the following scamming category\n 1. Scam messages. \n 2. Scam calls. \n 3. Identity Theft. \n 4. Cyber Bullying. \n \n 0 for previous menu. ";
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
                            $message_string="Enter the name of corrupt individual \n \n 0 for previous menu.";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 2,
                                "step_no" => 2
                            ]);
                        }elseif ($last_part ==2){
                            $message_string="Select one of the following complaint category\n 1. Sim related. \n 2. License. \n 3. Consumer Protection. \n \n 0 for previous menu.";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 3,
                                "step_no" => 1
                            ]);
                        }elseif ($last_part ==3){
                            $message_string="Select one of the following scamming category\n 1. Scam messages. \n 2. Scam calls. \n 3. Identity Theft. \n 4. Cyber Bullying. \n \n 0 for previous menu. ";
                            $request_type = "2";
                            //update the session record
                            $update_session = USSDSession::where('session_id', $session_id)->update([
                                "case_no" => 4,
                                "step_no" => 1
                            ]);
                        }
                    }elseif ($case_no ==2 && $step_no ==2 && !empty($last_part) && is_numeric($last_part)){
                        if($last_part ==1){
                            //save into the ussd inbox table
                            $save_inquiry=UssdInbox::create([
                                'phone_number' => $phone,
                                'message' => 'An inquiry for simcard deactivation'
                            ]);
                            $save_inquiry->save();
                            $formatted_message="Hi, we have received your simcard deactivation inquiry. Our team will get back to you soon ";
                            $url_encoded_message = urlencode($formatted_message);

                            $sendSMS = Http::withoutVerifying()
                                ->post('http://www.cloudservicezm.com/smsservice/httpapi?username=school&password=school&msg=' . $url_encoded_message . '.+&shortcode=2343&sender_id=Ontech&phone=' . $phone . '&api_key=121231313213123123');
                            $message_string="An inquiry for your simcard activation has been received successfully ";
                            $request_type = "3";

                        }
                    }
                    break;
                case '3': //Register Complaint
                    break;
                case '4': //Report Scam
                    break;
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