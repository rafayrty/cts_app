<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function quiz($id){
        $quiz = Quiz::findOrFail($id);
        return view('quiz_attempt',compact('quiz'));
    }


    public function submit(Request $request,$id){

        $quiz = Quiz::findOrFail($id);
        $total_questions = count($quiz->questions);
        $answer = $request->answer;
        $correct = 0;
        //Testing Scores
        for($i = 0;$i<$total_questions;$i++){
            if($quiz->questions[$i]['correct'] == $answer[$i]){
                $correct++;
            }
        }
        $classroom_id = $quiz->class_room->id;
        //dd($classroom_id);
        $attempt = Attempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::user()->id,
            'score' => $correct,
            'total' => $total_questions,
        ]);
        return redirect()->to('/admin/class-rooms/'.$classroom_id);

    }

    public function results($id){

        $quiz = Quiz::findOrFail($id);
        $results = Attempt::where('quiz_id',$id)->orderBy('score', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('quiz_scores', compact('results','quiz'));
    }
}
