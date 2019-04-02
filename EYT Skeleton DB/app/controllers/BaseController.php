<?php

class BaseController extends Controller
{

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    public function viewGameData($type, $game_id)
    {
        $model    = $this->getGameModel($type);
        $changes  = GameChange::whereGameId($game_id)->whereType($type)->with("user")->get();

		if ($type == "ecers") {
			$viewFile = "ecers_edit";
		} else if ($type == "prsistassessment") {
			$viewFile = "prsistassessment.prsistassessment_edit";
		} else {
			$viewFile = "game_edit";
		}

        return View::make($viewFile, ["type"    => $type,
                                      "changes" => $changes,
                                      "game"    => $model->whereId($game_id)->first()]);
    }

    public function updateGameData()
    {
        $type     = Input::get("type");
        $game_id  = Input::get("game_id");
        $oldModel = $this->getGameModel($type)->whereId($game_id)->first();
        $newModel = $this->getGameModel($type)->whereId($game_id)->first();
           
        $viewData = ["old_data" => $oldModel];

        if ($type == "ecers") {
            $newModel->centre   = (Input::has("centre")) ? Input::get("centre") : $newModel->centre;
            $newModel->room     = (Input::has("room")) ? Input::get("room") : $newModel->room;
            $newModel->observer = (Input::has("observer")) ? Input::get("observer") : $newModel->observer;
            $newModel->study    = (Input::has("study")) ? Input::get("study") : $newModel->study;
            $newModel->start    = (Input::has("start")) ? Input::get("start") : $newModel->start;
            $newModel->end      = (Input::has("end")) ? Input::get("end") : $newModel->end;
            $newModel->save();
        } else if ($type == "prsistassessment") {
            $newModel->child_id = (Input::has("child_id")) ? Input::get("child_id") : $newModel->child_id;
            $newModel->session_id = (Input::has("session_id")) ? Input::get("session_id") : $newModel->session_id;
            $newModel->test_name  = (Input::has("study")) ? Input::get("study") : $newModel->test_name;
            $newModel->room     = (Input::has("room")) ? Input::get("room") : $newModel->room;
            $newModel->dob        = (Input::has("dob")) ? Input::get("dob") : null; // If no dob has been entered, clear
            $newModel->age        = (Input::has("age")) ? Input::get("age") : $newModel->age;
            $newModel->sex        = (Input::has("sex")) ? Input::get("sex") : $newModel->sex;
            $newModel->assessor_name = (Input::has("assessor")) ? Input::get("assessor") : null;
            $newModel->save();
        } else {
            $newModel->subject_id = (Input::has("child_id")) ? Input::get("child_id") : $newModel->subject_id;
            $newModel->session_id = (Input::has("session_id")) ? Input::get("session_id") : $newModel->session_id;
            $newModel->test_name  = (Input::has("study")) ? Input::get("study") : $newModel->test_name;
            $newModel->grade      = (Input::has("grade")) ? Input::get("grade") : $newModel->grade;
            
            $newModel->dob        = (Input::has("dob")) ? Input::get("dob") : null; // If no dob has been entered, clear
            $newModel->age        = (Input::has("age")) ? Input::get("age") : $newModel->age;
            $newModel->sex        = (Input::has("sex")) ? Input::get("sex") : $newModel->sex;
            $newModel->save();
        }

        $viewData["new_data"] = $newModel;

        // add old data and new data to database
        $oldData = json_encode($oldModel);
        $newData = json_encode($newModel);

        $gameChange          = new GameChange();
        $gameChange->user_id = Session::get("user_id");
        $gameChange->game_id = $game_id;
        $gameChange->type    = $type;
        $gameChange->old     = $oldData;
        $gameChange->new     = $newData;
        $gameChange->save();


        return View::make("alert", ["type" => "success",
                                    "msg"  => "Successfully updated game data"]);
    }

    private function getGameModel($type)
    {
        if ($type == "vocab") {
            return new VocabGameNew();
        } else if ($type == "cardsort") {
            return new CardSortGame();
        } else if ($type == "mrant") {
            return new MrAntGame();
        } else if ($type == "fishshark") {
            return new FishSharkGame();
        } else if ($type == "notthis") {
            return new NotThisGame();
        } else if ($type == "questionnaire") {
            return new Questionnaire();
        } else if ($type == "early_numeracy") {
            return new EarlyNumeracyGame();
        } else if ($type == "prsistassessment") {
        	return new PrsistAssessmentGame();
        }
    }
}
