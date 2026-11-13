<?php

namespace App\Services;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Buyer\BuyerNotification;
use App\Models\User;
use App\Notifications\RiderEventNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnComplaintService
{
    public const PROBLEMS = ['Damaged Item','Defective / Not Working','Wrong Item','Missing Parts / Accessories','Not as Described','Quality Issue','Other'];

    public function log(OrderReturn $return, User $actor, string $status, string $message): void
    {
        $return->events()->create(['status'=>$status,'location'=>\Illuminate\Support\Str::limit($message,250),'scanned_at'=>now()]);
        $return->complaint?->updates()->create(['user_id'=>$actor->id,'type'=>'return_'.$status,'message'=>$message]);
    }

    public function notify(OrderReturn $return, string $title, string $message): void
    {
        $order=$return->order;
        if ($order->buyer) BuyerNotification::createForBuyer($order->buyer,'return_update',$title,$message,
            ['complaint_id'=>$return->complaint_id,'return_id'=>$return->id,'url'=>'/buyer/view-order?order='.rawurlencode($order->order_number)],$order);
        if ($order->seller?->user) app(SellerNotificationService::class)->send($order->seller->user,'order_updates',$title,$message,'/seller/performance');
    }

    public function notifyAssignedRiderOfRequest(OrderReturn $return): void
    {
        $return->loadMissing('order.shipment.rider.user');
        $order = $return->order;
        $riderUser = $order?->shipment?->rider?->user;
        if (! $riderUser) {
            return;
        }

        $riderUser->notify(new RiderEventNotification(
            'Buyer return/refund request',
            "The buyer submitted a {$return->return_type} request for order {$order->order_number}. It is awaiting review; you will be notified if a pickup is assigned.",
            'return_request',
            (int) $order->id,
        ));
    }

    public function decide(Complaint $complaint, User $admin, string $action, ?string $notes = null): OrderReturn
    {
        return DB::transaction(function () use ($complaint,$admin,$action,$notes) {
            // Consistent lock order across intake, review, pickup and refund.
            $order=Order::whereKey($complaint->order_id)->lockForUpdate()->firstOrFail();
            $case=Complaint::whereKey($complaint->id)->lockForUpdate()->firstOrFail();
            $return=OrderReturn::where('complaint_id',$case->id)->lockForUpdate()->firstOrFail();
            $return->setRelation('order',$order);$return->setRelation('complaint',$case);
            if ($action==='change_solution') {
                abort_unless($return->status==='pending',409,'The solution can only change while waiting for admin review.');
                $return->return_type=$return->return_type==='refund'?'return':'refund';
                $return->save();
                $this->log($return,$admin,'solution_changed','Admin changed the proposed solution to '.($return->return_type==='refund'?'Refund Only':'Return & Refund').'.'.($notes?' '.$notes:''));
                $this->notify($return,'Proposed solution updated','Admin changed the proposed solution for order '.$order->order_number.'.');
                return $return->fresh(['events','complaint']);
            }
            if ($action==='received') {
                if ($return->status==='refunded' && $return->shipping_status==='returned') return $return->fresh(['events','complaint']);
                abort_unless($return->status==='accepted' && $return->return_type==='return' && $return->pickup_date,409,'This return must be accepted and scheduled before receipt can be confirmed.');
                if ($return->order->shipment?->logistics_delivery_hub_id) {
                    abort_unless($return->shipping_status === 'picked_up',409,'The rider must collect the return before receipt can be confirmed.');
                }
                $return->forceFill(['shipping_status'=>'returned','returned_at'=>now()])->save();
                $this->log($return,$admin,'returned','Admin confirmed the returned parcel was received.'.($notes?' '.$notes:''));
                $this->refund($return,$admin);
                return $return->fresh(['events','complaint']);
            }
            // Retrying a finalized action never generates another wallet movement.
            if (($action==='accept' && in_array($return->status,['accepted','refunded'],true)) || ($action==='decline' && $return->status==='declined')) return $return->fresh(['events','complaint']);
            abort_unless($return->status==='pending',409,'This request has already been reviewed.');
            $accepted=$action==='accept';
            $return->forceFill(['status'=>$accepted?'accepted':'declined','reviewed_by'=>$admin->id,
                'reviewed_at'=>now(),'solution'=>$notes,'shipping_status'=>$accepted && $return->return_type==='return'?'awaiting_pickup_schedule':null])->save();
            $case->update(['status' => 'resolved']);
            $this->log($return,$admin,$accepted?'accepted':'declined',
                'Admin '.($accepted?'accepted':'declined').' the '.($return->return_type==='refund'?'Refund Only':'Return & Refund').' request.'.($notes?' '.$notes:''));
            if ($accepted && $return->return_type==='refund') $this->refund($return,$admin);
            else $this->notify($return,$accepted?'Return request accepted':'Request declined',
                'Order '.$order->order_number.': '.($accepted?'Your Return & Refund request was accepted. The buyer can now schedule pickup.':'Admin declined the request.').($notes?' '.$notes:''));
            return $return->fresh(['events','complaint']);
        });
    }

    private function refund(OrderReturn $return, User $admin): void
    {
        if ($return->refunded_at) return;
        $order=$return->order;
        $paid=max(0,round((float)$order->total-(float)$order->shipping_fee,2));
        $already=(float)OrderReturn::where('order_id',$order->id)->whereNotNull('refunded_at')->whereKeyNot($return->id)->sum('refund_amount');
        if ((float)$return->refund_amount<=0 || round($already+(float)$return->refund_amount,2)>$paid) {
            throw ValidationException::withMessages(['refund'=>'Refund exceeds the remaining paid merchandise amount.']);
        }
        $return->forceFill(['status'=>'refunded','refunded_at'=>now()])->save();
        app(BuyerWalletService::class)->move((int)$order->buyer_id,'refund_credit',$return->refund_amount,'order_return',$return->id,'Admin-approved refund for '.$order->order_number);
        app(CodSettlementService::class)->reverseSellerEarningsForRefund($return);
        app(SellerWalletService::class)->recordRefund($order->seller,$return);
        $return->complaint->update(['status'=>'resolved']);
        $this->log($return,$admin,'refunded','Refund of ₱'.number_format((float)$return->refund_amount,2).' transferred from seller wallet to buyer ShopEase Wallet.');
        $this->notify($return,'Refund completed','Order '.$order->order_number.': ₱'.number_format((float)$return->refund_amount,2).' was refunded to the buyer wallet.');
    }
}
