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

    // Shared document lightbox, for both Pending Registration and Company Details.
    const lmDocumentLayer = document.getElementById('lmDocumentPreviewLayer');
    const lmDocumentBody = document.getElementById('lmDocumentPreviewBody');
    const lmDocumentHeading = document.getElementById('lmDocumentPreviewHeading');
    function closeLmDocument() {
        if (!lmDocumentLayer) return;
        lmDocumentLayer.hidden = true;
        lmDocumentBody.replaceChildren();
        document.body.classList.remove('lm-document-preview-open');
    }
    document.addEventListener('click', function (event) {
        const view = event.target.closest('.ar-view-document');
        if (view) {
            event.preventDefault();
            const url = view.dataset.documentUrl || '';
            if (!/^https?:\/\/|^\/storage\//i.test(url)) return;
            lmDocumentHeading.textContent = view.dataset.documentTitle || 'Document Preview';
            lmDocumentBody.replaceChildren();
            const ext = new URL(url, window.location.origin).pathname.toLowerCase();
            if (/\.pdf$/.test(ext)) {
                const frame = document.createElement('iframe');
                frame.src = url;
                frame.title = lmDocumentHeading.textContent;
                lmDocumentBody.append(frame);
            } else {
                const img = document.createElement('img');
                img.src = url;
                img.alt = lmDocumentHeading.textContent;
                img.onerror = () => {lmDocumentBody.textContent='Document preview unavailable.';};
                lmDocumentBody.append(img);
            }
            lmDocumentLayer.hidden = false;
            document.body.classList.add('lm-document-preview-open');
        }
        if (event.target.closest('[data-close-lm-document]')) closeLmDocument();
    });
    lmDocumentLayer?.addEventListener('click', function(event) { if(event.target === lmDocumentLayer) closeLmDocument(); });
    document.addEventListener('keydown', event => { if(event.key === 'Escape' && lmDocumentLayer && !lmDocumentLayer.hidden) closeLmDocument(); });


    /* =========================================================
       DATA
    ========================================================== */
    let pendingLogisticsRequests =
        logisticsManagementConfig.pendingRequests || [];

    let pendingOriginalTotalEntries =
        Number(logisticsManagementConfig.pendingTotalEntries || 0);

    let logisticsCompanies =
        logisticsManagementConfig.companies || [];

    let rejectedLogisticsArchive =
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

    async function submitLogisticsDecision(registrationId, decision, payload = {}) {
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch(
            `/api/v1/admin/logistics-management/registrations/${registrationId}/${decision}`,
            {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify(payload)
            }
        );
        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validationMessage =
                Object.values(result.errors || {}).flat()[0];

            throw new Error(
                validationMessage || result.message || 'Unable to update this logistics request.'
            );
        }

        return result;
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
                const initials = String(request.contact_person || request.company || 'Logistics')
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean)
                    .map(part => part[0])
                    .slice(0, 2)
                    .join('')
                    .toUpperCase() || 'L';

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
                                <span>${escapeHtml(initials)}</span>
                                ${request.logo ? `<img src="${escapeHtml(request.logo)}" alt="${escapeHtml(request.contact_person || request.company)} profile photo" onerror="this.remove()">` : ''}
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

        const detailHost = document.getElementById('pending-registration-full-details');
        request = {...(logisticsManagementConfig.registrationPreview||{})[request.company], ...request};
        if (detailHost) {
            const val = keys => {for(const k of keys){let v=request[k];if(Array.isArray(v))v=v.join(', ');if(v!==null&&v!==undefined&&String(v).trim())return String(v);}return 'Not provided';};
            const section = (title,fields) => `<section class="ar-section"><h3>${escapeHtml(title)}</h3><div class="ar-grid">${fields.map(([label,keys])=>`<div class="ar-cell"><small>${escapeHtml(label)}</small><strong>${escapeHtml(val(keys))}</strong></div>`).join('')}</div></section>`;
            detailHost.innerHTML =
              section('Company Information',[
                ['SEC Registration No.',['sec_registration_number']],['TIN',['tin']],['Service Areas',['service_areas']],
                ['Company / Logistics Business Name',['company_name','company']],['Registered Legal Business Name',['registered_business_name']],
                ['Company Type',['company_type']],['Years in Operation',['years_in_operation']],['Company Email Address',['company_email','email']],
                ['Company Contact Number',['company_phone','phone']],['Company Website',['website','company_website']],['Number of Sorting Centers (Optional)',['sorting_center_count','branch_count']]
              ]) +
              section('Authorized Representative',[
                ['Representative Name',['representative_name','contact_person']],['Position',['representative_position']],
                ['Email Address',['representative_email']],['Contact Number',['representative_phone','phone']],
                ['Sex',['representative_sex']],['Date of Birth',['representative_birthday']]
              ]) +
              section('Registered Office Address',[
                ['Region',['region']],['Province',['province']],['City / Municipality',['city','municipality']],
                ['Barangay',['barangay']],['ZIP Code',['postal_code']],['Street',['street']],
                ['House No.',['house_no']],['Building / Unit',['building']],['Subdivision',['subdivision']],
                ['Full Office Address',['office_address']]
              ]) +
              `<section class="ar-section"><h3>Registration Documents</h3><div class="ar-docs">${[
                ['SEC / DTI / CDA Registration','business_registration'],['Mayor’s / Business Permit','mayors_permit'],
                ['BIR Certificate of Registration (Form 2303)','bir_certificate'],
                ['Courier / Delivery Accreditation (Optional)','courier_authority'],
                ['Business / Cargo Liability Insurance (Optional)','insurance_certificate'],
                ['Valid ID of Authorized Representative','representative_authorization']
              ].map(([label,key])=>{const d=request.documents?.[key]||request[key];const path=typeof d==='object'?(d?.url||''):(typeof d==='string'?d:'');const valid=typeof path==='string'&&(/^(https?:\/\/|\/storage\/)/i).test(path);return `<div class="ar-doc"><b>${escapeHtml(label)}</b>${valid?`<button type="button" class="ar-view-document" data-document-url="${escapeHtml(path)}" data-document-title="${escapeHtml(label)}">View Document</button>`:(key==='courier_authority'||key==='insurance_certificate'?'Not uploaded (optional)':'Document not available')}</div>`}).join('')}</div></section>`;
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

        document.getElementById('pending-review-logo-initials').textContent =
            String(request.contact_person || request.company || 'L')
                .trim().split(/\s+/).filter(Boolean).map(part => part[0]).slice(0, 2).join('').toUpperCase() || 'L';

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
            pendingReviewLogoBox.dataset.initials = String(request.contact_person || request.company || 'L')
                .trim().split(/\s+/).filter(Boolean).map(part => part[0]).slice(0, 2).join('').toUpperCase();
        } else {
            pendingReviewLogo.removeAttribute(
                'src'
            );

            pendingReviewLogo.alt =
                'Company logo';

            pendingReviewLogoBox.classList.remove(
                'has-image'
            );
            pendingReviewLogoBox.dataset.initials = String(request.contact_person || request.company || 'L')
                .trim().split(/\s+/).filter(Boolean).map(part => part[0]).slice(0, 2).join('').toUpperCase() || 'L';
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

    async function approveActivePendingRequest() {
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

        pendingReviewApprove.disabled = true;

        try {
            await submitLogisticsDecision(request.id, 'approve');
            showLogisticsFlash('approved', request.company);
            window.setTimeout(() => window.location.reload(), 900);
        } catch (error) {
            showLogisticsFlash('error', error.message);
        } finally {
            pendingReviewApprove.disabled = false;
        }
    }

    async function rejectActivePendingRequest() {
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

        confirmLogisticsReject.disabled = true;

        try {
            await submitLogisticsDecision(request.id, 'reject', {
                reason,
                details: logisticsRejectAdditionalDetails.value.trim()
            });
            showLogisticsFlash('rejected', request.company);
            closeLogisticsRejectModal();
            closePendingReview();
            window.setTimeout(() => window.location.reload(), 900);
        } catch (error) {
            showLogisticsFlash('error', error.message);
        } finally {
            confirmLogisticsReject.disabled = false;
        }
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

    async function loadLogisticsData() {
        const response = await fetch(
            logisticsManagementConfig.apiUrl || '/api/v1/admin/logistics-management',
            {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            }
        );
        const payload = await response.json();
        if (!response.ok) {
            throw new Error(payload.message || 'Unable to load logistics management data.');
        }

        pendingLogisticsRequests = (payload.pendingRequests?.length ? payload.pendingRequests : pendingLogisticsRequests).map(r => {
            const demo = (logisticsManagementConfig.pendingRequests||[]).find(d => d.company===r.company) || {};
            const full = (logisticsManagementConfig.registrationPreview||{})[r.company] || {};
            return {...demo,...full,...r};
        });
        logisticsCompanies = (payload.companies?.length ? payload.companies : logisticsCompanies).map(c => {
            const demo = (logisticsManagementConfig.companies||[]).find(d => d.company===c.company) || {};
            return {...demo,...c};
        });
        rejectedLogisticsArchive = payload.rejectedArchive || [];
        pendingOriginalTotalEntries = Number(payload.pendingTotalEntries || 0);
        originalTotalEntries = Number(payload.totalEntries || 0);
        pendingStatValue = Number(payload.stats?.pending_requests || 0);
        acceptedStatValue = Number(payload.stats?.accepted_logistics || 0);
        rejectedStatValue = Number(payload.stats?.rejected_logistics || 0);
        totalStatValue = Number(payload.stats?.total_companies || 0);

        renderPending();
        render();
        renderLogisticsStats();
        renderRejectedLogisticsArchive();
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
                    company.sorting_centers ?? company.branches,
                    company.delivery_hubs,
                    company.couriers,
                    company.registered_date,
                    company.contact_person,
                    company.contact_phone,
                    company.riders,
                    company.active_deliveries
                ].join(' ')
            ).includes(query)
        );
    }

    // UI-only account information change requests. No backend mutation.
    const accountChangeRequests = Array.isArray(logisticsManagementConfig.accountChangePreview) ? logisticsManagementConfig.accountChangePreview : [];
    const decisionKey = 'shopease-admin-logistics-change-requests-ui-v1';
    let decisions = {};
    try { decisions = JSON.parse(localStorage.getItem(decisionKey) || '{}') || {}; } catch (_) {}
    const pendingAccountChangeRequests = name => accountChangeRequests.filter(r => r.company === name && !decisions[r.id]);
    function showAccountChangeRequest(id) {
        const r = accountChangeRequests.find(x => x.id === id);
        if (!r) return;
        const layer = document.getElementById('lmChangeLayer');
        document.getElementById('lmChangeDesc').textContent = `${r.company} · ${r.id} · ${r.submitted} · ${String(decisions[id] || 'pending').toUpperCase()} (UI-only)`;
        document.getElementById('lmChangeBody').innerHTML = `<table class="lm-change-table"><thead><tr><th>Field</th><th>Current Information</th><th>Requested Information</th></tr></thead><tbody>${(r.changes || []).map(c => `<tr><td>${escapeHtml(c.field)}</td><td>${escapeHtml(c.before)}</td><td><strong>${escapeHtml(c.after)}</strong></td></tr>`).join('')}</tbody></table><p>Preview only. No changes are applied to the account or database.</p>`;
        document.getElementById('lmChangeActions').innerHTML = `<button type="button" data-change-action="close">Close</button>${!decisions[id] ? `<button type="button" class="reject" data-change-action="rejected" data-change-id="${escapeHtml(id)}">Reject</button><button type="button" class="approve" data-change-action="approved" data-change-id="${escapeHtml(id)}">Approve</button>` : ''}`;
        layer.hidden = false;
    }
    function refreshAccountChangeStrip() {
        let strip = document.getElementById('lmChangeStrip');
        if (!strip) { strip = document.createElement('div'); strip.id = 'lmChangeStrip'; strip.className = 'lm-change-strip'; document.querySelector('.logistics-main-tabs-card')?.after(strip); }
        const rows = accountChangeRequests.filter(r => !decisions[r.id]);
        strip.innerHTML = `<strong>Account Information Change Requests:</strong> ${rows.length} pending (demo) ` + rows.map(r => `<button type="button" data-change-id="${escapeHtml(r.id)}">${escapeHtml(r.company)} · Review ${escapeHtml(r.id)}</button>`).join('');
    }
    document.addEventListener('click', e => { const btn = e.target.closest('[data-change-id]'); if (btn && !btn.hasAttribute('data-change-action')) { e.preventDefault(); e.stopPropagation(); showAccountChangeRequest(btn.dataset.changeId); } });
    document.getElementById('lmChangeLayer')?.addEventListener('click', e => {
        const layer = document.getElementById('lmChangeLayer');
        const btn = e.target.closest('[data-change-action]');
        if (!btn && e.target !== layer) return;
        if (!btn || btn.dataset.changeAction === 'close') { layer.hidden = true; return; }
        decisions[btn.dataset.changeId] = btn.dataset.changeAction;
        localStorage.setItem(decisionKey, JSON.stringify(decisions));
        layer.hidden = true; refreshAccountChangeStrip(); render();
    });
    refreshAccountChangeStrip();

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
                const initials = String(company.company || 'Logistics')
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean)
                    .map(part => part[0])
                    .slice(0, 2)
                    .join('')
                    .toUpperCase() || 'L';

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
                                    <span>${escapeHtml(initials)}</span>
                                    ${company.logo ? `<img src="${escapeHtml(company.logo)}" alt="${escapeHtml(company.company)} profile photo" onerror="this.remove()">` : ''}
                                </span>

                                <span class="logistics-company-name">
                                    ${escapeHtml(company.company)}
                                    ${pendingAccountChangeRequests(company.company).length ? `<button type="button" class="lm-change-badge" data-change-id="${escapeHtml(pendingAccountChangeRequests(company.company)[0].id)}">Pending Account Change (${pendingAccountChangeRequests(company.company).length})</button>` : ''}
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="logistics-number">
                                ${escapeHtml(company.sorting_centers ?? company.branches ?? 0)}
                            </span>
                        </td>
                        <td><span class="logistics-number">${escapeHtml(company.delivery_hubs ?? 0)}</span></td>
                        <td><span class="logistics-number">${escapeHtml(company.couriers ?? 0)}</span></td>

                        <td>
                            <span class="logistics-number">
                                ${escapeHtml(company.riders ?? 0)}
                            </span>
                        </td>
                        <td><strong style="display:block;font-size:11px">${escapeHtml(company.contact_person || company.representative_name || '—')}</strong><small style="display:block;margin-top:4px;color:#8e8684;font-size:10px">${escapeHtml(company.contact_phone || company.representative_phone || company.contact_number || '—')}</small></td>
                        <td><span style="font-size:11px">${escapeHtml(company.registered_date || company.account_created_date || '—')}</span></td>
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
        const branches = Array.isArray(company?.branches_data) ? company.branches_data : [];
        if (!branches.length) { branchesBody.innerHTML=''; branchesEmpty.hidden=false; return; }
        branchesEmpty.hidden=true;
        branchesBody.innerHTML=branches.map((branch,i)=>`<tr class="lm-center-row" data-branch-index="${i}" role="button" tabindex="0" style="cursor:pointer">
            <td><b>${escapeHtml(branch.name||'—')}</b><small style="display:block;color:#9b8985">Click for sorting center & delivery hub details</small></td>
            <td>${escapeHtml(branch.address||'—')}</td><td>${escapeHtml(branch.contact_person||branch.manager_name||'—')}</td>
            <td>${escapeHtml(branch.phone||branch.manager_phone||'—')}</td><td>${escapeHtml(branch.riders??0)}</td><td>${escapeHtml(branch.delivery_hubs_count??branch.delivery_hubs?.length??0)}</td></tr>`).join('');
        function detail(i){
          const b=branches[i];if(!b)return;
          const layer=document.getElementById('lmCenterOverlay'),el=document.getElementById('lmCenterBody');
          if(!layer||!el)return;
          const rows=[['Sorting Center Name',b.name],['Province',b.province],['City / Municipality',b.city||b.municipality],['Barangay',b.barangay],['Postal Code',b.postal_code],['Complete Address',b.address],['Manager',b.manager_name||b.contact_person],['Manager Email',b.manager_email],['Manager Phone',b.manager_phone||b.phone],['Login Email',b.login_email],['Riders',b.riders],['Couriers',b.couriers]];
          const hubs=Array.isArray(b.delivery_hubs)?b.delivery_hubs:[];
          const field=(label,value)=>`<div class="ar-cell"><small>${escapeHtml(label)}</small><strong>${escapeHtml(value??'—')}</strong></div>`;
          const hubField=(label,value)=>`<div><small>${escapeHtml(label)}</small><strong>${escapeHtml(value??'—')}</strong></div>`;
          el.innerHTML=`<section class="ar-section"><h3>Sorting Center Information</h3><div class="ar-grid">${rows.map(([k,v])=>field(k,v)).join('')}</div></section><section class="ar-section"><h3>Delivery Hubs (${hubs.length})</h3>${hubs.length?hubs.map(h=>`<details class="lm-facility-detail"><summary>${escapeHtml(h.hub_name||h.name||'Delivery Hub')}</summary><div class="lm-hub-info">${[['Hub Name',h.hub_name||h.name],['Province',h.province],['City / Municipality',h.municipality||h.city],['Barangay',h.barangay],['Postal Code',h.postal_code],['Address',[h.house_no,h.street,h.building,h.subdivision,h.barangay,h.municipality||h.city,h.province].filter(Boolean).join(', ')],['Manager',h.manager_name],['Manager Email',h.manager_email],['Manager Phone',h.manager_phone],['Login Email',h.login_email],['Riders',h.riders],['Couriers',h.couriers]].map(([k,v])=>hubField(k,v)).join('')}</div></details>`).join(''):'<p style="padding:16px;font-size:11px;color:#948681">No delivery hubs assigned.</p>'}</section>`;
          document.getElementById('lmCenterTitle').textContent=b.name||'Sorting Center Details';
          layer.hidden=false;
        }
        branchesBody.querySelectorAll('[data-branch-index]').forEach(r=>{r.addEventListener('click',()=>detail(Number(r.dataset.branchIndex)));r.addEventListener('keydown',e=>{if(e.key==='Enter')detail(Number(r.dataset.branchIndex));});});
        const layer=document.getElementById('lmCenterOverlay');
        if(layer?.dataset.bound==='1')return;
        if(layer)layer.dataset.bound='1';
        const close=()=>{if(layer)layer.hidden=true;};
        document.getElementById('lmCenterClose')?.addEventListener('click',close);
        document.getElementById('lmCenterDone')?.addEventListener('click',close);
        layer?.addEventListener('click',e=>{if(e.target===layer)close();});
    }

    function populateCompanyDetails(company) {
        if (!company) {
            return;
        }

        const companyName =
            company.company ||
            'Logistics Company';
        const companyInitials = String(company.contact_person || company.company || 'L')
            .trim().split(/\s+/).filter(Boolean).map(part => part[0]).slice(0, 2).join('').toUpperCase() || 'L';
        detailsAvatar.querySelector('span').textContent = companyInitials;

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
            detailsAvatarImage.onerror = () => {
                detailsAvatar.classList.remove('has-image');
                detailsAvatarImage.removeAttribute('src');
            };
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

        const detailHost=document.getElementById('lmCompanyRegistrationDetails');
        if(detailHost){
          const v=(...keys)=>{for(const key of keys){let x=company[key]??company.registration_details?.[key];if(Array.isArray(x))x=x.join(', ');if(x!==null&&x!==undefined&&String(x).trim())return String(x);}return 'Not provided';};
          const sec=(title,fields)=>`<section class="ar-section"><h3>${escapeHtml(title)}</h3><div class="ar-grid">${fields.map(([label,...keys])=>`<div class="ar-cell"><small>${escapeHtml(label)}</small><strong>${escapeHtml(v(...keys))}</strong></div>`).join('')}</div></section>`;
          detailHost.innerHTML=sec('Complete Registration Information',[['Company / Logistics Business Name','company_name','company'],['Registered Legal Business Name','registered_business_name'],['SEC Registration No.','sec_registration_number'],['TIN','tin'],['Service Areas','service_areas'],['Registered Legal Business Name','registered_business_name'],['Company Type','company_type'],['Years in Operation','years_in_operation'],['Company Email','company_email','company_email'],['Company Contact Number','company_phone','contact_number'],['Website','website'],['Number of Sorting Centers (Registration)','branch_count'],['Sorting Centers','sorting_centers','branches'],['Delivery Hubs','delivery_hubs'],['Couriers','couriers']])+sec('Authorized Representative',[['Name','representative_name','contact_person'],['Position','representative_position'],['Email Address','representative_email'],['Contact Number','representative_phone','contact_phone'],['Sex','representative_sex'],['Birthday','representative_birthday']])+sec('Registered Office Address',[['Region','region'],['Province','province'],['City / Municipality','city','municipality'],['Barangay','barangay'],['ZIP Code','postal_code'],['Street','street'],['House No.','house_no'],['Building','building'],['Subdivision','subdivision'],['Full Office Address','office_address']])+`<section class="ar-section"><h3>Registration Documents</h3><div class="ar-docs">${[['SEC / DTI / CDA Registration','business_registration'],["Mayor’s / Business Permit",'mayors_permit'],['BIR Certificate of Registration (Form 2303)','bir_certificate'],['Courier / Delivery Accreditation (Optional)','courier_authority'],['Business / Cargo Liability Insurance (Optional)','insurance_certificate'],['Valid ID of Authorized Representative','representative_authorization']].map(([label,key])=>{const f=company.documents?.[key];const url=f?.url||'';return `<div class="ar-doc"><b>${escapeHtml(label)}</b>${url?`<button type="button" class="ar-view-document" data-document-url="${escapeHtml(url)}" data-document-title="${escapeHtml(label)}">View Document</button>`:'Not provided'}</div>`}).join('')}</div></section>`;
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
    loadLogisticsData().catch(error => {
        console.error(error);
        showLogisticsFlash('error', error.message);
    });
});