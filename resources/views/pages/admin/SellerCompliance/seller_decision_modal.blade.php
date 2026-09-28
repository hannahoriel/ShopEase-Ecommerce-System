{{-- partials/seller_decision-modal.blade.php
     One partial for BOTH "Remove Product" and "Issue Warning".
     Vars: $type (remove|warn), $title, $intro, $submit, $reasons --}}
<div id="decisionModal-{{ $type }}" class="sc-overlay sc-overlay--decision" data-decision="{{ $type }}"
     aria-hidden="true" hidden>
    <div class="decision-dialog decision-dialog--{{ $type }}" role="dialog" aria-modal="true"
         aria-labelledby="decisionTitle-{{ $type }}" tabindex="-1">

        <h2 id="decisionTitle-{{ $type }}">{{ $title }}</h2>
        <p class="decision-intro">{{ $intro }}</p>

        <fieldset class="decision-reasons">
            <legend>Reason<span>*</span></legend>
            @foreach ($reasons as $reason)
                <label class="decision-option">
                    <input type="radio" name="reason-{{ $type }}" value="{{ $reason }}">
                    <span class="decision-radio"></span>
                    <span>{{ $reason === 'Other' ? 'Other (please specify)' : $reason }}</span>
                </label>
            @endforeach
        </fieldset>

        <div class="decision-details">
            <label for="decisionDetails-{{ $type }}">Additional Details (Optional)</label>
            <div class="decision-textarea-wrap">
                <textarea id="decisionDetails-{{ $type }}" maxlength="300" rows="3"
                          placeholder="Write additional details here..."></textarea>
                <span data-counter>0/300</span>
            </div>
        </div>

        <div class="decision-actions">
            <button type="button" class="decision-cancel" data-action="decision-cancel">Cancel</button>
            <button type="button" class="decision-submit" data-action="decision-submit">{{ $submit }}</button>
        </div>
    </div>
</div>
