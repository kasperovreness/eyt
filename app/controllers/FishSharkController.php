<?php

class FishSharkController extends BaseController
{
    /**
     * TEMP CODE
     *
     * Used for importing data from another database
     *
     * @return array
     */
    public function importGames()
    {
        $results = [];
        $games   = Input::all();

        foreach ($games as $gameData) {
            $dob = DateTime::createFromFormat("d-m-Y", (Input::has("birthdate")) ? Input::get("birthdate") : $gameData["birthdate"]);

            $game                = new FishSharkGame();
            $game->subject_id    = $gameData["subject_id"];
            $game->session_id    = $gameData["session"];
            $game->test_name     = $gameData["studyName"];
            $game->grade         = $gameData["grade"];
            $game->dob           = (!$dob) ? "" : $dob->format("Y-m-d");
            $game->age           = $gameData["age"];
            $game->sex           = $gameData["sex"];
            $game->played_at     = $gameData["date"] . ":00";
            $game->animation     = $gameData["animation"];
            $game->blank_min     = $gameData["blank_min"];
            $game->blank_max     = $gameData["blank_max"];
            $game->ts_start      = (empty($gameData["timestamps"]["Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Start"]));
            $game->ts_lvl1_start = (empty($gameData["timestamps"]["Level 1 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 Start"]));
            $game->ts_lvl1_end   = (empty($gameData["timestamps"]["Level 1 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 End"]));
            $game->ts_lvl2_start = (empty($gameData["timestamps"]["Level 2 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 Start"]));
            $game->ts_lvl2_end   = (empty($gameData["timestamps"]["Level 2 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 End"]));
            $game->ts_lvl3_start = (empty($gameData["timestamps"]["Level 3 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 Start"]));
            $game->ts_lvl3_end   = (empty($gameData["timestamps"]["Level 3 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 End"]));
            $game->save();

            foreach ($gameData["tries"] as $score) {
                FishSharkScore::create(["game_id"      => $game->id,
                                        "level"        => $score["setNumber"],
                                        "part"         => $score["repNumber"],
                                        "value"        => $score["correct"],
                                        "responseTime" => $score["responseTime"],
                                        "blankTime"    => $score["blankTime"],
                                        "is_shark"     => $score["isShark"]]);
            }

            $results[] = $game->id;
        }

        return $results;
    }

    public function saveGames()
    {
        if (!Input::has("games")) {
            return ["error" => "No Game Data specified"];
        }

        // Log game data

        $games = Input::get("games");

        foreach ($games as $gameData) {
            $dob = DateTime::createFromFormat("d-m-Y", (Input::has("birthdate")) ? Input::get("birthdate") : $gameData["birthdate"]);

            if (!empty($gameData["test_name"]) && empty($gameData["studyName"])) {
                $gameData["studyName"] = $gameData["test_name"];
            }

            $game = FishSharkGame::create(["subject_id"    => $gameData["subject_id"],
                                           "session_id"    => $gameData["session"],
                                           "test_name"     => (empty($gameData["studyName"])) ? "Untitled Test" : $gameData["studyName"],
                                           "grade"         => $gameData["grade"],
                                           "dob"           => (!$dob) ? "" : $dob->format("Y-m-d"),
                                           "age"           => $gameData["age"],
                                           "sex"           => $gameData["sex"],
                                           "played_at"     => $gameData["date"] . ":00",
                                           "animation"     => (empty($gameData["animation"])) ? null : $gameData["animation"],
                                           "blank_min"     => (empty($gameData["blank_min"])) ? null : $gameData["blank_min"],
                                           "blank_max"     => (empty($gameData["blank_max"])) ? null : $gameData["blank_max"],
                                           "ts_start"      => (empty($gameData["timestamps"]["Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Start"])),
                                           "ts_lvl1_start" => (empty($gameData["timestamps"]["Level 1 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 Start"])),
                                           "ts_lvl1_end"   => (empty($gameData["timestamps"]["Level 1 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 End"])),
                                           "ts_lvl2_start" => (empty($gameData["timestamps"]["Level 2 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 Start"])),
                                           "ts_lvl2_end"   => (empty($gameData["timestamps"]["Level 2 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 End"])),
                                           "ts_lvl3_start" => (empty($gameData["timestamps"]["Level 3 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 Start"])),
                                           "ts_lvl3_end"   => (empty($gameData["timestamps"]["Level 3 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 End"]))]);

            foreach ($gameData["tries"] as $score) {
                FishSharkScore::create(["game_id"      => $game->id,
                                        "level"        => $score["setNumber"],
                                        "part"         => $score["repNumber"],
                                        "value"        => $score["correct"],
                                        "responseTime" => $score["responseTime"],
                                        "blankTime"    => (empty($score["blankTime"])) ? 0 : $score["blankTime"],
                                        "is_shark"     => (empty($score["isShark"])) ? 0 : $score["isShark"]]);
            }
        }

        return ["success"];
    }

    public function showResults($test_name = null, $start = null, $end = null)
    {
        $gameRep   = new Games(new FishSharkGame());
        $games     = $gameRep->getGames($test_name, $start, $end);
        $tests     = App::make('perms');
        $testNames = [];

        foreach ($tests as $test) {
            $key = str_replace("+", "%20", urlencode($test->test_name));
            if (!isset($testNames[$key])) {
                $testNames[$key] = $test;
            }
        }

        return View::make("fishshark/results", ["games"     => $games,
                                                "test_name" => $test_name,
                                                "start"     => (!empty($start)) ? DateTime::createFromFormat("Y-m-d", $start)->format("d/m/Y") : null,
                                                "end"       => (!empty($end)) ? DateTime::createFromFormat("Y-m-d", $end)->format("d/m/Y") : null,
                                                "tests"     => $testNames]);
    }

    public function viewScores($game_id)
    {
        $scores = FishSharkScore::where("game_id", "=", $game_id)->orderBy("level", "ASC")->get();

        return View::make("fishshark/scores", ["scores" => $scores]);
    }

    public function makeCSV($test_name = null, $start = null, $end = null, $returnFile = false)
    {
        ini_set('max_execution_time', 300); // 5 minutes

        $gameRep  = new Games(new FishSharkGame());
        $games    = $gameRep->getGames($test_name, $start, $end, "scores");
        $filename = "fishshark_" . date("U") . ".csv";

        $fp       = fopen(public_path() . "/tmp/" . $filename, 'w');
        $accCount = [];
        $rtCount  = [];
        $accClean = [];
        $rtClean  = [];

        if (count($games) > 0) {
            $fishes = [];
            foreach ($games[0]->scores as $score) {
                if ($score->is_shark == 0) {
                    $fishes[] = $score;
                }
            }

            $part = 1;
            foreach ($fishes as $fish) {
                if ($fish->level > 3) {
                    $accCount[] = "GO" . ($fish->level - 3) . "_" . $part . "_Acc";
                    $accClean[] = "GO" . ($fish->level - 3) . "_" . $part . "_Acc_Clean";
                    $part++;
                }
            }

            $sharks = [];
            foreach ($games[0]->scores as $score) {
                if ($score->is_shark == 1) {
                    $sharks[] = $score;
                }
            }
            $part = 1;
            foreach ($sharks as $shark) {
                if ($shark->level > 3) {
                    $accCount[] = "NG" . ($shark->level - 3) . "_" . $part . "_Acc";
                    $accClean[] = "NG" . ($shark->level - 3) . "_" . $part . "_Acc_Clean";
                    $part++;
                }
            }

            $part = 1;
            foreach ($fishes as $fish) {
                if ($fish->level > 3) {
                    $rtCount[] = "GO" . ($fish->level - 3) . "_" . $part . "_RT";
                    $rtClean[] = "GO" . ($fish->level - 3) . "_" . $part . "_RT_Clean";
                    $part++;
                }
            }

            $part = 1;
            foreach ($sharks as $shark) {
                if ($shark->level > 3) {
                    $rtCount[] = "NG" . ($shark->level - 3) . "_" . $part . "_RT";
                    $rtClean[] = "NG" . ($shark->level - 3) . "_" . $part . "_RT_Clean";
                    $part++;
                }
            }

            fputcsv($fp, array_merge(["game_id",
                                      "subject_id",
                                      "session_id",
                                      "study_name",
                                      "grade",
                                      "DOB",
                                      "age",
                                      "sex",
                                      "DOT",
                                      "TS_Start",
                                      "TS_Lvl1_Start",
                                      "TS_Lvl1_End",
                                      "TS_Lvl2_Start",
                                      "TS_Lvl2_End",
                                      "TS_Lvl3_Start",
                                      "TS_Lvl3_End",
                                      "ImpulseControl",
                                      "Go_Acc",
                                      "NG_Acc",
                                      "Go_Blk1_Acc",
                                      "Go_Blk2_Acc",
                                      "Go_Blk3_Acc",
                                      "NG_Blk1_Acc",
                                      "NG_Blk2_Acc",
                                      "NG_Blk3_Acc",
                                      "Go_Blk1_Acc",
                                      "Go_Blk2_Acc",
                                      "Go_Blk3_Acc",
                                      "NG_Blk1_Acc",
                                      "NG_Blk2_Acc",
                                      "NG_Blk3_Acc"], $accClean, $accCount, $rtClean, $rtCount));

            $x = 2;
            foreach ($games as $game) {
                $acc       = [];
                $accClean  = [];
                $resp      = [];
                $respClean = [];

                // Fish Accuracy
                foreach ($game->scores as $score) {
                    $fishScore  = (isset($score->value)) ? $score->value : ".";
                    $fishResp   = (isset($score->responseTime)) ? $score->responseTime : ".";
                    $sharkScore = (isset($score->value)) ? $score->value : ".";
                    $sharkResp  = (isset($score->responseTime)) ? $score->responseTime : ".";

                    // Fish Accuracy
                    if ($score->level > 3 && $score->is_shark == 0) {
                        $acc[] = $fishScore;
                        $accClean[] = ($fishResp < 0.3) ? "." : $fishScore;
                    }
                    // Shark Accuracy
                    if ($score->level > 3 && $score->is_shark == 1) {
                        $acc[] = $sharkScore;
                        $accClean[] = ($sharkResp < 0.3) ? "." : $sharkScore;
                    }
                    // Fish Response
                    if ($score->level > 3 && $score->is_shark == 0) {
                        $resp[] = $fishResp;
                        $respClean[] = ($fishResp < 0.3) ? "." : $fishResp;
                    }
                    // Shark Response
                    if ($score->level > 3 && $score->is_shark == 1) {
                        $resp[] = $sharkResp;
                        $respClean[] = ($sharkResp < 0.3) ? "." : $sharkResp;
                    }
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
                                          (empty($game->ts_start)) ? "." : $game->ts_start,
                                          (empty($game->ts_lvl1_start)) ? "." : $game->ts_lvl1_start,
                                          (empty($game->ts_lvl1_end)) ? "." : $game->ts_lvl1_end,
                                          (empty($game->ts_lvl2_start)) ? "." : $game->ts_lvl2_start,
                                          (empty($game->ts_lvl2_end)) ? "." : $game->ts_lvl2_end,
                                          (empty($game->ts_lvl3_start)) ? "." : $game->ts_lvl3_start,
                                          (empty($game->ts_lvl3_end)) ? "." : $game->ts_lvl3_end,
                                          "=R$x*S$x",
                                          "=AVERAGE(T$x:V$x)",
                                          "=AVERAGE(W$x:Y$x)",
                                          "=IF(OR(AND(Z$x>0.8,AC$x<0.2),AND(Z$x<0.2,AC$x>0.8))," . ",Z$x)",
                                          "=IF(OR(AND(AA$x>0.8,AD$x<0.2),AND(AA$x<0.2,AD$x>0.8))," . ",AA$x)",
                                          "=IF(OR(AND(AB$x>0.8,AE$x<0.2),AND(AB$x<0.2,AE$x>0.8))," . ",AB$x)",
                                          "=IF(OR(AND(Z$x>0.8,AC$x<0.2),AND(Z$x<0.2,AC$x>0.8))," . ",AC$x)",
                                          "=IF(OR(AND(AA$x>0.8,AD$x<0.2),AND(AA$x<0.2,AD$x>0.8))," . ",AD$x)",
                                          "=IF(OR(AND(AB$x>0.8,AE$x<0.2),AND(AB$x<0.2,AE$x>0.8))," . ",AE$x)",
                                          "=AVERAGE(AF$x:AY$x)",
                                          "=AVERAGE(AZ$x:BS$x)",
                                          "=AVERAGE(BT$x:CM$x)",
                                          "=AVERAGE(CN$x:CR$x)",
                                          "=AVERAGE(CS$x:CW$x)",
                                          "=AVERAGE(CX$x:DB$x)"], $accClean, $acc, $respClean, $resp));

                $x++;
            }

            fclose($fp);
        }

        if ($returnFile == true) {
            return $filename;
        } else {
            return View::make("csv", ["filename" => $filename]);
        }
    }

    public function deleteGame($game_id)
    {
        $user = App::make("user");

        if ($user->delete == 1) {
            FishSharkScore::where("game_id", "=", $game_id)->delete();
            FishSharkGame::where("id", "=", $game_id)->delete();

            return ["success" => true];
        } else {
            return ["success" => false];
        }
    }

    public function fixDuplicates()
    {
        ini_set('max_execution_time', 300); // 5 minutes

        $games   = FishSharkGame::all();
        $deleted = [];

        foreach ($games as $game) {
            if (in_array($game->id, $deleted)) {
                continue;
            }

            $duplicates = FishSharkGame::where("id", "!=", $game->id)->where("subject_id", "=", $game->subject_id)->where("session_id", "=", $game->session_id)->where("test_name", "=", $game->test_name)->where("played_at", "=", $game->played_at)->get();

            foreach ($duplicates as $duplicate) {
                FishSharkScore::where("game_id", "=", $duplicate->id)->delete();
                FishSharkGame::where("id", "=", $duplicate->id)->delete();

                $deleted[] = $duplicate->id;
            }
        }

        echo "Removed " . count($deleted) . " duplicates";
    }

    public function deleteGames()
    {
        $games   = Input::get("game_ids");
        $gameRep = new Games(new FishSharkGame());

        return $gameRep->deleteGames(new FishSharkScore(), $games);
    }
}