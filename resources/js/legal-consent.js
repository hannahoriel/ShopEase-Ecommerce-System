const storagePrefix = 'shopease:legal-section-acknowledgment';
let storageAvailable = true;
let storageErrorReported = false;

function getStorageValue(key) {
    try {
        return window.localStorage.getItem(key);
    } catch (error) {
        storageAvailable = false;
        if (!storageErrorReported) {
            console.error('ShopEase could not read legal acknowledgment progress from this browser.', error);
            storageErrorReported = true;
        }
        return null;
    }
}

function setStorageValue(key, value) {
    try {
        window.localStorage.setItem(key, value);
        return true;
    } catch (error) {
        storageAvailable = false;
        if (!storageErrorReported) {
            console.error('ShopEase could not save legal acknowledgment progress in this browser.', error);
            storageErrorReported = true;
        }
        return false;
    }
}

function removeStorageValue(key) {
    try {
        window.localStorage.removeItem(key);
        return true;
    } catch (error) {
        storageAvailable = false;
        if (!storageErrorReported) {
            console.error('ShopEase could not update legal acknowledgment progress in this browser.', error);
            storageErrorReported = true;
        }
        return false;
    }
}

function documentStorageKey(role, document, version, suffix) {
    return `${storagePrefix}:${role}:${document}:${version}:${suffix}`;
}

function initializeLegalDocument() {
    const article = document.querySelector('[data-legal-consent-page]');
    if (!article) {
        return;
    }

    const role = article.dataset.legalRole;
    const documentType = article.dataset.legalDocument;
    const version = article.dataset.legalVersion;
    const checkboxes = Array.from(article.querySelectorAll('[data-legal-section]'));
    const progress = article.querySelector('[data-legal-progress]');

    if (!role || !documentType || !version || checkboxes.length === 0) {
        console.error('ShopEase legal section acknowledgments are missing required page data.');
        return;
    }

    const sectionsKey = documentStorageKey(role, documentType, version, 'sections');
    const completeKey = documentStorageKey(role, documentType, version, 'complete');
    const savedSections = getStorageValue(sectionsKey);
    const acknowledgedSections = new Set((savedSections || '').split(',').filter(Boolean));

    checkboxes.forEach((checkbox) => {
        checkbox.checked = acknowledgedSections.has(checkbox.dataset.legalSection);
    });

    function updateProgress() {
        const checkedSections = checkboxes
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.dataset.legalSection);
        const allSectionsChecked = checkedSections.length === checkboxes.length;
        const saved = setStorageValue(sectionsKey, checkedSections.join(','));

        if (allSectionsChecked) {
            setStorageValue(completeKey, '1');
        } else {
            removeStorageValue(completeKey);
        }

        if (progress) {
            const countLabel = `${checkedSections.length} of ${checkboxes.length} sections acknowledged`;
            progress.textContent = storageAvailable && saved
                ? `${countLabel}. Your progress is saved in this browser.`
                : `${countLabel}. Progress could not be saved in this browser.`;
        }
    }

    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateProgress));
    updateProgress();
}

function initializeRegistrationConsent() {
    document.querySelectorAll('[data-legal-consent-form]').forEach((form) => {
        const role = form.dataset.legalRole;
        const termsVersion = form.dataset.termsVersion;
        const privacyVersion = form.dataset.privacyVersion;
        const termsCheckbox = form.querySelector('[data-master-consent="terms"]');
        const privacyCheckbox = form.querySelector('[data-master-consent="privacy"]');
        const status = form.querySelector('[data-consent-status]');

        if (!role || !termsVersion || !privacyVersion || !termsCheckbox || !privacyCheckbox) {
            console.error('ShopEase registration consent controls are missing required form data.');
            return;
        }

        const termsCompleteKey = documentStorageKey(role, 'terms', termsVersion, 'complete');
        const privacyCompleteKey = documentStorageKey(role, 'privacy', privacyVersion, 'complete');
        let syncingCheckboxes = false;

        [termsCheckbox, privacyCheckbox].forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                if (!syncingCheckboxes) {
                    delete checkbox.dataset.autoChecked;
                }
            });
        });

        function synchronizeConsent() {
            const termsComplete = getStorageValue(termsCompleteKey) === '1';
            const privacyComplete = getStorageValue(privacyCompleteKey) === '1';

            syncingCheckboxes = true;

            [
                [termsCheckbox, termsComplete],
                [privacyCheckbox, privacyComplete],
            ].forEach(([checkbox, isComplete]) => {
                if (isComplete && !checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.dataset.autoChecked = 'true';
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                } else if (!isComplete && checkbox.dataset.autoChecked === 'true') {
                    checkbox.checked = false;
                    delete checkbox.dataset.autoChecked;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            syncingCheckboxes = false;

            if (status) {
                if (!storageAvailable) {
                    status.textContent = 'Your browser could not save section progress. You can still select the required acknowledgments manually.';
                } else if (termsComplete && privacyComplete) {
                    status.textContent = 'All sections in both documents are acknowledged. Both registration checkboxes are selected.';
                } else {
                    status.textContent = '';
                }

                status.hidden = status.textContent === '';
            }
        }

        window.addEventListener('storage', (event) => {
            if (event.key === termsCompleteKey || event.key === privacyCompleteKey || event.key === null) {
                synchronizeConsent();
            }
        });

        synchronizeConsent();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeLegalDocument();
    initializeRegistrationConsent();
});
