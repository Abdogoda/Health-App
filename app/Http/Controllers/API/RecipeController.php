<?php

namespace App\Http\Controllers\API;

use App\Enums\CuisinesEnum;
use App\Enums\DishesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Models\User;
use App\Services\RecipesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    protected $recipesService;

    public function __construct(RecipesService $recipesService)
    {
        $this->recipesService = $recipesService;
    }

    // API Recipes

    public function getRecommendedRecipes(Request $request)
    {
        $request->validate([
            'count' => 'nullable|integer',
            'offset' => 'nullable|integer',
            'cuisine' => 'string|in:' . implode(',', CuisinesEnum::values()),
            'type' => 'nullable|string|in:' . implode(',', DishesEnum::values()),
            'search' => 'nullable|string',
            'for_me' => 'nullable|boolean',
        ]);

        try {
            $recipes = $this->recipesService->getRecipesForUser(
                count: $request->count ?? 10,
                offset: $request->offset ?? 0,
                cuisine: $request->cuisine,
                type: $request->type,
                search: $request->search,
                applyForMe: $request->for_me ?? true
            );

            return $this->response($recipes);
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }

    public function getRecipe($id)
    {
        try {
            $recipe = $this->recipesService->getRecipeById($id);

            return $this->response($recipe);
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }

    public function simillarRecipes(Request $request, int $id)
    {
        $request->validate([
            'count' => 'nullable|integer',
        ]);

        try {
            $similarRecipes = $this->recipesService->getSimilarRecipes($id, $request->count ?? 10);

            return $this->response($similarRecipes);
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }

    public function randomRecipes(Request $request)
    {
        $request->validate([
            'count' => 'nullable|integer',
        ]);

        try {
            $recipes = $this->recipesService->getRandomRecipes($request->count ?? 10);

            return $this->response($recipes);
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }

    public function findByIngredients(Request $request)
    {
        $request->validate([
            'ingredients' => 'required|string',
            'count' => 'nullable|integer',
        ]);

        try {
            $recipes = $this->recipesService->findByIngredients($request->ingredients, $request->count ?? 10);

            return $this->response($recipes);
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }

    public function autocompleteRecipes(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
            'number' => 'nullable|integer',
        ]);

        try {
            $recipes = $this->recipesService->autocompleteRecipes(query: $request->search, number: $request->number ?? 10);

            return $this->response($recipes);
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }

    // User Custom Recipes

    public function getFavoriteRecipes(Request $request)
    {
        $recipes = User::findOrFail(auth()->id())->recipes()->where('is_favorite', true)->get();
        return $this->response(RecipeResource::collection($recipes));
    }

    public function addFavoriteRecipe(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|integer'
        ]);

        $user = User::findOrFail(auth()->id());

        try {
            $recipe = $user->recipes()->where('id', $request->recipe_id)->first();

            if ($recipe) {
                $recipe->is_favorite = true;
                $recipe->save();
            } else {
                $apiResponse = $this->recipesService->getRecipeById($request->recipe_id);

                if ($apiResponse) {
                    $recipeData = array_merge(
                        $apiResponse,
                        [
                            'ingredients' => array_map(
                                fn($ingredient) =>
                                "{$ingredient['amount']} {$ingredient['unit']} {$ingredient['name']}",
                                $apiResponse['ingredients']
                            ),
                            'is_favorite' => true,
                            'user_id' => $user->id,
                        ]
                    );

                    $recipe = Recipe::create($recipeData);
                } else {
                    return $this->response(message: "Recipe not found", status: 404);
                }
            }
            return $this->response(RecipeResource::make($recipe), "Recipe already added to favorites");
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }

    public function removeFavoriteRecipe(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|integer'
        ]);

        $user = User::findOrFail(auth()->id());

        try {
            $recipe = $user->recipes()->where('id', $request->recipe_id)->first();

            if ($recipe) {
                $recipe->is_favorite = false;
                $recipe->save();
                return $this->response(RecipeResource::make($recipe), "Recipe removed from favorites");
            } else {
                return $this->response(message: "Recipe not found", status: 404);
            }
        } catch (\Exception $e) {
            return $this->response(message: $e->getMessage(), status: 500);
        }
    }


    // Recipe CRUD

    public function index()
    {
        return $this->response(RecipeResource::collection(Recipe::all()));
    }

    public function show(Recipe $recipe)
    {
        return $this->response(RecipeResource::make($recipe));
    }

    public function store(RecipeRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images/recipes', 'public');
        }

        $recipe = Recipe::create($validated);

        return $this->response(RecipeResource::make($recipe), message: "Recipe created successfully");
    }

    public function update(RecipeRequest $request, Recipe $recipe)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($recipe->image) {
                Storage::disk('public')->delete($recipe->image);
            }

            $validated['image'] = $request->file('image')->store('images/recipes', 'public');
        }

        $recipe->update($validated);

        return $this->response(RecipeResource::make($recipe), message: "Recipe updated successfully");
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return $this->response(message: "Recipe deleted successfully");
    }
}