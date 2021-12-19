<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function today(Request $request)
    {
        
        $data = $request->only('num_of_pages','sort');
        $validator = Validator::make($data, [
            'num_of_pages' => 'required|integer',
            'sort'=>'required|in:ascending,descending'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

         
        $sort = $request->sort;
        if($sort=='descending')
        {
            $sort='asc';
        }else
        {
            $sort='desc';
        }
        $reservations=Reservation::where('date',Carbon::today())->orderBy('created_at', $sort)->paginate($request->num_of_pages);

        return response()->json([
            'success' => true,
            'message' => 'data loaded successfully',
            'data' => $reservations
        ], Response::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function allReservation(Request $request)
    {
        $data = $request->only('num_of_pages','sort','table_id','from_date','to_date');
        $validator = Validator::make($data, [
            'num_of_pages' => 'required|integer',
            'sort'=>'in:ascending,descending',
            'table_id'=>'exists:reservations',
            'from_date'=>'',
            'to_date'=>''
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

         
        $sort = $request->sort;
        if($sort=='descending')
        {
            $sort='asc';
        }else
        {
            $sort='desc';
        }
        $query=Reservation::query();

        $query->when($request->table_id , function ($q) use ($request) {
            return $q->where('table_id', $request->table_id);
        });
        if($request->from_date && $request->to_date){
         $query->whereBetween('date', [$request->from_date, $request->to_date])->get();
    }
       $reservations=$query->paginate($request->num_of_pages);
       


        return response()->json([
            'success' => true,
            'message' => 'data loaded successfully',
            'data' => $reservations
        ], Response::HTTP_OK);
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
            'id' => 'required|integer|exists:reservations',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        if(Reservation::whereId($request->id)->where('date',Carbon::today())->first())
        { 
            Reservation::whereId($request->id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'reservation deleted.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'reservation not found.',
            ], 500);
        }
    }
}
