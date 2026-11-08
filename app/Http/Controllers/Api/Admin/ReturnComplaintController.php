<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Services\ReturnComplaintService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReturnComplaintController extends Controller
{
    public function update(Request $request, Complaint $complaint, ReturnComplaintService $service)
    {
        $data=$request->validate(['action'=>['required',Rule::in(['accept','decline','change_solution','received'])],
            'notes'=>['nullable','string','max:2000']]);
        return response()->json(['data'=>$service->decide($complaint,$request->user(),$data['action'],$data['notes']??null)]);
    }
}
