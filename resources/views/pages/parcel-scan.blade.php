<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update parcel tracking - ShopEase</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; background: #f6f3f1; color: #211b18; font: 15px Arial, sans-serif; }
        main { max-width: 560px; margin: 36px auto; padding: 28px; background: #fff; border: 1px solid #e7dfdb; border-radius: 16px; box-shadow: 0 12px 36px #30180d12; }
        h1 { margin: 0 0 8px; font-size: 24px; }
        .muted { color: #756b66; }
        .summary { margin: 22px 0; padding: 16px; background: #fff7f2; border-radius: 10px; line-height: 1.7; }
        label { display: block; margin: 16px 0 7px; font-weight: 700; }
        input, select { width: 100%; padding: 12px; border: 1px solid #cfc4be; border-radius: 8px; font: inherit; }
        button { width: 100%; margin-top: 20px; padding: 13px; color: #fff; background: #a92d25; border: 0; border-radius: 8px; font: inherit; font-weight: 700; cursor: pointer; }
        .notice { margin: 16px 0; padding: 12px; border-radius: 8px; background: #e8f6ec; color: #17652b; }
        .error { color: #b42318; font-size: 13px; }
    </style>
</head>
<body>
<main>
    <h1>Parcel tracking update</h1>
    <p class="muted">ShopEase · {{ $shipment->courier }}</p>
    <div class="summary">
        <strong>Order:</strong> #{{ $order->order_number ?: 'ORD-' . $order->id }}<br>
        <strong>Tracking:</strong> {{ $shipment->tracking_number }}<br>
        <strong>Current status:</strong> {{ \Illuminate\Support\Str::headline($order->status) }}<br>
        <strong>Last location:</strong> {{ $shipment->current_location ?: 'No scan location recorded yet' }}
    </div>

    @if (session('success'))
        <div class="notice" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="error" role="alert">{{ $errors->first() }}</div>
    @endif

    @if ($nextStatuses)
        <p>Enter the parcel's current facility or location, then confirm its next shipping stage.</p>
        <form method="POST" action="{{ route('parcel.scan.store', ['token' => $shipment->scan_token]) }}">
            @csrf
            <label for="location">Parcel location</label>
            <input id="location" name="location" maxlength="255" required value="{{ old('location') }}" placeholder="e.g. Calamba sorting hub">

            <label for="status">New shipping status</label>
            <select id="status" name="status" required>
                @foreach ($nextStatuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $nextStatuses[0]) === $status)>{{ \Illuminate\Support\Str::headline($status) }}</option>
                @endforeach
            </select>
            <button type="submit">Save parcel update</button>
        </form>
    @elseif ($order->status === 'delivered')
        <p class="notice">This parcel has already been delivered. No further status updates are available.</p>
    @else
        <p class="notice">The waybill is ready. Parcel status and location updates will be available after the order is prepared and marked Ready to Ship.</p>
    @endif
</main>
</body>
</html>
