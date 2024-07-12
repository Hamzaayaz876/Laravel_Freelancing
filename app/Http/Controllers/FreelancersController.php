<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFreelancerRequest;
use App\Http\Resources\FreelancersResource;
use App\Models\Freelancer;
use App\Models\Freelancer_tags;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreelancersController extends Controller
{
    use HttpResponses;

    /**
     * Store a newly created resource in storage.
     */
    public function ShowFreelancerByID(string $id){
        $freelancer=Freelancer::where('id',$id)->first();
        if($freelancer){
            return FreelancersResource::collection(
                $freelancer
            );
        }
        return [
            'message'=>'This freelancer is not availabe'
        ];
    }


    public function storeFreelancer(StoreFreelancerRequest $request)
{
    $request ->validated($request->all());

    $currentUserId = Auth::user()->id;
    $freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if (!$freelancer) {
        return $this->error('','You are not authorized.', 422);
    }

    $picture = null;
    $cv = null;

    if ($request->hasFile('picture')) {
        $picture = $request->file('picture')->get();
    }

    if ($request->hasFile('cv')) {
        $cv = file_get_contents($request->file('cv')->getRealPath());
    }

    $freelancer->update([
        'firstname' => $request->firstname,
        'lastname' => $request->lastname,
        'skill_name'=>$request->skill_name,
        'bio' => $request->bio,
        'picture' => $picture,
        'cv' => $cv,
        'Category'=>$request->Category
    ]);
    if ($request->tags) {
        $tags = explode(',', $request->tags);

        // Add each tag to the project_tags table
        foreach ($tags as $tag) {
            Freelancer_tags::create([
                'Tag_name' => trim($tag),
                'Freelancer_id' => $freelancer->id
            ]);
        }
    }

    return $this->success([
        'message'=>'Record of freelancer added Successfully'
    ]);
}

    public function showPicture($id)
    {
        $freelancer = Freelancer::findOrFail($id);

        if ($freelancer->picture) {
            $picture = $freelancer->picture;
            $response = response($picture, 200)->header('Content-Type', 'image/jpeg');
            return $response;
        } else {
            abort(404);
        }
    }

    public function showFile($id)
{
    $freelancer = Freelancer::findOrFail($id);

    $freelancer = Freelancer::findOrFail($id);

    if ($freelancer->cv) {
        $cv = $freelancer->cv;
        $response = response($cv, 200)->header('Content-Type', 'application/pdf');
        return $response;
    } else {
        abort(404);
    }
}


public function showFreelancers_Category(string $category)
{
    return FreelancersResource::collection(
        Freelancer::where('Category',$category)->get()
    );
}


//Show All Freelancer
    public function showAllFreelancer()
    {
        return FreelancersResource::collection(
            Freelancer::all()
        );
    }


    //show all projects by descending order
    public function searchFreelancer($search = null, $AsWhat = null)
{
    if ($AsWhat) {
        if ($AsWhat != "Total_Rating" && $AsWhat != "total-compeleted-jobs") {
            return $this->error('', 'Invalid input for the type of searching. Only "Total_Rating" and "total-compeleted-jobs" are accepted.', 422);
        }

        $query = Freelancer::orderByDesc($AsWhat);
    } else {
        $query = Freelancer::query();
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('skill_name', 'like', '%' . $search . '%')
                ->orWhereHas('tags', function ($q) use ($search) {
                    $q->where('Tag_name', 'like', '%' . $search . '%');
                });
        });
    }
    $freelancers = $query->get();
return FreelancersResource::collection($freelancers);
}




    /**
     * Display the specified resource.
     */
    public function show()
    {
        $currentUserId = Auth::id();
    $freelancer = Freelancer::where('user_id', $currentUserId)->first();
    return new FreelancersResource($freelancer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteFreelancer(string $id)
    {
        $freelancer = Freelancer::where('id', $id)->first();
        $freelancer->delete();
        return $this->success([
            'message'=>'Project deleted successfully'
        ]);
    }
}
