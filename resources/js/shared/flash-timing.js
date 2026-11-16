const modalSelector = 'dialog[open], [aria-modal="true"], [role="dialog"], [id$="modal" i], [class*="-modal"], [class*="-overlay"]';

const pendingFlashes = new Map();
let checkScheduled = false;

function hasVisibleModal() {
    return Array.from(document.querySelectorAll(modalSelector)).some(element => {
        if (element.matches('dialog:not([open])') || !element.getClientRects().length) return false;

        const style = getComputedStyle(element);
        const isModal = element.matches('dialog[open], [aria-modal="true"], [role="dialog"]')
            || style.position === 'fixed';
        return isModal && style.visibility === 'visible' && Number(style.opacity) > 0
            && (element.checkVisibility?.({ opacityProperty: true, visibilityProperty: true }) ?? true);
    });
}

function showPendingFlashes() {
    if (hasVisibleModal()) {
        setTimeout(showPendingFlashes, 75);
        return;
    }

    checkScheduled = false;
    const callbacks = Array.from(pendingFlashes.values());
    pendingFlashes.clear();
    callbacks.forEach(show => show());
}

export function deferFlashUntilModalCloses(renderer, show) {
    if (!checkScheduled && !hasVisibleModal()) return false;

    pendingFlashes.set(renderer, show);
    if (!checkScheduled) {
        checkScheduled = true;
        requestAnimationFrame(showPendingFlashes);
    }
    return true;
}

window.deferFlashUntilModalCloses = deferFlashUntilModalCloses;
