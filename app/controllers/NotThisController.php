<?php

class NotThisController extends BaseController
{
    public function saveGames()
    {
        if (!Input::has("games")) {
            return ["error" => "No Game Data specified"];
        }

        //return Input::get("games");

        // Log game data

        $games = Input::get("games");

        foreach ($games as $gameData) {
            $game             = new NotThisGame();
            $game->subject_id = $gameData["subject_id"];
            $game->session_id = $gameData["session"];
            $game->grade      = $gameData["grade"];
            $game->sex        = $gameData["sex"];
            $game->test_name  = (empty($gameData["test_name"])) ? "Untitled Test" : $gameData["test_name"];
            $game->played_at  = \DateTime::createFromFormat("Y-m-d H:i", $gameData["date"]);
            $game->age        = (empty($gameData["age"])) ? 0 : $gameData["age"];
            $game->dob        = $gameData["birthdate"];//(empty($gameData["birthdate"])) ? null : \DateTime::createFromFormat("d/m/Y", $gameData["birthdate"]);
            $game->score      = 0;
            $game->save();

            foreach ($gameData["tries"] as $score) {
                $gameScore               = new NotThisScore();
                $gameScore->game_id      = $game->id;
                $gameScore->set          = $score["setNumber"];
                $gameScore->rep          = $score["repNumber"];
                $gameScore->correct      = $score["correct"];
                $gameScore->responseTime = $score["responseTime"];
                $gameScore->attempted    = 1; //$score["attempted"];
                $gameScore->save();

                if ($score["correct"] == 1) {
                    $game->score++;
                }
            }

            $game->save();
        }

        return ["success"];
    }

    public function showResults($test_name = null, $start = null, $end = null)
    {
        $gameRep   = new Games(new NotThisGame());
        $games     = $gameRep->getGames($test_name, $start, $end);
        $tests     = App::make('perms');
        $testNames = [];

        foreach ($tests as $test) {
            $key = str_replace("+", "%20", urlencode($test->test_name));
            if (!isset($testNames[$key])) {
                $testNames[$key] = $test;
            }
        }

        return View::make("notthis/results", ["games"     => $games,
                                              "test_name" => $test_name,
                                              "start"     => (!empty($start)) ? DateTime::createFromFormat("Y-m-d", $start)->format("d/m/Y") : null,
                                              "end"       => (!empty($end)) ? DateTime::createFromFormat("Y-m-d", $end)->format("d/m/Y") : null,
                                              "tests"     => $testNames]);
    }

    public function viewScores($game_id)
    {
        $scores = NotThisScore::where("game_id", "=", $game_id)->orderBy("set", "ASC")->orderBy("rep", "ASC")->get();

        return View::make("notthis/scores", ["scores" => $scores]);
    }

    public function makeCSV($test_name = null, $start = null, $end = null, $returnFile = false)
    {
        $gameRep  = new Games(new NotThisGame());
        $games    = $gameRep->getGames($test_name, $start, $end);
        $filename = "notthis_" . date("U") . ".csv";

        $fp    = fopen(public_path() . "/tmp/" . $filename, 'w');
        $cards = [];

        // Loop through each set
        for ($x = 1; $x < 9; $x++) {
            // Loop through the 5 reps
            for ($y = 1; $y < 6; $y++) {
                $cards[] = "Level" . $x . "_" . $y . "Acc";
            }
        }

        // Loop through each set
        for ($x = 1; $x < 9; $x++) {
            // Loop through the 5 reps
            for ($y = 1; $y < 6; $y++) {
                $cards[] = "Level" . $x . "_" . $y . "Resp";
            }
        }

        // Response Time = RT
        fputcsv($fp, array_merge(["game_id",
                                  "subject_id",
                                  "session_id",
                                  "study_name",
                                  "grade",
                                  "DOB",
                                  "age",
                                  "sex",
                                  "DOT",
                                  "TOT",
                                  "NotThis_Pt",
                                  "NotThis_k",
                                  "NotThis_Acc",
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
                                  "Lvl8_Acc"], $cards));

        $x = 2;
        foreach ($games as $game) {
            $scores    = NotThisScore::where("game_id", "=", $game->id)->get(); //->orderBy("set", "ASC")->orderBy("rep", "ASC")->get();
            $scoreData = [];

            foreach ($scores as $score) {
                $value = $score->correct;

                // Set 1, Rep 1, Response Time = 0
                if ($score->set == 1 && $score->rep == 1 && $score->responseTime == 0) {
                    // Do Nothing
                    // All other sets/reps where Response Time = 0
                } else if ($score->set > 1 && $score->responseTime == 0) {
                    $value = ".";
                }

                $scoreData[] = $value;
            }

            foreach ($scores as $score) {
                $scoreData[] = ($score->responseTime == 0 || empty($score->responseTime)) ? "." : $score->responseTime;
            }

            $played_at = DateTime::createFromFormat("Y-m-d H:i:s", $game->played_at);

            fputcsv($fp, array_merge([$game->id,
                                      (empty($game->subject_id)) ? "." : $game->subject_id,
                                      (empty($game->session_id)) ? "." : $game->session_id,
                                      (empty($game->test_name)) ? "." : $game->test_name,
                                      (empty($game->grade)) ? "." : $game->grade,
                                      (empty($game->dob)) ? "." : $game->dob,
                                      (empty($game->age)) ? "." : $game->age,
                                      (empty($game->sex)) ? "." : $game->sex,
                                      (empty($game->played_at)) ? "." : $played_at->format("d/m/Y"),
                                      (empty($game->played_at)) ? "." : $played_at->format("H:i"),
                                      "=SUM(N$x:U$x)",
                                      "=(SUM(V$x:AC$x))/5",
                                      "=SUM(V$x:AC$x)",
                                      "=IF(V$x>3, 1, (V$x*(1/5)))",
                                      "=IF(W$x>3, 1, (W$x*(1/5)))",
                                      "=IF(X$x>3, 1, (X$x*(1/5)))",
                                      "=IF(Y$x>3, 1, (Y$x*(1/5)))",
                                      "=IF(Z$x>3, 1, (Z$x*(1/5)))",
                                      "=IF(AA$x>3, 1, (AA$x*(1/5)))",
                                      "=IF(AB$x>3, 1, (AB$x*(1/5)))",
                                      "=IF(AC$x>3, 1, (AC$x*(1/5)))",
                                      "=SUM(AD$x:AH$x)",
                                      "=SUM(AI$x:AM$x)",
                                      "=SUM(AN$x:AR$x)",
                                      "=SUM(AS$x:AW$x)",
                                      "=SUM(AX$x:BB$x)",
                                      "=SUM(BC$x:BG$x)",
                                      "=SUM(BH$x:BL$x)",
                                      "=SUM(BM$x:BQ$x)"], $scoreData));

            $x++;
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
            NotThisScore::where("game_id", "=", $game_id)->delete();
            NotThisGame::where("id", "=", $game_id)->delete();

            return ["success" => true];
        } else {
            return ["success" => false];
        }
    }

    public function fixDuplicates()
    {
        $games = NotThisGame::all();

        // Loop through each game
        foreach ($games as $game) {
            if (empty(NotThisGame::find($game->id)->id)) {
                continue;
            }

            $duplicate = NotThisGame::where("id", "!=", $game->id)->where("subject_id", "=", $game->subject_id)->where("session_id", "=", $game->session_id)->where("test_name", "=", $game->test_name)->where("grade", "=", $game->grade)->where("dob", "=", $game->dob)->where("age", "=", $game->age)->where("sex", "=", $game->sex)->where("played_at", "=", $game->played_at);

            foreach ($duplicate->get() as $gameData) {
                NotThisScore::where("game_id", "=", $gameData->id)->delete();
            }

            $duplicate->delete();
        }

        echo "Done";
    }

    public function deleteGames()
    {
        $games   = Input::get("game_ids");
        $gameRep = new Games(new NotThisGame());

        return $gameRep->deleteGames(new NotThisScore(), $games);
    }
}