<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Allergens;
use App\Http\Resources\AllergenResource;

/**
 * Работа с аллергенами
 *
 * Class AllergensController
 * @package App\Http\Controllers\Api
 */
class AllergensController extends Controller
{
    public function index()
    {
        $allergens = Allergens::with(['type', 'category'])->get();

        return AllergenResource::collection($allergens);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // TODO
    }
}
