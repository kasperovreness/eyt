<?php

use Illuminate\Database\Eloquent\Model;

/**
 * GameChange
 *
 * @property integer $id 
 * @property integer $user_id
 * @property integer $game_id
 * @property string $type
 * @property string $old 
 * @property string $new 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @method static \Illuminate\Database\Query\Builder|\GameChange whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\GameChange whereGameId($value)
 * @method static \Illuminate\Database\Query\Builder|\GameChange whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\GameChange whereOld($value)
 * @method static \Illuminate\Database\Query\Builder|\GameChange whereNew($value)
 * @method static \Illuminate\Database\Query\Builder|\GameChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\GameChange whereUpdatedAt($value)
 */
class GameChange extends Model {

	protected $fillable = [];
    protected $table = "game_changes";
    
    public function user() {
        return $this->hasOne("User", "id", "user_id");
    }

}