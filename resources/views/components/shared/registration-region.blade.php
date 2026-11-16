@props(['selected' => ''])

<div class="form-group">
    <label class="form-label" for="region">Region <span class="required">*</span></label>
    <select id="region" name="region" class="form-input" required>
        <option value="">Select region</option>
        @foreach ([
            '13' => 'National Capital Region (NCR)',
            '14' => 'Cordillera Administrative Region (CAR)',
            '01' => 'Region I (Ilocos Region)',
            '02' => 'Region II (Cagayan Valley)',
            '03' => 'Region III (Central Luzon)',
            '04' => 'Region IV-A (CALABARZON)',
            '17' => 'MIMAROPA Region',
            '05' => 'Region V (Bicol Region)',
            '06' => 'Region VI (Western Visayas)',
            '07' => 'Region VII (Central Visayas)',
            '08' => 'Region VIII (Eastern Visayas)',
            '09' => 'Region IX (Zamboanga Peninsula)',
            '10' => 'Region X (Northern Mindanao)',
            '11' => 'Region XI (Davao Region)',
            '12' => 'Region XII (SOCCSKSARGEN)',
            '16' => 'Region XIII (Caraga)',
            '15' => 'Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)',
        ] as $code => $label)
            <option value="{{ $label }}" data-region-code="{{ $code }}" @selected($selected === $label)>{{ $label }}</option>
        @endforeach
    </select>
    <small class="client-error" data-error-for="region">{{ $errors->first('region') }}</small>
</div>
