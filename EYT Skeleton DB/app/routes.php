<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::post("/update_game", "BaseController@updateGameData");

// Static Pages
Route::get("/support", "HomeController@supportPage");

// Login Routes
Route::get("/login", "UserController@loginPage");
Route::post("/login/submit", "UserController@login");
Route::get("/logout", "UserController@logout");

// Password Reset Routes
Route::get("/passwordreset/request", "UserController@requestPasswordReset");
Route::post("/passwordreset/request/submit", "UserController@submitPasswordResetRequest");
Route::get("/passwordreset/{code}", "UserController@resetPassword");
Route::post("/passwordreset/submit", "UserController@processResetPassword");

// Questionnaire Web Form
Route::get("/questionnaire/form", "QuestionnaireController@showForm");
Route::post("/questionnaire/form/submit", "QuestionnaireController@submitForm");

Route::group(["before" => "auth"], function() {
    // Home Route
    Route::get('/home', "HomeController@homePage");
    Route::get("/csv/{test_name}/{start}/{end}", "HomeController@makeCSV");
    Route::get("/csv/{test_name}", "HomeController@makeCSV");
    Route::get("/csv", "HomeController@makeCSV");
    Route::get("/game_data/{type}/{game_id}", "BaseController@viewGameData");

    // Vocab Routes
    Route::group(["before" => "vocab"], function() {
        /*
        Route::get("/vocab/new", "VocabController@showResultsNew");
        Route::get("/vocab/new/duplicates", "VocabController@fixDuplicatesNew");
        Route::get("/vocab/new/game/{id}", "VocabController@viewScoresNew");
        Route::get("/vocab/new/csv/{test_name}", "VocabController@makeCSVNew");
        Route::get("/vocab/new/csv", "VocabController@makeCSVNew");
        Route::get("/vocab/new/{test_name}", "VocabController@showResultsNew");
        */

        Route::get("/vocab/game/{id}/delete", ["before" => "delete",
                                               "uses"   => "VocabController@deleteGame"]);
        Route::get("/vocab/game/{id}", "VocabController@viewScores");
        Route::get("/vocab/csv/{test_name}/{start}/{end}", "VocabController@makeCSV");
        Route::get("/vocab/csv/{test_name}", "VocabController@makeCSV");
        Route::get("/vocab/csv", "VocabController@makeCSV");
        Route::get("/vocab/duplicates", "VocabController@fixDuplicates");
        Route::post("/vocab/delete", "VocabController@deleteGames");
        Route::get("/vocab/{test_name}/{start}/{end}", "VocabController@showResults");
        Route::get("/vocab/{test_name}", "VocabController@showResults");
        Route::get("/vocab", "VocabController@showResults");
    });


    // CardSort Routes
    Route::group(["before" => "cardsort"], function() {
        Route::get("/cardsort/game/{id}/delete", ["before" => "delete",
                                                  "uses"   => "CardSortController@deleteGame"]);
        Route::get("/cardsort/game/{id}", "CardSortController@viewScores");
        Route::get("/cardsort/csv/{test_name}/{start}/{end}", "CardSortController@makeCSV");
        Route::get("/cardsort/csv/{test_name}", "CardSortController@makeCSV");
        Route::get("/cardsort/csv", "CardSortController@makeCSV");
        Route::get("/cardsort/duplicates", "CardSortController@fixDuplicates");
        Route::post("/cardsort/delete", "CardSortController@deleteGames");
        Route::get("/cardsort/{test_name}", "CardSortController@showResults");
        Route::get("/cardsort/{test_name}/{start}/{end}", "CardSortController@showResults");
        Route::get("/cardsort", "CardSortController@showResults");
    });

    // Questionnaire Routes
    Route::group(["before" => "questionnaire"], function() {
        Route::get("/questionnaire/game/{id}/delete", ["before" => "delete",
                                                       "uses"   => "QuestionnaireController@deleteGame"]);
        Route::get("/questionnaire", "QuestionnaireController@showResults");
        Route::get("/questionnaire/game/{id}", "QuestionnaireController@viewScores");
        Route::get("/questionnaire/duplicates", "QuestionnaireController@fixDuplicates");
        Route::get("/questionnaire/csv/{test_name}", "QuestionnaireController@makeCSV");
        Route::get("/questionnaire/csv/{test_name}/{start}/{end}", "QuestionnaireController@makeCSV");
        Route::get("/questionnaire/csv", "QuestionnaireController@makeCSV");
        Route::post("/questionnaire/delete", "QuestionnaireController@deleteGames");
        Route::get("/questionnaire/{test_name}", "QuestionnaireController@showResults");
        Route::get("/questionnaire/{test_name}/{start}/{end}", "QuestionnaireController@showResults");
    });

    // MrAnt Routes
    Route::group(["before" => "mrant"], function() {
        Route::get("/mrant/game/{id}/delete", ["before" => "delete",
                                               "uses"   => "MrAntController@deleteGame"]);
        Route::get("/mrant/game/{id}", "MrAntController@viewScores");
        Route::get("/mrant/csv/{test_name}/{start}/{end}", "MrAntController@makeCSV");
        Route::get("/mrant/csv/{test_name}", "MrAntController@makeCSV");
        Route::get("/mrant/duplicates", "MrAntController@fixDuplicates");
        Route::get("/mrant/csv", "MrAntController@makeCSV");
        Route::post("/mrant/delete", "MrAntController@deleteGames");
        Route::get("/mrant/{test_name}", "MrAntController@showResults");
        Route::get("/mrant/{test_name}/{start}/{end}", "MrAntController@showResults");
        Route::get("/mrant", "MrAntController@showResults");
    });

    // Fish Shark Routes
    Route::group(["before" => "fishshark"], function() {
        Route::get("/fishshark/game/{id}/delete", ["before" => "delete",
                                                   "uses"   => "FishSharkController@deleteGame"]);
        Route::get("/fishshark/game/{id}", "FishSharkController@viewScores");
        Route::get("/fishshark/csv/{test_name}/{start}/{end}", "FishSharkController@makeCSV");
        Route::get("/fishshark/csv/{test_name}", "FishSharkController@makeCSV");
        Route::get("/fishshark/duplicates", "FishSharkController@fixDuplicates");
        Route::get("/fishshark/csv", "FishSharkController@makeCSV");
        Route::post("/fishshark/delete", "FishSharkController@deleteGames");
        Route::get("/fishshark/{test_name}", "FishSharkController@showResults");
        Route::get("/fishshark/{test_name}/{start}/{end}", "FishSharkController@showResults");
        Route::get("/fishshark", "FishSharkController@showResults");
    });

    // NotThis Routes
    Route::group(["before" => "notthis"], function() {
        Route::get("/notthis/game/{id}/delete", ["before" => "delete",
                                                 "uses"   => "NotThisController@deleteGame"]);
        Route::get("/notthis/game/{id}", "NotThisController@viewScores");
        Route::get("/notthis/csv/{test_name}/{start}/{end}", "NotThisController@makeCSV");
        Route::get("/notthis/csv/{test_name}", "NotThisController@makeCSV");
        Route::get("/notthis/duplicates", "NotThisController@fixDuplicates");
        Route::get("/notthis/csv", "NotThisController@makeCSV");
        Route::post("/notthis/delete", "NotThisController@deleteGames");
        Route::get("/notthis/{test_name}", "NotThisController@showResults");
        Route::get("/notthis/{test_name}/{start}/{end}", "NotThisController@showResults");
        Route::get("/notthis", "NotThisController@showResults");
    });


    // early_numeracy Routes
    Route::group(array("before" => "early_numeracy"), function() {
    	//Route::get("/early_numeracy/game/{entry_id}/delete", "EarlyNumeracyController@deleteGame");
        Route::get("/early_numeracy/game/{entry_id}/delete", ["before" => "delete",
                                                 "uses"   => "EarlyNumeracyController@deleteGame"]);
		Route::get("/early_numeracy/entry/{entry_id}", "EarlyNumeracyController@viewEntry");
		Route::get("/early_numeracy/game/{entry_id}", "EarlyNumeracyController@viewScores");
		Route::get("/early_numeracy/csv/{test_name}", "EarlyNumeracyController@makeCSV");
		Route::get("/early_numeracy/csv/{test_name}/{start}/{end}", "EarlyNumeracyController@makeCSV");
		Route::get("/early_numeracy/csv", "EarlyNumeracyController@makeCSV");
		Route::get("/early_numeracy/duplicates", "EarlyNumeracyController@fixDuplicates");
		Route::get("/early_numeracy/applystartstoprule", "EarlyNumeracyController@applyStartStopRule");
		Route::get("/early_numeracy/conformstartstoprule/{age}",
		"EarlyNumeracyController@conformStartStopRule");
	
		Route::post("/early_numeracy/delete", "EarlyNumeracyController@deleteGames");
		Route::get("/early_numeracy/{test_name}", "EarlyNumeracyController@showResults");
		Route::get("/early_numeracy/{test_name}/{start}/{end}", "EarlyNumeracyController@showResults");
		Route::get("/early_numeracy", "EarlyNumeracyController@showResults");
    });
    
    //Prsist Assessment Routes
    Route::group(array("before" => "prsistassessment"), function() {
        Route::get("/prsistassessment/game/{entry_id}/delete", ["before" => "delete",
                                                 "uses"   => "PrsistAssessmentController@deleteGame"]);
		Route::get("/prsistassessment/entry/{entry_id}", "PrsistAssessmentController@viewEntry");
		Route::get("/prsistassessment/game/{entry_id}", "PrsistAssessmentController@viewScores");
		Route::get("/prsistassessment/csv/{test_name}", "PrsistAssessmentController@makeCSV");
		Route::get("/prsistassessment/csv/{test_name}/{start}/{end}", "PrsistAssessmentController@makeCSV");
		Route::get("/prsistassessment/csv", "PrsistAssessmentController@makeCSV");
		Route::get("/prsistassessment/duplicates", "PrsistAssessmentController@fixDuplicates");
		
		Route::post("/prsistassessment/delete", "PrsistAssessmentController@deleteGames");
		Route::get("/prsistassessment/{test_name}", "PrsistAssessmentController@showResults");
		Route::get("/prsistassessment/{test_name}/{start}/{end}", "PrsistAssessmentController@showResults");
		Route::get("/prsistassessment", "PrsistAssessmentController@showResults");
    });

    // Admin Routes
    Route::group(["before" => "admin"], function() {
        Route::get("/admin/users/delete/{user_id}", "UserController@deleteUser");
        Route::get("/admin/users", "UserController@listUsers");
        Route::get("/admin/newuser", "UserController@newUser");
        Route::post("/admin/newuser/submit", "UserController@submitNewUser");
        Route::get("/admin/user/{user_id}", "UserController@viewUser");
        Route::post("/admin/user/{user_id}/update", "UserController@updateUser");
        Route::get("/admin/apps", "UserController@listAppUsers");
        Route::get("/admin/newappuser", "UserController@newAppUser");
        Route::post("/admin/newappuser/submit", "UserController@addAppUser");
        Route::get("/admin/appuser/password/{id}/{password}", "UserController@passwordAppUser");
    });
});

// App POST routes
Route::get("/vocab/copy/{date}", "VocabController@migrateOldToNew");
Route::post("/vocab/new/save", "VocabController@saveGames");
Route::post("/vocab/save", "VocabController@saveGames");
Route::post("/cardsort/save", "CardSortController@saveGame");
Route::post("/questionnaire/save", "QuestionnaireController@saveAnswers");
Route::post("/mrant/save", "MrAntController@saveAnswers");
Route::post("/fishshark/save", "FishSharkController@saveGames");
Route::post("/notthis/save", "NotThisController@saveGames");
Route::post("/earlynumeracy/save", "EarlyNumeracyController@saveEntries");
Route::post("/prsistassessment/save", "PrsistAssessmentController@saveAssessment");

Route::get("/", function() {
    if (!Session::has("user_id")) {
        return Redirect::to('http://yourwebsite.com.au');//
    } else {
        return Redirect::to('/home');
    }
});

Route::post("/game_data", function() {
    $file = Input::file('game_data');

    echo file_get_contents($file->getRealPath());
});

