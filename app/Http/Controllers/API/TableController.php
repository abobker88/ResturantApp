<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $data = $request->only('num_of_pages');
        $validator = Validator::make($data, [
            'num_of_pages' => 'required|integer',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $tables=Table::paginate($request->num_of_pages);

        return response()->json([
            'success' => true,
            'message' => 'data loaded successfully',
            'data' => $tables
        ], Response::HTTP_OK);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $data = $request->only('table_no','num_of_seats');
        $validator = Validator::make($data, [
            'table_no' => 'required|integer|unique:tables',
            'num_of_seats' => 'required|integer',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $table=Table::create([
            'table_no'=>$request->table_no,
            'num_of_seats'=>$request->num_of_seats
        ]);

        if($table)
        {
            return response()->json([
                'success' => true,
                'message' => 'data loaded successfully',
                'data' => $table
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'problem with server.',
            ], 500);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = $request->only('id');
        $validator = Validator::make($data, [
            'id' => 'required|integer|exists:tables',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        if(Reservation::where('table_id',$request->id)->get())
        {
            return response()->json([
                'success' => false,
                'message' => 'there are reservation related.',
            ], 500);
        }
        if(Table::whereId($request->id)->delete())
        {
            return response()->json([
                'success' => true,
                'message' => 'table deleted successfully',
                'data' => null
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'problem with server.',
            ], 500);
        }
    }
}
