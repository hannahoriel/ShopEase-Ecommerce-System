const platformSettingsConfigElement =
    document.getElementById('platformSettingsConfig');

const platformSettingsConfig = platformSettingsConfigElement
    ? JSON.parse(platformSettingsConfigElement.textContent)
    : {
        announcements: []
    };

document.addEventListener('DOMContentLoaded', function () {

    const tabs =
        Array.from(
            document.querySelectorAll(
                '[data-platform-tab]'
            )
        );

    const announcementsPanel =
        document.getElementById(
            'announcements-panel'
        );

    const announcementsGrid =
        document.getElementById(
            'announcements-grid'
        );

    const defaultAnnouncementData =
        platformSettingsConfig.announcements || [];

    const policiesPanel =
        document.getElementById(
            'policies-panel'
        );

    const createButton =
        document.getElementById(
            'platform-create-button'
        );

    const createLabel =
        document.getElementById(
            'platform-create-label'
        );

    const announcementModal =
        document.getElementById(
            'create-announcement-modal'
        );

    const policyModal =
        document.getElementById(
            'add-policy-modal'
        );

    const cancelAnnouncement =
        document.getElementById(
            'cancel-announcement-modal'
        );

    const cancelPolicy =
        document.getElementById(
            'cancel-policy-modal'
        );

    const submitAnnouncement =
        document.getElementById(
            'submit-announcement-modal'
        );

    const submitPolicy =
        document.getElementById(
            'submit-policy-modal'
        );

    let activePlatformTab =
        'announcements';

    let editingAnnouncementId = null;
    let editingAnnouncementSource = null;

    let currentAnnouncementBannerDataUrl = '';
    let currentAnnouncementBannerName = '';


    /* =========================================================
       TAB SWITCHING
    ========================================================== */
    function switchPlatformTab(tabName) {
        activePlatformTab =
            tabName;

        tabs.forEach(tab => {
            const active =
                tab.dataset.platformTab ===
                tabName;

            tab.classList.toggle(
                'active',
                active
            );

            tab.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });

        announcementsPanel.classList.toggle(
            'active',
            tabName === 'announcements'
        );

        policiesPanel.classList.toggle(
            'active',
            tabName === 'policies'
        );

        createLabel.textContent =
            tabName === 'announcements'
                ? 'Create Announcement'
                : 'Add New Policy';
    }

    tabs.forEach(tab => {
        tab.addEventListener(
            'click',
            function () {
                switchPlatformTab(
                    this.dataset.platformTab
                );
            }
        );
    });


    /* =========================================================
       FORM RESET HELPERS
       Any unfinished admin input is cleared when a modal closes.
    ========================================================== */
    function resetAnnouncementModal() {
        const titleInput =
            document.getElementById(
                'announcement-title-input'
            );

        const messageInput =
            document.getElementById(
                'announcement-message-input'
            );

        const dateInput =
            document.getElementById(
                'announcement-date-input'
            );

        const timeInput =
            document.getElementById(
                'announcement-time-input'
            );

        const bannerInputElement =
            document.getElementById(
                'announcement-banner-input'
            );

        const bannerFileElement =
            document.getElementById(
                'announcement-banner-file'
            );

        if (titleInput) {
            titleInput.value = '';
        }

        if (messageInput) {
            messageInput.value = '';
        }

        if (dateInput) {
            dateInput.value = '';
            dateInput.disabled = false;
        }

        if (timeInput) {
            timeInput.value = '';
            timeInput.disabled = false;
        }

        if (bannerInputElement) {
            bannerInputElement.value = '';
        }

        if (bannerFileElement) {
            bannerFileElement.textContent = '';
        }

        currentAnnouncementBannerDataUrl = '';
        currentAnnouncementBannerName = '';

        document
            .querySelectorAll(
                'input[name="announcement_type"]'
            )
            .forEach((radio, index) => {
                radio.checked = index === 0;
            });

        document
            .querySelectorAll(
                'input[name="announcement_audience"]'
            )
            .forEach((radio, index) => {
                radio.checked = index === 0;
            });

        document
            .querySelectorAll(
                'input[name="announcement_status"]'
            )
            .forEach((radio, index) => {
                radio.checked = index === 0;
            });

        document
            .querySelectorAll(
                '[data-announcement-type-card]'
            )
            .forEach((card, index) => {
                card.classList.toggle(
                    'is-selected',
                    index === 0
                );
            });

        document
            .querySelectorAll(
                '[data-announcement-status-card]'
            )
            .forEach((card, index) => {
                card.classList.toggle(
                    'is-selected',
                    index === 0
                );
            });

        const titleCount =
            document.getElementById(
                'announcement-title-count'
            );

        const messageCount =
            document.getElementById(
                'announcement-message-count'
            );

        if (titleCount) {
            titleCount.textContent = '0/80';
        }

        if (messageCount) {
            messageCount.textContent = '0/500';
        }

        announcementPreviewTouched = false;

        if (
            typeof updateAnnouncementScheduleFields ===
            'function'
        ) {
            updateAnnouncementScheduleFields();
        }

        if (
            typeof renderAnnouncementPreview ===
            'function'
        ) {
            renderAnnouncementPreview();
        }

        setAnnouncementModalMode(
            'create'
        );
    }

    function resetPolicyModal() {
        const ids = [
            'policy-title-input',
            'policy-description-input',
            'policy-content-input',
            'policy-date-input',
            'policy-time-input'
        ];

        ids.forEach(id => {
            const element =
                document.getElementById(
                    id
                );

            if (element) {
                element.value = '';
            }
        });

        const category =
            document.getElementById(
                'policy-category-input'
            );

        if (category) {
            category.value = '';
        }

        const counts = {
            'policy-title-count': '0/80',
            'policy-description-count': '0/500',
            'policy-content-count': '0/500'
        };

        Object.entries(counts)
            .forEach(([id, value]) => {
                const element =
                    document.getElementById(
                        id
                    );

                if (element) {
                    element.textContent =
                        value;
                }
            });
    }


    /* =========================================================
       MODAL HELPERS
    ========================================================== */
    function openPlatformModal(
        modal,
        resetForm = true
    ) {
        if (!modal) {
            return;
        }

        if (
            resetForm &&
            modal === announcementModal
        ) {
            resetAnnouncementModal();
        }

        if (
            resetForm &&
            modal === policyModal
        ) {
            resetPolicyModal();
        }

        modal.classList.add(
            'is-open'
        );

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closePlatformModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove(
            'is-open'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        if (modal === announcementModal) {
            resetAnnouncementModal();

            editingAnnouncementId = null;
            editingAnnouncementSource = null;
        }

        if (modal === policyModal) {
            resetPolicyModal();
        }

        if (
            !announcementModal.classList.contains('is-open') &&
            !policyModal.classList.contains('is-open')
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    createButton.addEventListener(
        'click',
        function () {
            if (
                activePlatformTab ===
                'announcements'
            ) {
                editingAnnouncementId = null;
                editingAnnouncementSource = null;

                setAnnouncementModalMode(
                    'create'
                );

                openPlatformModal(
                    announcementModal
                );
            } else {
                openPlatformModal(
                    policyModal
                );
            }
        }
    );

    cancelAnnouncement.addEventListener(
        'click',
        function () {
            closePlatformModal(
                announcementModal
            );
        }
    );

    cancelPolicy.addEventListener(
        'click',
        function () {
            closePlatformModal(
                policyModal
            );
        }
    );

    announcementModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                announcementModal
            ) {
                closePlatformModal(
                    announcementModal
                );
            }
        }
    );

    policyModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                policyModal
            ) {
                closePlatformModal(
                    policyModal
                );
            }
        }
    );


    /* =========================================================
       OPTION CARD SELECTION
    ========================================================== */
    function bindOptionCards(
        selector,
        radioName
    ) {
        const cards =
            Array.from(
                document.querySelectorAll(
                    selector
                )
            );

        cards.forEach(card => {
            const radio =
                card.querySelector(
                    `input[name="${radioName}"]`
                );

            if (!radio) {
                return;
            }

            radio.addEventListener(
                'change',
                function () {
                    cards.forEach(item => {
                        item.classList.toggle(
                            'is-selected',
                            item === card
                        );
                    });
                }
            );
        });
    }

    bindOptionCards(
        '[data-announcement-type-card]',
        'announcement_type'
    );

    bindOptionCards(
        '[data-announcement-status-card]',
        'announcement_status'
    );

    document
        .querySelectorAll(
            'input[name="announcement_type"], input[name="announcement_status"]'
        )
        .forEach(radio => {
            radio.addEventListener(
                'change',
                function () {
                    announcementPreviewTouched = true;

                    if (
                        this.name ===
                        'announcement_status'
                    ) {
                        updateAnnouncementScheduleFields();
                    }

                    if (
                        typeof renderAnnouncementPreview ===
                        'function'
                    ) {
                        renderAnnouncementPreview();
                    }
                }
            );
        });


    /* =========================================================
       CHARACTER COUNTERS
    ========================================================== */
    function bindCounter(
        inputId,
        counterId,
        max
    ) {
        const input =
            document.getElementById(
                inputId
            );

        const counter =
            document.getElementById(
                counterId
            );

        if (!input || !counter) {
            return;
        }

        function renderCount() {
            counter.textContent =
                `${input.value.length}/${max}`;
        }

        input.addEventListener(
            'input',
            renderCount
        );

        renderCount();
    }

    bindCounter(
        'announcement-title-input',
        'announcement-title-count',
        80
    );

    bindCounter(
        'announcement-message-input',
        'announcement-message-count',
        500
    );

    bindCounter(
        'policy-title-input',
        'policy-title-count',
        80
    );

    bindCounter(
        'policy-description-input',
        'policy-description-count',
        500
    );

    bindCounter(
        'policy-content-input',
        'policy-content-count',
        500
    );


    /* =========================================================
       ANNOUNCEMENT PREVIEW
    ========================================================== */
    const announcementTitleInput =
        document.getElementById(
            'announcement-title-input'
        );

    const announcementMessageInput =
        document.getElementById(
            'announcement-message-input'
        );

    const announcementDateInput =
        document.getElementById(
            'announcement-date-input'
        );

    const announcementTimeInput =
        document.getElementById(
            'announcement-time-input'
        );

    const announcementScheduleFields =
        document.getElementById(
            'announcement-schedule-fields'
        );

    const previewTitle =
        document.getElementById(
            'announcement-preview-title'
        );

    const previewDescription =
        document.getElementById(
            'announcement-preview-description'
        );

    const previewAudience =
        document.getElementById(
            'announcement-preview-audience'
        );

    const previewDate =
        document.getElementById(
            'announcement-preview-date'
        );

    const previewIcon =
        document.getElementById(
            'announcement-preview-icon'
        );

    const previewStatus =
        document.getElementById(
            'announcement-preview-status'
        );

    let announcementPreviewTouched =
        false;

    function formatPreviewDate(value) {
        if (!value) {
            return '';
        }

        const parts =
            value.split('-');

        if (parts.length !== 3) {
            return value;
        }

        const date =
            new Date(
                Number(parts[0]),
                Number(parts[1]) - 1,
                Number(parts[2])
            );

        return new Intl.DateTimeFormat(
            'en-US',
            {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }
        ).format(date);
    }

    function formatPreviewTime(value) {
        if (!value) {
            return '';
        }

        const parts =
            value.split(':');

        const date =
            new Date();

        date.setHours(
            Number(parts[0] || 0),
            Number(parts[1] || 0),
            0,
            0
        );

        return new Intl.DateTimeFormat(
            'en-US',
            {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }
        ).format(date);
    }

    function updateAnnouncementScheduleFields() {
        const checkedStatus =
            document.querySelector(
                'input[name="announcement_status"]:checked'
            );

        const isScheduled =
            checkedStatus &&
            checkedStatus.value === 'Schedule';

        announcementScheduleFields.classList.toggle(
            'is-visible',
            Boolean(isScheduled)
        );

        announcementDateInput.required =
            Boolean(isScheduled);

        announcementTimeInput.required =
            Boolean(isScheduled);

        if (!isScheduled) {
            announcementDateInput.value = '';
            announcementTimeInput.value = '';
        }
    }

    function renderAnnouncementPreview() {
        previewTitle.textContent =
            announcementTitleInput.value.trim();

        previewDescription.textContent =
            announcementMessageInput.value.trim();

        const checkedAudience =
            document.querySelector(
                'input[name="announcement_audience"]:checked'
            );

        previewAudience.textContent =
            announcementPreviewTouched &&
            checkedAudience
                ? checkedAudience.value
                : '';

        const checkedType =
            document.querySelector(
                'input[name="announcement_type"]:checked'
            );

        const isPolicyUpdate =
            checkedType &&
            checkedType.value === 'Policy Update';

        if (currentAnnouncementBannerDataUrl) {
            previewIcon.classList.add(
                'has-cover-photo'
            );

            previewIcon.innerHTML = `
                <img
                    src="${currentAnnouncementBannerDataUrl}"
                    alt="Announcement cover photo"
                >
            `;
        } else {
            previewIcon.classList.remove(
                'has-cover-photo'
            );

            if (isPolicyUpdate) {
                previewIcon.style.background =
                    '#D8C1F0';

                previewIcon.style.color =
                    '#48208D';

                previewIcon.innerHTML = `
                    <svg
                        viewBox="0 0 64 64"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                    </svg>
                `;
            } else {
                previewIcon.style.background =
                    '#FFD1D4';

                previewIcon.style.color =
                    '#C8121B';

                previewIcon.innerHTML = `
                    <svg
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                    </svg>
                `;
            }
        }

        const checkedStatus =
            document.querySelector(
                'input[name="announcement_status"]:checked'
            );

        if (!announcementPreviewTouched) {
            previewStatus.textContent = '';
            previewStatus.style.background = 'transparent';
            previewStatus.style.color = 'transparent';
        } else {
            previewStatus.textContent =
                checkedStatus &&
                checkedStatus.value === 'Schedule'
                    ? 'Scheduled'
                    : 'Published';

            if (
                checkedStatus &&
                checkedStatus.value === 'Schedule'
            ) {
                previewStatus.style.background =
                    '#FFF0C7';

                previewStatus.style.color =
                    '#8A5A00';
            } else {
                previewStatus.style.background =
                    '#D9EBD8';

                previewStatus.style.color =
                    '#3D8736';
            }
        }

        const previewDateText =
            formatPreviewDate(
                announcementDateInput.value
            );

        const previewTimeText =
            formatPreviewTime(
                announcementTimeInput.value
            );

        const shouldShowPreviewSchedule =
            announcementPreviewTouched &&
            checkedStatus &&
            checkedStatus.value === 'Schedule' &&
            (previewDateText || previewTimeText);

        previewDate.innerHTML =
            shouldShowPreviewSchedule
                ? `
                    <div>${previewDateText}</div>
                    <div>${previewTimeText}</div>
                `
                : '';
    }

    [
        announcementTitleInput,
        announcementMessageInput,
        announcementDateInput,
        announcementTimeInput
    ].forEach(input => {
        input.addEventListener(
            'input',
            function () {
                announcementPreviewTouched = true;
                renderAnnouncementPreview();
            }
        );

        input.addEventListener(
            'change',
            function () {
                announcementPreviewTouched = true;
                renderAnnouncementPreview();
            }
        );
    });

    document
        .querySelectorAll(
            'input[name="announcement_audience"]'
        )
        .forEach(radio => {
            radio.addEventListener(
                'change',
                function () {
                    announcementPreviewTouched = true;
                    renderAnnouncementPreview();
                }
            );
        });


    /* =========================================================
       BANNER UPLOAD
    ========================================================== */
    const bannerInput =
        document.getElementById(
            'announcement-banner-input'
        );

    const bannerDrop =
        document.getElementById(
            'announcement-banner-drop'
        );

    const bannerFile =
        document.getElementById(
            'announcement-banner-file'
        );

    function optimizeBannerImage(file) {
        return new Promise(
            (resolve, reject) => {
                const reader =
                    new FileReader();

                reader.onload =
                    function () {
                        const image =
                            new Image();

                        image.onload =
                            function () {
                                const targetWidth =
                                    1200;

                                const targetHeight =
                                    628;

                                const canvas =
                                    document.createElement(
                                        'canvas'
                                    );

                                canvas.width =
                                    targetWidth;

                                canvas.height =
                                    targetHeight;

                                const context =
                                    canvas.getContext(
                                        '2d'
                                    );

                                const sourceRatio =
                                    image.width /
                                    image.height;

                                const targetRatio =
                                    targetWidth /
                                    targetHeight;

                                let sourceX = 0;
                                let sourceY = 0;
                                let sourceWidth =
                                    image.width;
                                let sourceHeight =
                                    image.height;

                                if (
                                    sourceRatio >
                                    targetRatio
                                ) {
                                    sourceWidth =
                                        image.height *
                                        targetRatio;

                                    sourceX =
                                        (
                                            image.width -
                                            sourceWidth
                                        ) / 2;
                                } else {
                                    sourceHeight =
                                        image.width /
                                        targetRatio;

                                    sourceY =
                                        (
                                            image.height -
                                            sourceHeight
                                        ) / 2;
                                }

                                context.drawImage(
                                    image,
                                    sourceX,
                                    sourceY,
                                    sourceWidth,
                                    sourceHeight,
                                    0,
                                    0,
                                    targetWidth,
                                    targetHeight
                                );

                                resolve(
                                    canvas.toDataURL(
                                        'image/jpeg',
                                        0.78
                                    )
                                );
                            };

                        image.onerror =
                            reject;

                        image.src =
                            reader.result;
                    };

                reader.onerror =
                    reject;

                reader.readAsDataURL(
                    file
                );
            }
        );
    }

    async function handleBannerFile(file) {
        if (!file) {
            return;
        }

        const validTypes = [
            'image/jpeg',
            'image/png'
        ];

        const maxSize =
            5 * 1024 * 1024;

        if (
            !validTypes.includes(file.type)
        ) {
            bannerFile.textContent =
                'Please choose a JPG or PNG image.';

            return;
        }

        if (
            file.size > maxSize
        ) {
            bannerFile.textContent =
                'Image must be 5MB or smaller.';

            return;
        }

        bannerFile.textContent =
            'Preparing cover photo...';

        try {
            currentAnnouncementBannerDataUrl =
                await optimizeBannerImage(
                    file
                );

            currentAnnouncementBannerName =
                file.name;

            bannerFile.textContent =
                file.name;

            announcementPreviewTouched =
                true;

            renderAnnouncementPreview();
        } catch (error) {
            currentAnnouncementBannerDataUrl =
                '';

            currentAnnouncementBannerName =
                '';

            bannerFile.textContent =
                'Unable to preview this image.';
        }
    }

    bannerDrop.addEventListener(
        'click',
        function () {
            bannerInput.click();
        }
    );

    bannerDrop.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Enter' ||
                event.key === ' '
            ) {
                event.preventDefault();
                bannerInput.click();
            }
        }
    );

    bannerInput.addEventListener(
        'change',
        function () {
            handleBannerFile(
                this.files[0]
            );
        }
    );

    ['dragenter', 'dragover']
        .forEach(eventName => {
            bannerDrop.addEventListener(
                eventName,
                function (event) {
                    event.preventDefault();
                    bannerDrop.classList.add(
                        'is-dragover'
                    );
                }
            );
        });

    ['dragleave', 'drop']
        .forEach(eventName => {
            bannerDrop.addEventListener(
                eventName,
                function (event) {
                    event.preventDefault();
                    bannerDrop.classList.remove(
                        'is-dragover'
                    );
                }
            );
        });

    bannerDrop.addEventListener(
        'drop',
        function (event) {
            const file =
                event.dataTransfer.files[0];

            handleBannerFile(
                file
            );
        }
    );


    /* =========================================================
       NEW ANNOUNCEMENT CARDS
       Front-end demo persistence until backend/database is connected.
    ========================================================== */
    const LOCAL_ANNOUNCEMENTS_KEY =
        'shopease_platform_announcements';

    const DEFAULT_ANNOUNCEMENT_OVERRIDES_KEY =
        'shopease_platform_default_announcement_overrides';

    const HIDDEN_DEFAULT_ANNOUNCEMENTS_KEY =
        'shopease_platform_hidden_default_announcements';

    function escapePlatformHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getAnnouncementCardIcon(type) {
        if (type === 'Policy Update') {
            return `
                <div class="platform-card-icon theme-purple">
                    <svg
                        viewBox="0 0 64 64"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                    </svg>
                </div>
            `;
        }

        return `
            <div class="platform-card-icon theme-red">
                <svg
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                </svg>
            </div>
        `;
    }

    function getAnnouncementStatusStyle(status) {
        return status === 'Scheduled'
            ? 'background:#FFF0C7;color:#8A5A00;'
            : 'background:#D9EBD8;color:#3D8736;';
    }

    function getAnnouncementCardMedia(item) {
        if (item.bannerDataUrl) {
            return `
                <div class="platform-card-cover">
                    <img
                        src="${escapePlatformHtml(
                            item.bannerDataUrl
                        )}"
                        alt="${escapePlatformHtml(
                            item.title || 'Announcement'
                        )} cover photo"
                    >
                </div>
            `;
        }

        return getAnnouncementCardIcon(
            item.type
        );
    }

    function buildAnnouncementCard(item) {
        const safeId =
            escapePlatformHtml(item.id);

        const safeTitle =
            escapePlatformHtml(item.title);

        const safeDescription =
            escapePlatformHtml(item.description);

        const safeAudience =
            escapePlatformHtml(item.audience);

        const safeStatus =
            escapePlatformHtml(item.status);

        const safeDate =
            escapePlatformHtml(item.date);

        const safeTime =
            escapePlatformHtml(item.time);

        return `
            <article
                class="platform-card platform-card-dynamic"
                data-announcement-id="${safeId}"
            >
                <div class="platform-card-main">
                    ${getAnnouncementCardMedia(item)}

                    <h3 class="platform-card-title">
                        ${safeTitle}
                    </h3>

                    <p class="platform-card-description">
                        ${safeDescription}
                    </p>
                </div>

                <div class="platform-card-footer">
                    <div class="platform-card-audience">
                        <svg
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                            <path d="M3 12h18M12 3c2.3 2.6 3.5 5.6 3.5 9S14.3 18.4 12 21c-2.3-2.6-3.5-5.6-3.5-9S9.7 5.6 12 3Z" stroke-width="1.6"/>
                        </svg>

                        <span>${safeAudience}</span>
                    </div>

                    <div class="platform-card-meta">
                        <span
                            class="platform-status"
                            style="${getAnnouncementStatusStyle(item.status)}"
                        >
                            ${safeStatus}
                        </span>

                        <div class="platform-card-date">
                            <div>${safeDate}</div>
                            <div>${safeTime}</div>
                        </div>

                        <div class="platform-more-wrap">
                            <button
                                type="button"
                                class="platform-more js-announcement-more"
                                aria-label="More announcement actions"
                                aria-expanded="false"
                            >
                                <svg
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle cx="12" cy="5" r="2"/>
                                    <circle cx="12" cy="12" r="2"/>
                                    <circle cx="12" cy="19" r="2"/>
                                </svg>
                            </button>

                            <div class="platform-action-menu">
                                <button
                                    type="button"
                                    class="platform-action-item"
                                    data-announcement-action="edit"
                                >
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M16.862 3.487a2.12 2.12 0 0 1 3 3L8.5 17.85 4 19l1.15-4.5 11.712-11.013Z"
                                        />
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                <button
                                    type="button"
                                    class="platform-action-item delete"
                                    data-announcement-action="delete"
                                >
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"
                                        />
                                    </svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    function getStoredAnnouncements() {
        try {
            const stored =
                JSON.parse(
                    localStorage.getItem(
                        LOCAL_ANNOUNCEMENTS_KEY
                    ) || '[]'
                );

            return Array.isArray(stored)
                ? stored
                : [];
        } catch (error) {
            return [];
        }
    }

    function saveStoredAnnouncements(items) {
        try {
            localStorage.setItem(
                LOCAL_ANNOUNCEMENTS_KEY,
                JSON.stringify(items)
            );
        } catch (error) {
            // Front-end demo remains usable without localStorage.
        }
    }

    function getDefaultAnnouncementOverrides() {
        try {
            const value =
                JSON.parse(
                    localStorage.getItem(
                        DEFAULT_ANNOUNCEMENT_OVERRIDES_KEY
                    ) || '{}'
                );

            return (
                value &&
                typeof value === 'object' &&
                !Array.isArray(value)
            )
                ? value
                : {};
        } catch (error) {
            return {};
        }
    }

    function saveDefaultAnnouncementOverrides(value) {
        try {
            localStorage.setItem(
                DEFAULT_ANNOUNCEMENT_OVERRIDES_KEY,
                JSON.stringify(value)
            );
        } catch (error) {
            // Ignore storage failures in the front-end demo.
        }
    }

    function getHiddenDefaultAnnouncementIds() {
        try {
            const value =
                JSON.parse(
                    localStorage.getItem(
                        HIDDEN_DEFAULT_ANNOUNCEMENTS_KEY
                    ) || '[]'
                );

            return Array.isArray(value)
                ? value.map(String)
                : [];
        } catch (error) {
            return [];
        }
    }

    function saveHiddenDefaultAnnouncementIds(value) {
        try {
            localStorage.setItem(
                HIDDEN_DEFAULT_ANNOUNCEMENTS_KEY,
                JSON.stringify(value)
            );
        } catch (error) {
            // Ignore storage failures in the front-end demo.
        }
    }

    function normalizeDefaultAnnouncements() {
        const overrides =
            getDefaultAnnouncementOverrides();

        const hidden =
            new Set(
                getHiddenDefaultAnnouncementIds()
            );

        return defaultAnnouncementData
            .map((item, index) => {
                const id =
                    `default-${index}`;

                const base = {
                    ...item,
                    id,
                    source: 'default',
                    type:
                        item.icon === 'document'
                            ? 'Policy Update'
                            : 'Announcement',
                    rawDate: '',
                    rawTime: ''
                };

                return {
                    ...base,
                    ...(overrides[id] || {}),
                    id,
                    source: 'default'
                };
            })
            .filter(item =>
                !hidden.has(
                    String(item.id)
                )
            );
    }

    function getAllAnnouncements() {
        const stored =
            getStoredAnnouncements()
                .map(item => ({
                    ...item,
                    id: String(item.id),
                    source: 'local'
                }));

        return [
            ...stored,
            ...normalizeDefaultAnnouncements()
        ];
    }

    function renderAllAnnouncementCards() {
        if (!announcementsGrid) {
            return;
        }

        const items =
            getAllAnnouncements();

        announcementsGrid.innerHTML =
            items
                .map(buildAnnouncementCard)
                .join('');

        const placeholderCount =
            Math.max(
                0,
                8 - items.length
            );

        for (
            let index = 0;
            index < placeholderCount;
            index += 1
        ) {
            announcementsGrid.insertAdjacentHTML(
                'beforeend',
                `
                    <div
                        class="platform-empty-card"
                        aria-hidden="true"
                    ></div>
                `
            );
        }
    }

    function findAnnouncementById(id) {
        const stringId =
            String(id);

        return getAllAnnouncements()
            .find(item =>
                String(item.id) === stringId
            ) || null;
    }

    function closeAllAnnouncementMenus() {
        announcementsGrid
            ?.querySelectorAll(
                '.platform-action-menu.is-open'
            )
            .forEach(menu => {
                menu.classList.remove(
                    'is-open'
                );

                const trigger =
                    menu.parentElement?.querySelector(
                        '.js-announcement-more'
                    );

                trigger?.setAttribute(
                    'aria-expanded',
                    'false'
                );
            });
    }

    function formatCreatedAnnouncementDate(date) {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }
        ).format(date);
    }

    function formatCreatedAnnouncementTime(date) {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }
        ).format(date);
    }

    function setAnnouncementModalMode(mode) {
        const isEdit =
            mode === 'edit';

        const heading =
            document.getElementById(
                'create-announcement-title'
            );

        const subtitle =
            heading
                ?.parentElement
                ?.querySelector(
                    '.platform-modal-subtitle'
                );

        if (heading) {
            heading.textContent =
                isEdit
                    ? 'Edit Announcement'
                    : 'Create Announcement';
        }

        if (subtitle) {
            subtitle.textContent =
                isEdit
                    ? 'Update the announcement details below.'
                    : 'Post an announcement to keep all users informed.';
        }

        if (submitAnnouncement) {
            submitAnnouncement.textContent =
                isEdit
                    ? 'Save Changes'
                    : 'Upload';
        }
    }

    function setCheckedRadio(
        name,
        value
    ) {
        document
            .querySelectorAll(
                `input[name="${name}"]`
            )
            .forEach(radio => {
                radio.checked =
                    radio.value === value;
            });
    }

    function refreshAnnouncementChoiceCards() {
        document
            .querySelectorAll(
                '[data-announcement-type-card]'
            )
            .forEach(card => {
                const radio =
                    card.querySelector(
                        'input[name="announcement_type"]'
                    );

                card.classList.toggle(
                    'is-selected',
                    Boolean(radio?.checked)
                );
            });

        document
            .querySelectorAll(
                '[data-announcement-status-card]'
            )
            .forEach(card => {
                const radio =
                    card.querySelector(
                        'input[name="announcement_status"]'
                    );

                card.classList.toggle(
                    'is-selected',
                    Boolean(radio?.checked)
                );
            });
    }

    function parseDisplayDateToInput(value) {
        if (!value) {
            return '';
        }

        const parsed =
            new Date(value);

        if (
            Number.isNaN(
                parsed.getTime()
            )
        ) {
            return '';
        }

        const year =
            parsed.getFullYear();

        const month =
            String(
                parsed.getMonth() + 1
            ).padStart(2, '0');

        const day =
            String(
                parsed.getDate()
            ).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function parseDisplayTimeToInput(value) {
        if (!value) {
            return '';
        }

        const match =
            String(value)
                .trim()
                .match(
                    /^(\d{1,2}):(\d{2})\s*(AM|PM)$/i
                );

        if (!match) {
            return '';
        }

        let hour =
            Number(match[1]);

        const minute =
            match[2];

        const meridiem =
            match[3].toUpperCase();

        if (
            meridiem === 'PM' &&
            hour !== 12
        ) {
            hour += 12;
        }

        if (
            meridiem === 'AM' &&
            hour === 12
        ) {
            hour = 0;
        }

        return `${String(hour).padStart(2, '0')}:${minute}`;
    }

    function fillAnnouncementForm(item) {
        announcementTitleInput.value =
            item.title || '';

        announcementMessageInput.value =
            item.description || '';

        setCheckedRadio(
            'announcement_type',
            item.type || 'Announcement'
        );

        setCheckedRadio(
            'announcement_audience',
            item.audience || 'All Users'
        );

        setCheckedRadio(
            'announcement_status',
            item.status === 'Scheduled'
                ? 'Schedule'
                : 'Publish Now'
        );

        announcementDateInput.value =
            item.rawDate ||
            (
                item.status === 'Scheduled'
                    ? parseDisplayDateToInput(
                        item.date
                    )
                    : ''
            );

        announcementTimeInput.value =
            item.rawTime ||
            (
                item.status === 'Scheduled'
                    ? parseDisplayTimeToInput(
                        item.time
                    )
                    : ''
            );

        currentAnnouncementBannerDataUrl =
            item.bannerDataUrl || '';

        currentAnnouncementBannerName =
            item.bannerName || '';

        const bannerFileElement =
            document.getElementById(
                'announcement-banner-file'
            );

        if (bannerFileElement) {
            bannerFileElement.textContent =
                currentAnnouncementBannerName ||
                (
                    currentAnnouncementBannerDataUrl
                        ? 'Attached cover photo'
                        : ''
                );
        }

        const titleCount =
            document.getElementById(
                'announcement-title-count'
            );

        const messageCount =
            document.getElementById(
                'announcement-message-count'
            );

        if (titleCount) {
            titleCount.textContent =
                `${announcementTitleInput.value.length}/80`;
        }

        if (messageCount) {
            messageCount.textContent =
                `${announcementMessageInput.value.length}/500`;
        }

        refreshAnnouncementChoiceCards();

        announcementPreviewTouched =
            true;

        updateAnnouncementScheduleFields();
        renderAnnouncementPreview();
    }

    function openAnnouncementEditor(item) {
        if (!item) {
            return;
        }

        editingAnnouncementId =
            String(item.id);

        editingAnnouncementSource =
            item.source || 'local';

        resetAnnouncementModal();

        /*
         * resetAnnouncementModal restores create defaults,
         * so set edit state again before prefilling.
         */
        editingAnnouncementId =
            String(item.id);

        editingAnnouncementSource =
            item.source || 'local';

        setAnnouncementModalMode(
            'edit'
        );

        fillAnnouncementForm(
            item
        );

        openPlatformModal(
            announcementModal,
            false
        );
    }

    function collectAnnouncementFormData() {
        const type =
            document.querySelector(
                'input[name="announcement_type"]:checked'
            )?.value || 'Announcement';

        const audience =
            document.querySelector(
                'input[name="announcement_audience"]:checked'
            )?.value || 'All Users';

        const selectedStatus =
            document.querySelector(
                'input[name="announcement_status"]:checked'
            )?.value || 'Publish Now';

        const title =
            announcementTitleInput.value.trim();

        const description =
            announcementMessageInput.value.trim();

        if (!title || !description) {
            window.alert(
                'Please enter the announcement title and message.'
            );

            return null;
        }

        let status =
            'Published';

        let date =
            '';

        let time =
            '';

        if (selectedStatus === 'Schedule') {
            if (
                !announcementDateInput.value ||
                !announcementTimeInput.value
            ) {
                window.alert(
                    'Please select the publish date and time.'
                );

                return null;
            }

            status =
                'Scheduled';

            date =
                formatPreviewDate(
                    announcementDateInput.value
                );

            time =
                formatPreviewTime(
                    announcementTimeInput.value
                );
        } else {
            const now =
                new Date();

            date =
                formatCreatedAnnouncementDate(
                    now
                );

            time =
                formatCreatedAnnouncementTime(
                    now
                );
        }

        return {
            id:
                editingAnnouncementId ||
                String(Date.now()),

            source:
                editingAnnouncementSource ||
                'local',

            type,
            title,
            description,
            audience,
            status,
            date,
            time,

            rawDate:
                selectedStatus === 'Schedule'
                    ? announcementDateInput.value
                    : '',

            rawTime:
                selectedStatus === 'Schedule'
                    ? announcementTimeInput.value
                    : '',

            bannerDataUrl:
                currentAnnouncementBannerDataUrl,

            bannerName:
                currentAnnouncementBannerName
        };
    }


    function saveAnnouncementChanges(item) {
        const id =
            String(item.id);

        if (
            editingAnnouncementSource ===
            'default'
        ) {
            const overrides =
                getDefaultAnnouncementOverrides();

            overrides[id] = {
                ...item,
                id,
                source: 'default'
            };

            saveDefaultAnnouncementOverrides(
                overrides
            );
        } else {
            const stored =
                getStoredAnnouncements();

            const index =
                stored.findIndex(entry =>
                    String(entry.id) === id
                );

            const nextItem = {
                ...item,
                id,
                source: 'local'
            };

            if (index >= 0) {
                stored[index] =
                    nextItem;
            } else {
                stored.unshift(
                    nextItem
                );
            }

            saveStoredAnnouncements(
                stored
            );
        }
    }

    function deleteAnnouncement(item) {
        if (!item) {
            return;
        }

        const confirmed =
            window.confirm(
                `Delete "${item.title}"?`
            );

        if (!confirmed) {
            return;
        }

        const id =
            String(item.id);

        if (
            item.source === 'default'
        ) {
            const hidden =
                getHiddenDefaultAnnouncementIds();

            if (!hidden.includes(id)) {
                hidden.push(id);
            }

            saveHiddenDefaultAnnouncementIds(
                hidden
            );
        } else {
            const stored =
                getStoredAnnouncements()
                    .filter(entry =>
                        String(entry.id) !== id
                    );

            saveStoredAnnouncements(
                stored
            );
        }

        closeAllAnnouncementMenus();
        renderAllAnnouncementCards();
    }

    if (announcementsGrid) {
        announcementsGrid.addEventListener(
            'click',
            function (event) {
                const trigger =
                    event.target.closest(
                        '.js-announcement-more'
                    );

                if (trigger) {
                    event.stopPropagation();

                    const wrap =
                        trigger.closest(
                            '.platform-more-wrap'
                        );

                    const menu =
                        wrap?.querySelector(
                            '.platform-action-menu'
                        );

                    const alreadyOpen =
                        menu?.classList.contains(
                            'is-open'
                        );

                    closeAllAnnouncementMenus();

                    if (
                        menu &&
                        !alreadyOpen
                    ) {
                        menu.classList.add(
                            'is-open'
                        );

                        trigger.setAttribute(
                            'aria-expanded',
                            'true'
                        );
                    }

                    return;
                }

                const actionButton =
                    event.target.closest(
                        '[data-announcement-action]'
                    );

                if (!actionButton) {
                    return;
                }

                event.stopPropagation();

                const card =
                    actionButton.closest(
                        '[data-announcement-id]'
                    );

                const item =
                    findAnnouncementById(
                        card?.dataset
                            .announcementId
                    );

                if (!item) {
                    return;
                }

                const action =
                    actionButton.dataset
                        .announcementAction;

                closeAllAnnouncementMenus();

                if (action === 'edit') {
                    openAnnouncementEditor(
                        item
                    );

                    return;
                }

                if (action === 'delete') {
                    deleteAnnouncement(
                        item
                    );
                }
            }
        );
    }

    document.addEventListener(
        'click',
        function (event) {
            if (
                !event.target.closest(
                    '.platform-more-wrap'
                )
            ) {
                closeAllAnnouncementMenus();
            }
        }
    );


    /* =========================================================
       DEMO SUBMIT BUTTONS
       Front-end only until backend routes are connected.
    ========================================================== */
    submitAnnouncement.addEventListener(
        'click',
        function () {
            const announcement =
                collectAnnouncementFormData();

            if (!announcement) {
                return;
            }

            if (editingAnnouncementId) {
                saveAnnouncementChanges(
                    announcement
                );
            } else {
                const stored =
                    getStoredAnnouncements();

                stored.unshift({
                    ...announcement,
                    id: String(
                        announcement.id
                    ),
                    source: 'local'
                });

                saveStoredAnnouncements(
                    stored
                );
            }

            editingAnnouncementId = null;
            editingAnnouncementSource = null;

            renderAllAnnouncementCards();

            switchPlatformTab(
                'announcements'
            );

            closePlatformModal(
                announcementModal
            );
        }
    );

    submitPolicy.addEventListener(
        'click',
        function () {
            closePlatformModal(
                policyModal
            );
        }
    );


    /* =========================================================
       ESCAPE KEY
    ========================================================== */
    document.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Escape'
            ) {
                return;
            }

            if (
                announcementModal.classList.contains(
                    'is-open'
                )
            ) {
                closePlatformModal(
                    announcementModal
                );

                return;
            }

            if (
                policyModal.classList.contains(
                    'is-open'
                )
            ) {
                closePlatformModal(
                    policyModal
                );
            }
        }
    );


    switchPlatformTab(
        'announcements'
    );

    resetAnnouncementModal();
    resetPolicyModal();
    updateAnnouncementScheduleFields();
    renderAllAnnouncementCards();

});