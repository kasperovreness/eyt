<?php 

use Illuminate\Database\Eloquent\Model;

class PrsistAssessmentGame extends Model {

	protected $table = "prsistassessment_games";

	protected $fillable = [];
	
	public function scores()
    {
        return $this->hasMany("PrsistAssessmentScore", "game_id", "id");
    }

}