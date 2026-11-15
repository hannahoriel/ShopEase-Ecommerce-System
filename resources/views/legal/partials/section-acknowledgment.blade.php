<label class="section-acknowledgment" for="legal-section-{{ $sectionId }}">
    <input
        id="legal-section-{{ $sectionId }}"
        type="checkbox"
        value="1"
        data-legal-section="{{ $sectionId }}"
    >
    <span>{{ $message }}</span>
</label>
