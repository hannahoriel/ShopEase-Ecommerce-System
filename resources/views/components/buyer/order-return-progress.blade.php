@foreach($read('returns', []) as $return)
@if($return['complaint_id'] ?? null)
<section class="vo-card vo-return-case">
    <div class="vo-section-title"><h2>Return / Refund #{{ $return['id'] }}</h2><span class="vo-stage-pill {{ $return['status']==='pending' ? 'vo-case-investigation' : '' }}">{{ ['pending'=>'Under Investigation','cancelled'=>'Cancelled','accepted'=>'Accepted','declined'=>'Declined','refunded'=>'Refund Completed'][$return['status']] ?? ucfirst($return['status']) }}</span></div>
    <div class="vo-case-body"><div class="vo-case-overview"><div class="vo-case-details">
        <p class="vo-case-product"><strong>Product:</strong> {{ data_get($return,'return_items.0.product_name') }} · Qty {{ data_get($return,'return_items.0.quantity') }}</p>
        <p><strong>Problem:</strong> {{ $return['problem_type'] }}</p><div class="vo-case-description"><strong>Complaint summary</strong><p>{{ $return['reason'] }}</p></div>
        <p><strong>Current solution:</strong> {{ $return['return_type']==='refund'?'Refund Only':'Return & Refund' }}</p>
        <p><strong>Refund amount:</strong> ₱{{ number_format((float)$return['refund_amount'],2) }}</p>
        @if(($return['buyer_solution'] ?? $return['return_type']) !== $return['return_type'])<p>Admin changed your original {{ $return['buyer_solution']==='refund'?'Refund Only':'Return & Refund' }} request.</p>@endif
        @if($return['status']==='pending')<p>Waiting for admin’s review and discussion with you and the seller.</p>@endif
        @if($return['solution'])<p><strong>Admin’s note:</strong> {{ $return['solution'] }}</p>@endif
        @if($return['status']==='accepted' && $return['return_type']==='return' && (empty($return['pickup_date']) || ($return['shipping_status'] ?? null)==='pickup_failed') && (int)($return['failed_pickup_attempts'] ?? 0) < 3)
            <p>Your return was approved. Choose a pickup date and time. A pickup rider assigned to your barangay will be assigned automatically. The refund will be credited after the returned parcel is received.</p>
            <button type="button" class="vo-primary" data-return-pickup="{{ $return['id'] }}">{{ (int)($return['failed_pickup_attempts'] ?? 0) ? 'Reschedule Return Pickup (Attempt '.((int)$return['failed_pickup_attempts']+1).' of 3)' : 'Schedule Return Pickup (Attempt 1 of 3)' }}</button>
        @endif
        @if((int)($return['failed_pickup_attempts'] ?? 0) > 0)<p>Pickup attempts used: {{ $return['failed_pickup_attempts'] }} of 3.</p>@endif
        @if($return['status']==='cancelled')<p>Request cancelled after three failed pickup attempts.</p>@endif
        @if($return['pickup_date'])<p><strong>Pickup:</strong> {{ \Carbon\Carbon::parse($return['pickup_date'])->format('M d, Y') }} · {{ $return['pickup_time'] }}<br>{{ $return['pickup_address'] }}<br><strong>Pickup rider:</strong> {{ data_get($return, 'pickup_rider.user.name', 'Not assigned') }}<br><strong>Contact number:</strong> {{ data_get($return, 'pickup_rider.contact_no', 'Not available') }}<br>{{ $return['courier'] }} · {{ $return['tracking_number'] }}<br>Return status: {{ \Illuminate\Support\Str::headline($return['shipping_status']) }}</p>@endif
        @if($return['status']==='refunded')<p class="vo-success-note">₱{{ number_format((float)$return['refund_amount'],2) }} has been credited to your ShopEase Wallet.</p>@endif
        </div><aside class="vo-case-evidence"><h3>Evidence photos</h3><div class="vo-case-photos">@foreach($return['evidence'] ?? [] as $photo)<a href="{{ $photo['url'] }}" target="_blank" rel="noopener"><img src="{{ $photo['url'] }}" alt="Complaint evidence" style="width:72px;height:72px;border-radius:8px;object-fit:cover"></a>@endforeach</div></aside></div>
        <div class="vo-case-history"><h3>Request history</h3>
        @foreach($return['events'] ?? [] as $event)<p><strong>{{ $event['status']==='pending' ? 'Under Investigation' : \Illuminate\Support\Str::headline($event['status']) }}</strong> · {{ \Carbon\Carbon::parse($event['scanned_at'])->format('M d, Y · h:i A') }}<br>{{ $event['location'] }}</p>@endforeach
        </div><div class="vo-case-footer"><a class="vo-outline" href="{{ route('buyer.my-purchases',['tab'=>'complaints','complaint'=>$return['complaint_id']]) }}">Open complaint discussion</a></div>
    </div>
</section>
@endif
@endforeach
