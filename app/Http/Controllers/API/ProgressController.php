<?php

namespace App\Http\Controllers\API;

use App\Enums\DishesEnum;
use App\Helpers\HealthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProgressRequest;
use App\Http\Resources\ProgressResource;
use App\Models\Progress;
use App\Models\User;
use App\Services\RecipesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    protected $recipesService;

    public function __construct(RecipesService $recipesService)
    {
        $this->recipesService = $recipesService;

        if (!Auth::user()->profile) {
            abort(404, 'User profile not found. Please create a user profile first');
        }
    }

    public function index(Request $request)
    {
        return $this->response(ProgressResource::collection(Auth::user()->progress()->orderBy('date', 'desc')->get()));
    }

    public function getProgress(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $day = $validated['date'] ?? now()->format('Y-m-d');
        $progress = Progress::where('user_id', $user->id)->where('date', $day)->first();
        if (!$progress) {
            $progress = Progress::create([
                'user_id' => $request->user()->id,
                'date' => $day
            ]);
        }

        return $this->response(ProgressResource::make($progress));
    }

    public function updateProgress(ProgressRequest $request)
    {
        $user = User::findOrFail($request->user()->id);
        $validated = $request->validated();

        $day = $validated['date'] ?? now()->format('Y-m-d');
        $progress = Progress::where('user_id', $user->id)->where('date', $day)->first();

        if (!$progress) {
            $validated['user_id'] = $request->user()->id;
            $validated['date'] = $day;
            $progress = Progress::create($validated);
        } else {
            $progress->update($validated);
        }

        return $this->response(ProgressResource::make($progress), message: 'Progress updated successfully');
    }

    public function analyzeProgress(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Progress::where('user_id', $request->user()->id);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $progressLogs = $query->orderBy('date', 'desc')->get();

        if ($progressLogs->isEmpty()) {
            return $this->response(message: 'No progress logs found for the selected period', status: 404);
        }

        $daysCount = $progressLogs->first()->date->diffInDays($progressLogs->last()->date);

        $initialWeight = $progressLogs->first()->weight;
        $currentWeight = $progressLogs->last()->weight;
        $weightChange = $currentWeight - $initialWeight;

        $totalCaloriesConsumed = $progressLogs->sum('calories_consumed');
        $totalCaloriesBurned = $progressLogs->sum('calories_burned');

        $averageProtein = round($progressLogs->avg('protein'), 2);
        $averageCarbs = round($progressLogs->avg('carbs'), 2);
        $averageFats = round($progressLogs->avg('fats'), 2);

        return response()->json([
            'start_date' => $progressLogs->first()->date,
            'end_date' => $progressLogs->last()->date,
            'initial_weight' => $initialWeight,
            'current_weight' => $currentWeight,
            'weight_change' => $weightChange,
            'total_calories_consumed' => $totalCaloriesConsumed,
            'total_calories_burned' => $totalCaloriesBurned,
            'average_macronutrients' => [
                'protein' => $averageProtein,
                'carbs' => $averageCarbs,
                'fats' => $averageFats
            ],
            'message' => "This progress is in $daysCount days. " . ($weightChange > 0 ? 'You gained weight' : ($weightChange < 0 ? 'You lost weight' : 'Your weight is stable'))
        ]);
    }
}
