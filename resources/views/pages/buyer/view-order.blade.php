{{-- ShopEase Buyer | View Order. Self-contained view (CSS and JS inline).
     Optional controller variable: $order (array/model). Until connected, demo records are displayed.
     Access example: /buyer/view-order?order=ORD-DEMO-1801 --}}
@php
    $demoProducts = [
      ['Wireless Bluetooth Earbuds Pro','Tech Haven',1299,'processing','Black · Bluetooth 5.3',1],
      ['Everyday Canvas Tote Bag','Mia Boutique',459,'to-ship','Beige · Medium',2],
      ['Hydrating Skincare Essentials Set','Glow Essentials',899,'in-transit','Sensitive Skin',1],
      ['Everyday Running Sneakers','Stride Studio',1799,'out-for-delivery','White · Size 39',1],
      ['Minimalist Smart Watch','Watch Avenue',2199,'delivered','Midnight Black',1],
      ['Ceramic Coffee Mug Set','Home & Cozy',349,'completed','Cream · Set of 2',2],
      ['Vitamin C Glow Serum','Beauty Bloom',599,'completed','30 ml',1],
      ['Classic Crossbody Bag','Urban Finds',799,'return-refund','Brown · Small',1],
      ['Portable Wireless Speaker','Tech Haven',999,'return-refund','Navy Blue',1],
      ['Classic Canvas Sneakers','Stride Studio',1299,'cancelled','Black · Size 40',1],
      ['Noise-Cancelling Headphones','Sound Source',2499,'processing','Silver',1],
      ['Indoor Plant Pot Set','Green Living',549,'delivered','White · Set of 3',1],
    ];
    $requestedOrder = (string) request('order', 'SH-2026-1005');
    $demoIndex = null;
    if (preg_match('/^SH-2026-(10\d{2})$/', $requestedOrder, $demoMatches)) {
        $candidate = (int) $demoMatches[1] - 1001;
        if (array_key_exists($candidate, $demoProducts)) $demoIndex = $candidate;
    }
    // Legacy demo links remain supported.
    if ($demoIndex === null && preg_match('/^ORD-DEMO-(18\d{2})$/', $requestedOrder, $demoMatches)) {
        $candidate = (int) $demoMatches[1] - 1801;
        if (array_key_exists($candidate, $demoProducts)) $demoIndex = $candidate;
    }
    $sample = $demoProducts[$demoIndex ?? 4];
    $provided = $order ?? null;
    $isDemo = $provided === null;
    $read = fn($key, $fallback = null) => data_get($provided, $key, $fallback);
    $number = $isDemo ? ($demoIndex === null ? 'SH-2026-1005' : $requestedOrder) : (string) ($read('order_number', $read('order_id', $read('id', 'Order'))));
    $status = strtolower(str_replace('_', '-', (string) $read('status', $sample[3])));
    // UI-only five-stage journey; old demo labels map to the closest delivery stage.
    $status = ['return-refund'=>'delivered','cancelled'=>'processing','pending'=>'processing','to_ship'=>'to-ship'][$status] ?? $status;
    $validStatuses = ['processing','to-ship','in-transit','out-for-delivery','delivered','completed'];
    if (!in_array($status,$validStatuses,true)) $status = 'processing';
    $statusNames = ['processing'=>'Processing','to-ship'=>'To Ship','in-transit'=>'In Transit','out-for-delivery'=>'Out for Delivery','delivered'=>'Delivered','completed'=>'Completed'];
    $shop = $read('shop', $sample[1]);
    $name = $read('product_name', data_get($provided, 'items.0.product_name', $sample[0]));
    $variant = $read('variant', data_get($provided, 'items.0.variation', $sample[4]));
    $quantity = max(1,(int)$read('quantity',data_get($provided,'items.0.quantity',$sample[5])));
    $unitPrice = (float)$read('price',data_get($provided,'items.0.unit_price',$sample[2]));
    $subtotal = $unitPrice * $quantity;
    $shipping = (float)$read('shipping_fee', 60);
    $shippingDiscount = (float)$read('shipping_discount', 60);
    $voucher = (float)$read('voucher_discount', 0);
    $total = max(0, (float)$read('total', $subtotal + $shipping - $shippingDiscount - $voucher));
    $placed = (string)$read('created_at','2026-10-19 13:33:00');
    $placedAt = \Carbon\Carbon::parse($placed);
    $shippedAt = $placedAt->copy()->addDay();
    $deliveredAt = $placedAt->copy()->addDays(3);
    $currentStep = match($status) {'processing'=>1,'to-ship'=>2,'in-transit'=>3,'out-for-delivery'=>4,'delivered'=>5,'completed'=>5,default=>1};
    $buyer = auth()->user();
    $buyerName = trim((string)data_get($buyer,'name','Buyer')) ?: 'Buyer';
    $buyerPhone = (string)(data_get($buyer,'phone',data_get($buyer,'contact_number','Not provided')));
    $parts = array_filter([data_get($buyer,'street'),data_get($buyer,'barangay'),data_get($buyer,'municipality'),data_get($buyer,'province'),data_get($buyer,'zip_code')],fn($v)=>filled($v));
    $address = (string)$read('shipping_address', implode(', ', $parts) ?: 'Your registered delivery address will appear here.');
    $courier = (string)$read('courier','ShopEase Delivery');
    $tracking = (string)$read('tracking_number','SE-'.preg_replace('/[^A-Z0-9]/i','', $number));
    $productImage = (string)$read('product_image',data_get($provided,'items.0.image',''));
    $safeProductImage = str_starts_with($productImage,'/') || preg_match('~^https?://~i',$productImage) ? $productImage : '';
    $steps = [
      ['Processing','processing.png','Order being processed'],
      ['To Ship','to-ship.png','Seller is preparing your order'],
      ['In Transit','in-transit.png','Order has shipped'],
      ['Out for Delivery','out-for-delivery.png','Courier is delivering your parcel'],
      ['Delivered','delivered.png','Parcel has been delivered'],
    ];
    $backUrl = route('buyer.my-purchases', ['section'=>'purchases']);
@endphp
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Order {{ $number }} · ShopEase</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css'])
<style>
:root{--vo-maroon:#74232d;--vo-deep:#52070b;--vo-peach:#fff1ed;--vo-bg:#fcf6f4;--vo-line:#ece1dd;--vo-muted:#8c807c;--vo-good:#27ab69;--vo-text:#2a2221}
*{box-sizing:border-box}html{scroll-behavior:smooth}body.vo-page{margin:0;background:var(--vo-bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--vo-text);font-size:13px}button{font:inherit;cursor:pointer}button:disabled{cursor:not-allowed;opacity:.48}[hidden]{display:none!important}
.vo-main{padding:112px 22px 55px}.vo-container{max-width:1250px;margin:0 auto;display:grid;gap:17px}.vo-card{background:#fff;border:1px solid #eee3e0;border-radius:14px;box-shadow:0 3px 16px rgba(91,42,33,.035);overflow:hidden}.vo-topbar{padding:17px 21px;display:flex;justify-content:space-between;gap:16px;align-items:center}.vo-back{border:0;background:none;display:flex;align-items:center;gap:7px;color:#7b6463;font-weight:700;text-decoration:none}.vo-back:hover{color:var(--vo-maroon)}.vo-back svg{width:17px;height:17px}.vo-order-head{display:flex;align-items:center;flex-wrap:wrap;justify-content:flex-end;gap:11px;font-size:12px;color:#514441}.vo-order-head strong{color:var(--vo-maroon)}.vo-head-divider{height:18px;width:1px;background:#d9c9c3}.vo-status-head{color:var(--vo-maroon);font-weight:800;text-transform:uppercase;letter-spacing:.3px}.vo-demo{margin-top:6px;color:#aa8c80;font-size:10px}
.vo-progress{padding:30px 38px 34px}.vo-progress-track{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));position:relative;gap:0}.vo-progress-track:before{content:'';height:3px;position:absolute;left:10%;right:10%;top:30px;background:#e4e0de}.vo-progress-fill{position:absolute;height:3px;top:30px;left:10%;width:var(--progress-width);background:var(--vo-good);transition:width .3s}.vo-step{position:relative;z-index:1;text-align:center;min-width:0}.vo-step-icon{height:62px;width:62px;display:grid;place-items:center;margin:0 auto 13px;border-radius:50%;background:#fff;border:3px solid #dedbd9}.vo-step-icon img{height:37px;width:37px;object-fit:contain;filter:grayscale(1);opacity:.45}.vo-step.passed .vo-step-icon{border-color:var(--vo-good)}.vo-step.passed .vo-step-icon img{filter:none;opacity:1}.vo-step.current .vo-step-icon{background:#eaf9f0;border-color:var(--vo-good);box-shadow:0 0 0 5px #ecf8f0}.vo-step-title{display:block;font-size:12px;color:#817875;font-weight:600}.vo-step.passed .vo-step-title{color:#3b322e}.vo-step.current .vo-step-title{color:#13894e;font-weight:800}.vo-step-time{display:block;margin:7px auto 0;color:#aca4a0;font-size:10px;max-width:160px}.vo-step.current .vo-step-time{color:#529a74}.vo-step-desc{margin:0;font-size:11px}
.vo-actions-card{padding:0}.vo-attention{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:18px 22px;background:#fffdfb;border-bottom:1px solid var(--vo-line)}.vo-attention h3{margin:0 0 5px;font-size:13px}.vo-attention p{margin:0;color:#8c807b;line-height:1.7;font-size:11px}.vo-primary{border:1px solid var(--vo-maroon);background:var(--vo-maroon);color:#fff;font-size:12px;font-weight:700;border-radius:8px;padding:12px 18px;min-width:160px}.vo-primary:hover{background:#5c1721}.vo-action-row{display:flex;gap:10px;justify-content:flex-end;padding:12px 22px}.vo-outline{border:1px solid #e1d6d2;background:#fff;border-radius:8px;padding:10px 17px;font-weight:600;font-size:11px;color:#6c4545}.vo-outline:hover{border-color:#bd9193;color:var(--vo-maroon);background:#fffafa}.vo-warning{font-size:11px;color:#a97922;background:#fff8e9;border:1px solid #f5dfb5;padding:12px 16px;border-radius:9px}
.vo-section-title{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:19px 22px 13px}.vo-section-title h2{margin:0;font-size:16px;color:#331f22}.vo-section-title span{font-size:11px;color:#948b87}.vo-delivery{padding:0 22px 24px;display:grid;grid-template-columns:minmax(230px,.75fr) minmax(0,1.5fr);gap:30px}.vo-address{border-right:1px solid var(--vo-line);padding-right:25px}.vo-address h3{font-size:12px;margin:0 0 12px;color:#5c383b}.vo-address strong{font-size:12px}.vo-address p{color:#807571;margin:7px 0 0;font-size:11px;line-height:1.8;overflow-wrap:anywhere}.vo-timeline{padding-left:0;display:grid;gap:0}.vo-event{display:grid;grid-template-columns:24px 116px minmax(0,1fr);gap:12px;position:relative;min-height:52px}.vo-event:not(:last-child):before{content:'';position:absolute;left:11px;top:22px;bottom:-3px;background:#e1dedb;width:2px}.vo-dot{display:grid;place-items:center;height:23px;width:23px;background:#fff;border:2px solid #dedbd8;border-radius:50%;z-index:1;font-size:10px;color:#a9a2a0}.vo-event:first-child .vo-dot{background:#21b47b;color:#fff;border-color:#21b47b}.vo-event time{font-size:11px;color:#69605d;padding-top:5px;white-space:nowrap}.vo-event-copy{padding:3px 0 15px;min-width:0}.vo-event-copy strong{font-size:12px;color:#79615a}.vo-event:first-child .vo-event-copy strong{color:#12a46e}.vo-event-copy p{font-size:11px;margin:5px 0;color:#988b87;line-height:1.6}.vo-trackhead{display:flex;justify-content:space-between;gap:12px;padding:13px 22px;margin-bottom:18px;border-top:4px solid transparent;border-image:repeating-linear-gradient(90deg,#f3a4b3 0 23px,#fff 23px 30px,#83b8dc 30px 53px,#fff 53px 60px) 4;color:#826e6a;font-size:11px}.vo-trackhead strong{color:#4a3035}
.vo-shop-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:15px 22px;border-bottom:1px solid var(--vo-line)}.vo-shop-head strong{font-size:13px}.vo-product{display:flex;align-items:center;gap:16px;padding:20px 22px}.vo-product-img{height:90px;width:90px;border-radius:12px;border:1px solid #eaded8;background:#f8f0ec;flex-shrink:0;display:grid;place-items:center;overflow:hidden}.vo-product-img img{width:100%;height:100%;object-fit:cover}.vo-product-img svg{height:50px;width:50px;color:#b89591}.vo-product-copy{flex:1;min-width:0}.vo-product-copy h3{font-size:13px;margin:0 0 7px;line-height:1.5}.vo-product-copy p{font-size:11px;color:var(--vo-muted);margin:4px 0}.vo-product-price{text-align:right;color:var(--vo-maroon);font-size:15px;font-weight:800;white-space:nowrap}.vo-breakdown{background:#fffdfc;border-top:1px solid #eee6e2}.vo-money-row{display:flex;justify-content:space-between;gap:20px;padding:12px 22px;border-bottom:1px solid #f3ebe8;font-size:12px}.vo-money-row span:first-child{color:#958682}.vo-money-row strong{color:#382625}.vo-money-row.grand{background:#fff6f4;align-items:center}.vo-money-row.grand strong{font-size:22px;color:var(--vo-maroon)}.vo-cod{display:flex;gap:9px;align-items:center;padding:13px 22px;color:#83632c;background:#fff9ed;font-size:11px;border-bottom:1px solid #f2dfc0}.vo-payment{display:flex;align-items:center;justify-content:space-between;padding:15px 22px;color:#81736e;font-size:12px}.vo-payment strong{color:#42272a}.vo-success-note{color:#1b9460!important}.vo-footer-help{padding:3px 4px;color:#a29a96;font-size:11px}
.vo-modal-overlay{position:fixed;inset:0;z-index:12000;display:grid;place-items:center;padding:20px;background:rgba(34,20,23,.58);backdrop-filter:blur(2px)}.vo-modal{width:min(450px,100%);border-radius:19px;background:#fff;padding:28px;box-shadow:0 22px 66px #21070937;text-align:center}.vo-modal-icon{width:70px;height:70px;margin:0 auto 13px;display:grid;place-items:center;border-radius:50%;background:#ffe4e3;color:var(--vo-deep)}.vo-modal-icon svg{width:32px;height:32px}.vo-modal h2{margin:0 0 10px;font-size:19px}.vo-modal p{font-size:12px;color:#8b7b77;line-height:1.8}.vo-modal-buttons{display:flex;gap:10px;margin-top:25px}.vo-modal-buttons button{flex:1}.vo-toast{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:12010;background:#49212a;color:#fff;padding:12px 20px;border-radius:12px;font-size:12px;box-shadow:0 9px 25px #32101525}
@media(max-width:760px){.vo-main{padding:93px 12px 35px}.vo-topbar{align-items:flex-start;flex-direction:column}.vo-order-head{justify-content:flex-start}.vo-progress{padding:22px 10px}.vo-step-icon{width:46px;height:46px}.vo-step-icon img{width:27px;height:27px}.vo-progress-track:before,.vo-progress-fill{top:23px}.vo-step-title{font-size:10px}.vo-step-time{font-size:9px}.vo-delivery{grid-template-columns:1fr;gap:19px}.vo-address{border-right:0;border-bottom:1px solid var(--vo-line);padding-right:0;padding-bottom:14px}.vo-attention{align-items:stretch;flex-direction:column}.vo-action-row{flex-wrap:wrap}.vo-event{grid-template-columns:22px 101px minmax(0,1fr);gap:9px}.vo-event time{font-size:10px}}@media(max-width:420px){.vo-progress{overflow-x:auto}.vo-progress-track{min-width:495px}.vo-product{flex-wrap:wrap}.vo-product-price{text-align:left;margin-left:105px}}

/* Five-stage actions and rate-product modal */
.vo-receipt-buttons{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap}
.vo-receipt-buttons .vo-outline,.vo-receipt-buttons .vo-primary{min-height:42px}
.vo-cancel-modal{text-align:left}
.vo-cancel-modal .vo-modal-icon{margin:0 auto 12px}
.vo-cancel-modal h2,.vo-cancel-modal>p{text-align:center}
.vo-cancel-modal select,.vo-cancel-modal textarea{display:block;width:100%;border:1px solid #ded4d0;border-radius:9px;background:white;padding:11px 12px;font:inherit;font-size:12px;outline:none;margin:7px 0 14px}
.vo-cancel-modal select:focus,.vo-cancel-modal textarea:focus{border-color:#a57d85;box-shadow:0 0 0 3px #fcebef}
.vo-cancel-modal textarea{resize:vertical}
.vo-cancel-modal [hidden]{display:none!important}
.vo-rating-stars button:disabled{cursor:default;transform:none!important}
.vo-rating-modal textarea[readonly]{background:#fcf9f8;color:#524845}

.vo-stage-pill{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:8px 14px;background:#fff0ec;color:#7a2630;font-weight:700;font-size:11px;white-space:nowrap}
.vo-receipt-card{padding:20px 22px;display:flex;align-items:center;justify-content:space-between;gap:20px;background:#fffdfb}
.vo-receipt-card h3{margin:0 0 5px;font-size:14px;color:#3b2428}.vo-receipt-card p{margin:0;color:#8c807b;font-size:11px;line-height:1.7;max-width:740px}
.vo-rate-action{border-color:#cfa3aa;color:#832939;background:#fff7f7}
.vo-rating-modal{position:relative;text-align:left;width:min(485px,100%)}.vo-rating-modal>h2,.vo-rating-modal>.vo-rating-product,.vo-rating-modal>.vo-rating-icon{text-align:center}
.vo-rating-close{position:absolute;right:16px;top:12px;border:0;background:#f7f3f2;color:#7c6967;width:30px;height:30px;border-radius:50%;font-size:23px;line-height:1}
.vo-rating-icon{display:block;width:70px;height:70px;line-height:70px;margin:0 auto 12px;background:#ffe8e6;color:#842633;border-radius:50%;font-size:38px}
.vo-rating-product{line-height:1.55}.vo-rating-product span{color:#a29692}.vo-rating-stars{display:flex;justify-content:center;gap:5px;margin:15px 0 0}
.vo-rating-stars button{padding:0 3px;border:0;background:transparent;font-size:38px;color:#d8cfcb;line-height:1.3;transition:transform .12s;color .12s}.vo-rating-stars button:hover{transform:scale(1.13)}.vo-rating-stars button.selected{color:#e5a53c}
#voRatingHint{text-align:center;margin:5px 0 19px}.vo-rating-label{display:block;font-size:12px;font-weight:700;margin-bottom:7px}.vo-rating-label span{font-weight:400;color:#9a8d88}
.vo-rating-modal textarea{display:block;width:100%;padding:12px;border:1px solid #e1d6d2;outline:0;border-radius:9px;resize:vertical;font:inherit;font-size:12px;line-height:1.6}.vo-rating-modal textarea:focus{border-color:#a57d85;box-shadow:0 0 0 3px #fcebef}
/* Product review photo uploads */
.vo-rating-photo-block{margin-top:15px}
.vo-rating-upload{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;min-height:99px;border:1.5px dashed #dcc3c8;background:#fff9f9;border-radius:11px;color:#812b3d;cursor:pointer;text-align:center;padding:10px}
.vo-rating-upload:hover{border-color:#a33e53;background:#fff1f3}
.vo-rating-upload>span{font-size:25px;line-height:1}.vo-rating-upload strong{font-size:12px}.vo-rating-upload small{font-size:10px;color:#968487}
.vo-rating-photo-previews{display:flex;flex-wrap:wrap;gap:9px;margin-top:10px}
.vo-rating-photo-item{width:82px;height:82px;border:1px solid #eddee1;border-radius:10px;position:relative;overflow:hidden;background:#fff5f5}
.vo-rating-photo-item img{height:100%;width:100%;object-fit:cover}
.vo-rating-photo-remove{position:absolute;right:3px;top:3px;width:21px;height:21px;border:0;border-radius:50%;background:rgba(47,15,22,.85);color:#fff;font-size:16px;line-height:1;cursor:pointer}
.vo-rating-photo-view{display:block;width:100%;height:100%;padding:0;border:0;background:transparent;cursor:zoom-in}
.vo-rating-error[hidden]{display:none!important}
.vo-rating-check{display:flex;align-items:center;gap:7px;font-size:11px;color:#6c6160;margin-top:14px}.vo-rating-check input{accent-color:#7a2630}.vo-rating-error{color:#b42336!important}.vo-rating-demo{text-align:center;font-size:10px!important;margin:12px 0 0!important}
@media(max-width:650px){.vo-receipt-card{align-items:stretch;flex-direction:column}.vo-receipt-card button{width:100%}.vo-stage-pill{align-self:flex-start}.vo-modal{padding:22px}}
</style></head>
<body class="vo-page">
{{-- Exactly the same navbar include used by Buyer My Purchases --}}
@include('components.buyer.navbar', ['hideCategories' => true])
<main class="vo-main"><div class="vo-container">
  <section class="vo-card vo-topbar">
    <a class="vo-back" href="{{ $backUrl }}" id="voBack"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Back to My Purchases</a>
    <div class="vo-order-head"><span>ORDER ID: <strong>{{ $number }}</strong></span><span class="vo-head-divider"></span><span class="vo-status-head">{{ in_array($status, ['delivered','completed'], true) ? ($status === 'completed' ? 'Your order is completed' : 'Your order has been delivered') : 'Order '.$statusNames[$status] }}</span></div>
  </section>
  <section class="vo-card vo-progress" aria-label="Order progress">
    <div class="vo-progress-track" style="--progress-width:{{ ($currentStep - 1) * 20 }}%"><div class="vo-progress-fill"></div>
      @foreach($steps as $i => $step)
      @php $passed = ($i + 1) <= $currentStep; @endphp
      <div class="vo-step {{ $passed ? 'passed' : '' }} {{ $i + 1 === $currentStep ? 'current' : '' }}"><div class="vo-step-icon"><img src="{{ asset('icons/buyer/'.$step[1]) }}" alt="" loading="lazy"></div><span class="vo-step-title">{{ $step[0] }}</span><span class="vo-step-time">{{ $passed ? ($i===0 ? $placedAt->format('M d, Y · h:i A') : ($i===1 ? $placedAt->copy()->addHours(2)->format('M d, Y') : ($i===2 ? $shippedAt->format('M d, Y') : ($i===3 ? $deliveredAt->copy()->subHours(3)->format('M d, Y') : $deliveredAt->format('M d, Y'))))) : 'Awaiting update' }}</span></div>
      @endforeach
    </div>
  </section>
  <section class="vo-card vo-actions-card">
    <div class="vo-attention">
      <div>
        <h3>{{ match($status) {'processing'=>'Your order is being processed','to-ship'=>'Your parcel is being prepared','in-transit'=>'Your parcel is on the way','out-for-delivery'=>'Your parcel is out for delivery','delivered'=>'Your order has arrived!','completed'=>'Order completed',default=>'Order update'} }}</h3>
        <p>{{ match($status) {'processing'=>'We are confirming your order details. You can still cancel before the parcel enters transit.','to-ship'=>'Your seller is preparing your parcel. You can still request cancellation at this stage.','in-transit'=>'Your parcel has been shipped. Order cancellation is no longer available.','out-for-delivery'=>'Your courier is delivering your parcel. Please prepare your COD payment.','delivered'=>'Check your parcel. You can confirm receipt, request a return/refund, or rate your purchase.','completed'=>'Your product has been rated. You can review your submitted rating below.',''=>'Check your order status.',default=>'Track the progress of your order.'} }}</p>
      </div>
      <span class="vo-stage-pill">{{ $statusNames[$status] }}</span>
    </div>
    <div class="vo-action-row">
      @if(in_array($status, ['processing','to-ship'], true))<button class="vo-outline" type="button" data-vo-action="cancel">Cancel Order</button>@endif
      @if($status === 'delivered')<button class="vo-outline" type="button" data-vo-action="return">Request Return / Refund</button>@endif
      <button class="vo-outline" type="button" data-vo-action="seller">Contact Seller</button>
    </div>
  </section>
  <section class="vo-card"><div class="vo-section-title"><h2>Delivery & Tracking</h2><span>{{ $courier }} · {{ $tracking }}</span></div><div class="vo-trackhead"><span>Shipment progress and delivery updates</span><strong>{{ $courier }}</strong></div><div class="vo-delivery"><div class="vo-address"><h3>Delivery Address</h3><strong>{{ $buyerName }}</strong><p>{{ $buyerPhone }}</p><p>{{ $address }}</p></div><div class="vo-timeline" id="voTimeline">
    @php $events = [
      ['Delivered','Your parcel has been delivered.',$deliveredAt->format('M d, Y · h:i A'),5],
      ['Out for Delivery','The courier is on the way to your address.',$deliveredAt->copy()->subHours(3)->format('M d, Y · h:i A'),4],
      ['In Transit','Parcel has arrived at the local delivery hub.',$shippedAt->copy()->addHours(12)->format('M d, Y · h:i A'),3],
      ['Order Shipped','Parcel has departed from the sorting facility.',$shippedAt->format('M d, Y · h:i A'),3],
      ['Preparing for Shipment','Seller is preparing your parcel.',$placedAt->copy()->addHours(2)->format('M d, Y · h:i A'),2],
      ['Order Placed','Your order was placed successfully.',$placedAt->format('M d, Y · h:i A'),1]
    ];
    @endphp
    @foreach($events as $event)
    @if($currentStep >= $event[3])<div class="vo-event"><span class="vo-dot">{{ $loop->first ? '✓' : '·' }}</span><time>{{ $event[2] }}</time><div class="vo-event-copy"><strong>{{ $event[0] }}</strong><p>{{ $event[1] }}</p></div></div>@endif
    @endforeach
  </div></div></section>
  <section class="vo-card"><div class="vo-shop-head"><strong>▣ {{ $shop }}</strong><button class="vo-outline" data-vo-action="seller" type="button">Chat with Seller</button></div><div class="vo-product"><div class="vo-product-img">@if($safeProductImage)<img src="{{ $safeProductImage }}" alt="{{ $name }}">@else<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2"><rect x="10" y="15" width="44" height="42" rx="6"/><path d="M20 15c0-8 24-8 24 0M19 38l8-8 8 7 6-4 7 8"/><circle cx="44" cy="26" r="3"/></svg>@endif</div><div class="vo-product-copy"><h3>{{ $name }}</h3><p>Variation: {{ $variant }}</p><p>Quantity: {{ $quantity }}</p></div><span class="vo-product-price">₱{{ number_format($unitPrice,2) }}</span></div><div class="vo-breakdown"><div class="vo-money-row"><span>Merchandise Subtotal</span><strong>₱{{ number_format($subtotal,2) }}</strong></div><div class="vo-money-row"><span>Shipping Fee</span><strong>₱{{ number_format($shipping,2) }}</strong></div><div class="vo-money-row"><span>Shipping Discount</span><strong>−₱{{ number_format($shippingDiscount,2) }}</strong></div><div class="vo-money-row"><span>Voucher Discount</span><strong>−₱{{ number_format($voucher,2) }}</strong></div><div class="vo-money-row grand"><span>Order Total</span><strong>₱{{ number_format($total,2) }}</strong></div></div><div class="vo-cod">◉ <span>Cash on Delivery (COD) · Pay ₱{{ number_format($total,2) }} when you receive your parcel.</span></div><div class="vo-payment"><span>Payment Method</span><strong>Cash on Delivery</strong></div></section>
  @if(in_array($status, ['delivered','completed'], true))
  <section class="vo-card vo-receipt-card">
    <div><h3>Confirm your delivery</h3><p>Received everything in good condition? Confirm your order after checking your items. Return/refund requests remain a separate action.</p></div>
    <div class="vo-receipt-buttons">
      @if($status === 'delivered')<button type="button" class="vo-primary" id="voOrderReceived" data-vo-action="received">Order Received</button>@endif
      <button type="button" class="vo-outline vo-rate-action" id="voRateAction" data-vo-action="rate">☆ Rate Product</button>
    </div>
  </section>
  @endif
  <div class="vo-footer-help">{{ $isDemo ? 'Preview using sample order information. Real tracking, delivery confirmation, refunds and ratings require backend integration.' : 'Order data provided by the application. Carrier tracking actions require backend support.' }}</div>
</div></main>
@include('components.buyer.floating-chat')
<div class="vo-modal-overlay" id="voRateModal" hidden>
  <form class="vo-modal vo-rating-modal" id="voRatingForm" role="dialog" aria-modal="true" aria-labelledby="voRatingHeading">
    <button type="button" class="vo-rating-close" id="voRatingClose" aria-label="Close rating modal">×</button>
    <span class="vo-rating-icon">☆</span>
    <h2 id="voRatingHeading">Rate Your Purchase</h2>
    <p class="vo-rating-product">{{ $name }} <span>· {{ $shop }}</span></p>
    <div class="vo-rating-stars" id="voRatingStars" role="group" aria-label="Product rating">
      @for($star=1;$star<=5;$star++)<button type="button" data-vo-star="{{ $star }}" aria-label="Rate {{ $star }} {{ $star===1?'star':'stars' }}" aria-pressed="false">☆</button>@endfor
    </div>
    <p id="voRatingHint">Tap a star to rate your product</p>
    <label class="vo-rating-label" for="voRatingReview">Your review <span>(optional)</span></label>
    <textarea id="voRatingReview" maxlength="500" rows="4" placeholder="Share your experience with this product..."></textarea>
    <div class="vo-rating-photo-block">
      <label class="vo-rating-label" for="voRatingPhotos">Product photos <span>(optional · up to 3 photos)</span></label>
      <label class="vo-rating-upload" id="voRatingUploadLabel" for="voRatingPhotos"><span aria-hidden="true">＋</span><strong>Add product photos</strong><small>JPG, PNG, WEBP · max 1 MB each (demo storage)</small></label>
      <input id="voRatingPhotos" type="file" accept="image/jpeg,image/png,image/webp" multiple hidden>
      <div id="voRatingPhotoPreviews" class="vo-rating-photo-previews" aria-live="polite"></div>
    </div>
    <label class="vo-rating-check"><input type="checkbox" id="voRatingAnonymous"> Post anonymously</label>
    <p class="vo-rating-error" id="voRatingError" role="alert" hidden>Please choose a star rating first.</p>
    <div class="vo-modal-buttons"><button type="button" class="vo-outline" id="voRatingCancel">Cancel</button><button type="submit" class="vo-primary" id="voRatingSubmit" disabled>Submit Rating</button></div>
    <p class="vo-rating-demo">Preview only — rating is stored in this browser, not submitted to ShopEase.</p>
  </form>
</div>
<div class="vo-modal-overlay" id="voCancelModal" hidden role="presentation">
  <form class="vo-modal vo-cancel-modal" id="voCancelForm" role="dialog" aria-modal="true" aria-labelledby="voCancelTitle">
    <button type="button" class="vo-rating-close" id="voCancelClose" aria-label="Close cancel order modal">×</button>
    <div class="vo-modal-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg></div>
    <h2 id="voCancelTitle">Cancel Order</h2>
    <p>Please tell us why you want to cancel order <strong>{{ $number }}</strong>.</p>
    <label class="vo-rating-label" for="voCancelReason">Cancellation reason <span>*</span></label>
    <select id="voCancelReason" required>
      <option value="">Select a reason</option>
      <option value="changed-mind">I changed my mind</option>
      <option value="wrong-details">Wrong product, variation, or quantity</option>
      <option value="wrong-address">Incorrect delivery address</option>
      <option value="duplicate">I placed a duplicate order</option>
      <option value="found-better">Found a better option</option>
      <option value="delivery-time">Delivery will take too long</option>
      <option value="other">Other reason</option>
    </select>
    <div id="voCancelOtherWrap" hidden><label class="vo-rating-label" for="voCancelOther">Please specify <span>*</span></label><textarea id="voCancelOther" rows="3" maxlength="300" placeholder="Enter your reason"></textarea></div>
    <p id="voCancelError" class="vo-rating-error" role="alert" hidden></p>
    <p class="vo-rating-demo">Demo only — no cancellation request will be sent.</p>
    <div class="vo-modal-buttons"><button type="button" class="vo-outline" id="voCancelKeep">Keep Order</button><button type="submit" class="vo-primary">Submit Cancellation</button></div>
  </form>
</div>
<div class="vo-modal-overlay" id="voModal" hidden role="presentation"><div class="vo-modal" role="dialog" aria-modal="true" aria-labelledby="voModalTitle"><div class="vo-modal-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5m0 4h.01"/></svg></div><h2 id="voModalTitle">Action unavailable</h2><p id="voModalText">This action requires backend support.</p><div class="vo-modal-buttons"><button class="vo-primary" id="voModalOkay" type="button">Okay, Got it</button></div></div></div>
<script>
(() => {
 const orderId = @json($number);
 const demo = @json($isDemo);
 const status = @json($status);
 const modal=document.getElementById('voModal'), title=document.getElementById('voModalTitle'), copy=document.getElementById('voModalText');
 const open=(t,m)=>{title.textContent=t;copy.textContent=m;modal.hidden=false;document.getElementById('voModalOkay').focus();};
 const close=()=>{modal.hidden=true;};
 document.getElementById('voModalOkay').addEventListener('click',close);
 modal.addEventListener('click',e=>{if(e.target===modal)close();});
 const rateModal=document.getElementById('voRateModal');
 const form=document.getElementById('voRatingForm');
 const ratingButtons=[...document.querySelectorAll('[data-vo-star]')];
 const ratingSubmit=document.getElementById('voRatingSubmit');
 let rating=0;
 let savedRating=null;
 let ratingPhotos=[];
 const ratingPhotoInput=document.getElementById('voRatingPhotos');
 const ratingPhotoPreviews=document.getElementById('voRatingPhotoPreviews');
 const ratingUploadLabel=document.getElementById('voRatingUploadLabel');
 const ratingError=document.getElementById('voRatingError');
 const ratingKey='shopease-demo-rating:'+orderId;
 const receivedKey='shopease-demo-received:'+orderId;
 const labels=['','Very poor','Poor','Okay','Good','Excellent'];
 function renderRatingPhotos(locked=false){
   ratingPhotoPreviews.replaceChildren();
   ratingUploadLabel.hidden=locked || ratingPhotos.length>=3;
   ratingPhotoInput.disabled=locked;
   ratingPhotos.forEach((src,index)=>{
     const tile=document.createElement('div');tile.className='vo-rating-photo-item';
     const view=document.createElement('button');view.type='button';view.className='vo-rating-photo-view';view.title='View product photo';
     const img=document.createElement('img');img.src=src;img.alt='Product review photo '+(index+1);view.append(img);
     view.addEventListener('click',()=>window.open(src,'_blank','noopener,noreferrer'));
     tile.append(view);
     if(!locked){const remove=document.createElement('button');remove.type='button';remove.className='vo-rating-photo-remove';remove.textContent='×';remove.setAttribute('aria-label','Remove photo '+(index+1));remove.addEventListener('click',()=>{ratingPhotos.splice(index,1);ratingError.hidden=true;renderRatingPhotos(false);});tile.append(remove);}
     ratingPhotoPreviews.append(tile);
   });
 }
 ratingPhotoInput.addEventListener('change',async()=>{
   const chosen=[...ratingPhotoInput.files];ratingPhotoInput.value='';
   if(chosen.length+ratingPhotos.length>3){ratingError.textContent='You can upload a maximum of 3 product photos.';ratingError.hidden=false;return;}
   for(const file of chosen){
     if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>1024*1024){ratingError.textContent='Each photo must be JPG, PNG or WebP and not exceed 1 MB.';ratingError.hidden=false;return;}
   }
   try{
     const photos=await Promise.all(chosen.map(file=>new Promise((resolve,reject)=>{const reader=new FileReader();reader.onload=()=>resolve(String(reader.result));reader.onerror=reject;reader.readAsDataURL(file);})));
     ratingPhotos.push(...photos);ratingError.hidden=true;renderRatingPhotos(false);
   }catch(_){ratingError.textContent='Unable to preview your photos. Please try again.';ratingError.hidden=false;}
 });
 function showRating(viewOnly=false){
   const locked=viewOnly || status==='completed' || !!savedRating;
   document.getElementById('voRatingHeading').textContent=locked?'Your Product Rating':'Rate Your Purchase';
   document.getElementById('voRatingReview').readOnly=locked;
   document.getElementById('voRatingAnonymous').disabled=locked;
   ratingButtons.forEach(btn=>btn.disabled=locked);
   ratingSubmit.hidden=locked;
   if(locked && savedRating)ratingPhotos=Array.isArray(savedRating.photos)?savedRating.photos.slice(0,3):[];
   renderRatingPhotos(locked);
   document.getElementById('voRatingCancel').textContent=locked?'Close':'Cancel';
   rateModal.hidden=false;
   (locked?document.getElementById('voRatingClose'):ratingButtons[rating-1]||ratingButtons[0])?.focus();
 }
 function hideRating(){rateModal.hidden=true;}
 function paintStars(){
   ratingButtons.forEach(btn=>{const n=Number(btn.dataset.voStar), selected=n<=rating;btn.textContent=selected?'★':'☆';btn.classList.toggle('selected',selected);btn.setAttribute('aria-pressed',String(n===rating));});
   document.getElementById('voRatingHint').textContent=rating ? `${rating}/5 · ${labels[rating]}` : 'Tap a star to rate your product';
   ratingSubmit.disabled=rating===0;
   document.getElementById('voRatingError').hidden=true;
 }
 ratingButtons.forEach(btn=>btn.addEventListener('click',()=>{rating=Number(btn.dataset.voStar);paintStars();}));
 document.getElementById('voRatingClose').addEventListener('click',hideRating);
 document.getElementById('voRatingCancel').addEventListener('click',hideRating);
 rateModal.addEventListener('click',e=>{if(e.target===rateModal)hideRating();});
 try {const saved=JSON.parse(localStorage.getItem(ratingKey)||'null');if(saved?.stars>=1&&saved.stars<=5){savedRating=saved;rating=saved.stars;ratingPhotos=Array.isArray(saved.photos)?saved.photos.slice(0,3):[];document.getElementById('voRatingReview').value=saved.review||'';document.getElementById('voRatingAnonymous').checked=!!saved.anonymous;}}catch(_){}
 if(status==='completed' && !savedRating && demo){
   savedRating={stars:5,review:'The product arrived in good condition and met my expectations. (Sample rating)',anonymous:false};
   rating=savedRating.stars;
   document.getElementById('voRatingReview').value=savedRating.review;
   document.getElementById('voRatingAnonymous').checked=false;
 }
 paintStars();
 const rateAction=document.getElementById('voRateAction');
 function refreshRateAction(){
   const viewed=status==='completed'||!!savedRating;
   if(rateAction){rateAction.textContent=viewed?'★ View Rating':'☆ Rate Product';rateAction.dataset.voAction=viewed?'view-rating':'rate';}
   if(viewed){
     const pill=document.querySelector('.vo-stage-pill');if(pill)pill.textContent='Completed';
     const headline=document.querySelector('.vo-status-head');if(headline)headline.textContent='Your order is completed';
   }
 }
 refreshRateAction();
 const receivedButton=document.getElementById('voOrderReceived');
 function refreshReceived(){if(receivedButton && localStorage.getItem(receivedKey)==='1'){receivedButton.textContent='✓ Order Received';receivedButton.disabled=true;}}
 refreshReceived();
 form.addEventListener('submit',event=>{
   event.preventDefault();if(!rating){document.getElementById('voRatingError').hidden=false;return;}
   savedRating={stars:rating,review:document.getElementById('voRatingReview').value.trim(),anonymous:document.getElementById('voRatingAnonymous').checked,photos:ratingPhotos.slice()};
   try{localStorage.setItem(ratingKey,JSON.stringify(savedRating));}catch(_){ratingError.textContent='Photos exceed browser storage. Please remove some photos and retry.';ratingError.hidden=false;return;}refreshRateAction();
   hideRating();open('Rating Saved (Demo)','Your '+rating+'-star review was saved in this browser only. It has not been submitted to ShopEase.');
 });
 const cancelModal=document.getElementById('voCancelModal');
 const cancelForm=document.getElementById('voCancelForm');
 const cancelReason=document.getElementById('voCancelReason');
 const cancelOther=document.getElementById('voCancelOther');
 const cancelOtherWrap=document.getElementById('voCancelOtherWrap');
 const cancelError=document.getElementById('voCancelError');
 function showCancel(){cancelForm.reset();cancelOtherWrap.hidden=true;cancelError.hidden=true;cancelModal.hidden=false;cancelReason.focus();}
 function hideCancel(){cancelModal.hidden=true;}
 cancelReason.addEventListener('change',()=>{cancelOtherWrap.hidden=cancelReason.value!=='other';cancelError.hidden=true;});
 document.getElementById('voCancelClose').addEventListener('click',hideCancel);
 document.getElementById('voCancelKeep').addEventListener('click',hideCancel);
 cancelModal.addEventListener('click',e=>{if(e.target===cancelModal)hideCancel();});
 cancelForm.addEventListener('submit',e=>{e.preventDefault();const reason=cancelReason.value;const other=cancelOther.value.trim();if(!reason||(reason==='other'&&!other)){cancelError.textContent=!reason?'Please select a cancellation reason.':'Please explain your cancellation reason.';cancelError.hidden=false;return;}hideCancel();open('Cancellation Request Preview','Reason: '+(reason==='other'?other:cancelReason.options[cancelReason.selectedIndex].text)+'. This is a UI demo only. Your order was not cancelled.');});
 document.querySelectorAll('[data-vo-action]').forEach(button=>button.addEventListener('click',()=>{
   switch(button.dataset.voAction){
    case 'received':
      if(status!=='delivered')return;
      localStorage.setItem(receivedKey,'1');refreshReceived();
      open('Order Received (Demo)','You confirmed receipt for this demo order. No actual order status was changed on the server.');break;
    case 'rate':if(status==='delivered' || status==='completed')showRating(false);break;
    case 'view-rating':if(status==='delivered' || status==='completed')showRating(true);break;
    case 'return':if(status==='delivered')open('Request Return / Refund','Return/refund requests are available after delivery. This is a demo preview; no request was submitted.');break;
    case 'cancel':if(['processing','to-ship'].includes(status))showCancel();break;
    case 'seller':open('Contact Seller','Seller messaging for this order requires linking the page to its database order ID. No message was sent.');break;
   }
 }));
 document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!cancelModal.hidden)hideCancel();else if(!rateModal.hidden)hideRating();else if(!modal.hidden)close();}});
})();
</script>
</body></html>
