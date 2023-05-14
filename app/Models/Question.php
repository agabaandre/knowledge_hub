<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $appends = ['right_answers','wrong_answers'];

    public function answers(){
        return $this->hasMany(Answer::class);
    }

    public function responses(){
        return $this->hasMany(QuestionStats::class);
    }

    public function getRightAnswersAttribute(){
        $answers    = QuestionStats::where('is_correct',1)
                    ->where('question_id',$this->id)->get();

        return count($answers);
    }

    public function getWrongAnswersAttribute(){

        $answers    = QuestionStats::where('is_correct',0)
                    ->where('question_id',$this->id)->get();

        return count($answers);
    }
    
}
