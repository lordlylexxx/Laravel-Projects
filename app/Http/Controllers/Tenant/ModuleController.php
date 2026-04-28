<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'THIS IS MODULE INDEX';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Create module form placeholder.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModuleRequest $request): JsonResponse
    {
        $module = Module::create($request->validated());

        return response()->json($module, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        return response()->json($module);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        return response()->json(['message' => 'Edit module form placeholder.', 'data' => $module]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModuleRequest $request, Module $module): JsonResponse
    {
        $module->update($request->validated());

        return response()->json($module);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();

        return response()->json(null, 204);
    }
}
