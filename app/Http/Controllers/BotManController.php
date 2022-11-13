<?php
namespace App\Http\Controllers;
   
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;
   
class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');
   
        $botman->hears('{message}', function($botman, $message) {
   
            if ($message == 'Hi') {
                $this->askName($botman);
            }
            if ($message == '1') {
                $this->askPin($botman);
            }
            
            else{
                $botman->reply("Invalid Entry .<br> Select from the following options,<br> 1. Get PIN Number <br> 2. Check Filling Status <br> 3. Get TSO Location");
            }
   
        });
   
        $botman->listen();
    }
   
    /**
     * Place your BotMan logic here.
     */
    public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {
   
            $name = $answer->getText();
   
            $this->say('Nice to meet you '.$name);
        });
    }


    public function askPin($botman)
    {
        $botman->ask('Hello! What is your ID Number?', function(Answer $answer) {
   
            $IDNo = $answer->getText();
        $id_no=$IDNo;
        

            $ch = curl_init();
        $url="http://briananikayi.io.ke/kraapi/users_api.php?id=$id_no";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $resp= curl_exec($ch);
        //echo $resp;
        if ($e= curl_error($ch)) {
            echo $e;
            $this->say('There was an error code '.$e);
        } else {
            $decoded=json_decode($resp);
            //print_r($decoded);
            $responsecode_code = $decoded->response_code;
            $pin=$decoded->pin;
            $majina = $decoded->names;
            $messageback = $decoded->message;


           // return $messageback;
            $response_desc = $decoded->response_code;
	        extract(json_decode($resp, true));
            $this->say('Dear '.$majina.'<br>your request has been received. Your Pin will be delivered to your email.<br> Thank you for contacting KRA Chat Bot <br> ID No: '.$IDNo. ' <br> Pin No: '.$pin.' <br> Names: '.$majina);
        }
   
            
        });
    }
}