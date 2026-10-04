const logisticsManagementConfigElement =
    document.getElementById('logisticsManagementConfig');

const logisticsManagementConfig = logisticsManagementConfigElement
    ? JSON.parse(logisticsManagementConfigElement.textContent)
    : {
        pendingRequests: [],
        pendingTotalEntries: 0,
        companies: [],
        rejectedArchive: [],
        totalEntries: 0,
        stats: {
            pending_requests: 0,
            accepted_logistics: 0,
            rejected_logistics: 0,
            total_companies: 0
        }
    };

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       DATA
    ========================================================== */
    const pendingLogisticsRequests =
        logisticsManagementConfig.pendingRequests || [];

    let pendingOriginalTotalEntries =
        Number(logisticsManagementConfig.pendingTotalEntries || 0);

    const logisticsCompanies =
        logisticsManagementConfig.companies || [];

    const rejectedLogisticsArchive =
        logisticsManagementConfig.rejectedArchive || [];

    let originalTotalEntries =
        Number(logisticsManagementConfig.totalEntries || 0);

    const pendingLogisticsStatCount =
        document.getElementById('pending-logistics-stat-count');

    const acceptedLogisticsStatCount =
        document.getElementById('accepted-logistics-stat-count');

    const rejectedLogisticsStatCount =
        document.getElementById('rejected-logistics-stat-count');

    const totalLogisticsStatCount =
        document.getElementById('total-logistics-stat-count');

    const rejectedLogisticsButton =
        document.getElementById('rejected-logistics-button');

    const rejectedLogisticsModal =
        document.getElementById('rejected-logistics-modal');

    const closeRejectedLogistics =
        document.getElementById('close-rejected-logistics');

    const closeRejectedLogisticsBottom =
        document.getElementById('close-rejected-logistics-bottom');

    const rejectedLogisticsSearch =
        document.getElementById('rejected-logistics-search');

    const rejectedLogisticsBody =
        document.getElementById('rejected-logistics-body');

    const rejectedLogisticsCount =
        document.getElementById('rejected-logistics-count');

    const rejectedLogisticsDateSort =
        document.getElementById('rejected-logistics-date-sort');

    const rejectedLogisticsSortArrow =
        document.getElementById('rejected-logistics-sort-arrow');

    let rejectedLogisticsSortDirection =
        'desc';

    let pendingStatValue =
        Number(logisticsManagementConfig.stats.pending_requests || 0);

    let acceptedStatValue =
        Number(logisticsManagementConfig.stats.accepted_logistics || 0);

    let rejectedStatValue =
        Number(logisticsManagementConfig.stats.rejected_logistics || 0);

    let totalStatValue =
        Number(logisticsManagementConfig.stats.total_companies || 0);

    function renderLogisticsStats() {
        if (pendingLogisticsStatCount) {
            pendingLogisticsStatCount.textContent =
                pendingStatValue.toLocaleString();
        }

        if (acceptedLogisticsStatCount) {
            acceptedLogisticsStatCount.textContent =
                acceptedStatValue.toLocaleString();
        }

        if (rejectedLogisticsStatCount) {
            rejectedLogisticsStatCount.textContent =
                rejectedStatValue.toLocaleString();
        }

        if (totalLogisticsStatCount) {
            totalLogisticsStatCount.textContent =
                totalStatValue.toLocaleString();
        }
    }

    /* =========================================================
       REJECTED LOGISTICS ARCHIVE
       Mirrors the search / date-sort / modal behavior in Registrations.
    ========================================================== */
    function getRejectedLogisticsDate(entry) {
        const parsed =
            Date.parse(
                entry.rejected_display || ''
            );

        return Number.isNaN(parsed)
            ? 0
            : parsed;
    }

    function getFilteredRejectedLogistics() {
        const query =
            normalize(
                rejectedLogisticsSearch
                    ? rejectedLogisticsSearch.value
                    : ''
            );

        const rows =
            rejectedLogisticsArchive.filter(entry => {
                if (!query) {
                    return true;
                }

                return [
                    entry.company,
                    entry.email,
                    entry.phone,
                    entry.contact_person,
                    entry.rejected_display
                ].some(value =>
                    normalize(value).includes(query)
                );
            });

        rows.sort((a, b) => {
            const dateA =
                getRejectedLogisticsDate(a);

            const dateB =
                getRejectedLogisticsDate(b);

            return rejectedLogisticsSortDirection === 'asc'
                ? dateA - dateB
                : dateB - dateA;
        });

        return rows;
    }

    function rejectedCompanyInitials(name) {
        return String(name || '')
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(part =>
                part.charAt(0).toUpperCase()
            )
            .join('');
    }

    function renderRejectedLogisticsArchive() {
        if (
            !rejectedLogisticsBody ||
            !rejectedLogisticsCount
        ) {
            return;
        }

        const rows =
            getFilteredRejectedLogistics();

        if (!rows.length) {
            rejectedLogisticsBody.innerHTML = `
                <tr>
                    <td
                        colspan="5"
                        class="px-6 py-10 text-center
                               text-[12px] text-gray-400"
                    >
                        No rejected logistics found.
                    </td>
                </tr>
            `;
        } else {
            rejectedLogisticsBody.innerHTML =
                rows.map(entry => `
                    <tr class="hover:bg-[#FFF9F7] transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-7 h-7 rounded-full
                                           bg-[#F6D8D2]
                                           flex items-center justify-center
                                           text-[10px] font-semibold
                                           text-[#7B1B1B] shrink-0"
                                >
                                    ${escapeHtml(
                                        rejectedCompanyInitials(
                                            entry.company
                                        )
                                    )}
                                </div>

                                <span
                                    class="text-[12px] font-medium text-gray-800"
                                >
                                    ${escapeHtml(entry.company || '—')}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-[12px] text-gray-400">
                            ${escapeHtml(entry.email || '—')}
                        </td>

                        <td class="px-6 py-4 text-[12px]">
                            ${escapeHtml(entry.phone || '—')}
                        </td>

                        <td class="px-6 py-4 text-[12px]">
                            ${escapeHtml(entry.contact_person || '—')}
                        </td>

                        <td class="px-6 py-4 text-[12px]">
                            ${escapeHtml(entry.rejected_display || '—')}
                        </td>
                    </tr>
                `).join('');
        }

        rejectedLogisticsCount.textContent =
            `${rejectedLogisticsArchive.length} rejected logistics`;
    }

    function openRejectedLogisticsModal() {
        if (!rejectedLogisticsModal) {
            return;
        }

        if (rejectedLogisticsSearch) {
            rejectedLogisticsSearch.value = '';
        }

        rejectedLogisticsSortDirection =
            'desc';

        if (rejectedLogisticsSortArrow) {
            rejectedLogisticsSortArrow.textContent =
                '↓';
        }

        renderRejectedLogisticsArchive();

        rejectedLogisticsModal.classList.remove(
            'hidden'
        );

        rejectedLogisticsModal.classList.add(
            'flex'
        );

        rejectedLogisticsModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closeRejectedLogisticsModal() {
        if (!rejectedLogisticsModal) {
            return;
        }

        rejectedLogisticsModal.classList.add(
            'hidden'
        );

        rejectedLogisticsModal.classList.remove(
            'flex'
        );

        rejectedLogisticsModal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );
    }

    /* =========================================================
       MAIN TABS
    ========================================================== */
    const mainTabs =
        Array.from(
            document.querySelectorAll('[data-main-tab]')
        );

    const pendingPanel =
        document.getElementById('pending-requests-panel');

    const logisticsPanel =
        document.getElementById('logistics-panel');

    function switchMainTab(tabName) {
        mainTabs.forEach(tab => {
            const active =
                tab.dataset.mainTab === tabName;

            tab.classList.toggle(
                'active',
                active
            );

            tab.setAttribute(
                'aria-selected',
                active ? 'true' : 'false'
            );
        });

        pendingPanel.classList.toggle(
            'active',
            tabName === 'pending'
        );

        logisticsPanel.classList.toggle(
            'active',
            tabName === 'logistics'
        );
    }

    mainTabs.forEach(tab => {
        tab.addEventListener(
            'click',
            function () {
                switchMainTab(
                    tab.dataset.mainTab
                );
            }
        );
    });

    /* =========================================================
       HELPERS
    ========================================================== */
    function normalize(value) {
        return String(value || '')
            .trim()
            .toLowerCase();
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getPageNumbers(currentPage, totalPages) {
        if (totalPages <= 5) {
            return Array.from(
                { length: totalPages },
                (_, index) => index + 1
            );
        }

        if (currentPage <= 3) {
            return [1, 2, 3, 4, totalPages];
        }

        if (currentPage >= totalPages - 2) {
            return [
                1,
                totalPages - 3,
                totalPages - 2,
                totalPages - 1,
                totalPages
            ];
        }

        return [
            1,
            currentPage - 1,
            currentPage,
            currentPage + 1,
            totalPages
        ];
    }

    /* =========================================================
       PENDING REQUESTS — ELEMENTS / STATE
    ========================================================== */
    const pendingSearch =
        document.getElementById('pending-logistics-search');

    const pendingDate =
        document.getElementById('pending-logistics-date');

    const pendingReload =
        document.getElementById('pending-logistics-reload');

    const pendingBody =
        document.getElementById('pending-logistics-body');

    const pendingEmpty =
        document.getElementById('pending-logistics-empty');

    const pendingCount =
        document.getElementById('pending-logistics-count');

    const pendingPrev =
        document.getElementById('pending-logistics-prev');

    const pendingNext =
        document.getElementById('pending-logistics-next');

    const pendingPages =
        document.getElementById('pending-logistics-pages');

    const pendingItems =
        document.getElementById('pending-logistics-items');

    let pendingCurrentPage = 1;
    let pendingItemsPerPage =
        Number(pendingItems.value);

    let activePendingRequestIndex = null;

    const pendingReviewModal =
        document.getElementById('pending-review-modal');

    const pendingReviewClose =
        document.getElementById('pending-review-close');

    const pendingReviewReject =
        document.getElementById('pending-review-reject');

    const pendingReviewApprove =
        document.getElementById('pending-review-approve');

    const logisticsRejectModal =
        document.getElementById('logistics-reject-modal');

    const cancelLogisticsReject =
        document.getElementById('cancel-logistics-reject');

    const confirmLogisticsReject =
        document.getElementById('confirm-logistics-reject');

    const logisticsRejectReasonError =
        document.getElementById('logistics-reject-reason-error');

    const logisticsRejectAdditionalDetails =
        document.getElementById('logistics-reject-additional-details');

    const logisticsRejectDetailsCount =
        document.getElementById('logistics-reject-details-count');

    const logisticsFlash =
        document.getElementById('logistics-flash');

    const logisticsFlashIcon =
        document.getElementById('logistics-flash-icon');

    const logisticsFlashTitle =
        document.getElementById('logistics-flash-title');

    const logisticsFlashMessage =
        document.getElementById('logistics-flash-message');

    const logisticsFlashClose =
        document.getElementById('logistics-flash-close');

    let logisticsFlashTimer = null;

    const pendingReviewCompany =
        document.getElementById('pending-review-company');

    const pendingReviewPhone =
        document.getElementById('pending-review-phone');

    const pendingReviewEmail =
        document.getElementById('pending-review-email');

    const pendingReviewContactPerson =
        document.getElementById('pending-review-contact-person');

    const pendingReviewAddress =
        document.getElementById('pending-review-address');

    const pendingReviewDescription =
        document.getElementById('pending-review-description');

    const pendingReviewDate =
        document.getElementById('pending-review-date');

    const pendingReviewLogoBox =
        document.getElementById('pending-review-logo-box');

    const pendingReviewLogo =
        document.getElementById('pending-review-logo');

    const pendingReviewDtiButton =
        document.getElementById('pending-review-dti-button');

    const pendingReviewDtiPermit =
        document.getElementById('pending-review-dti-permit');

    const pendingReviewBusinessButton =
        document.getElementById('pending-review-business-button');

    const pendingReviewBusinessPermit =
        document.getElementById('pending-review-business-permit');

    const permitZoomModal =
        document.getElementById('permit-zoom-modal');

    const permitZoomTitle =
        document.getElementById('permit-zoom-title');

    const permitZoomImage =
        document.getElementById('permit-zoom-image');

    const permitZoomClose =
        document.getElementById('permit-zoom-close');

    function getFilteredPendingRows() {
        const query =
            normalize(pendingSearch.value);

        const selectedDate =
            pendingDate.value;

        return pendingLogisticsRequests.filter(request => {
            const searchMatch =
                !query ||
                normalize(
                    [
                        request.company,
                        request.email,
                        request.phone,
                        request.contact_person
                    ].join(' ')
                ).includes(query);

            const dateMatch =
                !selectedDate ||
                request.registered_date === selectedDate;

            return searchMatch && dateMatch;
        });
    }

    function renderPendingRows(filteredRows) {
        const start =
            (pendingCurrentPage - 1) *
            pendingItemsPerPage;

        const pageRows =
            filteredRows.slice(
                start,
                start + pendingItemsPerPage
            );

        pendingBody.innerHTML =
            pageRows.map(request => {
                const requestIndex =
                    pendingLogisticsRequests.indexOf(request);

                return `
                <tr
                    class="pending-logistics-row"
                    data-pending-index="${requestIndex}"
                    tabindex="0"
                    role="button"
                    aria-label="Review ${escapeHtml(request.company)} logistics application"
                >
                    <td>
                        <div class="pending-company-cell">
                            <span class="pending-company-avatar">
                                ${
                                    request.logo
                                        ? `<img src="${escapeHtml(request.logo)}" alt="${escapeHtml(request.company)} logo">`
                                        : ''
                                }
                            </span>

                            <span class="pending-company-name">
                                ${escapeHtml(request.company)}
                            </span>
                        </div>
                    </td>

                    <td>
                        <span class="pending-email">
                            ${escapeHtml(request.email)}
                        </span>
                    </td>

                    <td>
                        ${escapeHtml(request.phone)}
                    </td>

                    <td>
                        ${escapeHtml(request.contact_person)}
                    </td>

                    <td>
                        ${escapeHtml(request.registered_display)}
                    </td>
                </tr>
            `;
            }).join('');

        const hasRows =
            pageRows.length > 0;

        pendingBody.style.display =
            hasRows ? '' : 'none';

        pendingEmpty.style.display =
            hasRows ? 'none' : 'block';
    }

    function renderPendingPagination(filteredRows) {
        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    pendingItemsPerPage
                )
            );

        if (pendingCurrentPage > totalPages) {
            pendingCurrentPage = totalPages;
        }

        pendingPrev.disabled =
            pendingCurrentPage === 1;

        pendingNext.disabled =
            pendingCurrentPage === totalPages;

        pendingPages.innerHTML = '';

        let previousPage = 0;

        getPageNumbers(
            pendingCurrentPage,
            totalPages
        ).forEach(pageNumber => {

            if (
                previousPage &&
                pageNumber - previousPage > 1
            ) {
                const ellipsis =
                    document.createElement('span');

                ellipsis.textContent = '…';
                ellipsis.className =
                    'px-1 text-[11px] text-[#9CA3AF]';

                pendingPages.appendChild(
                    ellipsis
                );
            }

            const button =
                document.createElement('button');

            button.type = 'button';
            button.className =
                'pending-page-button' +
                (
                    pageNumber === pendingCurrentPage
                        ? ' active'
                        : ''
                );

            button.textContent =
                pageNumber;

            button.addEventListener(
                'click',
                function () {
                    pendingCurrentPage =
                        pageNumber;

                    renderPending();
                }
            );

            pendingPages.appendChild(
                button
            );

            previousPage =
                pageNumber;
        });
    }

    function renderPendingCount(filteredRows) {
        const start =
            (pendingCurrentPage - 1) *
            pendingItemsPerPage;

        const visible =
            filteredRows.slice(
                start,
                start + pendingItemsPerPage
            ).length;

        const hasFilters =
            Boolean(
                pendingSearch.value.trim() ||
                pendingDate.value
            );

        const total =
            hasFilters
                ? filteredRows.length
                : pendingOriginalTotalEntries;

        pendingCount.textContent =
            `Showing ${visible} out of ${total.toLocaleString()} entries`;
    }

    function makePermitPlaceholder(title, companyName) {
        const safeTitle =
            String(title || 'Permit');

        const safeCompany =
            String(companyName || 'Logistics Company');

        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="720" height="460" viewBox="0 0 720 460">
                <rect width="720" height="460" fill="#f8f5f2"/>
                <rect x="34" y="34" width="652" height="392" rx="8" fill="#ffffff" stroke="#cfc7c2" stroke-width="2"/>
                <rect x="70" y="76" width="580" height="56" rx="6" fill="#fff0eb"/>
                <text x="360" y="111" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="25" font-weight="600" fill="#8f241f">${safeTitle}</text>
                <text x="360" y="166" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="18" font-weight="600" fill="#231f1d">${safeCompany}</text>
                <line x1="95" y1="205" x2="625" y2="205" stroke="#ddd7d3" stroke-width="2"/>
                <line x1="95" y1="244" x2="530" y2="244" stroke="#e5e0dc" stroke-width="12" stroke-linecap="round"/>
                <line x1="95" y1="279" x2="580" y2="279" stroke="#e5e0dc" stroke-width="12" stroke-linecap="round"/>
                <line x1="95" y1="314" x2="470" y2="314" stroke="#e5e0dc" stroke-width="12" stroke-linecap="round"/>
                <circle cx="558" cy="348" r="42" fill="#fff5f1" stroke="#bd5148" stroke-width="3"/>
                <text x="558" y="354" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" font-weight="700" fill="#9e231f">PERMIT</text>
                <text x="95" y="384" font-family="Poppins, Arial, sans-serif" font-size="13" fill="#938a85">Attached document preview</text>
            </svg>
        `;

        return (
            'data:image/svg+xml;charset=UTF-8,' +
            encodeURIComponent(svg)
        );
    }

    function openPermitZoom(title, imageSrc) {
        if (!imageSrc) {
            return;
        }

        permitZoomTitle.textContent =
            title;

        permitZoomImage.src =
            imageSrc;

        permitZoomImage.alt =
            `${title} enlarged preview`;

        permitZoomModal.classList.add(
            'is-open'
        );

        permitZoomModal.setAttribute(
            'aria-hidden',
            'false'
        );
    }

    function closePermitZoom() {
        permitZoomModal.classList.remove(
            'is-open'
        );

        permitZoomModal.setAttribute(
            'aria-hidden',
            'true'
        );

        permitZoomImage.removeAttribute(
            'src'
        );
    }

    function populatePendingReview(request) {
        if (!request) {
            return;
        }

        pendingReviewCompany.textContent =
            request.company || '—';

        pendingReviewPhone.textContent =
            request.phone || '—';

        pendingReviewEmail.textContent =
            request.email || '—';

        pendingReviewContactPerson.textContent =
            request.contact_person || '—';

        pendingReviewAddress.textContent =
            request.office_address || '—';

        pendingReviewDescription.textContent =
            request.description ||
            'No logistics description was provided.';

        pendingReviewDate.textContent =
            request.registered_display || '—';

        const dtiPermitSource =
            request.dti_permit ||
            makePermitPlaceholder(
                'DTI Permit',
                request.company
            );

        const businessPermitSource =
            request.business_permit ||
            makePermitPlaceholder(
                'Business Permit',
                request.company
            );

        pendingReviewDtiPermit.src =
            dtiPermitSource;

        pendingReviewDtiPermit.dataset.fullSrc =
            dtiPermitSource;

        pendingReviewBusinessPermit.src =
            businessPermitSource;

        pendingReviewBusinessPermit.dataset.fullSrc =
            businessPermitSource;

        if (request.logo) {
            pendingReviewLogo.src =
                request.logo;

            pendingReviewLogo.alt =
                `${request.company || 'Company'} logo`;

            pendingReviewLogoBox.classList.add(
                'has-image'
            );
        } else {
            pendingReviewLogo.removeAttribute(
                'src'
            );

            pendingReviewLogo.alt =
                'Company logo';

            pendingReviewLogoBox.classList.remove(
                'has-image'
            );
        }
    }

    function openPendingReview(requestIndex) {
        const numericIndex =
            Number(requestIndex);

        const request =
            pendingLogisticsRequests[
                numericIndex
            ];

        if (!request) {
            return;
        }

        activePendingRequestIndex =
            numericIndex;

        populatePendingReview(
            request
        );

        pendingReviewModal.classList.add(
            'is-open'
        );

        pendingReviewModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closePendingReview() {
        pendingReviewModal.classList.remove(
            'is-open'
        );

        pendingReviewModal.setAttribute(
            'aria-hidden',
            'true'
        );

        activePendingRequestIndex = null;

        if (
            !logisticsRejectModal.classList.contains('is-open') &&
            !permitZoomModal.classList.contains('is-open')
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    function showLogisticsFlash(type, companyName) {
        if (!logisticsFlash) {
            return;
        }

        const approved =
            type === 'approved';

        const isError =
            type === 'error';

        logisticsFlash.classList.remove(
            'approved',
            'rejected',
            'error'
        );

        logisticsFlash.classList.add(
            approved
                ? 'approved'
                : (isError ? 'error' : 'rejected')
        );

        logisticsFlashIcon.style.background =
            approved
                ? '#DDF0D6'
                : '#FFE3E5';

        logisticsFlashIcon.innerHTML =
            approved
                ? `
                    <svg
                        fill="none"
                        stroke="#28721B"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m5 12 4 4L19 6"
                        />
                    </svg>
                `
                : `
                    <svg
                        fill="none"
                        stroke="#B3262E"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M18 6 6 18"
                        />
                    </svg>
                `;

        if (isError) {
            logisticsFlashTitle.textContent =
                'Action Failed';

            logisticsFlashMessage.textContent =
                companyName ||
                'Unable to update this logistics request.';
        } else {
            logisticsFlashTitle.textContent =
                approved
                    ? 'Logistics Request Approved'
                    : 'Logistics Request Rejected';

            logisticsFlashMessage.textContent =
                approved
                    ? `${companyName}'s logistics request has been approved. The company will be notified via email.`
                    : `${companyName}'s logistics request has been rejected. The company will be notified via email.`;
        }

        clearTimeout(
            logisticsFlashTimer
        );

        logisticsFlash.classList.add(
            'show'
        );

        logisticsFlashTimer =
            window.setTimeout(
                function () {
                    logisticsFlash.classList.remove(
                        'show'
                    );
                },
                3800
            );
    }

    function resetLogisticsRejectForm() {
        document
            .querySelectorAll(
                'input[name="logistics_reject_reason"]'
            )
            .forEach(input => {
                input.checked = false;
            });

        logisticsRejectAdditionalDetails.value = '';
        logisticsRejectDetailsCount.textContent = '0/300';
        logisticsRejectReasonError.classList.remove('show');
    }

    function getSelectedLogisticsRejectReason() {
        const checked =
            document.querySelector(
                'input[name="logistics_reject_reason"]:checked'
            );

        return checked
            ? checked.value
            : '';
    }

    function openLogisticsRejectModal() {
        if (
            activePendingRequestIndex === null ||
            !pendingLogisticsRequests[
                activePendingRequestIndex
            ]
        ) {
            return;
        }

        resetLogisticsRejectForm();

        logisticsRejectModal.classList.add(
            'is-open'
        );

        logisticsRejectModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closeLogisticsRejectModal() {
        logisticsRejectModal.classList.remove(
            'is-open'
        );

        logisticsRejectModal.setAttribute(
            'aria-hidden',
            'true'
        );

        if (
            !pendingReviewModal.classList.contains('is-open') &&
            !permitZoomModal.classList.contains('is-open')
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    function formatDecisionDate() {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }
        ).format(new Date());
    }

    function formatDecisionTime() {
        return new Intl.DateTimeFormat(
            'en-US',
            {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }
        ).format(new Date());
    }

    function approveActivePendingRequest() {
        if (
            activePendingRequestIndex === null
        ) {
            return;
        }

        const request =
            pendingLogisticsRequests[
                activePendingRequestIndex
            ];

        if (!request) {
            return;
        }

        const confirmed =
            window.confirm(
                `Are you sure you want to approve ${request.company}'s logistics request?`
            );

        if (!confirmed) {
            return;
        }

        /*
         * Match Registrations: show the decision flash immediately
         * after the admin confirms the action.
         */
        showLogisticsFlash(
            'approved',
            request.company
        );

        /*
         * Front-end demo behavior.
         * Replace with the real approval endpoint when backend review
         * routes are connected.
         */
        const approvedCompany = {
            company:
                request.company,

            branches:
                0,

            riders:
                0,

            contact_number:
                request.phone || '—',

            company_email:
                request.email || '—',

            office_address:
                request.office_address || '—',

            description:
                request.description || '',

            dti_permit:
                request.dti_permit || '',

            business_permit:
                request.business_permit || '',

            partnership_since:
                new Date().getFullYear(),

            account_created_date:
                formatDecisionDate(),

            account_created_time:
                formatDecisionTime(),

            logo:
                request.logo || '',

            branches_data:
                []
        };

        pendingLogisticsRequests.splice(
            activePendingRequestIndex,
            1
        );

        logisticsCompanies.unshift(
            approvedCompany
        );

        pendingOriginalTotalEntries =
            Math.max(
                0,
                pendingOriginalTotalEntries - 1
            );

        originalTotalEntries += 1;

        pendingStatValue =
            Math.max(
                0,
                pendingStatValue - 1
            );

        acceptedStatValue += 1;
        totalStatValue += 1;

        closePendingReview();

        pendingCurrentPage = 1;
        currentPage = 1;

        renderPending();
        render();
        renderLogisticsStats();
    }

    function rejectActivePendingRequest() {
        if (
            activePendingRequestIndex === null
        ) {
            return;
        }

        const request =
            pendingLogisticsRequests[
                activePendingRequestIndex
            ];

        if (!request) {
            return;
        }

        const reason =
            getSelectedLogisticsRejectReason();

        if (!reason) {
            logisticsRejectReasonError.classList.add(
                'show'
            );

            return;
        }

        /*
         * Same immediate rejection flash behavior as Registrations.
         */
        showLogisticsFlash(
            'rejected',
            request.company
        );

        /*
         * Keep the selected rejection data on the object before it
         * leaves the pending list. This can later be sent to the API.
         */
        request.rejection_reason =
            reason;

        request.rejection_details =
            logisticsRejectAdditionalDetails.value.trim();

        request.rejected_at =
            `${formatDecisionDate()} ${formatDecisionTime()}`;

        rejectedLogisticsArchive.unshift({
            company:
                request.company || '—',

            email:
                request.email || '—',

            phone:
                request.phone || '—',

            contact_person:
                request.contact_person || '—',

            rejected_display:
                formatDecisionDate(),

            rejection_reason:
                request.rejection_reason,

            rejection_details:
                request.rejection_details
        });

        pendingLogisticsRequests.splice(
            activePendingRequestIndex,
            1
        );

        pendingOriginalTotalEntries =
            Math.max(
                0,
                pendingOriginalTotalEntries - 1
            );

        pendingStatValue =
            Math.max(
                0,
                pendingStatValue - 1
            );

        rejectedStatValue += 1;

        closeLogisticsRejectModal();
        closePendingReview();

        pendingCurrentPage = 1;

        renderPending();
        renderLogisticsStats();
        renderRejectedLogisticsArchive();
    }

    function renderPending() {
        const filteredRows =
            getFilteredPendingRows();

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    pendingItemsPerPage
                )
            );

        if (pendingCurrentPage > totalPages) {
            pendingCurrentPage =
                totalPages;
        }

        renderPendingRows(
            filteredRows
        );

        renderPendingPagination(
            filteredRows
        );

        renderPendingCount(
            filteredRows
        );
    }

    pendingSearch.addEventListener(
        'input',
        function () {
            pendingCurrentPage = 1;
            renderPending();
        }
    );

    pendingDate.addEventListener(
        'change',
        function () {
            pendingCurrentPage = 1;
            renderPending();
        }
    );

    pendingItems.addEventListener(
        'change',
        function () {
            pendingItemsPerPage =
                Number(pendingItems.value);

            pendingCurrentPage = 1;
            renderPending();
        }
    );

    pendingPrev.addEventListener(
        'click',
        function () {
            if (pendingCurrentPage <= 1) {
                return;
            }

            pendingCurrentPage--;
            renderPending();
        }
    );

    pendingNext.addEventListener(
        'click',
        function () {
            const filteredRows =
                getFilteredPendingRows();

            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        filteredRows.length /
                        pendingItemsPerPage
                    )
                );

            if (
                pendingCurrentPage >=
                totalPages
            ) {
                return;
            }

            pendingCurrentPage++;
            renderPending();
        }
    );

    pendingReload.addEventListener(
        'click',
        function () {
            pendingSearch.value = '';
            pendingDate.value = '';
            pendingCurrentPage = 1;

            pendingReload.classList.add(
                'spinning'
            );

            window.setTimeout(
                function () {
                    pendingReload.classList.remove(
                        'spinning'
                    );
                },
                550
            );

            renderPending();
        }
    );

    pendingBody.addEventListener(
        'click',
        function (event) {
            const row =
                event.target.closest(
                    '.pending-logistics-row'
                );

            if (!row) {
                return;
            }

            openPendingReview(
                row.dataset.pendingIndex
            );
        }
    );

    pendingBody.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Enter' &&
                event.key !== ' '
            ) {
                return;
            }

            const row =
                event.target.closest(
                    '.pending-logistics-row'
                );

            if (!row) {
                return;
            }

            event.preventDefault();

            openPendingReview(
                row.dataset.pendingIndex
            );
        }
    );

    if (logisticsFlashClose) {
        logisticsFlashClose.addEventListener(
            'click',
            function () {
                clearTimeout(
                    logisticsFlashTimer
                );

                logisticsFlash.classList.remove(
                    'show'
                );
            }
        );
    }

    if (rejectedLogisticsButton) {
        rejectedLogisticsButton.addEventListener(
            'click',
            openRejectedLogisticsModal
        );
    }

    if (closeRejectedLogistics) {
        closeRejectedLogistics.addEventListener(
            'click',
            closeRejectedLogisticsModal
        );
    }

    if (closeRejectedLogisticsBottom) {
        closeRejectedLogisticsBottom.addEventListener(
            'click',
            closeRejectedLogisticsModal
        );
    }

    if (rejectedLogisticsModal) {
        rejectedLogisticsModal.addEventListener(
            'click',
            function (event) {
                if (
                    event.target ===
                    rejectedLogisticsModal
                ) {
                    closeRejectedLogisticsModal();
                }
            }
        );
    }

    if (rejectedLogisticsSearch) {
        rejectedLogisticsSearch.addEventListener(
            'input',
            renderRejectedLogisticsArchive
        );
    }

    if (rejectedLogisticsDateSort) {
        rejectedLogisticsDateSort.addEventListener(
            'click',
            function () {
                rejectedLogisticsSortDirection =
                    rejectedLogisticsSortDirection === 'desc'
                        ? 'asc'
                        : 'desc';

                if (rejectedLogisticsSortArrow) {
                    rejectedLogisticsSortArrow.textContent =
                        rejectedLogisticsSortDirection === 'asc'
                            ? '↑'
                            : '↓';
                }

                renderRejectedLogisticsArchive();
            }
        );
    }

    pendingReviewClose.addEventListener(
        'click',
        closePendingReview
    );

    pendingReviewReject.addEventListener(
        'click',
        openLogisticsRejectModal
    );

    pendingReviewApprove.addEventListener(
        'click',
        approveActivePendingRequest
    );

    cancelLogisticsReject.addEventListener(
        'click',
        closeLogisticsRejectModal
    );

    confirmLogisticsReject.addEventListener(
        'click',
        rejectActivePendingRequest
    );

    logisticsRejectAdditionalDetails.addEventListener(
        'input',
        function () {
            logisticsRejectDetailsCount.textContent =
                `${logisticsRejectAdditionalDetails.value.length}/300`;
        }
    );

    document
        .querySelectorAll(
            'input[name="logistics_reject_reason"]'
        )
        .forEach(input => {
            input.addEventListener(
                'change',
                function () {
                    logisticsRejectReasonError.classList.remove(
                        'show'
                    );
                }
            );
        });

    logisticsRejectModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                logisticsRejectModal
            ) {
                closeLogisticsRejectModal();
            }
        }
    );

    pendingReviewDtiButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'DTI Permit',
                pendingReviewDtiPermit.dataset.fullSrc ||
                pendingReviewDtiPermit.src
            );
        }
    );

    pendingReviewBusinessButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'Business Permit',
                pendingReviewBusinessPermit.dataset.fullSrc ||
                pendingReviewBusinessPermit.src
            );
        }
    );

    permitZoomClose.addEventListener(
        'click',
        closePermitZoom
    );

    permitZoomModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                permitZoomModal
            ) {
                closePermitZoom();
            }
        }
    );

    pendingReviewModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                pendingReviewModal
            ) {
                closePendingReview();
            }
        }
    );

    /* =========================================================
       LOGISTICS TAB — CURRENT TABLE
    ========================================================== */
    const search =
        document.getElementById('logistics-search');

    const tbody =
        document.getElementById('logistics-table-body');

    const empty =
        document.getElementById('logistics-empty');

    const count =
        document.getElementById('logistics-count');

    const prev =
        document.getElementById('logistics-prev');

    const next =
        document.getElementById('logistics-next');

    const pages =
        document.getElementById('logistics-pages');

    const items =
        document.getElementById('logistics-items');

    let currentPage = 1;
    let itemsPerPage =
        Number(items.value);

    function getFilteredRows() {
        const query =
            normalize(search.value);

        if (!query) {
            return logisticsCompanies;
        }

        return logisticsCompanies.filter(company =>
            normalize(
                [
                    company.company,
                    company.company_email,
                    company.branches,
                    company.riders
                ].join(' ')
            ).includes(query)
        );
    }

    function renderRows(filteredRows) {
        const startIndex =
            (currentPage - 1) *
            itemsPerPage;

        const pageRows =
            filteredRows.slice(
                startIndex,
                startIndex + itemsPerPage
            );

        tbody.innerHTML =
            pageRows.map(company => {
                const companyIndex =
                    logisticsCompanies.indexOf(company);

                return `
                    <tr
                        class="logistics-company-row"
                        data-company-index="${companyIndex}"
                        tabindex="0"
                        role="button"
                        aria-label="View ${escapeHtml(company.company)} company details"
                    >
                        <td>
                            <div class="logistics-company-cell">
                                <span class="logistics-company-avatar">
                                    ${
                                        company.logo
                                            ? `<img src="${escapeHtml(company.logo)}" alt="${escapeHtml(company.company)} logo">`
                                            : ''
                                    }
                                </span>

                                <span class="logistics-company-name">
                                    ${escapeHtml(company.company)}
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="logistics-number">
                                ${escapeHtml(company.branches)}
                            </span>
                        </td>

                        <td>
                            <span class="logistics-number">
                                ${escapeHtml(company.riders)}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');

        const hasRows =
            pageRows.length > 0;

        tbody.style.display =
            hasRows ? '' : 'none';

        empty.style.display =
            hasRows ? 'none' : 'block';
    }

    function renderPagination(filteredRows) {
        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        prev.disabled =
            currentPage === 1;

        next.disabled =
            currentPage === totalPages;

        pages.innerHTML = '';

        let previousPageNumber = 0;

        getPageNumbers(
            currentPage,
            totalPages
        ).forEach(pageNumber => {

            if (
                previousPageNumber &&
                pageNumber -
                previousPageNumber > 1
            ) {
                const ellipsis =
                    document.createElement('span');

                ellipsis.textContent = '…';
                ellipsis.className =
                    'px-1 text-[11px] text-[#8C8784]';

                pages.appendChild(
                    ellipsis
                );
            }

            const button =
                document.createElement('button');

            button.type = 'button';
            button.className =
                'logistics-page-button' +
                (
                    pageNumber === currentPage
                        ? ' active'
                        : ''
                );

            button.textContent =
                pageNumber;

            button.addEventListener(
                'click',
                function () {
                    currentPage =
                        pageNumber;

                    render();
                }
            );

            pages.appendChild(
                button
            );

            previousPageNumber =
                pageNumber;
        });
    }

    function renderCount(filteredRows) {
        const startIndex =
            (currentPage - 1) *
            itemsPerPage;

        const visibleRows =
            filteredRows.slice(
                startIndex,
                startIndex + itemsPerPage
            ).length;

        const hasSearch =
            Boolean(
                search.value.trim()
            );

        const total =
            hasSearch
                ? filteredRows.length
                : originalTotalEntries;

        count.textContent =
            `Showing ${visibleRows} out of ${total.toLocaleString()} entries`;
    }

    function render() {
        const filteredRows =
            getFilteredRows();

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        renderRows(
            filteredRows
        );

        renderPagination(
            filteredRows
        );

        renderCount(
            filteredRows
        );
    }

    search.addEventListener(
        'input',
        function () {
            currentPage = 1;
            render();
        }
    );

    items.addEventListener(
        'change',
        function () {
            itemsPerPage =
                Number(items.value);

            currentPage = 1;
            render();
        }
    );

    prev.addEventListener(
        'click',
        function () {
            if (currentPage <= 1) {
                return;
            }

            currentPage--;
            render();
        }
    );

    next.addEventListener(
        'click',
        function () {
            const filteredRows =
                getFilteredRows();

            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        filteredRows.length /
                        itemsPerPage
                    )
                );

            if (
                currentPage >=
                totalPages
            ) {
                return;
            }

            currentPage++;
            render();
        }
    );

    /* =========================================================
       CURRENT COMPANY DETAILS MODAL
    ========================================================== */
    const detailsModal =
        document.getElementById('logistics-details-modal');

    const detailsCloseButton =
        document.getElementById('close-logistics-details');

    const detailsTabs =
        Array.from(
            document.querySelectorAll('[data-details-tab]')
        );

    const companyDetailsPanel =
        document.getElementById('logistics-company-details-panel');

    const branchesPanel =
        document.getElementById('logistics-branches-panel');

    const detailsAvatar =
        document.getElementById('logistics-details-avatar');

    const detailsAvatarImage =
        document.getElementById('logistics-details-avatar-image');

    const detailsCompanyHeading =
        document.getElementById('logistics-details-company-name');

    const detailsCompanyPartnership =
        document.getElementById('logistics-details-company-partnership');

    const detailsCompanyName =
        document.getElementById('details-company-name');

    const detailsContactNumber =
        document.getElementById('details-contact-number');

    const detailsCompanyEmail =
        document.getElementById('details-company-email');

    const detailsOfficeAddress =
        document.getElementById('details-office-address');

    const detailsCompanyDescription =
        document.getElementById('details-company-description');

    const detailsDtiPermitButton =
        document.getElementById('details-dti-permit-button');

    const detailsDtiPermit =
        document.getElementById('details-dti-permit');

    const detailsBusinessPermitButton =
        document.getElementById('details-business-permit-button');

    const detailsBusinessPermit =
        document.getElementById('details-business-permit');

    const detailsCreatedDate =
        document.getElementById('details-created-date');

    const detailsCreatedTime =
        document.getElementById('details-created-time');

    const branchesBody =
        document.getElementById('logistics-branches-body');

    const branchesEmpty =
        document.getElementById('logistics-branches-empty');

    function switchDetailsTab(tabName) {
        detailsTabs.forEach(tab => {
            const active =
                tab.dataset.detailsTab ===
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

        companyDetailsPanel.classList.toggle(
            'active',
            tabName === 'company'
        );

        branchesPanel.classList.toggle(
            'active',
            tabName === 'branches'
        );
    }

    function renderCompanyBranches(company) {
        const branches =
            Array.isArray(company?.branches_data)
                ? company.branches_data
                : [];

        if (!branches.length) {
            branchesBody.innerHTML = '';
            branchesEmpty.hidden = false;
            return;
        }

        branchesEmpty.hidden = true;

        branchesBody.innerHTML =
            branches.map(branch => `
                <tr>
                    <td>${escapeHtml(branch.name || '—')}</td>
                    <td>${escapeHtml(branch.address || '—')}</td>
                    <td>${escapeHtml(branch.contact_person || '—')}</td>
                    <td>${escapeHtml(branch.phone || '—')}</td>
                    <td>${escapeHtml(branch.riders ?? '—')}</td>
                </tr>
            `).join('');
    }

    function populateCompanyDetails(company) {
        if (!company) {
            return;
        }

        const companyName =
            company.company ||
            'Logistics Company';

        detailsCompanyHeading.textContent =
            companyName;

        detailsCompanyPartnership.textContent =
            `In partnership with ShopEase since ${company.partnership_since || 2019}`;

        detailsCompanyName.textContent =
            companyName;

        detailsContactNumber.textContent =
            company.contact_number || '—';

        detailsCompanyEmail.textContent =
            company.company_email || '—';

        detailsOfficeAddress.textContent =
            company.office_address || '—';

        detailsCompanyDescription.textContent =
            company.description ||
            'No logistics description was provided.';

        const companyDtiPermitSource =
            company.dti_permit ||
            makePermitPlaceholder(
                'DTI Permit',
                companyName
            );

        const companyBusinessPermitSource =
            company.business_permit ||
            makePermitPlaceholder(
                'Business Permit',
                companyName
            );

        detailsDtiPermit.src =
            companyDtiPermitSource;

        detailsDtiPermit.dataset.fullSrc =
            companyDtiPermitSource;

        detailsBusinessPermit.src =
            companyBusinessPermitSource;

        detailsBusinessPermit.dataset.fullSrc =
            companyBusinessPermitSource;

        detailsCreatedDate.textContent =
            company.account_created_date ||
            'May 26, 2026';

        detailsCreatedTime.textContent =
            company.account_created_time ||
            '6:45 PM';

        if (company.logo) {
            detailsAvatarImage.src =
                company.logo;

            detailsAvatarImage.alt =
                `${companyName} logo`;

            detailsAvatar.classList.add(
                'has-image'
            );
        } else {
            detailsAvatarImage.removeAttribute(
                'src'
            );

            detailsAvatarImage.alt =
                'Company logo';

            detailsAvatar.classList.remove(
                'has-image'
            );
        }

        renderCompanyBranches(
            company
        );
    }

    function openCompanyDetails(companyIndex) {
        const company =
            logisticsCompanies[
                Number(companyIndex)
            ];

        if (!company) {
            return;
        }

        populateCompanyDetails(
            company
        );

        switchDetailsTab(
            'company'
        );

        detailsModal.classList.add(
            'is-open'
        );

        detailsModal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closeCompanyDetails() {
        detailsModal.classList.remove(
            'is-open'
        );

        detailsModal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );
    }

    detailsTabs.forEach(tab => {
        tab.addEventListener(
            'click',
            function () {
                switchDetailsTab(
                    tab.dataset.detailsTab
                );
            }
        );
    });

    detailsCloseButton.addEventListener(
        'click',
        closeCompanyDetails
    );

    detailsDtiPermitButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'DTI Permit',
                detailsDtiPermit.dataset.fullSrc ||
                detailsDtiPermit.src
            );
        }
    );

    detailsBusinessPermitButton.addEventListener(
        'click',
        function () {
            openPermitZoom(
                'Business Permit',
                detailsBusinessPermit.dataset.fullSrc ||
                detailsBusinessPermit.src
            );
        }
    );

    detailsModal.addEventListener(
        'click',
        function (event) {
            if (
                event.target ===
                detailsModal
            ) {
                closeCompanyDetails();
            }
        }
    );

    tbody.addEventListener(
        'click',
        function (event) {
            const row =
                event.target.closest(
                    '.logistics-company-row'
                );

            if (!row) {
                return;
            }

            openCompanyDetails(
                row.dataset.companyIndex
            );
        }
    );

    tbody.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Enter' &&
                event.key !== ' '
            ) {
                return;
            }

            const row =
                event.target.closest(
                    '.logistics-company-row'
                );

            if (!row) {
                return;
            }

            event.preventDefault();

            openCompanyDetails(
                row.dataset.companyIndex
            );
        }
    );

    window.addEventListener(
        'keydown',
        function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            if (
                permitZoomModal.classList.contains(
                    'is-open'
                )
            ) {
                closePermitZoom();
                return;
            }

            if (
                logisticsRejectModal.classList.contains(
                    'is-open'
                )
            ) {
                closeLogisticsRejectModal();
                return;
            }

            if (
                rejectedLogisticsModal &&
                !rejectedLogisticsModal.classList.contains(
                    'hidden'
                )
            ) {
                closeRejectedLogisticsModal();
                return;
            }

            if (
                pendingReviewModal.classList.contains(
                    'is-open'
                )
            ) {
                closePendingReview();
                return;
            }

            if (
                detailsModal.classList.contains(
                    'is-open'
                )
            ) {
                closeCompanyDetails();
            }
        }
    );

    /* =========================================================
       INITIAL RENDER
    ========================================================== */
    switchMainTab('pending');
    renderPending();
    render();
    renderLogisticsStats();
    renderRejectedLogisticsArchive();
});