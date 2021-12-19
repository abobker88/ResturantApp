<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\Resturant;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class ReservationController extends Controller
{
    //

    public function getShift(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => Resturant::get(),
        ], Response::HTTP_OK);
    }


    public function checkAvailableSlot(Request $request)
    {
        $data = $request->only('num_of_seats');
        $validator = Validator::make($data, [
            'num_of_seats' => 'required|integer',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $date=Carbon::now('PST');// for saudi time zone 
        
        $tables=Table::where('num_of_seats','>',$request->num_of_seats)->get();
      
        $shift=Resturant::take(1)->first();
        
        $now_time=$date->format('g:i A');
        $start_shift=$shift->start_shift->format('g:i A');
        
        if($now_time > $start_shift)
        {
            $start_shift=$now_time;
        }
        
      
        $end_shift=$shift->end_shift;

        $result = [];
        foreach($tables as $table)
        {
           $reservations=Reservation::where('table_id',$table->id)->where('date',Carbon::today())->get();
           $result[]=$this->openSlots($start_shift, $end_shift, $reservations,$table->id);
                       
        }
     
        return response()->json([
            'success' => true,
            'message' => 'available slot',
            'data' => $result
        ], Response::HTTP_OK);
        
    }


    function openSlots($start_shift,$end_shift, $reservations,$table_id)
{
    if (count($reservations) == 0) { 
        return ['start_slot' => $start_shift, 'end_slot' => Carbon::parse($end_shift)->format('g:i A'),'table_id'=>$table_id];
    }

    $freeslots = []; 

   
    if ($reservations[0]['start_time'] !== $start_shift) {
        $freeslots[] = [
            'start_slot' => $start_shift,
            'end_slot' => Carbon::parse($reservations[0]['start_time'])->format('g:i A') ,
            'table_id'=>$table_id
        ];
    }


    for ($g = 0; $g < count($reservations) - 1; $g++) {
        if ($reservations[$g]['end_time'] !== $reservations[$g + 1]['start_time']) {
            
            $freeslots[] = [
                'start_slot' => Carbon::parse($reservations[$g]['end_time'])->format('g:i A') ,
                'end_slot' => Carbon::parse($reservations[$g + 1]['start_time'])->format('g:i A') ,
                'table_id'=>$table_id
            ];
        }
    }

    $lastBook = $reservations[count($reservations) - 1];
  
    if ($lastBook['end_time'] !== $end_shift) {
        $freeslots[] = [
            'start_slot' => Carbon::parse($lastBook['end_time'])->format('g:i A') ,
            'end_slot' =>Carbon::parse($end_shift)->format('g:i A'),
            'table_id'=>$table_id
        ];
    }

    return $freeslots;
}


public function booking(Request $request)
{
    $data = $request->only('table_id','num_of_seats','start_time','end_time');
        $validator = Validator::make($data, [
            'table_id' => 'required|integer',
            'num_of_seats'=>'required|integer',
            'start_time'=>'required|date_format:H:i',
            'end_time'=>'required|date_format:H:i'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $table=Table::where([['num_of_seats','>',$request->num_of_seats],['id',$request->table_id]])->first();

        if($table)
        {
            $shift=Resturant::take(1)->first();
        
    
            $start_shift=$shift->start_shift->format('H:i:s');
            $start_shift=Carbon::parse($start_shift);
            $end_shift=$shift->end_shift->format('H:i:s');
            $end_shift=Carbon::parse($end_shift);
            $from_time=date('Y/m/d H:i:s',strtotime($request->start_time));
          $from_time =  Carbon::parse($from_time);
            $end_time=date('Y/m/d H:i:s',strtotime($request->end_time));
            $end_time=Carbon::parse($end_time); // saudi time zone 
            
           
            if($from_time->between($start_shift, $end_shift) && $end_time->between($start_shift, $end_shift))
            {
                $today_reservations=Reservation::where('table_id',$table->id)->where('date',Carbon::today())->get();
            
                if(count($today_reservations)>0)
                {
                   if($this->checkSlotIsNotBooked($today_reservations,$from_time,$end_time))
                     {
                  
                        $reservation=Reservation::create([
                            'table_id'=>$table->id,
                            'start_time'=>$from_time,
                            'end_time'=>$end_time,
                            'num_of_seats'=>$request->num_of_seats,
                            'date'=>Carbon::today()
                        ]);
                        return response()->json([
                            'success' => true,
                            'message' => 'User created successfully',
                            'data' => $reservation
                        ], Response::HTTP_OK);
                     } else {
                return response()->json(['error' => 'this time is booked already' ], 200);
                     }
                } else {
                    $reservation=Reservation::create([
                        'table_id'=>$table->id,
                        'start_time'=>$from_time,
                        'end_time'=>$end_time,
                        'num_of_seats'=>$request->num_of_seats,
                        'date'=>Carbon::today()
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'User created successfully',
                        'data' => $reservation
                    ], Response::HTTP_OK);
                }


            } else
            {
                return response()->json(['error' => 'this times out of restaurant  shift' ], 200);
            }
        } else {
            return response()->json(['error' => 'this table seats less than number of seats' ], 200);

        }
}

 function checkSlotIsNotBooked($reservations,$start_time,$end_time)
{
    foreach($reservations as $reservation)
    {
        if($start_time->between($reservation->start_time, $reservation->end_time) || $end_time->between($reservation->start_time, $reservation->end_time))
        {
               return false;
        }

        return true;
    }


}
}
