<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\User;
use App\Notifications\JobCreated;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class JobsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->is_manager){
            $jobs = Job::with('user')->all();
        }else{
            $jobs = $user->jobs()->with('user')->get();
        }
    
        return new JobResource($jobs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJobRequest $request)
    {        
        try{
            $job = Auth::user()->jobs()->create($request->all());

            if($job){
                $managers = User::where('is_manager',1)->get();
                try{
                    Notification::send($managers, new JobCreated($job));
                }catch(Exception $e){
                }
            }
    
            return (new JobResource($job->load('user')))
                   ->response()
                   ->setStatusCode(201);
        }catch(Exception $e){
            Log::error($e);
            return response()->json('Server Error', 500);
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
        try{
            $job = Auth::user()->jobs()->find($id);
            if(!$job){
                return response()->json('Job not found!', 404);
            }
            return (new JobResource($job->load('user')))
                    ->response()
                    ->setStatusCode(200);
        }catch(Exception $e){
            Log::error($e);
            return response()->json('Server Error', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJobRequest $request, $id)
    {
        try{
            $job = Auth::user()->jobs()->find($id);
            
            if(!$job){
                return response()->json('Job not found!', 404);
            }

            $job->update($request->all());

            return (new JobResource($job->load('user')))
                ->response()
                ->setStatusCode(200);
        }catch(Exception $e){
            Log::error($e);
            return response()->json('Server Error', 500);
        }

    }

}
