<?php 

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class PrsistAssessmentController extends Controller {

    public function saveAssessment()
    {
        if (!Input::has("games")) {
            return Response::json(["error" => "No Game Data specified"], 400);
        }

        Log::info(json_encode(Input::all()));

        // Log game data
       // Mail::send('email_log', [], function($message) {
           // $message->to(["eyt_dev@sockii.com"])->subject("Prsist Assessment Log " . date("H:i:s d/m/Y"));
       // });

        $games = Input::get("games");

        foreach ($games as $gameData) {
            if (empty($gameData["user_data"])) {
                continue;
            }

            $game             = new PrsistAssessmentGame();
            $game->child_id   = $gameData["user_data"]["child_id"];
            $game->session_id = $gameData["user_data"]["session_id"];
            $game->test_name  = (empty($gameData["user_data"]["test_name"])) ? "Untitled Test" : $gameData["user_data"]["test_name"];
            $game->room      = $gameData["user_data"]["room"];
            $game->dob        = (empty($gameData["user_data"]["dob"])) ? null : \DateTime::createFromFormat("d/m/Y", $gameData["user_data"]["dob"]);
            $game->age        = $gameData["user_data"]["age"];
            $game->sex        = $gameData["user_data"]["sex"];
            $game->played_at  = $gameData["played_at"];
            $game->game_type  = $gameData["user_data"]["game_type"];
            $game->assessor_name  = $gameData["user_data"]["assessor_name"];
            $game->save();

 //           $gameScore = 0;
            foreach ($gameData["results"] as $score_key => $score_data) {
                $score             = new PrsistAssessmentScore();
                $score->game_id    = $game->id;
                $score->item_type  = $score_data["item_type"];
                $score->item_no    = $score_data["item_no"];
                $score->item_score = $score_data["item_score"];
                $score->save();
                
            }

  
            $game->save();
        }

        return Response::json(["success"]);
    }

    public function showResults($test_name = null, $start = null, $end = null)
    {
        $gameRep   = new Games(new PrsistAssessmentGame());
        $games     = $gameRep->getGames($test_name, $start, $end);
        $tests     = App::make('perms');
        $testNames = [];

        foreach ($tests as $test) {
            $key = str_replace("+", "%20", urlencode($test->test_name));
            if (!isset($testNames[$key])) {
                $testNames[$key] = $test;
            }
        }

        return View::make("prsistassessment/results", ["games"     => $games,
                                                     "test_name" => $test_name,
                                                     "start"     => (!empty($start)) ? DateTime::createFromFormat("Y-m-d", $start)->format("d/m/Y") : null,
                                                     "end"       => (!empty($end)) ? DateTime::createFromFormat("Y-m-d", $end)->format("d/m/Y") : null,
                                                     "tests"     => $testNames]);
    }
    
    public function viewScores($game_id)
    {
        $scores = PrsistAssessmentScore::where("game_id", "=", $game_id)->orderBy("item_no", "ASC")->get();

        return View::make("prsistassessment/scores", ["scores" => $scores]);
    }
    
    
    public function makeCSV($test_name = null, $start = null, $end = null)
    {
    
        $gameRep  = new Games(new PrsistAssessmentGame());
        $games    = $gameRep->getGames($test_name, $start, $end);

        $filename = date("U") . ".csv";

        $fp     = fopen(public_path() . "/tmp/" . $filename, 'w');
        $qCount = [];

        for ($x = 1; $x < 10; $x++) {
            $qCount[] = "Item_" . $x;
        }

        fputcsv($fp, array_merge(["game_id",
                                  "child_id",
                                  "session_id",
                                  "study_name",
                                  "room",
                                  "DOB",
                                  "age",
                                  "sex",
                                  "game_type",
                                  "assessor_name",
                                  "played_at",
                                  "Cognitive_score",
                                  "Behavioural_score",], $qCount));

        foreach ($games as $game) {
            $answers = [];

    		$cognitive = 0;
    		$behavioural = 0;
			$cognitiveAnswered = 0;
			$behaviouralAnswered = 0;
			
			
			//Work out the subscale
			foreach ($game->scores as $score) {
			    if (strpos($score["item_type"], 'Cognitive Self-Regulation') !== false) {
        			$cognitive = $cognitive + $score["item_score"];	
        		
        			if ($score["item_score"] != 0) {
        				$cognitiveAnswered = $cognitiveAnswered +1;
        			}	
        		}
        	
        		if (strpos($score["item_type"], 'Behavioural Self-Regulation') !== false) {
        			$behavioural = $behavioural + $score["item_score"];	
        		
        			if ($score["item_score"] != 0) {
        				$behaviouralAnswered = $behaviouralAnswered +1;
        			}		
 				}	
 				
 				//Get answers
 				$answers[] = ($score["item_score"] != 0) ? $score["item_score"] : ".";
			}
		
			//Calculate subscales
		    if ($cognitiveAnswered != 0) {
        		$cognitive = $cognitive/$cognitiveAnswered;
        	} else {
        		$cognitive = 0;
        	}
        
        	if ($behaviouralAnswered != 0) {
        		$behavioural = $behavioural/$behaviouralAnswered;
        	} else {
        		$behavioural = 0;
        	}


            fputcsv($fp, array_merge([$game->id,
                                      (empty($game->child_id)) ? "." : $game->child_id,
                                      (empty($game->session_id)) ? "." : $game->session_id,
                                      (empty($game->test_name)) ? "." : $game->test_name,
                                      (empty($game->room)) ? "." : $game->room,
                                      (empty($game->dob)) ? "." : $game->dob,
                                      (empty($game->age)) ? "." : $game->age,
                                      (empty($game->sex)) ? "." : $game->sex,
                                      (empty($game->game_type)) ? "." : $game->game_type,
                                      (empty($game->assessor_name)) ? "." : $game->assessor_name,
                                      (empty($game->played_at)) ? "." : $game->played_at,
                                      ($cognitive == 0) ? "." : $cognitive,
                                      ($behavioural == 0) ? "." : $behavioural], $answers));
        }

        fclose($fp);

        return View::make("csv", ["filename" => $filename]);
    }


}