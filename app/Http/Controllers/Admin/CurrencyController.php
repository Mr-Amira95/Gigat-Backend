<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('pages.currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        // $currency = Card::findOrFail($id);

        // return view('pages.currencies.show', compact('card'));
    }

    public function create()
    {
        return view('pages.currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255'],
            'symbol_en' => ['required', 'string', 'max:255'],
            'symbol_ar' => ['required', 'string', 'max:255'],
            'exchange_rate' => 'required',
        ]);

        $currency = new Currency();
        $currency->code = $request->code;
        $currency->symbol_en = $request->symbol_en;
        $currency->symbol_ar = $request->symbol_ar;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->save();

        return redirect()->route('currencies.index')->with('success', 'Currency created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);

        return view('pages.currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255'],
            'symbol_en' => ['required', 'string', 'max:255'],
            'symbol_ar' => ['required', 'string', 'max:255'],
            'exchange_rate' => 'required',
        ]);

        $currency = Currency::findOrFail($id);
        $currency->code = $request->code;
        $currency->symbol_en = $request->symbol_en;
        $currency->symbol_ar = $request->symbol_ar;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->save();

        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Currency::destroy($id);
        return back()->with('success', 'Currency deleted successfully.');
    }
}
