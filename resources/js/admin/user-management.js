const userManagementConfigElement =
    document.getElementById('userManagementConfig');

const userManagementConfig = userManagementConfigElement
    ? JSON.parse(userManagementConfigElement.textContent)
    : {
        users: [],
        counts: {},
        statusUrl: '',
        detailUrl: '',
        iconBase: '',
        icons: {
            seller: '',
            buyer: ''
        }
    };

document.addEventListener('DOMContentLoaded', function () {
    const users = userManagementConfig.users || [];
    const counts = userManagementConfig.counts || {};
    const statusUrl = userManagementConfig.statusUrl || '';
    const detailUrl = userManagementConfig.detailUrl || '';

    const csrfToken =
        document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

    const state = {
        page: 1,
        itemsPerPage: 10,
        filtered: [...users]
    };

    const tbody =
        document.getElementById(
            'user-management-body'
        );

    const search =
        document.getElementById(
            'user-management-search'
        );

    const typeFilter =
        document.getElementById(
            'user-management-type'
        );

    const statusFilter =
        document.getElementById(
            'user-management-status'
        );

    const dateFilter =
        document.getElementById(
            'user-management-date'
        );

    const countLabel =
        document.getElementById(
            'user-management-count'
        );

    const pagesWrap =
        document.getElementById(
            'user-management-pages'
        );

    const prev =
        document.getElementById(
            'user-management-prev'
        );

    const next =
        document.getElementById(
            'user-management-next'
        );

    const itemsSelect =
        document.getElementById(
            'user-management-items'
        );

    const reload =
        document.getElementById(
            'user-management-reload'
        );

    const reloadIcon =
        document.getElementById(
            'user-management-reload-icon'
        );

    const dateBtn =
        document.getElementById(
            'user-management-date-button'
        );

    const modal =
        document.getElementById(
            'user-management-modal'
        );

    const documentModal =
        document.getElementById(
            'user-document-modal'
        );

    const documentFrame =
        document.getElementById(
            'user-document-frame'
        );

    const documentImage =
        document.getElementById(
            'user-document-image'
        );

    const documentEmpty =
        document.getElementById(
            'user-document-empty'
        );

    const documentTitle =
        document.getElementById(
            'user-document-title'
        );

    const suspendModal =
        document.getElementById(
            'suspend-account-modal'
        );

    const deactivateModal =
        document.getElementById(
            'deactivate-account-modal'
        );

    const actionFlash =
        document.getElementById(
            'account-action-flash'
        );

    let selectedUser = null;
    let flashTimeout = null;

    function initials(name) {
        return name
            .split(' ')
            .map(
                value =>
                    value[0]
            )
            .slice(
                0,
                2
            )
            .join('')
            .toUpperCase();
    }

    function statusBadge(status) {
        const config = {
            active: [
                'Active',
                'bg-[#DDF0D6]',
                'text-[#28721B]'
            ],

            suspended: [
                'Suspended',
                'bg-[#FFD7D9]',
                'text-[#B3262E]'
            ],

            deactivated: [
                'Deactivated',
                'bg-[#FFE5D0]',
                'text-[#D16B12]'
            ]
        }[status];

        return `
            <span
                class="
                    inline-flex
                    items-center
                    px-3
                    py-1
                    rounded-full
                    ${config[1]}
                    ${config[2]}
                    text-[11px]
                    font-medium
                "
            >
                ${config[0]}
            </span>
        `;
    }

    function typeIcon(type) {
        const icon =
            type === 'seller'
                ? 'seller.png'
                : 'buyer.png';

        const label =
            type === 'seller'
                ? 'Seller'
                : 'Buyer';

        const iconBase =
            userManagementConfig.iconBase || '';

        return `
            <div class="flex items-center gap-2">

                <img
                    src="${iconBase}/${icon}"
                    class="w-[18px] h-[18px] object-contain"
                    alt="${label}"
                >

                <span class="text-[12px] text-gray-800">
                    ${label}
                </span>

            </div>
        `;
    }

    function filterUsers() {
        const q =
            (
                search.value ||
                ''
            )
                .toLowerCase()
                .trim();

        const type =
            typeFilter.value;

        const status =
            statusFilter.value;

        const date =
            dateFilter.value;

        state.filtered =
            users.filter(
                user => {
                    const matchSearch =
                        !q ||
                        [
                            user.name,
                            user.email,
                            user.phone
                        ].some(
                            value =>
                                value
                                    .toLowerCase()
                                    .includes(q)
                        );

                    const matchType =
                        type === 'all' ||
                        user.type === type;

                    const matchStatus =
                        status === 'all' ||
                        user.status === status;

                    const matchDate =
                        !date ||
                        user.date === date;

                    return (
                        matchSearch &&
                        matchType &&
                        matchStatus &&
                        matchDate
                    );
                }
            );

        state.page = 1;

        render();
        updateCards();
    }

    function render() {
        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    state.filtered.length /
                    state.itemsPerPage
                )
            );

        if (
            state.page >
            totalPages
        ) {
            state.page =
                totalPages;
        }

        const start =
            (
                state.page -
                1
            ) *
            state.itemsPerPage;

        const pageItems =
            state.filtered.slice(
                start,
                start +
                state.itemsPerPage
            );

        tbody.innerHTML =
            pageItems
                .map(
                    user => `
                        <tr
                            class="
                                user-management-row
                                cursor-pointer
                                hover:bg-[#FFF9F7]
                                transition
                            "
                            data-id="${user.id}"
                            tabindex="0"
                            role="button"
                        >

                            <td class="px-4 py-2.5">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="
                                            w-7
                                            h-7
                                            rounded-full
                                            bg-[#F4D0CA]
                                            flex
                                            items-center
                                            justify-center
                                            text-[10px]
                                            font-semibold
                                            text-[#7B1B1B]
                                            shrink-0
                                        "
                                    >
                                        ${initials(user.name)}
                                    </div>

                                    <span class="text-[12px] font-medium text-gray-800">
                                        ${user.name}
                                    </span>

                                </div>

                            </td>

                            <td class="px-4 py-2.5">
                                ${typeIcon(user.type)}
                            </td>

                            <td class="px-4 py-2.5 text-[12px] text-gray-400">
                                ${user.email}
                            </td>

                            <td class="px-4 py-2.5 text-[12px] text-gray-800">
                                ${user.phone}
                            </td>

                            <td class="px-4 py-2.5">

                                <div class="text-[13px] text-gray-800">
                                    ${user.dateLabel}
                                </div>

                                <div class="text-[11px] text-gray-400">
                                    10:30 AM
                                </div>

                            </td>

                            <td class="px-4 py-2.5">
                                ${statusBadge(user.status)}
                            </td>

                        </tr>
                    `
                )
                .join('');

        countLabel.textContent =
            `Showing ${
                Math.min(
                    state.itemsPerPage,
                    pageItems.length
                )
            } out of ${
                state.filtered.length
            } users`;

        renderPages(
            totalPages
        );
    }

    function renderPages(
        totalPages
    ) {
        pagesWrap.innerHTML =
            '';

        const maxVisible =
            5;

        let startPage =
            Math.max(
                1,
                state.page -
                2
            );

        let endPage =
            Math.min(
                totalPages,
                startPage +
                maxVisible -
                1
            );

        startPage =
            Math.max(
                1,
                endPage -
                maxVisible +
                1
            );

        for (
            let page =
                startPage;
            page <=
            endPage;
            page++
        ) {
            const btn =
                document.createElement(
                    'button'
                );

            btn.type =
                'button';

            btn.textContent =
                page;

            btn.className =
                `w-7 h-7 flex items-center justify-center rounded-md text-[12px] transition ${
                    page ===
                    state.page
                        ? 'bg-[#FFD1C2] text-[#7B1B1B] font-semibold'
                        : 'hover:bg-gray-100 text-gray-700'
                }`;

            btn.addEventListener(
                'click',
                () => {
                    state.page =
                        page;

                    render();
                }
            );

            pagesWrap.appendChild(
                btn
            );
        }

        prev.disabled =
            state.page ===
            1;

        next.disabled =
            state.page ===
            totalPages;

        prev.classList.toggle(
            'opacity-40',
            prev.disabled
        );

        next.classList.toggle(
            'opacity-40',
            next.disabled
        );
    }

    function updateCards() {
        document
            .getElementById(
                'buyers-count-card'
            )
            .textContent =
            counts.buyers;

        document
            .getElementById(
                'sellers-count-card'
            )
            .textContent =
            counts.sellers;

        document
            .getElementById(
                'suspended-count-card'
            )
            .textContent =
            counts.suspended;

        document
            .getElementById(
                'total-users-count-card'
            )
            .textContent =
            Number(
                counts.total ||
                0
            ).toLocaleString();
    }

    function renderModalActions(
        user
    ) {
        const actions =
            document.getElementById(
                'user-modal-actions'
            );

        const suspensionInfo =
            document.getElementById(
                'user-suspension-info'
            );

        const suspensionDuration =
            document.getElementById(
                'user-suspension-duration'
            );

        actions.innerHTML =
            '';

        suspensionInfo.classList.add(
            'hidden'
        );

        if (
            user.status ===
            'active'
        ) {
            actions.innerHTML = `
                <button
                    type="button"
                    id="user-modal-suspend"
                    class="
                        px-6
                        py-2
                        rounded-lg
                        border
                        border-[#D41F1F]
                        bg-[#FFE0E0]
                        text-[#AE0000]
                        text-[13px]
                        font-semibold
                        hover:bg-[#FFD1D1]
                        transition
                    "
                >
                    Suspend
                </button>

                <button
                    type="button"
                    id="user-modal-deactivate"
                    class="
                        px-6
                        py-2
                        rounded-lg
                        border
                        border-[#F08B4D]
                        bg-[#FFF0E6]
                        text-[#C96A00]
                        text-[13px]
                        font-semibold
                        hover:bg-[#FFE6D8]
                        transition
                    "
                >
                    Deactivate
                </button>
            `;
        } else if (
            user.status ===
            'suspended'
        ) {
            suspensionInfo
                .classList
                .remove(
                    'hidden'
                );

            const days =
                Number(
                    user.suspensionDays ||
                    7
                );

            suspensionDuration.textContent =
                `${days} ${
                    days === 1
                        ? 'day'
                        : 'days'
                } remaining`;

            actions.innerHTML = `
                <button
                    type="button"
                    id="user-modal-activate"
                    class="
                        px-6
                        py-2
                        rounded-lg
                        border
                        border-[#79C56C]
                        bg-[#DDF0D6]
                        text-[#16710C]
                        text-[13px]
                        font-semibold
                        hover:bg-[#D2EAC9]
                        transition
                    "
                >
                    Activate
                </button>
            `;
        } else {
            actions.innerHTML = `
                <button
                    type="button"
                    id="user-modal-activate"
                    class="
                        px-6
                        py-2
                        rounded-lg
                        border
                        border-[#79C56C]
                        bg-[#DDF0D6]
                        text-[#16710C]
                        text-[13px]
                        font-semibold
                        hover:bg-[#D2EAC9]
                        transition
                    "
                >
                    Activate
                </button>
            `;
        }

        const suspendBtn =
            document.getElementById(
                'user-modal-suspend'
            );

        const deactivateBtn =
            document.getElementById(
                'user-modal-deactivate'
            );

        const activateBtn =
            document.getElementById(
                'user-modal-activate'
            );

        if (
            suspendBtn
        ) {
            suspendBtn.addEventListener(
                'click',
                openSuspendAccountModal
            );
        }

        if (
            deactivateBtn
        ) {
            deactivateBtn.addEventListener(
                'click',
                openDeactivateAccountModal
            );
        }

        if (
            activateBtn
        ) {
            activateBtn.addEventListener(
                'click',
                () =>
                    updateSelectedStatus(
                        'active',
                        true
                    )
            );
        }
    }

    function clearRadioGroup(
        name
    ) {
        document
            .querySelectorAll(
                `input[name="${name}"]`
            )
            .forEach(
                input => {
                    input.checked =
                        false;
                }
            );
    }

    function resetSuspendForm() {
        clearRadioGroup(
            'suspend-reason'
        );

        document
            .getElementById(
                'suspension-duration-input'
            )
            .value =
            '';

        document
            .getElementById(
                'suspend-additional-details'
            )
            .value =
            '';

        document
            .getElementById(
                'suspend-details-count'
            )
            .textContent =
            '0/300';
    }

    function resetDeactivateForm() {
        clearRadioGroup(
            'deactivate-reason'
        );

        document
            .getElementById(
                'deactivate-additional-details'
            )
            .value =
            '';

        document
            .getElementById(
                'deactivate-details-count'
            )
            .textContent =
            '0/300';
    }

    function openSuspendAccountModal() {
        if (
            !selectedUser
        ) {
            return;
        }

        resetSuspendForm();

        suspendModal
            .classList
            .remove(
                'hidden'
            );

        suspendModal
            .classList
            .add(
                'flex'
            );

        suspendModal.setAttribute(
            'aria-hidden',
            'false'
        );
    }

    function closeSuspendAccountModal() {
        suspendModal
            .classList
            .add(
                'hidden'
            );

        suspendModal
            .classList
            .remove(
                'flex'
            );

        suspendModal.setAttribute(
            'aria-hidden',
            'true'
        );
    }

    function openDeactivateAccountModal() {
        if (
            !selectedUser
        ) {
            return;
        }

        resetDeactivateForm();

        deactivateModal
            .classList
            .remove(
                'hidden'
            );

        deactivateModal
            .classList
            .add(
                'flex'
            );

        deactivateModal.setAttribute(
            'aria-hidden',
            'false'
        );
    }

    function closeDeactivateAccountModal() {
        deactivateModal
            .classList
            .add(
                'hidden'
            );

        deactivateModal
            .classList
            .remove(
                'flex'
            );

        deactivateModal.setAttribute(
            'aria-hidden',
            'true'
        );
    }

    function closeActionFlash() {
        if (
            flashTimeout
        ) {
            clearTimeout(
                flashTimeout
            );

            flashTimeout =
                null;
        }

        actionFlash
            ?.classList
            .remove(
                'show'
            );
    }

    function showActionFlash(
        status,
        user
    ) {
        if (
            !actionFlash
        ) {
            return;
        }

        const userName =
            user?.name ||
            'User';

        const userEmail =
            user?.email ||
            'their registered email address';

        const config = {
            active: {
                className:
                    'activated',

                title:
                    'Account Activated',

                message:
                    `${userName}'s account has been activated. A notification will be sent to ${userEmail}.`
            },

            suspended: {
                className:
                    'suspended',

                title:
                    'Account Suspended',

                message:
                    `${userName}'s account has been suspended. A notification will be sent to ${userEmail}.`
            },

            deactivated: {
                className:
                    'deactivated',

                title:
                    'Account Deactivated',

                message:
                    `${userName}'s account has been deactivated. A notification will be sent to ${userEmail}.`
            }
        }[status] || {
            className:
                'activated',

            title:
                'Account Updated',

            message:
                `${userName}'s account status has been updated. A notification will be sent to ${userEmail}.`
        };

        document
            .getElementById(
                'account-action-flash-title'
            )
            .textContent =
            config.title;

        document
            .getElementById(
                'account-action-flash-message'
            )
            .textContent =
            config.message;

        actionFlash
            .classList
            .remove(
                'activated',
                'suspended',
                'deactivated'
            );

        actionFlash
            .classList
            .add(
                config.className
            );

        if (
            flashTimeout
        ) {
            clearTimeout(
                flashTimeout
            );

            flashTimeout =
                null;
        }

        actionFlash
            .classList
            .add(
                'show'
            );

        flashTimeout =
            setTimeout(
                closeActionFlash,
                3800
            );
    }

    function getSelectedRadioValue(
        name
    ) {
        const selected =
            document.querySelector(
                `input[name="${name}"]:checked`
            );

        return selected
            ? selected.value
            : '';
    }

    function renderUserCategories(
        value
    ) {
        const categories =
            document.getElementById(
                'user-profile-categories'
            );

        categories.replaceChildren();

        const categoryList =
            Array.isArray(
                value
            )
                ? value
                : String(
                    value ||
                    ''
                ).split(',');

        const categoryValues =
            categoryList
                .map(
                    category =>
                        category.trim()
                )
                .filter(
                    Boolean
                );

        if (
            categoryValues.length ===
            0
        ) {
            const emptyState =
                document.createElement(
                    'span'
                );

            emptyState.className =
                'text-[13px] text-gray-500';

            emptyState.textContent =
                'Not provided';

            categories.appendChild(
                emptyState
            );

            return;
        }

        const categoryStyles = {
            'pet-supplies': {
                background: '#E7F5E9',
                color: '#2F6B3A'
            },

            'electronics-and-gadgets': {
                background: '#DDEBFF',
                color: '#185FA3'
            },

            'womens-apparel': {
                background: '#F9DFEA',
                color: '#A12763'
            },

            'mens-apparel': {
                background: '#E6E2F8',
                color: '#5A4A9A'
            },

            'kids-and-baby': {
                background: '#FFE5B8',
                color: '#9A5B00'
            },

            'home-and-garden': {
                background: '#DDF3E4',
                color: '#27704A'
            },

            'sports-and-outdoors': {
                background: '#DDECF2',
                color: '#23627A'
            },

            'health-and-beauty': {
                background: '#FFE0DC',
                color: '#A63B2C'
            },

            'books-and-media': {
                background: '#E6E8F2',
                color: '#3F4A68'
            },

            'food-and-gourmet': {
                background: '#FFF0C7',
                color: '#8A5A00'
            },

            'automotive-motorcycle': {
                background: '#E3E3E3',
                color: '#434343'
            },

            'furniture-and-office-equipment': {
                background: '#EBDCCF',
                color: '#795548'
            },

            'jewelry-and-watches': {
                background: '#F8E2B8',
                color: '#946B00'
            },

            'office-and-school-supplies': {
                background: '#E2F0F7',
                color: '#2B617D'
            },

            default: {
                background: '#F1EFEE',
                color: '#6B6663'
            }
        };

        const slugifyCategory =
            category =>
                String(
                    category ||
                    ''
                )
                    .trim()
                    .toLowerCase()
                    .replace(
                        /&/g,
                        'and'
                    )
                    .replace(
                        /[^a-z0-9]+/g,
                        '-'
                    )
                    .replace(
                        /^-+|-+$/g,
                        ''
                    );

        categoryValues.forEach(
            category => {
                const badge =
                    document.createElement(
                        'span'
                    );

                const style =
                    categoryStyles[
                        slugifyCategory(
                            category
                        )
                    ] ||
                    categoryStyles.default;

                badge.className =
                    'inline-flex items-center rounded-full px-3 py-1 text-[12px] font-medium';

                badge.style.backgroundColor =
                    style.background;

                badge.style.color =
                    style.color;

                badge.textContent =
                    category;

                categories.appendChild(
                    badge
                );
            }
        );
    }

    async function loadUserDetails(
        user
    ) {
        const response =
            await fetch(
                detailUrl.replace(
                    '__USER__',
                    user.id
                ),
                {
                    headers: {
                        Accept:
                            'application/json'
                    },

                    credentials:
                        'same-origin'
                }
            );

        if (
            !response.ok ||
            selectedUser?.id !==
            user.id
        ) {
            return;
        }

        const details =
            (
                await response.json()
            ).details ||
            {};

        const fields = {
            'user-modal-last-name':
                details.last_name,

            'user-modal-first-name':
                details.first_name,

            'user-modal-middle-name':
                details.middle_initial,

            'user-modal-sex':
                details.sex,

            'user-modal-birthday':
                details.birthday,

            'user-modal-age':
                details.age,

            'user-modal-province':
                details.province,

            'user-modal-municipality':
                details.municipality,

            'user-modal-barangay':
                details.barangay,

            'user-modal-street':
                details.street,

            'user-modal-house':
                details.house_number,

            'user-modal-zip':
                details.zip_code,

            'user-modal-business-name':
                details.business_name,

            'user-modal-business-category':
                details.line_of_business
        };

        Object
            .entries(
                fields
            )
            .forEach(
                (
                    [
                        id,
                        value
                    ]
                ) => {
                    document
                        .getElementById(
                            id
                        )
                        .textContent =
                        value ||
                        'Not provided';
                }
            );

        renderUserCategories(
            details.line_of_business
        );

        const validId =
            document.getElementById(
                'user-modal-valid-id'
            );

        const validIdEmpty =
            document.getElementById(
                'user-modal-valid-id-empty'
            );

        const validIdLink =
            document.getElementById(
                'user-modal-valid-id-link'
            );

        validId.src =
            details.valid_id_url ||
            '';

        validId.classList.toggle(
            'hidden',
            !details.valid_id_url
        );

        validIdEmpty.classList.toggle(
            'hidden',
            Boolean(
                details.valid_id_url
            )
        );

        validIdLink.href =
            '#';

        validIdLink.dataset.url =
            details.valid_id_url ||
            '';

        validIdLink.classList.add(
            'hidden'
        );

        const permitLink =
            document.getElementById(
                'user-modal-business-permit-link'
            );

        const permitName =
            document.getElementById(
                'user-modal-business-permit'
            );

        permitLink.href =
            '#';

        permitLink.dataset.url =
            details.business_permit_url ||
            '';

        permitLink.classList.toggle(
            'pointer-events-none',
            !details.business_permit_url
        );

        permitName.textContent =
            details.upload_business_permit
                ? details.upload_business_permit
                    .split('/')
                    .pop()
                : 'Business permit not provided';
    }

    function openModal(
        user
    ) {
        selectedUser =
            user;

        const isSeller =
            user.type ===
            'seller';

        document
            .getElementById(
                'user-profile-name'
            )
            .textContent =
            user.name;

        document
            .getElementById(
                'user-profile-type'
            )
            .textContent =
            isSeller
                ? 'Seller'
                : 'Buyer';

        document
            .getElementById(
                'user-profile-type-icon'
            )
            .src =
            isSeller
                ? userManagementConfig.icons.seller
                : userManagementConfig.icons.buyer;

        document
            .getElementById(
                'user-profile-type-icon'
            )
            .alt =
            isSeller
                ? 'Seller'
                : 'Buyer';

        const profileStatus =
            document.getElementById(
                'user-profile-status'
            );

        profileStatus.innerHTML =
            statusBadge(
                user.status
            );

        document
            .getElementById(
                'user-profile-date'
            )
            .textContent =
            user.dateLabel;

        document
            .getElementById(
                'user-profile-time'
            )
            .textContent =
            user.timeLabel ||
            '';

        document
            .getElementById(
                'user-modal-last-name'
            )
            .textContent =
            'Loading...';

        document
            .getElementById(
                'user-modal-first-name'
            )
            .textContent =
            'Loading...';

        document
            .getElementById(
                'user-modal-middle-name'
            )
            .textContent =
            'Loading...';

        document
            .getElementById(
                'user-modal-sex'
            )
            .textContent =
            'Loading...';

        document
            .getElementById(
                'user-modal-birthday'
            )
            .textContent =
            'Loading...';

        document
            .getElementById(
                'user-modal-age'
            )
            .textContent =
            'Loading...';

        document
            .getElementById(
                'user-modal-email'
            )
            .textContent =
            user.email;

        document
            .getElementById(
                'user-modal-phone'
            )
            .textContent =
            user.phone;

        [
            'user-modal-province',
            'user-modal-municipality',
            'user-modal-barangay',
            'user-modal-street',
            'user-modal-house',
            'user-modal-zip'
        ].forEach(
            id => {
                document
                    .getElementById(
                        id
                    )
                    .textContent =
                    'Loading...';
            }
        );

        const categories =
            document.getElementById(
                'user-profile-categories'
            );

        const businessSection =
            document.getElementById(
                'seller-business-section'
            );

        if (
            isSeller
        ) {
            categories
                .classList
                .remove(
                    'hidden'
                );

            businessSection
                .classList
                .remove(
                    'hidden'
                );

            renderUserCategories(
                ''
            );

            document
                .getElementById(
                    'user-modal-business-name'
                )
                .textContent =
                'Loading...';

            document
                .getElementById(
                    'user-modal-business-category'
                )
                .textContent =
                'Loading...';

            document
                .getElementById(
                    'user-modal-business-permit'
                )
                .textContent =
                'Loading...';
        } else {
            categories
                .classList
                .add(
                    'hidden'
                );

            businessSection
                .classList
                .add(
                    'hidden'
                );
        }

        renderModalActions(
            user
        );

        modal
            .classList
            .remove(
                'hidden'
            );

        modal
            .classList
            .add(
                'flex'
            );

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body
            .classList
            .add(
                'overflow-hidden'
            );

        loadUserDetails(
            user
        );
    }

    function closeModal() {
        modal
            .classList
            .add(
                'hidden'
            );

        modal
            .classList
            .remove(
                'flex'
            );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body
            .classList
            .remove(
                'overflow-hidden'
            );

        selectedUser =
            null;
    }

    async function updateSelectedStatus(
        newStatus,
        notifyUser = false,
        action = {}
    ) {
        if (
            !selectedUser
        ) {
            return;
        }

        const user =
            users.find(
                item =>
                    item.id ===
                    selectedUser.id
            );

        if (
            !user
        ) {
            return;
        }

        const previousUserState = {
            ...user
        };

        const previousCounts = {
            ...counts
        };

        const previousStatus =
            user.status;

        user.status =
            newStatus;

        if (
            newStatus ===
            'suspended'
        ) {
            user.suspensionDays =
                Number(
                    action.duration ||
                    user.suspensionDays ||
                    7
                );
        } else {
            user.suspensionDays =
                0;
        }

        if (
            previousStatus !==
            newStatus
        ) {
            if (
                previousStatus ===
                'suspended' &&
                newStatus !==
                'suspended'
            ) {
                counts.suspended =
                    Math.max(
                        0,
                        Number(
                            counts.suspended ||
                            0
                        ) -
                        1
                    );
            } else if (
                previousStatus !==
                    'suspended' &&
                newStatus ===
                    'suspended'
            ) {
                counts.suspended =
                    Number(
                        counts.suspended ||
                        0
                    ) +
                    1;
            }
        }

        selectedUser =
            user;

        document
            .getElementById(
                'user-profile-status'
            )
            .innerHTML =
            statusBadge(
                user.status
            );

        renderModalActions(
            user
        );

        render();
        updateCards();

        if (
            notifyUser
        ) {
            showActionFlash(
                newStatus,
                user
            );
        }

        try {
            const response =
                await fetch(
                    statusUrl.replace(
                        '__USER__',
                        user.id
                    ),
                    {
                        method:
                            'PATCH',

                        credentials:
                            'same-origin',

                        headers: {
                            'Content-Type':
                                'application/json',

                            Accept:
                                'application/json',

                            'X-CSRF-TOKEN':
                                csrfToken
                        },

                        body:
                            JSON.stringify({
                                status:
                                    newStatus,

                                ...action
                            })
                    }
                );

            if (
                !response.ok
            ) {
                const error =
                    await response
                        .json()
                        .catch(
                            () => ({})
                        );

                Object.assign(
                    user,
                    previousUserState
                );

                Object
                    .keys(
                        counts
                    )
                    .forEach(
                        key =>
                            delete counts[key]
                    );

                Object.assign(
                    counts,
                    previousCounts
                );

                selectedUser =
                    user;

                document
                    .getElementById(
                        'user-profile-status'
                    )
                    .innerHTML =
                    statusBadge(
                        user.status
                    );

                renderModalActions(
                    user
                );

                render();
                updateCards();

                closeActionFlash();

                alert(
                    error.message ||
                    'Unable to update this account.'
                );

                return;
            }

            const result =
                await response.json();

            Object.assign(
                user,
                result.user
            );

            Object.assign(
                counts,
                result.counts
            );

            selectedUser =
                user;

            document
                .getElementById(
                    'user-profile-status'
                )
                .innerHTML =
                statusBadge(
                    user.status
                );

            renderModalActions(
                user
            );

            render();
            updateCards();

        } catch (
            error
        ) {
            console.error(
                error
            );

            Object.assign(
                user,
                previousUserState
            );

            Object
                .keys(
                    counts
                )
                .forEach(
                    key =>
                        delete counts[key]
                );

            Object.assign(
                counts,
                previousCounts
            );

            selectedUser =
                user;

            document
                .getElementById(
                    'user-profile-status'
                )
                .innerHTML =
                statusBadge(
                    user.status
                );

            renderModalActions(
                user
            );

            render();
            updateCards();

            closeActionFlash();

            alert(
                'Something went wrong while updating the account.'
            );
        }
    }

    search.addEventListener(
        'input',
        filterUsers
    );

    typeFilter.addEventListener(
        'change',
        filterUsers
    );

    statusFilter.addEventListener(
        'change',
        filterUsers
    );

    dateFilter.addEventListener(
        'change',
        filterUsers
    );

    itemsSelect.addEventListener(
        'change',
        () => {
            state.itemsPerPage =
                parseInt(
                    itemsSelect.value,
                    10
                );

            state.page =
                1;

            render();
        }
    );

    dateBtn.addEventListener(
        'click',
        () => {
            if (
                typeof dateFilter.showPicker ===
                'function'
            ) {
                dateFilter.showPicker();
            } else {
                dateFilter.click();
            }
        }
    );

    prev.addEventListener(
        'click',
        () => {
            if (
                state.page >
                1
            ) {
                state.page--;

                render();
            }
        }
    );

    next.addEventListener(
        'click',
        () => {
            const totalPages =
                Math.ceil(
                    state.filtered.length /
                    state.itemsPerPage
                );

            if (
                state.page <
                totalPages
            ) {
                state.page++;

                render();
            }
        }
    );

    reload.addEventListener(
        'click',
        () => {
            reloadIcon
                .classList
                .add(
                    'animate-spin'
                );

            reload.disabled =
                true;

            setTimeout(
                () => {
                    search.value =
                        '';

                    typeFilter.value =
                        'all';

                    statusFilter.value =
                        'all';

                    dateFilter.value =
                        '';

                    itemsSelect.value =
                        '10';

                    state.itemsPerPage =
                        10;

                    state.page =
                        1;

                    state.filtered = [
                        ...users
                    ];

                    render();
                    updateCards();

                    reloadIcon
                        .classList
                        .remove(
                            'animate-spin'
                        );

                    reload.disabled =
                        false;
                },
                500
            );
        }
    );

    tbody.addEventListener(
        'click',
        function (
            event
        ) {
            const row =
                event.target.closest(
                    '.user-management-row'
                );

            if (
                !row
            ) {
                return;
            }

            const user =
                users.find(
                    item =>
                        item.id ===
                        Number(
                            row.dataset.id
                        )
                );

            if (
                user
            ) {
                openModal(
                    user
                );
            }
        }
    );

    tbody.addEventListener(
        'keydown',
        function (
            event
        ) {
            if (
                event.key !==
                    'Enter' &&
                event.key !==
                    ' '
            ) {
                return;
            }

            const row =
                event.target.closest(
                    '.user-management-row'
                );

            if (
                !row
            ) {
                return;
            }

            event.preventDefault();

            const user =
                users.find(
                    item =>
                        item.id ===
                        Number(
                            row.dataset.id
                        )
                );

            if (
                user
            ) {
                openModal(
                    user
                );
            }
        }
    );

    function openDocumentModal(
        url,
        title
    ) {
        if (
            !url
        ) {
            return;
        }

        documentTitle.textContent =
            title ||
            'Document';

        documentEmpty
            .classList
            .add(
                'hidden'
            );

        documentImage
            .classList
            .add(
                'hidden'
            );

        documentFrame
            .classList
            .add(
                'hidden'
            );

        documentImage.src =
            '';

        documentFrame.src =
            'about:blank';

        const path =
            String(
                url
            )
                .split('?')[0]
                .toLowerCase();

        const isImage =
            /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i.test(
                path
            );

        if (
            isImage
        ) {
            documentImage.src =
                url;

            documentImage
                .classList
                .remove(
                    'hidden'
                );
        } else {
            documentFrame.src =
                url;

            documentFrame
                .classList
                .remove(
                    'hidden'
                );
        }

        documentModal
            .classList
            .remove(
                'hidden'
            );

        documentModal
            .classList
            .add(
                'flex',
                'is-open'
            );

        documentModal.setAttribute(
            'aria-hidden',
            'false'
        );
    }

    function closeDocumentModal() {
        documentModal
            .classList
            .add(
                'hidden'
            );

        documentModal
            .classList
            .remove(
                'flex',
                'is-open'
            );

        documentModal.setAttribute(
            'aria-hidden',
            'true'
        );

        documentImage.src =
            '';

        documentFrame.src =
            'about:blank';
    }

    document
        .getElementById(
            'user-modal-valid-id-preview'
        )
        .addEventListener(
            'click',
            function () {
                openDocumentModal(
                    document
                        .getElementById(
                            'user-modal-valid-id-link'
                        )
                        .dataset.url,
                    'Valid ID'
                );
            }
        );

    document
        .getElementById(
            'user-modal-business-permit-link'
        )
        .addEventListener(
            'click',
            function (
                event
            ) {
                event.preventDefault();

                openDocumentModal(
                    this.dataset.url,
                    'Business Permit'
                );
            }
        );

    document
        .getElementById(
            'close-user-document-modal'
        )
        .addEventListener(
            'click',
            closeDocumentModal
        );

    documentModal.addEventListener(
        'click',
        function (
            event
        ) {
            if (
                event.target ===
                documentModal
            ) {
                closeDocumentModal();
            }
        }
    );

    document
        .getElementById(
            'close-user-management-modal'
        )
        .addEventListener(
            'click',
            closeModal
        );

    modal.addEventListener(
        'click',
        event => {
            if (
                event.target ===
                modal
            ) {
                closeModal();
            }
        }
    );

    document
        .getElementById(
            'cancel-suspend-account'
        )
        .addEventListener(
            'click',
            closeSuspendAccountModal
        );

    suspendModal.addEventListener(
        'click',
        event => {
            if (
                event.target ===
                suspendModal
            ) {
                closeSuspendAccountModal();
            }
        }
    );

    document
        .getElementById(
            'cancel-deactivate-account'
        )
        .addEventListener(
            'click',
            closeDeactivateAccountModal
        );

    deactivateModal.addEventListener(
        'click',
        event => {
            if (
                event.target ===
                deactivateModal
            ) {
                closeDeactivateAccountModal();
            }
        }
    );

    document
        .getElementById(
            'close-account-action-flash'
        )
        .addEventListener(
            'click',
            closeActionFlash
        );

    document
        .getElementById(
            'suspend-additional-details'
        )
        .addEventListener(
            'input',
            function () {
                document
                    .getElementById(
                        'suspend-details-count'
                    )
                    .textContent =
                    `${this.value.length}/300`;
            }
        );

    document
        .getElementById(
            'deactivate-additional-details'
        )
        .addEventListener(
            'input',
            function () {
                document
                    .getElementById(
                        'deactivate-details-count'
                    )
                    .textContent =
                    `${this.value.length}/300`;
            }
        );

    const suspensionDurationInput =
        document.getElementById(
            'suspension-duration-input'
        );

    suspensionDurationInput.addEventListener(
        'keydown',
        function (
            event
        ) {
            const allowedControlKeys = [
                'Backspace',
                'Delete',
                'Tab',
                'Escape',
                'ArrowLeft',
                'ArrowRight',
                'Home',
                'End'
            ];

            if (
                allowedControlKeys.includes(
                    event.key
                ) ||
                event.ctrlKey ||
                event.metaKey
            ) {
                return;
            }

            if (
                !/^[0-9]$/.test(
                    event.key
                )
            ) {
                event.preventDefault();
            }
        }
    );

    suspensionDurationInput.addEventListener(
        'input',
        function () {
            this.value =
                this.value.replace(
                    /\D/g,
                    ''
                );
        }
    );

    document
        .getElementById(
            'confirm-suspend-account'
        )
        .addEventListener(
            'click',
            function () {
                if (
                    !selectedUser
                ) {
                    return;
                }

                const reason =
                    getSelectedRadioValue(
                        'suspend-reason'
                    );

                const duration =
                    Number(
                        document
                            .getElementById(
                                'suspension-duration-input'
                            )
                            .value
                    );

                if (
                    !reason
                ) {
                    alert(
                        'Please select a suspension reason.'
                    );

                    return;
                }

                if (
                    !Number.isInteger(
                        duration
                    ) ||
                    duration <
                        1
                ) {
                    alert(
                        'Please enter a valid suspension duration in days.'
                    );

                    return;
                }

                const user =
                    users.find(
                        item =>
                            item.id ===
                            selectedUser.id
                    );

                if (
                    !user
                ) {
                    return;
                }

                user.suspensionDays =
                    duration;

                closeSuspendAccountModal();

                updateSelectedStatus(
                    'suspended',
                    true,
                    {
                        reason,
                        duration,

                        details:
                            document
                                .getElementById(
                                    'suspend-additional-details'
                                )
                                .value
                    }
                );
            }
        );

    document
        .getElementById(
            'confirm-deactivate-account'
        )
        .addEventListener(
            'click',
            function () {
                if (
                    !selectedUser
                ) {
                    return;
                }

                const reason =
                    getSelectedRadioValue(
                        'deactivate-reason'
                    );

                if (
                    !reason
                ) {
                    alert(
                        'Please select a deactivation reason.'
                    );

                    return;
                }

                closeDeactivateAccountModal();

                updateSelectedStatus(
                    'deactivated',
                    true,
                    {
                        reason,

                        details:
                            document
                                .getElementById(
                                    'deactivate-additional-details'
                                )
                                .value
                    }
                );
            }
        );

    document.addEventListener(
        'keydown',
        event => {
            if (
                event.key !==
                'Escape'
            ) {
                return;
            }

            if (
                !suspendModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {
                closeSuspendAccountModal();

                return;
            }

            if (
                !deactivateModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {
                closeDeactivateAccountModal();

                return;
            }

            if (
                !documentModal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {
                closeDocumentModal();

                return;
            }

            if (
                !modal
                    .classList
                    .contains(
                        'hidden'
                    )
            ) {
                closeModal();
            }
        }
    );

    const pageContent =
        document.getElementById(
            'admin-content'
        );

    pageContent.classList.add(
        'opacity-0',
        'translate-y-2'
    );

    requestAnimationFrame(
        () => {
            setTimeout(
                () =>
                    pageContent
                        .classList
                        .remove(
                            'opacity-0',
                            'translate-y-2'
                        ),
                70
            );
        }
    );

    render();
    updateCards();
});