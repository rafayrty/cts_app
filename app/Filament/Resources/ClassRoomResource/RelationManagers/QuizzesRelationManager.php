<?php

namespace App\Filament\Resources\ClassRoomResource\RelationManagers;

use App\Models\Attempt;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Forms\Components\Grid;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';

    protected static ?string $recordTitleAttribute = 'class_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            Grid::make(1)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_of_attempts')
                    ->numeric()
                    ->maxLength(255),
                Repeater::make('questions')

                    ->schema([
                        TextInput::make('question')->required(),
                        TextInput::make('option_a')->required(),
                        TextInput::make('option_b')->required(),
                        TextInput::make('option_c')->required(),
                    Radio::make('correct')
                        ->required()
                        ->options([
                            'a' => 'a',
                            'b' => 'b',
                            'c' => 'c',
                        ])->reactive(),
                    ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('updated_at')->label('Attempt Now')->disableClick()
                ->formatStateUsing(function (Quiz $record){
                    if(Auth::user()->hasRole('Student')){
                        $attempts = Attempt::where('user_id',Auth::user()->id)->where('quiz_id',$record->id)->get()->count();
                        $canAttempt = false;
                        if($attempts < $record->no_of_attempts){
                            $canAttempt = true;
                        }
                        if($canAttempt){
                            return new HtmlString('
                            <a href="'.route('quiz.attempt',$record->id).'" style="display:flex;" class="
                                filament-link relative inline-flex items-center justify-center font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action">
                                <span>
                                Attempt Quiz</span></a>
                                ');
                        }else{
                        $latest_attempt = Attempt::where('user_id',Auth::user()->id)->where('quiz_id',$record->id)->latest()->get()->first();
                            return new HtmlString("<strong>Your Score is ".$latest_attempt->score."/".$latest_attempt->total);
                        }
                    }else{
                            return new HtmlString('
                            <a href="'.route('quiz.results',$record->id).'" style="display:flex;" class="
                                filament-link relative inline-flex items-center justify-center font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action">
                                <span>
                                View Results</span></a>
                                ');
                    }
                })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
