<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Services\ReturnComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReturnComplaintIntakeController extends Controller
{
    public function store(Request $request, Order $order, ReturnComplaintService $service)
    {
        abort_unless((int)$order->buyer_id===(int)$request->user()->id,404);
        $data=$request->validate([
            'return_type'=>['required',Rule::in(['return','refund'])],
            'problem_type'=>['required',Rule::in(ReturnComplaintService::PROBLEMS)],
            'reason'=>['required','string','min:10','max:2000'],
            'evidence'=>['required','array','min:1','max:5'],
            'evidence.*'=>['required','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'items'=>['required','array','min:1','max:1'],
            'items.*.order_item_id'=>['required','integer',Rule::exists('order_items','id')->where('order_id',$order->id)],
            'items.*.quantity'=>['required','integer','min:1'],
        ]);
        $paths=[];
        try {
            $return=DB::transaction(function () use ($request,$order,$data,$service,&$paths) {
                $locked=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                abort_unless($locked->status==='completed' && $locked->buyer_received_at && !$locked->buyer_received_automatically,422,'Please click Order Received before requesting a return or refund.');
                abort_if(now()->gt($locked->returnRequestDeadline()),422,'The 7-day return and refund request window has expired.');
                abort_if($locked->complaints()->whereNotIn('status',['resolved','closed','declined','rejected'])->exists()
                    || $locked->returns()->whereIn('status', ['pending', 'accepted'])->exists(),422,'This order already has an ongoing complaint. Please follow its existing discussion.');
                $item=$locked->items()->with('product')->findOrFail($data['items'][0]['order_item_id']);
                $quantity=(int)$data['items'][0]['quantity'];
                $reserved=OrderReturn::where('order_id',$locked->id)->whereNotIn('status',['declined','rejected'])->get()
                    ->sum(fn ($record) => collect($record->return_items)->where('order_item_id',$item->id)->sum('quantity'));
                abort_if($quantity+$reserved>$item->quantity,422,'The selected quantity is already requested or exceeds the purchased quantity.');
                $subtotal=(float)$locked->items()->selectRaw('SUM(quantity * unit_price) as subtotal')->value('subtotal');
                $net=min($subtotal,max(0,(float)$locked->total-(float)$locked->shipping_fee));
                $amount=$subtotal>0?round((float)$item->unit_price*$quantity*$net/$subtotal,2):0;
                abort_if($amount<=0,422,'This product has no refundable paid amount.');
                foreach ($request->file('evidence',[]) as $file) $paths[]=$file->store('buyer-complaints/returns','public');
                $case=Complaint::create(['user_id'=>$request->user()->id,'order_id'=>$locked->id,
                    'subject'=>$data['problem_type'],'type'=>$data['problem_type'],'description'=>$data['reason'],'status'=>'open','evidence'=>$paths]);
                $return=OrderReturn::create(['order_id'=>$locked->id,'complaint_id'=>$case->id,
                    'return_type'=>$data['return_type'],'buyer_solution'=>$data['return_type'],'problem_type'=>$data['problem_type'],
                    'status'=>'pending','reason'=>$data['reason'],'requested_at'=>now(),'refund_amount'=>$amount,
                    'return_items'=>[['order_item_id'=>$item->id,'product_name'=>$item->product_name,
                        'is_fragile'=>(bool)($item->is_fragile||$item->product?->is_fragile),
                        'variation'=>$item->variation,'color'=>$item->color,'size'=>$item->size,'quantity'=>$quantity,'unit_price'=>$item->unit_price]],
                    'evidence'=>collect($paths)->map(fn ($path) => ['path'=>$path,'url'=>Storage::disk('public')->url($path)])->all()]);
                $return->setRelation('complaint',$case);$return->setRelation('order',$locked);
                $service->log($return,$request->user(),'pending','Buyer requested '.($return->return_type==='refund'?'Refund Only':'Return & Refund').'. Waiting for admin review.');
                $service->notify($return,'New return/refund complaint','Order '.$locked->order_number.': a product complaint is waiting for admin review.');
                $service->notifyAssignedRiderOfRequest($return);
                return $return;
            });
        } catch (\Throwable $exception) { foreach ($paths as $path) Storage::disk('public')->delete($path); throw $exception; }
        return response()->json(['data'=>$return->load('events'),'message'=>'Request submitted. Waiting for admin action.'],201);
    }

    public function schedule(Request $request, OrderReturn $return)
    {
        $data=$request->validate(['pickup_date'=>['required','date','after_or_equal:today'],
            'pickup_time'=>['required',Rule::in(OrderReturn::PICKUP_TIMES)]]);
        $scheduled = app(\App\Services\ReturnPickupService::class)->scheduleByBuyer($return, $request->user(), $data);
        return response()->json(['data'=>$scheduled]);
    }
}
