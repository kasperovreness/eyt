<?php

class MrAntController extends BaseController
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
            $game = MrAntGame::create(["subject_id"    => $gameData["subject_id"],
                                       "session_id"    => $gameData["session"],
                                       "test_name"     => (empty($gameData["studyName"])) ? "Untitled Test" : $gameData["studyName"],
                                       "grade"         => $gameData["grade"],
                                       "dob"           => $gameData["birthdate"],
                                       "age"           => $gameData["age"],
                                       "sex"           => $gameData["sex"],
                                       "played_at"     => $gameData["date"] . ":00",
                                       "score"         => (!empty($gameData["score"])) ? $gameData["score"] : "",
                                       "ts_start"      => (empty($gameData["timestamps"]["Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Start"])),
                                       "ts_lvl1_start" => (empty($gameData["timestamps"]["Level 1 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 Start"])),
                                       "ts_lvl1_end"   => (empty($gameData["timestamps"]["Level 1 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 End"])),
                                       "ts_lvl2_start" => (empty($gameData["timestamps"]["Level 2 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 Start"])),
                                       "ts_lvl2_end"   => (empty($gameData["timestamps"]["Level 2 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 End"])),
                                       "ts_lvl3_start" => (empty($gameData["timestamps"]["Level 3 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 Start"])),
                                       "ts_lvl3_end"   => (empty($gameData["timestamps"]["Level 3 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 End"])),
                                       "ts_lvl4_start" => (empty($gameData["timestamps"]["Level 4 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 4 Start"])),
                                       "ts_lvl4_end"   => (empty($gameData["timestamps"]["Level 4 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 4 End"])),
                                       "ts_lvl5_start" => (empty($gameData["timestamps"]["Level 5 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 5 Start"])),
                                       "ts_lvl5_end"   => (empty($gameData["timestamps"]["Level 5 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 5 End"])),
                                       "ts_lvl6_start" => (empty($gameData["timestamps"]["Level 6 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 6 Start"])),
                                       "ts_lvl6_end"   => (empty($gameData["timestamps"]["Level 6 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 6 End"])),
                                       "ts_lvl7_start" => (empty($gameData["timestamps"]["Level 7 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 7 Start"])),
                                       "ts_lvl7_end"   => (empty($gameData["timestamps"]["Level 7 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 7 End"])),
                                       "ts_lvl8_start" => (empty($gameData["timestamps"]["Level 8 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 8 Start"])),
                                       "ts_lvl8_end"   => (empty($gameData["timestamps"]["Level 8 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 8 End"]))]);

            foreach ($gameData["tries"] as $score) {
                MrAntScore::create(["game_id"      => $game->id,
                                    "level"        => $score["setNumber"],
                                    "part"         => $score["repNumber"],
                                    "value"        => $score["correct"],
                                    "responseTime" => $score["responseTime"]]);
            }

            $results[] = $game->id;
        }

        return $results;
    }

    public function saveAnswers()
    {
        if (!Input::has("games")) {
            return ["error" => "No Game Data specified"];
        }

        // Log game data

        $games = Input::get("games");

        foreach ($games as $gameData) {
            $game = MrAntGame::create(["subject_id"    => $gameData["subject_id"],
                                       "session_id"    => $gameData["session"],
                                       "test_name"     => $gameData["studyName"],
                                       "grade"         => $gameData["grade"],
                                       "dob"           => $gameData["birthdate"],
                                       "age"           => $gameData["age"],
                                       "sex"           => $gameData["sex"],
                                       "played_at"     => $gameData["date"] . ":00",
                                       "score"         => (!empty($gameData["score"])) ? $gameData["score"] : "",
                                       "ts_start"      => (empty($gameData["timestamps"]["Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Start"])),
                                       "ts_lvl1_start" => (empty($gameData["timestamps"]["Level 1 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 Start"])),
                                       "ts_lvl1_end"   => (empty($gameData["timestamps"]["Level 1 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 1 End"])),
                                       "ts_lvl2_start" => (empty($gameData["timestamps"]["Level 2 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 Start"])),
                                       "ts_lvl2_end"   => (empty($gameData["timestamps"]["Level 2 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 2 End"])),
                                       "ts_lvl3_start" => (empty($gameData["timestamps"]["Level 3 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 Start"])),
                                       "ts_lvl3_end"   => (empty($gameData["timestamps"]["Level 3 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 3 End"])),
                                       "ts_lvl4_start" => (empty($gameData["timestamps"]["Level 4 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 4 Start"])),
                                       "ts_lvl4_end"   => (empty($gameData["timestamps"]["Level 4 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 4 End"])),
                                       "ts_lvl5_start" => (empty($gameData["timestamps"]["Level 5 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 5 Start"])),
                                       "ts_lvl5_end"   => (empty($gameData["timestamps"]["Level 5 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 5 End"])),
                                       "ts_lvl6_start" => (empty($gameData["timestamps"]["Level 6 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 6 Start"])),
                                       "ts_lvl6_end"   => (empty($gameData["timestamps"]["Level 6 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 6 End"])),
                                       "ts_lvl7_start" => (empty($gameData["timestamps"]["Level 7 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 7 Start"])),
                                       "ts_lvl7_end"   => (empty($gameData["timestamps"]["Level 7 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 7 End"])),
                                       "ts_lvl8_start" => (empty($gameData["timestamps"]["Level 8 Start"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 8 Start"])),
                                       "ts_lvl8_end"   => (empty($gameData["timestamps"]["Level 8 End"])) ? null : date("Y-m-d H:i:s", strtotime($gameData["timestamps"]["Level 8 End"]))]);

            foreach ($gameData["tries"] as $score) {
                MrAntScore::create(["game_id"      => $game->id,
                                    "level"        => $score["setNumber"],
                                    "part"         => $score["repNumber"],
                                    "value"        => $score["correct"],
                                    "responseTime" => $score["responseTime"]]);
            }
        }

        return ["success"];
    }

    public function showResults($test_name = null, $start = null, $end = null)
    {
        $gameRep   = new Games(new MrAntGame());
        $games     = $gameRep->getGames($test_name, $start, $end);
        $tests     = App::make('perms');
        $testNames = [];

        foreach ($tests as $test) {
            $key = str_replace("+", "%20", urlencode($test->test_name));
            if (!isset($testNames[$key])) {
                $testNames[$key] = $test;
            }
        }

        return View::make("mrant/results", ["games"     => $games,
                                            "test_name" => $test_name,
                                            "start"     => (!empty($start)) ? DateTime::createFromFormat("Y-m-d", $start)->format("d/m/Y") : null,
                                            "end"       => (!empty($end)) ? DateTime::createFromFormat("Y-m-d", $end)->format("d/m/Y") : null,
                                            "tests"     => $testNames]);
    }

    public function viewScores($game_id)
    {
        $scores = MrAntScore::where("game_id", "=", $game_id)->orderBy("level", "ASC")->orderBy("part", "ASC")->get();

        return View::make("mrant/scores", ["scores" => $scores]);
    }

    public function makeCSV($test_name = null, $start = null, $end = null, $returnFile = false)
    {
        $gameRep  = new Games(new MrAntGame());
        $games    = $gameRep->getGames($test_name, $start, $end);
        $filename = "mrant_" . date("U") . ".csv";

        $fp         = fopen(public_path() . "/tmp/" . $filename, 'w');
        $gamesCount = [];

        for ($x = 1; $x < 9; $x++) {
            for ($y = 1; $y < 4; $y++) {
                $gamesCount[] = "Level" . $x . "_" . $y . "_Acc";
            }
        }

        for ($x = 1; $x < 9; $x++) {
            for ($y = 1; $y < 4; $y++) {
                $gamesCount[] = "Level" . $x . "_" . $y . "_RT";
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
                                  "TS_Lvl4_Start",
                                  "TS_Lvl4_End",
                                  "TS_Lvl5_Start",
                                  "TS_Lvl5_End",
                                  "TS_Lvl6_Start",
                                  "TS_Lvl6_End",
                                  "TS_Lvl7_Start",
                                  "TS_Lvl7_End",
                                  "TS_Lvl8_Start",
                                  "TS_Lvl8_End",
                                  "MrAnt_Pt",
                                  "MrAnt_k",
                                  "MrAnt_Acc",
                                  "Lvl1_PtCalc",
                                  "Lvl2_PtCalc",
                                  "Lvl3_PtCalc",
                                  "Lvl4_PtCalc",
                                  "Lvl5_PtCalc",
                                  "Lvl6_PtCalc",
                                  "Lvl7_PtCalc",
                                  "Lvl8_PtCalc",
                                  "Lvl1_Acc",
                                  "Lvl2_Acc",
                                  "Lvl3_Acc",
                                  "Lvl4_Acc",
                                  "Lvl5_Acc",
                                  "Lvl6_Acc",
                                  "Lvl7_Acc",
                                  "Lvl8_Acc",], $gamesCount));

        $i = 2;
        foreach ($games as $game) {
            $scores     = [];
            $gameScores = [];

            foreach ($game->scores as $gameScore) {
                if (empty($gameScores[$gameScore->level])) {
                    $gameScores[$gameScore->level] = [];
                }

                $gameScores[$gameScore->level][$gameScore->part] = $gameScore;
            }

            for ($x = 1; $x < 9; $x++) {
                for ($y = 1; $y < 4; $y++) {
                    $score    = (isset($gameScores[$x][$y])) ? $gameScores[$x][$y] : null;
                    $scores[] = ($score != null && isset($score->value) && $score->responseTime != "0") ? $score->value : ".";
                }
            }

            for ($x = 1; $x < 9; $x++) {
                for ($y = 1; $y < 4; $y++) {
                    $score    = (isset($gameScores[$x][$y])) ? $gameScores[$x][$y] : null;
                    $scores[] = ($score != null && isset($score->responseTime) && $score->responseTime != "0") ? $score->responseTime : ".";
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
                                      (empty($game->ts_lvl4_start)) ? "." : $game->ts_lvl4_start,
                                      (empty($game->ts_lvl4_end)) ? "." : $game->ts_lvl4_end,
                                      (empty($game->ts_lvl5_start)) ? "." : $game->ts_lvl5_start,
                                      (empty($game->ts_lvl5_end)) ? "." : $game->ts_lvl5_end,
                                      (empty($game->ts_lvl6_start)) ? "." : $game->ts_lvl6_start,
                                      (empty($game->ts_lvl6_end)) ? "." : $game->ts_lvl6_end,
                                      (empty($game->ts_lvl7_start)) ? "." : $game->ts_lvl7_start,
                                      (empty($game->ts_lvl7_end)) ? "." : $game->ts_lvl7_end,
                                      (empty($game->ts_lvl8_start)) ? "." : $game->ts_lvl8_start,
                                      (empty($game->ts_lvl8_end)) ? "." : $game->ts_lvl8_end,
                                      "=SUM(AD$i:AK$i)",
                                      "=(SUM(AL$i:AS$i))/3",
                                      "=SUM(AL$i:AS$i)",
                                      "=IF(AL$i>1,1,(AL$i*(1/3)))",
                                      "=IF(AM$i>1,1,(AM$i*(1/3)))",
                                      "=IF(AN$i>1,1,(AN$i*(1/3)))",
                                      "=IF(AO$i>1,1,(AO$i*(1/3)))",
                                      "=IF(AP$i>1,1,(AP$i*(1/3)))",
                                      "=IF(AQ$i>1,1,(AQ$i*(1/3)))",
                                      "=IF(AR$i>1,1,(AR$i*(1/3)))",
                                      "=IF(AS$i>1,1,(AS$i*(1/3)))",
                                      "=SUM(AT$i:AV$i)",
                                      "=SUM(AW$i:AY$i)",
                                      "=SUM(AZ$i:BB$i)",
                                      "=SUM(BC$i:BE$i)",
                                      "=SUM(BF$i:BH$i)",
                                      "=SUM(BI$i:BK$i)",
                                      "=SUM(BL$i:BN$i)",
                                      "=SUM(BO$i:BQ$i)",], $scores));

            $i++;
        }

        fclose($fp);

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
            MrAntScore::where("game_id", "=", $game_id)->delete();
            MrAntGame::where("id", "=", $game_id)->delete();

            return ["success" => true];
        } else {
            return ["success" => false];
        }
    }

    public function fixDuplicates()
    {
        ini_set('max_execution_time', 300); // 5 minutes

        $games   = MrAntGame::all();
        $deleted = [];

        foreach ($games as $game) {
            if (in_array($game->id, $deleted)) {
                continue;
            }

            $duplicates = MrAntGame::where("id", "!=", $game->id)->where("subject_id", "=", $game->subject_id)->where("session_id", "=", $game->session_id)->where("test_name", "=", $game->test_name)->where("played_at", "=", $game->played_at)->get();

            foreach ($duplicates as $duplicate) {
                MrAntScore::where("game_id", "=", $duplicate->id)->delete();
                MrAntGame::where("id", "=", $duplicate->id)->delete();

                $deleted[] = $duplicate->id;
            }
        }

        echo "Removed " . count($deleted) . " duplicates";
    }

    public function deleteGames()
    {
        $games   = Input::get("game_ids");
        $gameRep = new Games(new MrAntGame());

        return $gameRep->deleteGames(new MrAntScore(), $games);
    }
}