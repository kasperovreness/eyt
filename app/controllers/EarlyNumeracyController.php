<?php

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class EarlyNumeracyController extends Controller
{
    public function saveEntries()
    {
        if (!Input::has("games")) {
            return Response::json(["error" => "No Game Data specified"], 400);
        }

        Log::info(json_encode(Input::all()));

        // Log game data

        $games = Input::get("games");

        foreach ($games as $gameData) {
            if (empty($gameData["user_data"])) {
                continue;
            }

            $game             = new EarlyNumeracyGame();
            $game->subject_id   = $gameData["user_data"]["child_id"];
            $game->session_id = $gameData["user_data"]["session_id"];
            $game->test_name  = (empty($gameData["user_data"]["test_name"])) ? "Untitled Test" : $gameData["user_data"]["test_name"];
            $game->grade      = $gameData["user_data"]["grade"];
            $game->dob        = (empty($gameData["user_data"]["dob"])) ? null : \DateTime::createFromFormat("d/m/Y", $gameData["user_data"]["dob"]);
            $game->age        = (empty($gameData["user_data"]["age"])) ? null : $gameData["user_data"]["age"];
            $game->sex        = (empty($gameData["user_data"]["sex"])) ? null : $gameData["user_data"]["sex"];
            $game->played_at  = $gameData["played_at"];
            $game->score      = 0;
            $game->save();

            $gameScore = 0;
            foreach ($gameData["scores"] as $score_key => $score_data) {
                $score           = new EarlyNumeracyScore();
                $score->game_id  = $game->id;
                $score->item     = $score_key;
                $score->value    = $score_data["answer"];
                $score->response = $score_data["response"];
                $score->save();
                
                if ($score_data["answer"] == 1) {
                	$gameScore = $gameScore +1;
                }
                
            }

            $game->score = $gameScore;
            $game->save();
        }

        return Response::json(["success"]);
    }

    public function showResults($test_name = null, $start = null, $end = null)
    {
        $gameRep   = new Games(new EarlyNumeracyGame());
        $games     = $gameRep->getGames($test_name, $start, $end);
        $tests     = App::make('perms');
        $testNames = [];

        foreach ($tests as $test) {
            $key = str_replace("+", "%20", urlencode($test->test_name));
            if (!isset($testNames[$key])) {
                $testNames[$key] = $test;
            }
        }

        return View::make("early_numeracy/results", ["games"     => $games,
                                                     "test_name" => $test_name,
                                                     "start"     => (!empty($start)) ? DateTime::createFromFormat("Y-m-d", $start)->format("d/m/Y") : null,
                                                     "end"       => (!empty($end)) ? DateTime::createFromFormat("Y-m-d", $end)->format("d/m/Y") : null,
                                                     "tests"     => $testNames]);
    }

    public function viewScores($game_id)
    {
        $scores = EarlyNumeracyScore::where("game_id", "=", $game_id)->get()->toArray();

		$gameIDValues = ["1_Meas1",
						"2_NC1",
						"3_Meas2",
						"4_NC2",
						"5_NC3",
						"6_Meas3",
						"7_NC4",
						"8_Cardin1",
						"9_NC5",
						"10_Meas4",
						"11_Meas5",
						"12_NC6",
						"13_NC7",
						"14_IdentNum1",
						"14_MatchNum1",
						"15_CountSub1",
						"16_NC8",
						"17_Meas6",
						"18_Meas7",
						"19_IdentNum2",
						"19_MatchNum2",
						"20_NC9",
						"21_Meas8",
						"22_NC10",
						"23_IdentNum3",
						"23_MatchNum3",
						"24_Cardin2",
						"25_NumOrd1",
						"26_Subit1",
						"27_Meas9",
						"28_NC11",
						"29_IdentNum4",
						"29_MatchNum4",
						"30_IdentNum5",
						"30_MatchNum5",
						"31_Meas10",
						"32_NumOrd2",
						"33_Ordin1",
						"34_Meas11",
						"35_Ordin2",
						"36_Subit2",
						"37_CountSub2",
						"38_WordProb1",
						"39_Pattern1",
						"40_Ordin3",
						"41_Subit3",
						"42_IdentNum6",
						"42_MatchNum6",
						"43_Ordin4",
						"44_NumOrd3",
						"45_Pattern2",
						"46_Meas12",
						"47_NumOrd4",
						"48_CountSub3",
						"49_CountSub4",
						"50_Meas13",
						"51_NC12",
						"52_Pattern3",
						"53_Pattern4",
						"54_Subit4",
						"55_Equat1",
						"56_NumOrd5",
						"57_Ordin5",
						"58_Cardin3",
						"59_Subit5",
						"60_Ordin6",
						"61_CountSub5",
						"62_Pattern5",
						"63_Subit6",
						"64_CountSub6",
						"65_Pattern6",
						"66_Cardin4",
						"67_Equat2",
						"68_Equat3",
						"69_NumOrd6",
						"70_Cardin5",
						"71_WordProb2",
						"72_Equat4",
						"73_Cardin6",
						"74_Equat5",
						"75_WordProb3",
						"76_WordProb4",
						"77_WordProb5",
						"78_Equat6",
						"79_WordProb6"];

/*
        $gameIDValues = ["Number Concepts #1",
                        "Number Concepts #2",
                        "Number Concepts #3",
                        "Cardinality #1",
                        "Number Concepts #4",
                        "Number Concepts #5",
                        "Subitising #1",
                        "Spatial & Measurement Concepts #1",
                        "Number Concepts #6",
                        "Spatial & Measurement Concepts #2",
                        "Number Concepts #7",
                        "Number Concepts #8",
                        "Patterning #1",
                        "Spatial & Measurement Concepts #3",
                        "Spatial & Measurement Concepts #4",
                        "Spatial & Measurement Concepts #5",
                        "Number Concepts #9",
                        "Number Concepts #10",
                        "Ordinality #1",
                        "Subitising #2",
                        "Number Concepts #11",
                        "Subitising #3",
                        "Counting a Subset #1",
                        "Cardinality #2",
                        "Ordinality #2",
                        "Spatial & Measurement Concepts #6",
                        "Subitising #4",
                        "Patterning #2",
                        "Spatial & Measurement Concepts #7",
                        "Identifying Digit & Quantity #1",
                        "Matching Digit & Quantity #1",
                        "Identifying Digit & Quantity #2",
                        "Matching Digit & Quantity #2",
                        "Counting a Subset #2",
                        "Cardinality #3",
                        "Ordinality #3",
                        "Ordinality #4",
                        "Ordinality #5",
                        "Number Concepts #12",
                        "Spatial & Measurement Concepts #8",
                        "Number Order #1",
                        "Spatial & Measurement Concepts #9",
                        "Spatial & Measurement Concepts #10",
                        "Patterning #3",
                        "Subitising #5",
                        "Numerical Word Problems #1",
                        "Ordinality #6",
                        "Spatial & Measurement Concepts #11",
                        "Numerical Word Problems #2",
                        "Cardinality #4",
                        "Counting a Subset #3",
                        "Number Order #2",
                        "Subitising #6",
                        "Numerical Word Problems #3",
                        "Spatial & Measurement Concepts #12",
                        "Number Order #3",
                        "Number Order #4",
                        "Identifying Digit & Quantity #3",
                        "Matching Digit & Quantity #3",
                        "Patterning #4",
                        "Spatial & Measurement Concepts #13",
                        "Cardinality #5",
                        "Identifying Digit & Quantity #4",
                        "Matching Digit & Quantity #4",
                        "Cardinality #6",
                        "Counting a Subset #4",
                        "Identifying Digit & Quantity #5",
                        "Matching Digit & Quantity #5",
                        "Counting a Subset #5",
                        "Counting a Subset #6",
                        "Identifying Digit & Quantity #6",
                        "Matching Digit & Quantity #6",
                        "Number Order #5",
                        "Number Order #6",
                        "Numerical Word Problems #4",
                        "Patterning #5",
                        "Numerical Word Problems #5",
                        "Numerical Equations #1",
                        "Numerical Equations #2",
                        "Numerical Equations #3",
                        "Numerical Word Problems #6", 
                        "Numerical Equations #4",
                        "Numerical Equations #5",
                        "Numerical Equations #6",
                        "Patterning #6"];
                        */
                        
        $scoreValues = array_flip($gameIDValues);

        foreach ($scores as $key => $val) {
            $a_item = explode(".", $val["item"]);
            $a_key  = trim($a_item[count($a_item) - 1]);

            $scores[$key]["item"] = $a_key;
        }
        
        /*usort($scores, function($a, $b) {
            return strcmp($a["item"], $b["item"]);
        });*/
        
		usort($scores, function($a, $b) use ($scoreValues) {
			$a_item = explode(".", $a["item"]);
			$a_key  = trim($a_item[count($a_item) - 1]);
			$a_sort = $scoreValues[$a_key];
			$b_item = explode(".", $b["item"]);
			$b_key  = trim($b_item[count($b_item) - 1]);
			$b_sort = $scoreValues[$b_key];

			return ($a_sort > $b_sort) ? 1 : -1;
		});        


        return View::make("early_numeracy/scores", ["scores" => $scores]);
    }

    public function makeCSV($test_name = null, $start = null, $end = null, $returnFile = false)
    {
        $gameRep     = new Games(new EarlyNumeracyGame());
        $games       = $gameRep->getGames($test_name, $start, $end);
        $filename    = "early_numeracy_" . date("U") . ".csv";
        $fp          = fopen(public_path() . "/tmp/" . $filename, 'w');
        $gamesCount  = [];
        $gameIDValues = ["1_Meas1",
						"2_NC1",
						"3_Meas2",
						"4_NC2",
						"5_NC3",
						"6_Meas3",
						"7_NC4",
						"8_Cardin1",
						"9_NC5",
						"10_Meas4",
						"11_Meas5",
						"12_NC6",
						"13_NC7",
						"14_IdentNum1",
						"14_MatchNum1",
						"15_CountSub1",
						"16_NC8",
						"17_Meas6",
						"18_Meas7",
						"19_IdentNum2",
						"19_MatchNum2",
						"20_NC9",
						"21_Meas8",
						"22_NC10",
						"23_IdentNum3",
						"23_MatchNum3",
						"24_Cardin2",
						"25_NumOrd1",
						"26_Subit1",
						"27_Meas9",
						"28_NC11",
						"29_IdentNum4",
						"29_MatchNum4",
						"30_IdentNum5",
						"30_MatchNum5",
						"31_Meas10",
						"32_NumOrd2",
						"33_Ordin1",
						"34_Meas11",
						"35_Ordin2",
						"36_Subit2",
						"37_CountSub2",
						"38_WordProb1",
						"39_Pattern1",
						"40_Ordin3",
						"41_Subit3",
						"42_IdentNum6",
						"42_MatchNum6",
						"43_Ordin4",
						"44_NumOrd3",
						"45_Pattern2",
						"46_Meas12",
						"47_NumOrd4",
						"48_CountSub3",
						"49_CountSub4",
						"50_Meas13",
						"51_NC12",
						"52_Pattern3",
						"53_Pattern4",
						"54_Subit4",
						"55_Equat1",
						"56_NumOrd5",
						"57_Ordin5",
						"58_Cardin3",
						"59_Subit5",
						"60_Ordin6",
						"61_CountSub5",
						"62_Pattern5",
						"63_Subit6",
						"64_CountSub6",
						"65_Pattern6",
						"66_Cardin4",
						"67_Equat2",
						"68_Equat3",
						"69_NumOrd6",
						"70_Cardin5",
						"71_WordProb2",
						"72_Equat4",
						"73_Cardin6",
						"74_Equat5",
						"75_WordProb3",
						"76_WordProb4",
						"77_WordProb5",
						"78_Equat6",
						"79_WordProb6"];
        /*
        $gameIDValues = ["Number Concepts #1",
                        "Number Concepts #2",
                        "Number Concepts #3",
                        "Cardinality #1",
                        "Number Concepts #4",
                        "Number Concepts #5",
                        "Subitising #1",
                        "Spatial & Measurement Concepts #1",
                        "Number Concepts #6",
                        "Spatial & Measurement Concepts #2",
                        "Number Concepts #7",
                        "Number Concepts #8",
                        "Patterning #1",
                        "Spatial & Measurement Concepts #3",
                        "Spatial & Measurement Concepts #4",
                        "Spatial & Measurement Concepts #5",
                        "Number Concepts #9",
                        "Number Concepts #10",
                        "Ordinality #1",
                        "Subitising #2",
                        "Number Concepts #11",
                        "Subitising #3",
                        "Counting a Subset #1",
                        "Cardinality #2",
                        "Ordinality #2",
                        "Spatial & Measurement Concepts #6",
                        "Subitising #4",
                        "Patterning #2",
                        "Spatial & Measurement Concepts #7",
                        "Identifying Digit & Quantity #1",
                        "Matching Digit & Quantity #1",
                        "Identifying Digit & Quantity #2",
                        "Matching Digit & Quantity #2",
                        "Counting a Subset #2",
                        "Cardinality #3",
                        "Ordinality #3",
                        "Ordinality #4",
                        "Ordinality #5",
                        "Number Concepts #12",
                        "Spatial & Measurement Concepts #8",
                        "Number Order #1",
                        "Spatial & Measurement Concepts #9",
                        "Spatial & Measurement Concepts #10",
                        "Patterning #3",
                        "Subitising #5",
                        "Numerical Word Problems #1",
                        "Ordinality #6",
                        "Spatial & Measurement Concepts #11",
                        "Numerical Word Problems #2",
                        "Cardinality #4",
                        "Counting a Subset #3",
                        "Number Order #2",
                        "Subitising #6",
                        "Numerical Word Problems #3",
                        "Spatial & Measurement Concepts #12",
                        "Number Order #3",
                        "Number Order #4",
                        "Identifying Digit & Quantity #3",
                        "Matching Digit & Quantity #3",
                        "Patterning #4",
                        "Spatial & Measurement Concepts #13",
                        "Cardinality #5",
                        "Identifying Digit & Quantity #4",
                        "Matching Digit & Quantity #4",
                        "Cardinality #6",
                        "Counting a Subset #4",
                        "Identifying Digit & Quantity #5",
                        "Matching Digit & Quantity #5",
                        "Counting a Subset #5",
                        "Counting a Subset #6",
                        "Identifying Digit & Quantity #6",
                        "Matching Digit & Quantity #6",
                        "Number Order #5",
                        "Number Order #6",
                        "Numerical Word Problems #4",
                        "Patterning #5",
                        "Numerical Word Problems #5",
                        "Numerical Equations #1",
                        "Numerical Equations #2",
                        "Numerical Equations #3",
                        "Numerical Word Problems #6", 
                        "Numerical Equations #4",
                        "Numerical Equations #5",
                        "Numerical Equations #6",
                        "Patterning #6"];
                       */
                        
        $scoreValues = array_flip($gameIDValues);


        /*for ($x = 0; $x < 87; $x++) {
            $gamesCount[] = "Item_" . ($x + 1);
        }*/



        fputcsv($fp, array_merge(["game_id",
                                  "child_id",
                                  "session_id",
                                  "study_name",
                                  "grade",
                                  "DOB",
                                  "age",
                                  "sex",
                                  "played_at",
                                  "score"], $gameIDValues));

        foreach ($games as $game) {
            $gameScores = $game->scores->toArray();

            foreach ($gameScores as $key => $val) {
                $a_item = explode(".", $val["item"]);
                $a_key  = trim($a_item[count($a_item) - 1]);

                $gameScores[$key]["item"] = $a_key;
            }

       		/*usort($gameScores, function($a, $b) {
            	return strcmp($a["item"], $b["item"]);
        	});*/
	
            usort($gameScores, function($a, $b) use ($scoreValues) {
                $a_item = explode(".", $a["item"]);
                $a_key  = trim($a_item[count($a_item) - 1]);
                $a_sort = $scoreValues[$a_key];
                $b_item = explode(".", $b["item"]);
                $b_key  = trim($b_item[count($b_item) - 1]);
                $b_sort = $scoreValues[$b_key];

                return ($a_sort > $b_sort) ? 1 : -1;
            });

            //echo "<pre>";
            //dd($gameScores);

            /*if (count($gameScores) > 60) {
                unset($gameScores[43]);
            }*/


            $scores = [];

            foreach (array_flip($scoreValues) as $scoreValue) {
                if ($scoreValue == "Subitizing-Dots-Loading") {
                    continue;
                }

                $found = false;

                foreach ($gameScores as $score) {
                    if ($score["item"] == $scoreValue) {
                        $val = ($score["value"] == 1 || $score["value"] == 0) ? $score["value"] : 0;
                        
                        //Add in the response value
                        if ($val == 0) {
                        	$val = $val ." (". $score["response"].")";
                        }
                        
                        $scores[] = $val;
                        $found = true;
                    }
                }
                if ($found == false) {
                    $scores[] = ".";
                }
            }

            for ($x = count($scores); $x < count($scores); $x++) {
                $scores[] = ".";
            }

                                      
            fputcsv($fp, array_merge([$game->id,
                                      (empty($game->subject_id)) ? "." : $game->subject_id,
                                      (empty($game->session_id)) ? "." : $game->session_id,
                                      (empty($game->test_name)) ? "." : $game->test_name,
                                      (empty($game->grade)) ? "." : $game->grade,
                                      (empty($game->dob)) ? "." : $game->dob,
                                      (empty($game->age)) ? "." : $game->age,
                                      (empty($game->sex)) ? "." : $game->sex,
                                      (empty($game->played_at)) ? "." : $game->played_at,
                                      (empty($game->score)) ? "." : $game->score], $scores));                                      
        }

        fclose($fp);

        if ($returnFile == true) {
            return $filename;
        } else {
            return View::make("csv", ["filename" => $filename]);
        }
    }

    public function fixDuplicates()
    {
        ini_set('max_execution_time', 300); // 5 minutes

        $games   = EarlyNumeracyGame::all();
        $deleted = [];
        
        $num_deleted = 0;

        foreach ($games as $game) {
            if (in_array($game->id, $deleted)) {
                continue;
            }

            $duplicates = EarlyNumeracyGame::where("id", "!=", $game->id)->where("subject_id", "=", $game->subject_id)->where("session_id", "=", $game->session_id)->where("test_name", "=", $game->test_name)->where("played_at", "=", $game->played_at)->get();

            foreach ($duplicates as $duplicate) {
            	$num_deleted = $num_deleted + 1;
            	$deleted[] = $duplicate->id;
            	
                EarlyNumeracyScore::where("game_id", "=", $duplicate->id)->delete();
                EarlyNumeracyGame::where("id", "=", $duplicate->id)->delete();

            }
        }
	
        //echo "Removed " . sizeof($deleted) . " duplicates";
        echo "Removed " . $num_deleted . " duplicates";
    }

    public function deleteGames()
    {
        $games   = Input::get("game_ids");
        $gameRep = new Games(new EarlyNumeracyGame());

        return $gameRep->deleteGames(new EarlyNumeracyScore(), $games);
    }
    
    
    public function deleteGame($game_id)
    {
        $user = App::make("user");

        if ($user->delete == 1) {
            EarlyNumeracyScore::where("game_id", "=", $game_id)->delete();
            EarlyNumeracyGame::where("id", "=", $game_id)->delete();

            return ["success" => true];
        } else {
            return ["success" => false];
        }
    }
    
    
    public function applyStartStopRule() 
    {
        $gameIDValues = ["1_Meas1",
						"2_NC1",
						"3_Meas2",
						"4_NC2",
						"5_NC3",
						"6_Meas3",
						"7_NC4",
						"8_Cardin1",
						"9_NC5",
						"10_Meas4",
						"11_Meas5",
						"12_NC6",
						"13_NC7",
						"14_IdentNum1",
						"14_MatchNum1",
						"15_CountSub1",
						"16_NC8",
						"17_Meas6",
						"18_Meas7",
						"19_IdentNum2",
						"19_MatchNum2",
						"20_NC9",
						"21_Meas8",
						"22_NC10",
						"23_IdentNum3",
						"23_MatchNum3",
						"24_Cardin2",
						"25_NumOrd1",
						"26_Subit1",
						"27_Meas9",
						"28_NC11",
						"29_IdentNum4",
						"29_MatchNum4",
						"30_IdentNum5",
						"30_MatchNum5",
						"31_Meas10",
						"32_NumOrd2",
						"33_Ordin1",
						"34_Meas11",
						"35_Ordin2",
						"36_Subit2",
						"37_CountSub2",
						"38_WordProb1",
						"39_Pattern1",
						"40_Ordin3",
						"41_Subit3",
						"42_IdentNum6",
						"42_MatchNum6",
						"43_Ordin4",
						"44_NumOrd3",
						"45_Pattern2",
						"46_Meas12",
						"47_NumOrd4",
						"48_CountSub3",
						"49_CountSub4",
						"50_Meas13",
						"51_NC12",
						"52_Pattern3",
						"53_Pattern4",
						"54_Subit4",
						"55_Equat1",
						"56_NumOrd5",
						"57_Ordin5",
						"58_Cardin3",
						"59_Subit5",
						"60_Ordin6",
						"61_CountSub5",
						"62_Pattern5",
						"63_Subit6",
						"64_CountSub6",
						"65_Pattern6",
						"66_Cardin4",
						"67_Equat2",
						"68_Equat3",
						"69_NumOrd6",
						"70_Cardin5",
						"71_WordProb2",
						"72_Equat4",
						"73_Cardin6",
						"74_Equat5",
						"75_WordProb3",
						"76_WordProb4",
						"77_WordProb5",
						"78_Equat6",
						"79_WordProb6"];
						
		$scoreValues = array_flip($gameIDValues);
    
    	
		echo "Applying Start and Stop Rule...<br><br>";
		ini_set('max_execution_time', 180); // 3 minutes         
	
		//Get entries that match our condition (aged 4)
		$gameEntries = EarlyNumeracyGame::where("age", "=", 4)->get();
		echo "No. games for aged 4: ";
		echo count($gameEntries);
		echo "<br><br>";
		
        foreach ($gameEntries as $entry) {
        	
        	//Get the game ID
            $gameID = $entry->id;
        	
        	echo "<br>Game ID: ";
        	echo $gameID;

            //Fetch the game scores for the game ID
            $gameScores = EarlyNumeracyScore::where("game_id", "=", $gameID)->get()->toArray();
            echo "<br>No. of results in game: ";
            echo count($gameScores);
            echo "<br><br>";
        
     		
     		// Sort the game scores
            // First place arrange the scores by value and item
//             foreach ($gameScores as $key => $val) {
//             
//             	$a_item = explode(".", $val["item"]);
//             	$a_key  = trim($a_item[count($a_item) - 1]);
//             	
//             	$scores[$key]["item"] = $a_key;
//         	}
        
        	/*usort($scores, function($a, $b) {
          	  return strcmp($a["item"], $b["item"]);
        	});*/
        
        	//This will sort the scores in the respective item ordering
			usort($gameScores, function($a, $b) use ($scoreValues) {
				$a_item = explode(".", $a["item"]);
				$a_key  = trim($a_item[count($a_item) - 1]);
				$a_sort = $scoreValues[$a_key];
				
				$b_item = explode(".", $b["item"]);
				$b_key  = trim($b_item[count($b_item) - 1]);
				$b_sort = $scoreValues[$b_key];

				return ($a_sort > $b_sort) ? 1 : -1;
			});        
			
			
            //Look at each game question result
            foreach ($gameScores as $score) {
      			foreach ($score as $scoreItem) {
      			
      				// $value = $scores_items[$key];
					//echo "<br>";
            		//echo $scoreItem;
      			
      			}            
            }
            
        	//var_dump($gameScores);
            	
            //Check for consecutive incorrect answers
            
            //EarlyNumeracyScore::where("game_id", "=", $duplicate->id)->delete();
            //EarlyNumeracyGame::where("id", "=", $duplicate->id)->delete();

            //$deleted[] = $duplicate->id;
        }
        
        echo "<br>Completed!";

	}
    
    
    public function conformStartStopRule($age)
    {
    	$gameIDValues = ["1_Meas1",
						"2_NC1",
						"3_Meas2",
						"4_NC2",
						"5_NC3",
						"6_Meas3",
						"7_NC4",
						"8_Cardin1",
						"9_NC5",
						"10_Meas4",
						"11_Meas5",
						"12_NC6",
						"13_NC7",
						"14_IdentNum1",
						"14_MatchNum1",
						"15_CountSub1",
						"16_NC8",
						"17_Meas6",
						"18_Meas7",
						"19_IdentNum2",
						"19_MatchNum2",
						"20_NC9",
						"21_Meas8",
						"22_NC10",
						"23_IdentNum3",
						"23_MatchNum3",
						"24_Cardin2",
						"25_NumOrd1",
						"26_Subit1",
						"27_Meas9",
						"28_NC11",
						"29_IdentNum4",
						"29_MatchNum4",
						"30_IdentNum5",
						"30_MatchNum5",
						"31_Meas10",
						"32_NumOrd2",
						"33_Ordin1",
						"34_Meas11",
						"35_Ordin2",
						"36_Subit2",
						"37_CountSub2",
						"38_WordProb1",
						"39_Pattern1",
						"40_Ordin3",
						"41_Subit3",
						"42_IdentNum6",
						"42_MatchNum6",
						"43_Ordin4",
						"44_NumOrd3",
						"45_Pattern2",
						"46_Meas12",
						"47_NumOrd4",
						"48_CountSub3",
						"49_CountSub4",
						"50_Meas13",
						"51_NC12",
						"52_Pattern3",
						"53_Pattern4",
						"54_Subit4",
						"55_Equat1",
						"56_NumOrd5",
						"57_Ordin5",
						"58_Cardin3",
						"59_Subit5",
						"60_Ordin6",
						"61_CountSub5",
						"62_Pattern5",
						"63_Subit6",
						"64_CountSub6",
						"65_Pattern6",
						"66_Cardin4",
						"67_Equat2",
						"68_Equat3",
						"69_NumOrd6",
						"70_Cardin5",
						"71_WordProb2",
						"72_Equat4",
						"73_Cardin6",
						"74_Equat5",
						"75_WordProb3",
						"76_WordProb4",
						"77_WordProb5",
						"78_Equat6",
						"79_WordProb6"];
						
		$scoreValues = array_flip($gameIDValues);
    	
    	// Get entries that match our condition ($age)
// 		$gameEntries = EarlyNumeracyGame::all();
// 
// 		echo "No. games found: ";
// 		echo count($gameEntries);
// 		echo "<br><br>";	

    	$above = 1;
    	$sign = "=";
    	
    	echo "Begin Processing Start and Stop Rule...for age ";
    	echo $age;
    	
    	if ($age <= 3)
    	{
    		$above = 0;
    	}
    	
    	if ($above)
    	{
    		if ($age == 5)
    		{
    			$sign = ">=";
				echo "+";
    		}    		
    	}
    	else
    	{
    		$sign = "<=";
    		echo " and below (or not specified i.e. age = '.')"; 
    	}
    	
    	echo "<br>";
    	
		ini_set('max_execution_time', 180); // 3 minutes         
		
		// Get entries that match our condition ($age)
		$gameEntries;
		
		if ($above == 0)
		{
			// We also want to add game entries with no age entered
			$gameEntries = EarlyNumeracyGame::where("age","!=", 4)->where("age","!=", 5)->get();
		}
		else
		{	
			$gameEntries = EarlyNumeracyGame::where("age", $sign, $age)->get();
		}
		
		echo "No. games found: ";
		echo count($gameEntries);
		echo "<br>";
		
		foreach ($gameEntries as $entry) {
		
			//Get the game ID
			$gameID = $entry->id;
		
			echo "<br>Game ID: ";
			echo $gameID;

			//Fetch the game scores for the game ID
			$gameScores = EarlyNumeracyScore::where("game_id", "=", $gameID)->get();
			$scoresArray = $gameScores->toArray();
			
			echo "<br>No. of results in game: ";
			echo count($scoresArray);
			echo "<br>";
		
			// The old game scores always have 85 recorded
			if (count($scoresArray) >= 85)
			{
				$old_game_score = $entry["score"];
				$score_count = 0;
			
				// Retrieve the game scores in order of item ordering
				foreach ($gameScores as $key => $val) {
	  
					$a_item = explode(".", $val["item"]);
					$a_key  = trim($a_item[count($a_item) - 1]);
		
					$b_val = explode(".", $val["value"]);
					$b_key = trim($b_val[count($b_val) -1]);
							
					$index = $scoreValues[$a_key];
			
					// $a_key - item id with a corresponding placement 'index'
					// $b_key - score for item (either 0 or 1)				
					$scores[$index] = $b_key;
				}
	
				// Item 1
				$start = 0;
				$item_start = $start;
							
				if ($age != 3)
				{
				
					// Start rules only apply for ages 4+
					$check_count = 5;
					if ($age == 4)
					{
						// Item 12
						$start = 11;
			
						// Age 4 first 5 items, contains two part
						$check_count = 6;
					}
					else if ($age == 5)
					{
						// Item 21 begins at index 22
						$start = 22;
						$check_count = 6;
					}
		
					// Check the initial 5 items
					$incorrect = 0;
					$stp_rule = 0;
					$ident_score = 0;
				
					$item_start = $start + $check_count;
				
					for ($i = $start; ($i < $start + $check_count) && ($stp_rule != 1) ; $i++)
					{
						echo "<br> id: ";
						$id = $gameIDValues[$i];
						echo $id;
						echo " with score: ";
			
						$score = $scores[$i];
						echo $score;
					
						if ($score == 1)
						{
							$score_count++;
						}
					
						$two_part = str_contains($id, 'IdentNum') || str_contains($id, 'MatchNum');
						if ($two_part)
						{
							if (str_contains($id, 'IdentNum'))
							{
								// Remember the score for Part A
								$ident_score = $score;
							}
							else
							{
								if (($ident_score == 0) || ($score == 0))
								{
									// Mark this item as incorrect
									$incorrect++;
								}
							}
						}
						else
						{
							if ($score == 0)
							{
								$incorrect++;
							}
						}
					
						if ($incorrect >= 3)
						{
							$stp_rule = 1;
							$item_start = $i + 1;
						}
					}

					echo "<br> No. of Incorrects: ";
					echo $incorrect;
					
					// Check for consecutive incorrect answers
					if ($incorrect >= 3)
					{
						// Loop back to item 1 and check for 5 consecutive wrong
						echo "<br>*** Loop back to Item 1*** <br>";
			
						$start = 0;	     	
					}
					else
					{
						// Mark all preceeding as correct
						echo "<br>*** Marking all preceeding as correct **<br>";
						for ($i = 0; $i < $start; $i++)
						{
							echo "<br> id: ";
							$id = $gameIDValues[$i];
							echo $id;
							echo " with score: ";
			
							$score = $scores[$i];
							echo $score;
						
							// All marked as correct
							$score_count++;
						
							if ($score != 1)
							{
								echo " -> new score: 1";
				
								// Mark as correct in database
								// "2017-10-18 11:00:40"
								$today = date('Y-m-d H:i:s');
							
								// Update the value and last modified date
								EarlyNumeracyScore::where("game_id", "=", $gameID)->where("item","=", $id)->update(['value'=> 1, 'updated_at'=> $today, 'response'=> ""]);
							
								echo " ----- ";
								$newScore = EarlyNumeracyScore::where("game_id", "=", $gameID)->where("item","=", $id)->get();
								echo $newScore;
								echo "<br>";
							}
						
						}
					}
		
					echo "<br>";
				}
				
				if ($item_start != 0)
				{
					echo "<br>*** Continue checking scores **<br>";
				}
				
				$incorrect = 0;
				$stp_rule = 0;
				$stop_index = $item_start;
		
				for ($i = $item_start; ($i < count($scores)) && ($stp_rule != 1); $i++)
				{
					echo "<br> id: ";
					$id = $gameIDValues[$i];
					echo $id;
					echo " with score: ";
		
					$score = $scores[$i];
					echo $score;
			
					if ($score == 1)
					{
						$score_count++;
					}
			
					$two_part = str_contains($id, 'IdentNum') || str_contains($id, 'MatchNum');
					if ($two_part)
					{
						if (str_contains($id, 'IdentNum'))
						{
							// Remember the score for Part A
							$ident_score = $score;
						}
						else
						{
							if (($ident_score == 0) || ($score == 0))
							{
								// Mark this item as incorrect
								$incorrect++;
							}
						}
					}
					else
					{
						if ($score == 0)
						{
							$incorrect++;
						}

					}
								
					if ($incorrect >=5)
					{
						$stp_rule = 1;
						$stop_index = $i;
					}
			
					if ($two_part != 1)
					{
						if ($score != 0)
						{
							if ($i > 0)
							{
								// Rest incorrect count - as we are looking for in a row
								$incorrect = 0;
							}
						}	
					}
					else
					{
						// We do not want to reset the incorrect count until all parts are done	
						if (($ident_score == 1) || ($score == 1))
						{
							
							if ($i > 0)
							{
								// Rest incorrect count - as we are looking for in a row
								$incorrect = 0;
							}
						}
					}	
				}

				if ($stp_rule == 1)
				{
					// Next successive item 
					$stop_index++;
				
					echo "<br>";
					echo $incorrect;
					echo " wrong in a row<br>STOP RULE APPLIED...<br>";
		
					// We want to delete the rest
					echo "*** Mark rest of items as Not Attempted***<br>";
					
					for ($i = $stop_index; $i < count($scores); $i++)
					{
						echo "<br> id: ";
						$id = $gameIDValues[$i];
						echo $id;
						echo " with score: ";
						$score = $scores[$i];
						echo $score;
						
						// Delete score in database
						EarlyNumeracyScore::where("game_id", "=", $gameID)->where("item","=", $id)->delete();
												
						echo " ----> DELETED!";
													
					}		
					echo "<br><br>";
				}
			
				echo "<br>Old score: ";
				echo $old_game_score;
				echo "  --> changed score: ";
				echo $score_count;
			
				EarlyNumeracyGame::where("id","=", $gameID)->update(['score'=> $score_count]);
			
				$updated_entry = EarlyNumeracyGame::where("id","=", $gameID)->get();
				echo "<br> SCORE UPDATED ---- ";
				echo $updated_entry;
				echo "<br>";	
			}
		}
		
		echo "<br>Completed!";
    }
    
}