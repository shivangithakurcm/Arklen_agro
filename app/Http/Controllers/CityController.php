<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::latest()->get();
        return view('cities.index', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:cities,name',
        ], [
            'name.required' => 'City name required.',
            'name.unique'   => 'already exist.',
        ]);

        City::create(['name' => $request->name]);

        return redirect()->route('cities.index')
                         ->with('success', 'City successfully added');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('cities.index')
                         ->with('success', 'City successfully deleted!');
    }
}