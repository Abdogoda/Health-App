<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MealPlansService;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{

    protected $mealPlansService;

    public function __construct(MealPlansService $mealPlansService)
    {
        $this->mealPlansService = $mealPlansService;
    }

    public function getRecommendedMeals(Request $request)
    {
        try {
            return response()->json($this->mealPlansService->generatePlan());
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }
}