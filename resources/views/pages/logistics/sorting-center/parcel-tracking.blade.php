{{-- Sorting Center Parcel Tracking. Pass already scoped $sortingCenterParcels / $sortingCenterReturns from controller. --}}
@php
    // Resolve the center ONLY from the authenticated account, never a query parameter.
    $scAssignedCenter = \App\Models\Logistics\LogisticsBranch::query()
        ->where('user_id', auth()->id())
        ->first();
    $scCenterId = $scAssignedCenter?->id;

    $scSourceParcels = $sortingCenterParcels ?? $logisticsParcels ?? [];
    $scSourceReturns = $sortingCenterReturns ?? $logisticsReturns ?? [];
    $scSourceLocations = $logisticsLocations ?? [];

    // Delivery hubs are resolved on EVERY page request from the authenticated
    // sorting center. Hubs added by Logistics become visible after refresh.
    $scHubIds = [];
    $scDbHubs = collect();
    if ($scAssignedCenter) {
        $hubModel = new \App\Models\Logistics\LogisticsDeliveryHub();
        $hubTable = $hubModel->getTable();
        foreach (['logistics_branch_id', 'branch_id', 'sorting_center_id'] as $parentColumn) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($hubTable, $parentColumn)) {
                $scDbHubs = $hubModel->newQuery()
                    ->where($parentColumn, $scAssignedCenter->id)->get()
                    ->filter(fn ($hub) => !in_array(strtolower((string) data_get($hub, 'status', 'active')), ['inactive', 'archived', 'deleted'], true)
                        && empty(data_get($hub, 'archived_at')))->values();
                $scHubIds = $scDbHubs->pluck('id')->map(fn ($id) => (string) $id)->all();
                break;
            }
        }
    }

    // Fail closed: if a record has no provable current sorting-center / hub association,
    // it is NOT exposed in the view or embedded JSON. Historical origin alone is not sufficient.
    $scInScope = function ($record) use ($scCenterId, $scHubIds) {
        if (!$scCenterId) return false;
        $get = fn($key) => data_get($record, $key);
        $currentCenter = $get('current_sorting_center_id') ?? $get('sorting_center_id');
        $currentHub = $get('current_delivery_hub_id') ?? $get('delivery_hub_id');
        $awayCenter = $get('transferred_to_sorting_center_id') ?? $get('next_sorting_center_id');
        $transferState = strtolower((string) ($get('transfer_status') ?? ''));
        $transferComplete = in_array($transferState, ['departed','transferred','received','completed'], true)
            || (bool) $get('transferred_out_at');
        // Once a confirmed inter-center transfer is complete, its previous center loses visibility.
        if ($transferComplete && $awayCenter && (string)$awayCenter !== (string)$scCenterId) return false;
        if ($currentCenter !== null && (string)$currentCenter !== (string)$scCenterId) return false;
        if ($currentHub !== null) return in_array((string)$currentHub, $scHubIds, true);
        return $currentCenter !== null && (string)$currentCenter === (string)$scCenterId;
    };

    $scParcels = collect($scSourceParcels)->filter($scInScope)->values();
    $scVisibleParcelIds = $scParcels->map(fn ($p) => (string)(data_get($p, 'id') ?? ''))->filter()->all();
    $scVisibleTracking = $scParcels->map(fn ($p) => (string)(data_get($p, 'tracking_number') ?? data_get($p, 'tracking_no') ?? ''))->filter()->all();
    $scReturns = collect($scSourceReturns)->filter(function ($r) use ($scInScope, $scVisibleParcelIds, $scVisibleTracking) {
        $shipmentId = data_get($r,'shipment_id') ?? data_get($r,'parcel_id');
        $tracking = data_get($r,'tracking_number') ?? data_get($r,'tracking_no');
        if ($shipmentId && in_array((string)$shipmentId, $scVisibleParcelIds, true)) return true;
        if ($tracking && in_array((string)$tracking, $scVisibleTracking, true)) return true;
        return $scInScope($r);
    })->values();
    // Prefer actual DB hubs, not only a possibly stale logisticsLocations payload.
    $scAllowedHubs = $scDbHubs->map(fn ($hub) => [
        'id' => $hub->id,
        'name' => data_get($hub, 'name') ?? data_get($hub, 'hub_name') ?? ('Delivery Hub #' . $hub->id),
    ])->all();

    // Preview data is ONLY used if there is no real parcel dataset and no
    // assigned DB hub, avoiding leakage/mixture of fake and actual records.
    $scDemoMode = collect($scSourceParcels)->isEmpty();
    if ($scDemoMode) {
        $scPreviewHubs = [
            ['id'=>'preview-hub-1','name'=>'Santa Cruz Delivery Hub'],
            ['id'=>'preview-hub-2','name'=>'Calamba Delivery Hub'],
            ['id'=>'preview-hub-3','name'=>'San Pablo Delivery Hub'],
        ];
        $scAllowedHubs = $scDbHubs->isNotEmpty() ? $scAllowedHubs : $scPreviewHubs;
        $demoHub = fn ($index) => $scAllowedHubs[$index % count($scAllowedHubs)];
        $sampleName = $scAssignedCenter?->name ?? 'Sorting Center (UI Preview)';
        $sampleParcels = [];
        $sampleRows = [
            ['SHP-DEMO-101','ORD-2026-101','Maria Santos','Wireless Earbuds','in-transit',$demoHub(0)['id'],$demoHub(0)['name']],
            ['SHP-DEMO-102','ORD-2026-102','John Reyes','Laptop Stand','out-for-delivery',$demoHub(1)['id'],$demoHub(1)['name']],
            ['SHP-DEMO-103','ORD-2026-103','Angela Cruz','Desk Lamp','delivered',$demoHub(2)['id'],$demoHub(2)['name']],
            ['SHP-DEMO-104','ORD-2026-104','Kevin Lim','Travel Backpack','in-transit',null,$sampleName],
            ['SHP-DEMO-105','ORD-2026-105','Nicole Ramos','Phone Case','processing',$demoHub(0)['id'],$demoHub(0)['name']],
            ['SHP-DEMO-106','ORD-2026-106','Paolo Mendoza','Running Shoes','delivered',$demoHub(1)['id'],$demoHub(1)['name']],
        ];
        foreach ($sampleRows as $index=>$row) {
            [$tracking,$order,$buyer,$product,$status,$hubId,$location] = $row;
            $sampleParcels[] = [
                'id'=>'preview-parcel-'.($index+1), 'order_number'=>$order,
                'tracking_number'=>$tracking,'buyer_name'=>$buyer,
                'buyer_email'=>'preview@example.test','buyer_phone'=>'09XX XXX XXXX',
                'seller_name'=>'ShopEase Demo Store','seller_email'=>'store@example.test',
                'product_name'=>$product,'items'=>[['product_name'=>$product,'quantity'=>1,'product_price'=>499 + $index*150]],
                'shipping_status'=>$status,'current_location'=>$location,
                'current_delivery_hub_id'=>$hubId,'current_sorting_center_id'=>$scCenterId,
                'payment_method'=>'Cash on Delivery','total_amount'=>499 + $index*150,
                'ordered_at'=>'2026-10-26 09:00:00','estimated_delivery'=>'2026-10-31 18:00:00',
                'tracking_history'=>[['status'=>'picked_up','timestamp'=>'2026-10-27 11:20:00','location'=>$sampleName],['status'=>$status,'timestamp'=>'2026-10-29 08:15:00','location'=>$location]],
                'notes'=>'UI preview parcel only — not a real shipment.',
            ];
        }
        $scParcels = collect($sampleParcels);
        $scReturns = collect([
            ['id'=>'preview-return-1','order_id'=>'ORD-2026-103','tracking_number'=>'SHP-DEMO-103',
             'buyer_name'=>'Angela Cruz','seller_name'=>'ShopEase Demo Store','product_name'=>'Desk Lamp',
             'status'=>'pending','shipping_status'=>'delivered','reason'=>'Item arrived damaged',
             'solution'=>'Replacement','requested_at'=>'2026-10-29 10:30:00','return_items'=>[['product_name'=>'Desk Lamp','quantity'=>1,'product_price'=>799]]],
        ]);
    }
    $scLocationData = [
        'sorting_centers' => $scAssignedCenter ? [['id'=>$scAssignedCenter->id,'name'=>$scAssignedCenter->name]] : [],
        'delivery_hubs' => $scAllowedHubs,
    ];
@endphp
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>Parcel Tracking — ShopEase Sorting Center</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"><style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
:root{--lg-maroon:#7B1B1B;--lg-dark:#52070B;--lg-peach:#FFE6DE;--lg-bg:#FCF8F6;--lg-line:#F0E9E6;--lg-ink:#17120F;--lg-muted:#8C8784}
*{box-sizing:border-box}body.lg-body{margin:0;background:var(--lg-bg);font-family:Poppins,sans-serif;color:var(--lg-ink)}button,input,select{font-family:inherit}button{cursor:pointer}a{text-decoration:none}body.lg-modal-open{overflow:hidden}
.lg-sidebar{position:fixed;left:0;top:0;bottom:0;width:260px;z-index:50;background:linear-gradient(155deg,#7B1B1B 0%,#52070B 80%);border-top-right-radius:58px;padding:20px 14px 18px;display:flex;flex-direction:column;transition:width .3s ease;box-shadow:4px 0 18px #52070b12;overflow:hidden;color:white}
.lg-sidebar-logo{height:74px;display:flex;align-items:center;justify-content:center;gap:7px;margin-bottom:20px;color:white;white-space:nowrap}.lg-sidebar-logo img{max-width:115px;max-height:53px;object-fit:contain}.lg-sidebar-logo>span{font-size:11px;letter-spacing:1.1px;font-weight:700}.lg-sidebar-logo small{display:block;font-size:8px;font-weight:400;letter-spacing:1.5px;opacity:.65}
.lg-sidebar-menu{overflow:auto;scrollbar-width:thin;flex:1}.lg-nav-category{margin:15px 13px 9px;font-size:10px;letter-spacing:1.4px;color:#f5c8c7;font-weight:600}.lg-nav-link{height:43px;width:100%;display:flex;align-items:center;gap:12px;padding:0 13px;border:0;background:none;color:#fff;border-radius:999px;font-size:12px;font-weight:500;text-align:left;margin:3px 0;white-space:nowrap;transition:background .2s}.lg-nav-link:hover{background:#ffffff1b}.lg-nav-link.active{background:#ffffff30}.lg-nav-unavailable{opacity:.64;cursor:default;user-select:none}.lg-nav-unavailable:hover{background:none}.lg-nav-link img{width:20px;height:20px;object-fit:contain;flex:0 0 20px}.lg-sidebar-bottom{border-top:1px solid #ffffff25;padding:14px 11px 3px;white-space:nowrap;font-size:11px}.lg-sidebar-bottom small{display:block;color:#ffc6bc;font-size:10px;margin-top:3px}
.lg-navbar{position:fixed;top:0;left:0;right:0;height:88px;background:var(--lg-peach);z-index:35;display:flex;align-items:center;justify-content:space-between;padding:0 26px 0 288px;transition:padding-left .3s ease;gap:15px}.lg-nav-left,.lg-nav-right{display:flex;align-items:center;gap:22px}.lg-nav-left h2{font-size:21px;font-weight:700;margin:0;white-space:nowrap}.lg-icon-button{border:0;background:transparent;display:grid;place-items:center;width:30px;height:34px;color:#52070B;flex-shrink:0}.lg-icon-button svg{width:24px;height:24px}.lg-icon-button img{width:24px;height:26px;object-fit:contain}.lg-navbar-caption{font-size:12px;color:#815d57}.lg-profile-wrap{position:relative}.lg-profile-btn{border:1px solid #ebe0dc;background:white;border-radius:10px;padding:6px 10px;display:flex;align-items:center;gap:9px;box-shadow:0 2px 8px #0000000b;color:#29211f;text-align:left;min-width:190px}.lg-profile-btn strong{display:block;font-size:12px;font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.lg-profile-btn small{font-size:10px;color:#a19b98}.lg-profile-chevron{margin-left:auto}.lg-profile-menu{position:absolute;right:0;top:53px;width:214px;border:1px solid var(--lg-line);box-shadow:0 12px 30px #0002;border-radius:13px;background:white;padding:8px;z-index:100}.lg-profile-menu[hidden]{display:none}.lg-profile-menu>span{display:block;font-size:10px;color:var(--lg-muted);padding:9px}.lg-profile-menu a,.lg-profile-menu button{display:block;width:100%;text-align:left;border:0;background:transparent;padding:10px;border-radius:7px;color:var(--lg-ink);font-size:12px}.lg-profile-menu a:hover,.lg-profile-menu button:hover{background:#FFF2EE}
.lg-main{margin-left:260px;padding:114px 25px 36px;min-height:100vh;transition:margin-left .3s ease;max-width:none}.lg-body.lg-collapsed .lg-sidebar{width:80px}.lg-body.lg-collapsed .lg-sidebar-logo>span,.lg-body.lg-collapsed .lg-nav-category,.lg-body.lg-collapsed .lg-nav-link span,.lg-body.lg-collapsed .lg-sidebar-bottom{display:none}.lg-body.lg-collapsed .lg-sidebar-logo img{max-width:60px}.lg-body.lg-collapsed .lg-nav-link{justify-content:center;padding:0}.lg-body.lg-collapsed .lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-navbar{padding-left:108px}
.lg-welcome{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:16px}.lg-overline{font-size:10px;letter-spacing:1.3px;color:#9b1239;font-weight:700;margin:0 0 5px}.lg-welcome h1{margin:0;font-weight:650;font-size:24px}.lg-welcome p:not(.lg-overline){font-size:12px;color:var(--lg-muted);line-height:1.7;margin:4px 0 0}.lg-welcome-actions{display:flex;align-items:center;gap:11px;flex-shrink:0}.lg-date{padding:11px 13px;background:white;border:1px solid var(--lg-line);border-radius:9px;color:#665b56;font-size:11px;white-space:nowrap}.lg-btn{border-radius:8px;padding:10px 15px;min-height:37px;font-weight:600;font-size:11px;display:inline-flex;justify-content:center;align-items:center;gap:7px;transition:background .2s,box-shadow .2s;white-space:nowrap}.lg-btn-primary{color:white;border:1px solid #7B1B1B;background:#7B1B1B}.lg-btn-primary:hover{background:#621515}.lg-btn-outline{color:#7B1B1B;background:white;border:1px solid #d9bfc0}.lg-btn-outline:hover{background:#fff6f5}
.lg-notice{display:flex;gap:10px;align-items:start;margin-bottom:19px;font-size:11px;line-height:1.7;color:#80572e;padding:11px 14px;border:1px solid #f3e4d5;background:#fff9f2;border-radius:10px}.lg-notice strong{font-weight:600}.lg-notice-dot{width:8px;height:8px;background:#cd9250;flex:0 0 8px;border-radius:50%;margin-top:5px}
.lg-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin-bottom:20px}.lg-stat{display:flex;align-items:center;gap:12px;padding:17px 16px;min-height:91px;background:white;border:1px solid var(--lg-line);border-radius:16px;box-shadow:0 2px 12px rgba(42,20,15,.05)}.lg-stat-icon{width:40px;height:40px;object-fit:contain;flex-shrink:0}.lg-stat strong{display:block;font-size:23px;line-height:1.2;font-weight:600;white-space:nowrap}.lg-stat span{display:block;font-size:11px;color:var(--lg-muted);margin-top:5px}
.lg-main-grid{display:grid;grid-template-columns:minmax(0,1.66fr) minmax(320px,1fr);gap:18px;align-items:start}.lg-side-stack{display:grid;gap:18px}.lg-panel{background:white;border:1px solid var(--lg-line);border-radius:16px;box-shadow:0 1px 4px #00000008;min-width:0;overflow:hidden}.lg-panel-header{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:19px 19px 15px}.lg-panel-header h2{font-size:15px;font-weight:600;margin:0}.lg-panel-header p{font-size:11px;line-height:1.6;color:var(--lg-muted);margin:4px 0 0}.lg-panel-icon{width:30px;height:30px;object-fit:contain}.lg-toolbar{display:flex;gap:9px;padding:0 19px 16px;flex-wrap:wrap}.lg-toolbar select,.lg-search-box{height:37px;background:white;border:1px solid #D9D6D4;border-radius:8px;font-size:11px;color:#68615e}.lg-toolbar select{padding:0 8px;max-width:170px}.lg-search-box{display:flex;gap:6px;align-items:center;padding:0 10px;flex:1;min-width:190px}.lg-search-box span{font-size:19px}.lg-search-box input{width:100%;outline:none;border:0;background:none;font-size:11px}.lg-table-scroll{overflow:auto}.lg-table{width:100%;border-collapse:collapse;min-width:625px}.lg-table th{font-weight:500;font-size:11px;color:var(--lg-muted);padding:13px 15px;white-space:nowrap;text-align:left;border-bottom:1px solid #E5E7EB}.lg-table td{font-size:11px;padding:14px 15px;border-bottom:1px solid #f0edef;color:var(--lg-ink);vertical-align:middle}.lg-table tr:hover td{background:#FFFAF8}.lg-table tr:last-child td{border-bottom:0}.lg-table td strong{display:block;font-size:11px;font-weight:600}.lg-table td small{display:block;color:#9c9692;font-size:10px;margin-top:4px}.lg-tag{display:inline-block;border-radius:20px;padding:5px 9px;font-size:10px;white-space:nowrap}.lg-tag.sorting{color:#9b1239;background:#fce9ee}.lg-tag.delivery{color:#765422;background:#fff0d9}.lg-tag.active{color:#2d7745;background:#e9f7ec}.lg-table-empty{text-align:center;padding:38px 14px!important;color:#8c8784!important}.lg-table-foot{display:flex;justify-content:space-between;color:var(--lg-muted);font-size:10px;padding:13px 18px;border-top:1px solid #ece9e7;gap:10px}
.lg-scan-form{display:grid;gap:8px;padding:0 19px 20px}.lg-scan-form label,.lg-field label{font-size:11px;font-weight:500;color:#2b2624}.lg-scan-form label span,.lg-field label span{color:#d32443}.lg-scan-form input,.lg-scan-form select,.lg-field input,.lg-field select{height:39px;border:1px solid #D9D6D4;border-radius:8px;background:#fff;padding:0 11px;font-size:11px;color:#302b29;min-width:0;outline:none}.lg-scan-form input:focus,.lg-scan-form select:focus,.lg-field input:focus,.lg-field select:focus{border-color:#9b1239;box-shadow:0 0 0 2px #9b123916}.lg-scan-form .lg-full{margin-top:8px;width:100%}.lg-help{font-size:10px;color:#9b9290;line-height:1.65;margin:6px 0 0}.lg-flow{padding-bottom:15px}.lg-flow-step{display:flex;align-items:flex-start;gap:13px;margin:0 19px;padding:13px 0;border-top:1px solid #f2ebeb}.lg-flow-step>span{display:grid;place-items:center;color:#9b1239;background:#fce9ee;border-radius:8px;width:30px;height:30px;flex:0 0 30px;font-size:11px;font-weight:700}.lg-flow-step b{font-size:11px;font-weight:600;display:block;margin-top:1px}.lg-flow-step small{font-size:10px;color:var(--lg-muted);display:block;line-height:1.6;margin-top:3px}
.lg-lower-grid{display:grid;grid-template-columns:minmax(0,1.66fr) minmax(320px,1fr);gap:18px;align-items:stretch;margin-top:18px}.lg-activity-table{min-width:580px}.lg-soft-pill{border-radius:20px;background:#fce9ee;color:#9b1239;padding:6px 10px;font-size:10px;white-space:nowrap}.lg-earnings-total{margin:0 19px 15px;background:#fff9f7;border:1px solid #f3e5e0;border-radius:12px;padding:15px}.lg-earnings-total small,.lg-earnings-total span{display:block;color:var(--lg-muted);font-size:10px}.lg-earnings-total strong{display:block;font-size:24px;margin:4px 0;color:#7B1B1B}.lg-earnings-row{display:flex;justify-content:space-between;gap:9px;border-bottom:1px solid #f3eded;padding:11px 0;margin:0 19px;font-size:11px}.lg-earnings-row span{color:var(--lg-muted)}.lg-earnings-row b{font-weight:600}.lg-lower-grid .lg-help{margin:12px 19px 17px}.lg-announcements{margin-top:18px;padding-bottom:15px}.lg-announcements>*:not(.lg-panel-header){margin:0 18px}
.lg-modal{position:fixed;inset:0;background:#1a0b0b88;display:grid;place-items:center;z-index:200;padding:15px}.lg-modal[hidden]{display:none}.lg-modal-card{background:white;border-radius:17px;max-width:660px;width:100%;max-height:95vh;overflow:auto;box-shadow:0 24px 70px #0003;padding:24px}.lg-modal-header{display:flex;justify-content:space-between;gap:20px;margin-bottom:23px}.lg-modal-header h2{font-size:19px;margin:0}.lg-modal-header p{color:var(--lg-muted);font-size:11px;margin:5px 0}.lg-close{background:#f8f1ef;border:0;border-radius:50%;width:32px;height:32px;font-size:23px;color:#625452}.lg-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.lg-field{display:grid;gap:7px}.lg-field small{font-size:10px;color:#9b9390;line-height:1.5}.lg-field.lg-wide{grid-column:1/-1}.lg-form-error{font-size:11px;color:#b42323;min-height:15px}.lg-modal-actions{display:flex;justify-content:flex-end;gap:10px}.lg-toast{position:fixed;right:22px;bottom:20px;background:white;border:1px solid #d6babc;border-left:4px solid #7B1B1B;border-radius:10px;padding:14px 19px;font-size:12px;max-width:370px;z-index:250;box-shadow:0 12px 35px #0002}.lg-toast[hidden]{display:none}
@media(max-width:1260px){.lg-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.lg-main-grid,.lg-lower-grid{grid-template-columns:1fr}.lg-side-stack{grid-template-columns:1fr 1fr}}
@media(max-width:900px){.lg-sidebar{width:80px}.lg-sidebar-logo>span,.lg-nav-category,.lg-nav-link span,.lg-sidebar-bottom{display:none}.lg-sidebar-logo img{max-width:60px}.lg-nav-link{justify-content:center;padding:0}.lg-navbar{padding-left:108px}.lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-sidebar{width:260px;box-shadow:0 0 0 100vw #0003}.lg-body.lg-collapsed .lg-sidebar-logo>span,.lg-body.lg-collapsed .lg-nav-category,.lg-body.lg-collapsed .lg-nav-link span,.lg-body.lg-collapsed .lg-sidebar-bottom{display:block}.lg-body.lg-collapsed .lg-nav-link{justify-content:flex-start;padding:0 13px}.lg-body.lg-collapsed .lg-main{margin-left:80px}.lg-body.lg-collapsed .lg-navbar{padding-left:108px}}
@media(max-width:700px){.lg-sidebar{transform:translateX(-100%);width:260px}.lg-body.lg-collapsed .lg-sidebar{transform:translateX(0)}.lg-navbar,.lg-body.lg-collapsed .lg-navbar{padding:0 14px;height:75px}.lg-nav-left h2{font-size:16px}.lg-nav-right{gap:8px}.lg-navbar-caption,.lg-profile-btn small,.lg-profile-chevron{display:none}.lg-profile-btn{min-width:unset}.lg-profile-btn strong{font-size:10px;max-width:92px}.lg-main,.lg-body.lg-collapsed .lg-main{margin-left:0;padding:96px 13px 25px}.lg-welcome{align-items:flex-start;flex-direction:column}.lg-welcome-actions{width:100%;flex-wrap:wrap}.lg-side-stack{grid-template-columns:1fr}.lg-panel-header{align-items:flex-start;flex-wrap:wrap}.lg-form-grid{grid-template-columns:1fr}.lg-field.lg-wide{grid-column:auto}}
@media(max-width:440px){.lg-stats{grid-template-columns:1fr 1fr;gap:8px}.lg-stat{padding:12px 9px;gap:7px;min-height:85px}.lg-stat-icon{width:27px;height:27px}.lg-stat strong{font-size:17px}.lg-stat span{font-size:9px}.lg-toolbar>*{max-width:none!important;width:100%}.lg-date{display:none}}
@media(prefers-reduced-motion:reduce){.lg-sidebar,.lg-main,.lg-navbar,.lg-btn{transition:none}}


/* Merged logistics command-center layout */
.lg-main{max-width:1650px;margin-right:auto}.lg-dash-top{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:18px;align-items:start;margin:0 0 18px}.lg-dash-left{display:grid;gap:18px;min-width:0}.lg-tracker{padding:22px 23px}.lg-clock{color:var(--lg-dark);font-weight:700;font-size:17px;font-variant-numeric:tabular-nums;margin:0 0 4px}.lg-clock-sub{color:var(--lg-muted);font-size:11px;margin:0 0 17px}.lg-tracker-row{display:flex;gap:9px;align-items:center}.lg-track-input{height:43px;min-width:0;flex:1;border:1px solid #ded1cd;border-radius:9px;padding:0 14px;outline:none;font-size:12px}.lg-track-input:focus{border-color:var(--lg-maroon);box-shadow:0 0 0 3px #7b1b1b10}.lg-tracker-row button{height:43px}.lg-tracker-result{font-size:11px;color:#715e59;margin:11px 0 0;min-height:15px}.lg-attention-row{display:flex;align-items:center;gap:13px;padding:16px 18px;border:1px solid #eddcd6;border-radius:12px;background:#fff}.lg-attention-icon{height:36px;width:36px;border-radius:11px;display:grid;place-items:center;background:#ffece5;color:#8a2222;font-size:19px;flex:0 0 auto}.lg-attention-row b{font-size:12px}.lg-attention-row p{font-size:11px;color:var(--lg-muted);margin:3px 0 0}.lg-attention-row .lg-btn{margin-left:auto}.lg-section-title{font-size:15px;font-weight:650;margin:0 0 12px}.lg-kpi-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:11px}.lg-kpi{background:#fff;border:1px solid var(--lg-line);border-radius:13px;padding:15px 12px;min-height:113px;display:flex;gap:10px;align-items:center}.lg-kpi .lg-kpi-icon{width:35px;height:35px;border-radius:10px;display:grid;place-items:center;flex:0 0 auto;font-size:16px;background:#fff0eb;color:#7B1B1B}.lg-kpi:nth-child(3n+2) .lg-kpi-icon{background:#eaf2fe;color:#306ca0}.lg-kpi:nth-child(3n) .lg-kpi-icon{background:#edf8f1;color:#2c8051}.lg-kpi span{font-size:10px;line-height:1.45;color:var(--lg-muted);display:block}.lg-kpi strong{display:block;margin-top:5px;font-size:21px;line-height:1.15}.lg-dash-middle{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(300px,1fr);gap:18px;margin-top:18px}.lg-announcements{margin:0;min-height:345px;background:var(--lg-dark);color:#fff;border-color:var(--lg-dark);padding:7px 5px 20px}.lg-announcements .lg-panel-header h2{color:#ffd0be;font-size:13px;letter-spacing:.5px;text-transform:uppercase}.lg-announcements .lg-panel-header p{color:#f0c2b8}.lg-announcements .lg-help{color:#f4c5bc!important;font-size:13px;margin-top:18px!important}.lg-announcements article small{color:#f5b7a9}.lg-announcements article h3{font-size:18px;margin:10px 0}.lg-announcements article p{font-size:12px;line-height:1.8;color:#f6dcd5}.lg-announcements .lg-btn-outline{background:transparent;color:#fff;border-color:#ffffff77;margin-top:20px!important}.lg-network-panel{margin-top:0}.lg-dash-notice{font-size:11px;color:#8a7974;margin:0 0 16px}.lg-kpi-note{font-size:10px;color:#a29590;margin:10px 0 0}.lg-activity-table{min-width:580px}
@media(max-width:1450px){.lg-kpi-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.lg-dash-top{grid-template-columns:minmax(0,1fr) 310px}}
@media(max-width:1100px){.lg-dash-top,.lg-dash-middle{grid-template-columns:1fr}.lg-announcements{min-height:200px}.lg-kpi-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:650px){.lg-kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.lg-kpi{padding:12px 10px}.lg-attention-row{flex-wrap:wrap}.lg-attention-row .lg-btn{margin-left:0}.lg-tracker-row{flex-wrap:wrap}.lg-tracker-row button{width:100%}.lg-clock{font-size:15px}}


/* Minimal time label, subordinate to parcel tracking */
.lg-tracker .lg-clock{display:inline-flex;align-items:center;gap:6px;margin:0 0 14px;font-size:11px;font-weight:400;letter-spacing:0;color:#988a85;font-variant-numeric:tabular-nums}
.lg-tracker .lg-clock:before{content:"";width:5px;height:5px;border-radius:50%;background:#bd9991;flex-shrink:0}
/* Announcements: compact right-column carousel */
.lg-announcements{display:flex;flex-direction:column;min-height:310px;padding:8px 6px 14px}
.lg-announcements .lg-panel-header{padding-bottom:12px}
.lg-announcements .lg-announcement-slides{margin:0 18px!important;flex:1;display:flex;align-items:center;min-height:155px}
.lg-announcements .lg-announcement-slides article{width:100%;animation:lgAnnouncementIn .25s ease}
.lg-announcements .lg-announcement-slides article[hidden]{display:none}
.lg-announcements .lg-announcement-slides article small{font-size:10px;letter-spacing:.45px;text-transform:uppercase}
.lg-announcements .lg-announcement-slides article h3{font-size:18px;line-height:1.35;margin:12px 0}
.lg-announcements .lg-announcement-slides article p{font-size:11px;line-height:1.75;margin:0}
.lg-announcement-controls{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:12px 18px 0;margin-top:auto;border-top:1px solid #ffffff21}
.lg-announcement-controls[hidden]{display:none}
.lg-announcement-dots,.lg-announcement-arrows{display:flex;align-items:center;gap:7px}
.lg-announcement-dot{height:5px;width:9px;min-width:9px;border:0;border-radius:20px;padding:0;background:#ffffff60;transition:width .2s,background .2s}
.lg-announcement-dot.active{width:26px;background:#ffdacd}
.lg-announcement-arrows button{height:29px;width:29px;border:1px solid #ffffff60;background:transparent;border-radius:50%;color:white;font-size:21px;line-height:1;display:grid;place-items:center}
.lg-announcement-arrows button:hover{background:#ffffff22}
@keyframes lgAnnouncementIn{from{opacity:.4;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}
@media(prefers-reduced-motion:reduce){.lg-announcements article{animation:none!important}}

/* Keep areas scrollable without visible scrollbar indicators. */
.lg-sidebar-menu,.lg-table-scroll{scrollbar-width:none;-ms-overflow-style:none}
.lg-sidebar-menu::-webkit-scrollbar,.lg-table-scroll::-webkit-scrollbar{display:none}
html,body.lg-body{overflow-x:hidden}
    .lg-facility-kpis{grid-template-columns:repeat(5,minmax(0,1fr));max-width:none}
    .lg-facility-kpis .lg-kpi{min-width:0;padding:14px 11px;gap:9px}
    .lg-facility-kpis .lg-kpi-icon{width:34px;height:34px;flex-shrink:0}
    @media(max-width:1350px){.lg-facility-kpis{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:650px){.lg-facility-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}

/* Parcel Tracking: seller-inspired shipping table + returns table. */
.pt-page{max-width:1500px;margin:0 auto}
.pt-heading{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:20px}
.pt-eyebrow{color:#985850;letter-spacing:1.3px;text-transform:uppercase;font-size:10px;font-weight:700}
.pt-heading h1{font-size:25px;margin:6px 0;color:#261719}
.pt-heading p{font-size:12px;color:#948883;margin:0}
.pt-demo{border-radius:30px;background:#fff0de;color:#925b24;font-size:10px;font-weight:650;padding:7px 13px}
.pt-panel{background:white;border-radius:15px;border:1px solid #f0e5e1;overflow:hidden;box-shadow:0 3px 16px #2c090908}
.pt-top-tabs{display:flex;gap:27px;border-bottom:1px solid #eadfdd;padding:0 24px}
.pt-tab{position:relative;background:none;border:0;color:#9a8a87;font-size:13px;font-weight:500;padding:20px 5px;cursor:pointer}
.pt-tab.active{color:#7b1b1b;font-weight:700}
.pt-tab.active:after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:3px 3px 0 0;background:#7b1b1b}
.pt-tabpanel[hidden]{display:none}
.pt-subbar{padding:17px 22px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
.pt-pills{display:flex;gap:6px;flex-wrap:wrap}
.pt-pill{background:#faf5f3;border:1px solid #f1e7e3;border-radius:8px;padding:9px 13px;font-size:11px;font-weight:500;color:#766a67;cursor:pointer}
.pt-pill.active{background:#7b1b1b;color:#fff;border-color:#7b1b1b}
.pt-search{border:1px solid #e7dbd7;border-radius:9px;display:flex;align-items:center;gap:10px;padding:0 13px;height:40px;min-width:245px;max-width:360px;flex:1;color:#9e918c}
.pt-search input{border:0;outline:none;width:100%;font:inherit;font-size:11px;background:none}
.pt-scroll{overflow:auto;scrollbar-width:thin}
.pt-table{width:100%;border-collapse:collapse;text-align:left;min-width:850px}
.pt-table th{background:#fff9f7;color:#847773;font-size:11px;font-weight:600;padding:15px 19px;white-space:nowrap;border-top:1px solid #f4ebe7;border-bottom:1px solid #f0e7e2}
.pt-table td{font-size:12px;padding:20px 19px;border-bottom:1px solid #f3ece9;vertical-align:middle;color:#443835}
.pt-table tbody tr{cursor:pointer;transition:background .15s}
.pt-table tbody tr:hover,.pt-table tbody tr:focus-visible{background:#fff7f4;outline:0}
.pt-table td strong{color:#392723;font-size:12px;font-weight:600}
.pt-table td small{display:block;color:#9c918e;margin-top:5px;font-size:10px}
.pt-order{color:#892333!important}
.pt-money{color:#8b202f;font-weight:700}
.pt-status{display:inline-flex;align-items:center;gap:6px;border-radius:30px;background:#fff0d9;color:#986323;padding:7px 12px;font-weight:600;font-size:10px;white-space:nowrap}
.pt-status.delivered,.pt-status.refunded{background:#e6f6e9;color:#247647}
.pt-status.in-transit,.pt-status.picked-up{background:#e9f0fe;color:#3469a4}
.pt-status.out-for-delivery,.pt-status.return-in-transit{background:#fff0e3;color:#ab6732}
.pt-status.pending,.pt-status.return-requested{background:#fff0dd;color:#996521}
.pt-status.rejected{background:#fbe6e9;color:#a02e42}
.pt-footer{padding:17px 21px;display:flex;justify-content:space-between;gap:12px;align-items:center;font-size:11px;color:#8a7d78}
.pt-pagination{display:flex;gap:8px}
.pt-pagination button{border:1px solid #e7dbd6;background:#fff;color:#7b1b1b;padding:7px 12px;border-radius:7px;cursor:pointer}
.pt-pagination button:disabled{color:#c0b8b5;cursor:not-allowed}
.pt-empty{text-align:center!important;padding:45px!important;color:#9c928e!important}
.pt-modal{position:fixed;inset:0;background:#220e13a8;z-index:300;display:flex;align-items:center;justify-content:center;padding:22px;opacity:0;visibility:hidden;transition:opacity .2s,visibility .2s}
.pt-modal.open{opacity:1;visibility:visible}
.pt-dialog{max-width:850px;width:100%;max-height:min(90vh,900px);display:flex;flex-direction:column;background:#fff;border-radius:16px;box-shadow:0 24px 70px #16080a55;overflow:hidden;transform:translateY(12px);transition:transform .2s}
.pt-modal.open .pt-dialog{transform:translateY(0)}
.pt-modal-head{padding:21px 28px;display:flex;justify-content:space-between;align-items:center;gap:20px;border-bottom:1px solid #eee3df}
.pt-modal-head h2{margin:0;font-size:19px;color:#271b1a}
.pt-x{border:0;background:#f8f0ee;color:#7b1b1b;border-radius:50%;font-size:23px;width:35px;height:35px;cursor:pointer}
.pt-modal-content{padding:23px 28px;overflow:auto}
.pt-modal-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.pt-detail-card{border:1px solid #eee4df;border-radius:11px;overflow:hidden;margin-bottom:16px}
.pt-detail-title{background:#fff7f5;padding:13px 16px;font-size:12px;color:#69232b;font-weight:700;border-bottom:1px solid #eee3dd}
.pt-detail-body{padding:16px}
.pt-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.pt-field label{font-size:10px;color:#978882;display:block;margin-bottom:5px}
.pt-field strong{display:block;font-size:12px;font-weight:600;color:#332a27;overflow-wrap:anywhere}
.pt-wide{grid-column:1/-1}
.pt-progress{display:flex;align-items:flex-start;justify-content:space-between;position:relative;gap:8px;margin:19px 0}
.pt-step{text-align:center;flex:1;position:relative;z-index:1;color:#a9a09d;font-size:10px}
.pt-step i{width:27px;height:27px;display:grid;place-items:center;border:2px solid #e0d5d1;border-radius:50%;background:#fff;margin:0 auto 7px;font-size:12px;font-style:normal}
.pt-step.done i{background:#7b1b1b;border-color:#7b1b1b;color:#fff}
.pt-step.done{color:#7b1b1b;font-weight:600}
.pt-history{list-style:none;padding:0;margin:0}
.pt-history li{padding:11px 0 11px 22px;border-left:2px solid #e8dcd7;position:relative;font-size:12px}
.pt-history li:before{content:'';position:absolute;left:-6px;top:16px;width:9px;height:9px;border-radius:50%;background:#9b2935}
.pt-history small{display:block;margin-top:4px;color:#968984;font-size:10px}
.pt-notes{border:1px solid #f2e0d9;background:#fff9f6;border-radius:9px;padding:14px;font-size:12px;color:#695d58;line-height:1.7}
.pt-modal-actions{padding:15px 27px;border-top:1px solid #efe4df;display:flex;justify-content:flex-end}
.pt-close{background:white;border:1px solid #a6a6a6;color:#777;border-radius:8px;padding:9px 21px;font-size:12px;cursor:pointer}
.pt-close:hover{background:#faf8f7}
body.pt-modal-open{overflow:hidden}
.pt-placeholder{font-size:11px;color:#a1938f;padding:8px 0}
.pt-section-heading{font-size:12px;color:#7b1b1b;margin:0 0 14px}
.pt-desc{font-size:11px;color:#a39a96;margin:0 0 4px}
.pt-count{font-size:10px;color:#ab9893}
.pt-returns-note{margin:0 22px 16px;font-size:11px;color:#927e76}
.pt-table .pt-customer{min-width:170px}
.pt-party-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.pt-info-pair{display:grid;grid-template-columns:130px minmax(0,1fr);gap:10px 14px;align-items:start}
.pt-info-pair+.pt-info-pair{margin-top:10px;padding-top:10px;border-top:1px solid #f2e7e2}
.pt-info-pair label{font-size:11px;color:#9d8d87}
.pt-info-pair strong{font-size:12px;color:#332a27;font-weight:600;overflow-wrap:anywhere}
.pt-item-stack{display:flex;flex-direction:column;gap:14px}
.pt-order-item{border:1px solid #ead7d1;border-radius:14px;overflow:hidden;background:#fff}
.pt-order-item-head{background:#f7efed;padding:14px 16px;border-bottom:1px solid #eadfdb}
.pt-order-item-index{font-size:10px;letter-spacing:1.4px;text-transform:uppercase;color:#a2574c;font-weight:700;display:block;margin-bottom:8px}
.pt-order-item-name{font-size:13px;font-weight:700;color:#2e2321}
.pt-order-item-body{padding:16px}
.pt-order-item-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 24px}
.pt-order-item-field label{font-size:11px;color:#9d8d87;display:block;margin-bottom:6px}
.pt-order-item-field strong{display:block;font-size:12px;color:#332a27;font-weight:600;overflow-wrap:anywhere}
.pt-returns-table{width:100%;min-width:720px;table-layout:fixed}
.pt-returns-table th:nth-child(1),.pt-returns-table td:nth-child(1){width:20%}
.pt-returns-table th:nth-child(2),.pt-returns-table td:nth-child(2){width:34%}
.pt-returns-table th:nth-child(3),.pt-returns-table td:nth-child(3){width:21%}
.pt-returns-table th:nth-child(4),.pt-returns-table td:nth-child(4){width:25%}
.pt-returns-table th,.pt-returns-table td{overflow-wrap:anywhere}
.pt-returns-table th:nth-child(-n+2),.pt-returns-table td:nth-child(-n+2){padding-left:34px}
.pt-returns-table .pt-customer{min-width:0}
@media(max-width:900px){.pt-returns-table th:nth-child(-n+2),.pt-returns-table td:nth-child(-n+2){padding-left:22px}}


.pt-location-bar{display:grid;grid-template-columns:repeat(2,minmax(220px,310px));gap:13px 15px;padding:0 22px 18px;align-items:start}
.pt-location-control{display:flex;flex-direction:column;gap:6px;min-width:0;width:100%}
.pt-location-control label{font-size:11px;font-weight:600;line-height:1.4;color:#765e5a}
.pt-location-control select{width:100%;min-width:0;height:40px;padding:0 35px 0 12px;border:1px solid #e7dbd7;border-radius:9px;background:#fff;color:#51413e;font:500 11px Poppins,sans-serif;outline:0;transition:border-color .15s,box-shadow .15s}
.pt-location-control select:focus{border-color:#7b1b1b;box-shadow:0 0 0 3px #7b1b1b16}
.pt-location-control select:disabled{background:#f5f1ef;color:#a99d99;cursor:not-allowed;opacity:1}
.pt-location-help{grid-column:1/-1;margin:0;padding:9px 12px;border:1px solid #f0e5e1;border-radius:8px;background:#fff9f7;color:#8d7974;font-size:10px;line-height:1.5}
@media(max-width:640px){.pt-location-bar{grid-template-columns:minmax(0,1fr);gap:10px;padding:0 14px 15px}.pt-location-help{grid-column:auto}}
.pt-address-heading{font-size:12px;font-weight:700;color:#8c2931;margin:18px 0 13px;padding-top:15px;border-top:1px solid #eee4df;display:flex;align-items:center;gap:6px}.pt-address-pin{display:inline-flex;align-items:center;color:#982d30}.pt-address-fields{display:flex;flex-direction:column;gap:11px}.pt-address-field{display:grid;grid-template-columns:minmax(90px,110px) minmax(0,1fr);gap:12px;align-items:start}.pt-address-field label{font-size:11px;color:#9b9694;margin:0;line-height:1.55}.pt-address-value{color:#302729;font-size:11px;font-weight:600;overflow-wrap:anywhere;line-height:1.55}@media(max-width:640px){.pt-address-field{grid-template-columns:95px minmax(0,1fr);gap:9px}}

/* Seller Returns Shipping Status-style tracking progress inside the Parcels details modal. */
.pt-shipping-progress{display:flex;align-items:flex-start;justify-content:center;max-width:520px;width:100%;margin:15px auto 20px;gap:0}
.pt-progress-item{width:99px;flex:0 0 99px;min-width:0;display:flex;flex-direction:column;align-items:center;text-align:center}
.pt-progress-item img{width:36px;height:36px;max-width:36px;object-fit:contain;position:relative;z-index:1}
.pt-progress-item small{display:block;margin-top:8px;font-size:11px;color:#928984;line-height:1.4}
.pt-progress-line{flex:1;min-width:14px;max-width:65px;height:1px;margin-top:18px;background:#d2cdcb}
.pt-progress-line.done{background:#7db473}
@media(max-width:550px){.pt-progress-item{width:67px;flex-basis:67px}.pt-progress-item img{width:29px;height:29px}.pt-progress-item small{font-size:9px}.pt-progress-line{margin-top:15px;min-width:7px}}

/* Returns table: tighter, balanced spacing with less unused whitespace. */
.pt-returns-table{
  min-width:680px;
  table-layout:fixed;
}
.pt-returns-table th,
.pt-returns-table td{
  padding:13px 14px;
  line-height:1.45;
  vertical-align:middle;
}
.pt-returns-table th:nth-child(1),
.pt-returns-table td:nth-child(1){width:19%}
.pt-returns-table th:nth-child(2),
.pt-returns-table td:nth-child(2){width:35%}
.pt-returns-table th:nth-child(3),
.pt-returns-table td:nth-child(3){width:20%}
.pt-returns-table th:nth-child(4),
.pt-returns-table td:nth-child(4){width:26%}
.pt-returns-table td strong{line-height:1.45}
.pt-returns-table td small{margin-top:3px;line-height:1.45;overflow-wrap:anywhere}
.pt-returns-table .pt-customer{min-width:0}
.pt-returns-table th:nth-child(-n+2),
.pt-returns-table td:nth-child(-n+2){padding-left:64px}
@media(max-width:900px){
  .pt-returns-table th:nth-child(-n+2),
  .pt-returns-table td:nth-child(-n+2){padding-left:32px}
}
#ptReturns .pt-subbar{padding:14px 18px;gap:12px}
#ptReturns .pt-returns-note{margin:0 18px 12px;line-height:1.5}
#ptReturns .pt-footer{padding:12px 18px}
@media(max-width:700px){
  .pt-returns-table th,.pt-returns-table td{padding:11px 12px}
  #ptReturns .pt-subbar{padding:12px 14px}
  #ptReturns .pt-returns-note{margin:0 14px 12px}
}
/* Tracking history card styled like the supplied reference. */
.pt-history-card .pt-detail-title{background:#fff7f5}
.pt-history{
  list-style:none;
  padding:0 0 0 1px;
  margin:0;
}
.pt-history li{
  padding:11px 0 11px 20px;
  border-left:2px solid #eadbd6;
  position:relative;
  font-size:12px;
  line-height:1.45;
}
.pt-history li:first-child{padding-top:7px}
.pt-history li:last-child{padding-bottom:7px}
.pt-history li:before{
  content:'';
  position:absolute;
  left:-6px;
  top:15px;
  width:8px;
  height:8px;
  border-radius:50%;
  background:#a9273b;
}
.pt-history li strong{
  display:block;
  color:#302523;
  font-size:11px;
  font-weight:700;
}
.pt-history small{
  display:block;
  margin-top:4px;
  color:#9b8e89;
  font-size:10px;
  line-height:1.5;
}

</style></head>
<body class="lg-body">
@include('components.logistics.sorting-center.sidebar')
@include('components.logistics.sorting-center.navbar')
<main class="lg-main" id="lgMain"><div class="pt-page">
<header class="pt-heading"><div><span class="pt-eyebrow">ShopEase · Sorting Center Operations</span><h1>Parcel Tracking</h1><p>Monitor parcels at this sorting center and its assigned delivery hubs.</p></div></header>
@if($scDemoMode)
<div class="lg-notice" role="status"><span class="lg-notice-dot"></span><div><strong>UI preview only.</strong> Showing fictional parcel, return and delivery hub records. The Delivery Hub options use real assigned hubs when available. Fictional parcels disappear when live parcel data is provided. Refresh after Logistics adds a Delivery Hub to see it.</div></div>
@endif
<section class="pt-panel" aria-label="Parcel tracking records">
<div class="pt-top-tabs" role="tablist" aria-label="Parcel tracking sections"><button class="pt-tab active" id="ptTabParcels" data-main="parcels" role="tab" aria-selected="true" aria-controls="ptParcels">Parcels</button><button class="pt-tab" id="ptTabReturns" data-main="returns" role="tab" aria-selected="false" aria-controls="ptReturns">Returns</button></div>
<div id="ptParcels" class="pt-tabpanel" role="tabpanel" aria-labelledby="ptTabParcels">
<div class="pt-subbar"><div class="pt-pills" id="ptShipmentFilters" aria-label="Filter shipments"><button class="pt-pill active" data-filter="all">All</button><button class="pt-pill" data-filter="in-transit">In Transit</button><button class="pt-pill" data-filter="out-for-delivery">Out for Delivery</button><button class="pt-pill" data-filter="delivered">Delivered</button></div><label class="pt-search"><span aria-hidden="true">⌕</span><input id="ptSearchParcels" type="search" placeholder="Search order, tracking number, buyer…" aria-label="Search parcels"></label></div>
<div class="pt-location-bar"><div class="pt-location-control"><label for="ptSortingFilter">Assigned Sorting Center</label><select id="ptSortingFilter" aria-label="Assigned sorting center" disabled><option value="all">Assigned Sorting Center</option></select></div><div class="pt-location-control"><label for="ptDeliveryFilter">Delivery Hub</label><select id="ptDeliveryFilter" aria-label="Filter parcels by delivery hub"><option value="all">All Assigned Delivery Hubs</option></select></div><span class="pt-location-help">Only parcels currently in this sorting center or its delivery hubs are visible. Parcels transferred to a different sorting center are excluded.</span></div>
<div class="pt-scroll"><table class="pt-table"><thead><tr><th>Order Information</th><th>Tracking Number</th><th>Shipping Status</th><th>Current Location</th><th>Estimated Delivery</th></tr></thead><tbody id="ptParcelsRows"></tbody></table></div><div class="pt-footer"><span id="ptParcelsCount"></span><div class="pt-pagination"><button id="ptParcelsPrev">Previous</button><button id="ptParcelsNext">Next</button></div></div>
</div>
<div id="ptReturns" class="pt-tabpanel" role="tabpanel" aria-labelledby="ptTabReturns" hidden>
<div class="pt-subbar"><div class="pt-pills" id="ptReturnFilters"><button class="pt-pill active" data-filter="all">All</button><button class="pt-pill" data-filter="pending">Pending</button><button class="pt-pill" data-filter="returned">Returned</button></div><label class="pt-search"><span aria-hidden="true">⌕</span><input id="ptSearchReturns" type="search" placeholder="Search order, buyer, product…" aria-label="Search returns"></label></div>
<p class="pt-returns-note">Return requests are shown for tracking and review. Approval remains under the authorized seller / admin workflow.</p>
<div class="pt-scroll"><table class="pt-table pt-returns-table"><thead><tr><th>Order ID</th><th>Buyer / Product</th><th>Status</th><th>Date &amp; Time</th></tr></thead><tbody id="ptReturnsRows"></tbody></table></div><div class="pt-footer"><span id="ptReturnsCount"></span><div class="pt-pagination"><button id="ptReturnsPrev">Previous</button><button id="ptReturnsNext">Next</button></div></div>
</div></section></div></main>
<div class="pt-modal" id="ptParcelModal" aria-hidden="true"><div class="pt-dialog" role="dialog" aria-modal="true" aria-labelledby="ptParcelModalTitle" tabindex="-1"><header class="pt-modal-head"><h2 id="ptParcelModalTitle">Order Details</h2><button type="button" class="pt-x" data-close="ptParcelModal" aria-label="Close">×</button></header><div class="pt-modal-content" id="ptParcelModalBody"></div><div class="pt-modal-actions"><button type="button" class="pt-close" data-close="ptParcelModal">Close</button></div></div></div>
<div class="pt-modal" id="ptReturnModal" aria-hidden="true"><div class="pt-dialog" role="dialog" aria-modal="true" aria-labelledby="ptReturnModalTitle" tabindex="-1"><header class="pt-modal-head"><h2 id="ptReturnModalTitle">Return Details</h2><button type="button" class="pt-x" data-close="ptReturnModal" aria-label="Close">×</button></header><div class="pt-modal-content" id="ptReturnModalBody"></div><div class="pt-modal-actions"><button type="button" class="pt-close" data-close="ptReturnModal">Close</button></div></div></div>
<script id="ptParcelData" type="application/json">{!! json_encode($scParcels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script id="ptReturnData" type="application/json">{!! json_encode($scReturns, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script id="ptLocationData" type="application/json">{!! json_encode($scLocationData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
'use strict';
const $ = id => document.getElementById(id);
const escape = v => String(v ?? '—').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const clean = v => String(v ?? '').toLowerCase().replace(/[_\s]+/g,'-').trim();
const formatDate = v => {if(!v)return '—';const d=new Date(v);return isNaN(d)?escape(v):d.toLocaleString('en-PH',{year:'numeric',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'});};
const money=v=>{const n=Number(String(v??'').replace(/[^\d.-]/g,''));return (v===null||v===undefined||v==='')?'—':Number.isFinite(n)?'₱'+n.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}):escape(v);};
const statusName=v=>({ 'in-transit':'In Transit','out-for-delivery':'Out for Delivery','delivered':'Delivered','picked-up':'Picked Up','processing':'Processing','pending':'Pending','returned':'Returned','refunded':'Refunded','returned-refunded':'Returned & Refunded','return-requested':'Return Requested','return-in-transit':'Return In Transit','rejected':'Rejected'}[clean(v)]||String(v||'Pending').replace(/[-_]/g,' '));
const returnStatusName=v=>['refunded','returned-refunded','return-completed'].includes(clean(v))?'Returned':statusName(v);
const pill=v=>`<span class="pt-status ${escape(clean(v))}">${escape(statusName(v))}</span>`;
const field=(title,value)=>`<div class="pt-field"><label>${escape(title)}</label><strong>${escape(value??'—')}</strong></div>`;
const card=(title,body)=>`<section class="pt-detail-card"><div class="pt-detail-title">${escape(title)}</div><div class="pt-detail-body">${body}</div></section>`;
const rowsFrom=id=>{try{let data=JSON.parse($(id)?.textContent||'[]');if(data&& !Array.isArray(data) && Array.isArray(data.data))data=data.data;return Array.isArray(data)?data:[]}catch(e){return []}};
const parcels=rowsFrom('ptParcelData');const returns=rowsFrom('ptReturnData');let trackingLocations={};try{trackingLocations=JSON.parse($('ptLocationData')?.textContent||'{}')}catch{}
const states={parcels:{tab:'all',location:'all',locationType:null,search:'',page:1,data:parcels},returns:{tab:'all',search:'',page:1,data:returns}};
const perPage=7;let active='parcels';let lastFocus=null;
function parcelStatus(p){return clean(p.shipping_status||p.status||'processing')}
function returnsStatus(r){return clean(r.status||r.return_status||'pending')}
function isRefundOnly(r){return /refund[\s_-]*only|partial[\s_-]*refund/i.test(String(r.return_type||r.refund_type||r.type||''))}
function networkLocations(){return [...(trackingLocations.sorting_centers||[]).map(x=>({id:String(x.id),name:String(x.name),kind:'Sorting Center'})),...(trackingLocations.delivery_hubs||[]).map(x=>({id:String(x.id),name:String(x.name),kind:'Delivery Hub'}))]}
function updateLocationOptions(){
  const sorting=$('ptSortingFilter'),delivery=$('ptDeliveryFilter');
  if(!sorting||!delivery)return;
  const center=(trackingLocations.sorting_centers||[])[0];
  sorting.replaceChildren(new Option(center?.name||'Unassigned Sorting Center',center?.id||'unassigned'));
  sorting.disabled=true;
  const selected=delivery.value;
  delivery.replaceChildren(new Option('All Assigned Delivery Hubs','all'));
  (trackingLocations.delivery_hubs||[]).forEach(h=>delivery.add(new Option(h.name,String(h.id))));
  delivery.value=[...delivery.options].some(o=>o.value===selected)?selected:'all';
  states.parcels.locationType=delivery.value==='all'?null:'delivery';
  states.parcels.location=delivery.value;
}
$('ptDeliveryFilter')?.addEventListener('change',()=>{
  const delivery=$('ptDeliveryFilter');
  states.parcels.location=delivery.value;
  states.parcels.locationType=delivery.value==='all'?null:'delivery';
  states.parcels.page=1;render('parcels');
});
window.addEventListener('pageshow',()=>{updateLocationOptions();render('parcels');});
function filterRecords(section){const s=states[section];return s.data.filter(o=>{if(section==='parcels'&&s.location!=='all'){const locationId=o.current_delivery_hub_id??o.delivery_hub_id;if(String(locationId)!==String(s.location))return false;}if(section==='returns'&&isRefundOnly(o))return false;const status=section==='parcels'?parcelStatus(o):returnsStatus(o);const tab=s.tab;const matches=tab==='all'||(section==='returns'&&tab==='returned'&&['returned','refunded','returned-refunded','return-completed'].includes(status))||(section==='parcels'&&tab==='in-transit'&&['in-transit','picked-up','processing','sorting-center'].includes(status))||status===tab;const search=JSON.stringify(o).toLowerCase().includes(s.search);return matches&&search})}
function render(section){
  const filtered=filterRecords(section),state=states[section];
  const total=Math.max(1,Math.ceil(filtered.length/perPage));
  state.page=Math.min(state.page,total);
  const shown=filtered.slice((state.page-1)*perPage,state.page*perPage);
  if(section==='parcels'){
    $('ptParcelsRows').innerHTML=shown.map(o=>{
      const id=parcels.indexOf(o);
      return `<tr role="button" tabindex="0" data-id="${id}" aria-label="View parcel ${escape(o.order_number||o.order_id||o.tracking_number)}"><td><strong class="pt-order">${escape(o.order_number||o.order_id||o.id)}</strong><small>${escape(o.buyer_name||o.customer_name||'—')} · ${escape(o.product_name||o.items?.[0]?.name||'Parcel')}</small></td><td>${escape(o.tracking_number||o.tracking_no||'—')}</td><td>${pill(parcelStatus(o))}</td><td>${escape(o.current_location||o.location||'Not scanned yet')}</td><td>${formatDate(o.estimated_delivery||o.estimated_delivery_at||o.eta)}</td></tr>`;
    }).join('')||'<tr><td class="pt-empty" colspan="5">No parcel records match your search or filters.</td></tr>';
  }else{
    $('ptReturnsRows').innerHTML=shown.map(o=>`<tr role="button" tabindex="0" data-id="${returns.indexOf(o)}" aria-label="View return ${escape(o.order_id||o.order_number)}"><td><strong class="pt-order">${escape(o.order_id||o.order_number)}</strong></td><td class="pt-customer"><strong>${escape(o.buyer_name||o.customer_name)}</strong><small>${escape(o.product_name||o.return_items?.[0]?.product_name||'—')}</small></td><td>${pill(returnStatusName(returnsStatus(o)))}</td><td>${formatDate(o.returned_at||o.requested_at||o.date||o.created_at)}</td></tr>`).join('')||'<tr><td class="pt-empty" colspan="4">No return records match your search or filters.</td></tr>';
  }
  $(section==='parcels'?'ptParcelsCount':'ptReturnsCount').textContent=filtered.length?`Showing ${(state.page-1)*perPage+1}–${Math.min(filtered.length,state.page*perPage)} of ${filtered.length} records`:'0 records';
  $(section==='parcels'?'ptParcelsPrev':'ptReturnsPrev').disabled=state.page<=1;
  $(section==='parcels'?'ptParcelsNext':'ptReturnsNext').disabled=state.page>=total;
}
function switchTab(tab){active=tab;document.querySelectorAll('[data-main]').forEach(b=>{const selected=b.dataset.main===tab;b.classList.toggle('active',selected);b.setAttribute('aria-selected',String(selected));});$('ptParcels').hidden=tab!=='parcels';$('ptReturns').hidden=tab!=='returns';render(tab)}
document.querySelectorAll('[data-main]').forEach(b=>b.addEventListener('click',()=>switchTab(b.dataset.main)));
for(const section of ['parcels','returns']){const prefix=section==='parcels'?'ptParcels':'ptReturns',input=$(section==='parcels'?'ptSearchParcels':'ptSearchReturns'),filters=$(section==='parcels'?'ptShipmentFilters':'ptReturnFilters');input.addEventListener('input',()=>{states[section].search=input.value.toLowerCase().trim();states[section].page=1;render(section)});filters.addEventListener('click',e=>{const b=e.target.closest('[data-filter]');if(!b)return;states[section].tab=b.dataset.filter;states[section].page=1;filters.querySelectorAll('[data-filter]').forEach(x=>x.classList.toggle('active',x===b));render(section)});$(prefix+'Prev').addEventListener('click',()=>{states[section].page--;render(section)});$(prefix+'Next').addEventListener('click',()=>{states[section].page++;render(section)});const body=$(section==='parcels'?'ptParcelsRows':'ptReturnsRows');function activate(e){const tr=e.target.closest('tr[data-id]');if(!tr)return;if(e.type==='keydown'&&!['Enter',' '].includes(e.key))return;if(e.type==='keydown')e.preventDefault();section==='parcels'?openParcel(parcels[Number(tr.dataset.id)]):openReturn(returns[Number(tr.dataset.id)])}body.addEventListener('click',activate);body.addEventListener('keydown',activate)}
function historyList(o){
  const candidates=[o.tracking_history,o.trackingHistory,o.history,o.events,o.scans,o.status_history];
  const history=candidates.find(candidate=>Array.isArray(candidate)&&candidate.length)||[];
  const statusLabels={
    new:'Order Placed',pending:'Order Placed',preparing:'Order Prepared',prepared:'Order Prepared',
    to_ship:'Order Prepared',ready_to_ship:'Order Prepared',picked_up:'Picked Up',in_transit:'Picked Up',
    sorting_center:'Arrived at Sorting Center',at_sorting_center:'Arrived at Sorting Center',
    arrived_at_sorting_center:'Arrived at Sorting Center',
    out_for_delivery:'Out for Delivery',delivered:'Delivered',refunded:'Returned',returned_refunded:'Returned'
  };
  const events=history.map((event,index)=>{
    const rawStatus=event.status||event.title||event.to_status||event.new_status||event.description||'Tracking update';
    const key=clean(rawStatus).replace(/-/g,'_');
    const timestamp=event.timestamp||event.scanned_at||event.created_at||event.date||event.changed_at||event.updated_at;
    const parsed=timestamp?Date.parse(timestamp):NaN;
    return {
      title:statusLabels[key]||rawStatus,
      timestamp,
      sortTime:Number.isNaN(parsed)?null:parsed,
      location:event.location||event.current_location||'—',
      index
    };
  });
  if(o.prepared_at||o.preparing_at){
    const preparedAt=o.prepared_at||o.preparing_at;
    const preparedTime=Date.parse(preparedAt);
    events.push({title:'Order Prepared',timestamp:preparedAt,sortTime:Number.isNaN(preparedTime)?null:preparedTime,location:'—',index:-1});
  }
  events.sort((a,b)=>{
    if(a.sortTime!==null&&b.sortTime!==null)return a.sortTime-b.sortTime||a.index-b.index;
    if(a.sortTime!==null)return -1;
    if(b.sortTime!==null)return 1;
    return a.index-b.index;
  });
  return events.length?`<ul class="pt-history">${events.map(ev=>`<li><strong>${escape(ev.title)}</strong><small>${formatDate(ev.timestamp)} · ${escape(ev.location)}</small></li>`).join('')}</ul>`:'<p class="pt-placeholder">No tracking events recorded yet.</p>';
}
function progress(s){
  // Match the four-stage icon tracker used by Seller Returns Shipping Status.
  const normalized=clean(s);
  const positions={'processing':0,'ready-to-ship':0,'picked-up':1,'sorting-center':1,'in-transit':1,'out-for-delivery':2,'delivered':3};
  const pos=positions[normalized]??0;
  const stages=['Processing','In Transit','Out for Delivery','Delivered'];
  const icons=stages.map((label,i)=>{
    if(i===0)return pos>0?'in-transit-check.png':'processing.png';
    if(i===1)return pos>1?'in-transit-check.png':pos===1?'in-transit-blue.png':'in-transit-gray.png';
    if(i===2)return pos>2?'delivered-check.png':pos===2?'out-for-delivery.png':'out-for-delivery-gray.png';
    return pos===3?'delivered-check.png':'delivered-gray.png';
  });
  return `<div class="pt-shipping-progress" role="img" aria-label="Tracking progress: ${escape(returnStatusName(s))}">${stages.map((label,i)=>`<div class="pt-progress-item"><img src="/icons/seller/shipping-status/${icons[i]}" alt=""><small>${label}</small></div>${i<stages.length-1?`<span class="pt-progress-line ${i<pos?'done':''}"></span>`:''}`).join('')}</div>`;
}
function infoPair(label,value){return `<div class="pt-info-pair"><label>${escape(label)}</label><strong>${escape(value??'—')}</strong></div>`}
function addressDetails(o,role){
  // Only show structured values actually present in each record; do not split a freeform address into guessed fields.
  const addr=o[role+'_address_details']||o[role+'_address_data']||(role==='buyer'?(o.shipping_address_details||o.delivery_address_details||o.shipping_address_data||o.delivery_address_data):o.origin_address_details)||{};
  const obj=addr&&typeof addr==='object'&&!Array.isArray(addr)?addr:{};
  const pick=(...keys)=>{for(const key of keys){const value=obj[key]??o[role+'_'+key]??(role==='buyer'?o['shipping_'+key]??o['delivery_'+key]:o['pickup_'+key]??o['origin_'+key]);if(value!==undefined&&value!==null&&String(value).trim()!=='')return typeof value==='object'?(value.name||value.label||'—'):value}return '—'};
  const fields=[['Province',pick('province','province_name')],['Municipality / City',pick('municipality','municipality_name','city','city_name')],['Barangay',pick('barangay','barangay_name')],['ZIP Code',pick('zip_code','zipcode','postal_code','zip')],['Street',pick('street','street_name')],['House No.',pick('house_no','house_number','unit','unit_number')],['Building',pick('building','building_name')],['Subdivision',pick('subdivision','village')]];
  return `<div class="pt-address-heading"><span class="pt-address-pin" aria-hidden="true">📍</span>Address</div><div class="pt-address-fields">${fields.map(([label,value])=>`<div class="pt-address-field"><label>${escape(label)}</label><div class="pt-address-value">${escape(value)}</div></div>`).join('')}</div>`;
}
function partyCard(title,pairs,o,role){return card(title,`<div>${pairs.map(([label,value])=>infoPair(label,value)).join('')}</div>${addressDetails(o,role)}`)}
function returnItemsMarkup(items,record,quantityLabel='Quantity to Return'){const list=(Array.isArray(items)&&items.length?items:[{product_name:record.product_name||'Product',variation:record.variation,color:record.color,size:record.size,quantity:record.quantity||1,product_price:record.product_price||record.price}]);return `<div class="pt-item-stack">${list.map((x,index)=>`<article class="pt-order-item"><div class="pt-order-item-head"><span class="pt-order-item-index">Item ${index+1}</span><div class="pt-order-item-name">${escape(x.product_name||x.name||'Product')}</div></div><div class="pt-order-item-body"><div class="pt-order-item-fields"><div class="pt-order-item-field"><label>Variation</label><strong>${escape(x.variation||'—')}</strong></div><div class="pt-order-item-field"><label>Color</label><strong>${escape(x.color||'—')}</strong></div><div class="pt-order-item-field"><label>Size</label><strong>${escape(x.size||'—')}</strong></div><div class="pt-order-item-field"><label>${escape(quantityLabel)}</label><strong>${escape(x.quantity||1)}</strong></div><div class="pt-order-item-field"><label>Unit Price</label><strong>${money(x.product_price||x.price||x.unit_price||0)}</strong></div></div></div></article>`).join('')}</div>`}

function openParcel(o){if(!o)return;const status=parcelStatus(o),items=Array.isArray(o.items)?o.items:[];const buyerPairs=[['Name',o.buyer_name||o.customer_name],['Email',o.buyer_email||o.customer_email],['Contact',o.buyer_phone||o.customer_phone]];const sellerPairs=[['Name',o.seller_name||o.store_name||o.merchant_name],['Email',o.seller_email||o.store_email||o.merchant_email],['Contact',o.seller_phone||o.store_phone||o.merchant_phone]];$('ptParcelModalBody').innerHTML=card('Order Information',`<div class="pt-fields">${field('Order Number',o.order_number||o.order_id||o.id)}${field('Ordered At',formatDate(o.ordered_at||o.created_at))}${field('Estimated Delivery',formatDate(o.estimated_delivery||o.estimated_delivery_at||o.eta))}${field('Tracking Number',o.tracking_number||o.tracking_no)}${field('Current Location',o.current_location||o.location||'Not scanned yet')}${field('Shipping Status',statusName(status))}</div>`)+card('Tracking Progress',progress(status))+`<div class="pt-party-grid">${partyCard('Buyer Information',buyerPairs,o,'buyer')}${partyCard('Seller Information',sellerPairs,o,'seller')}</div>`+card('Payment Information',`<div class="pt-fields">${field('Payment Method',o.payment_method)}${field('Order Total',money(o.total_amount??o.total))}</div>`)+card('Ordered Items',returnItemsMarkup(items,o,'Quantity Ordered'))+card('Tracking History',historyList(o))+card('Customer Notes',`<div class="pt-notes">${escape(o.notes||o.customer_notes||'No customer notes provided.')}</div>`);openModal('ptParcelModal')}
function openReturn(o){
  if(!o)return;
  const items=Array.isArray(o.return_items)?o.return_items:(Array.isArray(o.items)?o.items:[]);
  const buyerPairs=[['Name',o.buyer_name||o.customer_name],['Email',o.buyer_email||o.customer_email],['Contact',o.buyer_phone||o.customer_phone]];
  const sellerPairs=[['Name',o.seller_name||o.store_name||o.merchant_name],['Email',o.seller_email||o.store_email||o.merchant_email],['Contact',o.seller_phone||o.store_phone||o.merchant_phone]];
  const shipmentStatus=o.shipping_status||o.return_shipping_status||'processing';
  const returnHistoryRecord=o;
  $('ptReturnModalBody').innerHTML=
    card('Return Summary',`<div class="pt-fields">${field('Order ID',o.order_id||o.order_number)}${field('Status',returnStatusName(returnsStatus(o)))}${field('Return Type','Return')}${field('Date & Time',formatDate(o.returned_at||o.requested_at||o.date||o.created_at))}</div>`)+
    card('Tracking Progress',progress(shipmentStatus))+
    `<div class="pt-party-grid">${partyCard('Buyer Information',buyerPairs,o,'buyer')}${partyCard('Seller Information',sellerPairs,o,'seller')}</div>`+
    card('Return Details',`<div class="pt-notes">${escape(o.reason||o.description||'No return reason supplied.')}</div><div class="pt-fields" style="margin-top:15px">${field('Requested Solution',o.solution||o.resolution||'No return solution supplied.')}${field('Requested At',formatDate(o.requested_at||o.date||o.created_at))}</div>`)+
    card('Shipment Information',`<div class="pt-fields">${field('Tracking Number',o.tracking_number)}${field('Courier',o.courier)}${field('Shipping Status',returnStatusName(o.shipping_status||'—'))}${field('Return Destination',o.seller_name||o.store_name||o.merchant_name)}</div>`)+
    card('Items for Return',returnItemsMarkup(items,o))+
    card('Supporting Evidence',`<div class="pt-notes">${escape(o.evidence_description||'No supporting evidence supplied.')}</div>`)+
    card('Tracking History',historyList(returnHistoryRecord));
  openModal('ptReturnModal');
}
function openModal(id){lastFocus=document.activeElement;const modal=$(id);modal.classList.add('open');modal.setAttribute('aria-hidden','false');document.body.classList.add('pt-modal-open');modal.querySelector('.pt-x').focus()}
function closeModal(id){const modal=$(id);modal.classList.remove('open');modal.setAttribute('aria-hidden','true');document.body.classList.remove('pt-modal-open');lastFocus?.focus?.()}
document.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',()=>closeModal(b.dataset.close)));
document.querySelectorAll('.pt-modal').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)closeModal(m.id)}));document.addEventListener('keydown',e=>{if(e.key==='Escape'){document.querySelectorAll('.pt-modal.open').forEach(m=>closeModal(m.id))}if(e.key==='Tab'){const modal=document.querySelector('.pt-modal.open');if(!modal)return;const focusable=[...modal.querySelectorAll('button:not([disabled]),a[href],input:not([disabled])')];if(!focusable.length)return;const first=focusable[0],last=focusable.at(-1);if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus()}else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus()}}});
// Matches the existing Logistics Dashboard behavior with the same shared navbar/sidebar components.
const body=document.body;
$('lgSidebarToggle')?.addEventListener('click',()=>{body.classList.toggle('lg-collapsed');$('lgSidebarToggle').setAttribute('aria-expanded',String(!body.classList.contains('lg-collapsed')))});
$('lgProfileButton')?.addEventListener('click',()=>{const m=$('lgProfileMenu');m.hidden=!m.hidden;$('lgProfileButton').setAttribute('aria-expanded',String(!m.hidden))});
document.addEventListener('click',e=>{if(!e.target.closest('.lg-profile-wrap')&&$('lgProfileMenu')){$('lgProfileMenu').hidden=true;$('lgProfileButton')?.setAttribute('aria-expanded','false')}});
$('lgNotifyButton')?.addEventListener('click',()=>window.alert('No live notifications connected yet.'));
$('lgChangeProfilePhoto')?.addEventListener('click',()=>$('lgProfilePhotoInput')?.click());
$('lgProfilePhotoInput')?.addEventListener('change',async e=>{const file=e.target.files?.[0];if(!file)return;if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024){window.alert('Choose a JPG, PNG, or WebP under 5 MB.');e.target.value='';return}const data=new FormData();data.append('profile_photo',file);try{const response=await fetch('/api/v1/profile/photo',{method:'PUT',credentials:'same-origin',headers:{Accept:'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},body:data});const result=await response.json();if(!response.ok)throw Error(result.message||'Unable to update photo');const avatar=document.querySelector('#lgProfileButton .account-avatar');if(avatar){let image=avatar.querySelector('img');if(!image){image=document.createElement('img');avatar.append(image)}image.src=result.profile_photo_url;image.style.cssText='position:absolute;inset:0;width:100%;height:100%;border-radius:inherit;object-fit:cover;'}}catch(err){window.alert(err.message||'Could not update profile photo')}finally{e.target.value=''}});
updateLocationOptions();render('parcels');render('returns');if(window.location.hash==='#returns')switchTab('returns');
});

</script>
</body></html>