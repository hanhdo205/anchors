<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Anchor;

class AnchorController extends Controller
{
    /**
     * Display a listing of the keywords.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $anchors = Anchor::all();
        return view('anchors.index',compact('anchors',$anchors));
    }
	
	/**
     * Show the form for registering a new keyword.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('anchors.create');
    }
	
	/**
     * Store a newly registered keyword in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required',
        ]);

        Anchor::create($request->all());

        return redirect('/anchors');

    }
}
