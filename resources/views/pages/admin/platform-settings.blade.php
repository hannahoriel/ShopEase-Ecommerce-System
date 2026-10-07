@extends('layouts.admin')

@section('page-title', 'Platform Settings')

@section('content')

@vite('resources/css/admin/platform-settings.css')

@php
    $announcements = $announcements ?? [];
    $policies = $policies ?? [];
@endphp



<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>

    <div class="platform-toolbar">

        <div
            class="platform-tabs"
            role="tablist"
            aria-label="Platform Settings"
        >
            <button
                id="announcements-tab"
                type="button"
                class="platform-tab active"
                role="tab"
                aria-selected="true"
                aria-controls="announcements-panel"
                data-platform-tab="announcements"
            >
                Announcements
            </button>

            <button
                id="policies-tab"
                type="button"
                class="platform-tab"
                role="tab"
                aria-selected="false"
                aria-controls="policies-panel"
                data-platform-tab="policies"
            >
                Platform Policies
            </button>
        </div>

        <button
            id="platform-create-button"
            type="button"
            class="platform-create-button"
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
                    stroke-width="2"
                    d="M12 5v14M5 12h14"
                />
            </svg>

            <span id="platform-create-label">
                Create Announcement
            </span>
        </button>

    </div>

    <section class="platform-board">

        <!-- =====================================================
             ANNOUNCEMENTS PANEL
        ====================================================== -->
        <div
            id="announcements-panel"
            class="platform-panel active"
            role="tabpanel"
            aria-labelledby="announcements-tab"
        >
            <div
                id="announcements-grid"
                class="platform-grid"
            >

                @foreach ($announcements as $item)
                    <article class="platform-card">

                        <div class="platform-card-main">

                            <div class="platform-card-icon theme-{{ $item['theme'] }}">

                                @if ($item['icon'] === 'megaphone')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M13 29v8c0 4.4 3.6 8 8 8h4l4 11h8l-4-12c6-1 13-4 21-10V20c-10 8-19 10-27 10h-6c-4.4 0-8 3.6-8 8Z"/>
                                        <path d="M47 22h5v10h-5z"/>
                                    </svg>
                                @elseif ($item['icon'] === 'box')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M11 20 32 9l21 11-21 11L11 20Zm3 7 15 8v20l-15-8V27Zm21 8 15-8v20l-15 8V35Z"/>
                                    </svg>
                                @elseif ($item['icon'] === 'shield')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M32 7 11 15v15c0 13 8 23 21 29 13-6 21-16 21-29V15L32 7Zm-2 43c-8-5-12-11-13-19V20l13-5v35Zm4 0V15l13 5v11c-1 8-5 14-13 19Z"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                                    </svg>
                                @endif

                            </div>

                            <h3 class="platform-card-title">
                                {{ $item['title'] }}
                            </h3>

                            <p class="platform-card-description">
                                {{ $item['description'] }}
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

                                <span>{{ $item['audience'] }}</span>
                            </div>

                            <div class="platform-card-meta">

                                <span class="platform-status">
                                    {{ $item['status'] }}
                                </span>

                                <div class="platform-card-date">
                                    <div>{{ $item['date'] }}</div>
                                    <div>{{ $item['time'] }}</div>
                                </div>

                                <button
                                    type="button"
                                    class="platform-more"
                                    aria-label="More announcement actions"
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

                            </div>

                        </div>

                    </article>
                @endforeach

                @for ($i = count($announcements); $i < 8; $i++)
                    <div
                        class="platform-empty-card"
                        aria-hidden="true"
                    ></div>
                @endfor

            </div>
        </div>


        <!-- =====================================================
             PLATFORM POLICIES PANEL
        ====================================================== -->
        <div
            id="policies-panel"
            class="platform-panel"
            role="tabpanel"
            aria-labelledby="policies-tab"
        >
            <div id="policies-grid" class="platform-grid">

                @foreach ($policies as $item)
                    <article class="platform-card">

                        <div class="platform-card-main">

                            <div class="platform-card-icon theme-{{ $item['theme'] }}">

                                @if ($item['icon'] === 'shield')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M32 7 11 15v15c0 13 8 23 21 29 13-6 21-16 21-29V15L32 7Zm-2 43c-8-5-12-11-13-19V20l13-5v35Zm4 0V15l13 5v11c-1 8-5 14-13 19Z"/>
                                    </svg>
                                @elseif ($item['icon'] === 'truck')
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M8 17h29v24H8V17Zm31 8h8l9 10v6H39V25Zm-24 20a6 6 0 1 1 0 12 6 6 0 0 1 0-12Zm32 0a6 6 0 1 1 0 12 6 6 0 0 1 0-12ZM39 29v9h12l-7-9h-5Z"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                        <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                                    </svg>
                                @endif

                            </div>

                            <h3 class="platform-card-title">
                                {{ $item['title'] }}
                            </h3>

                            <p class="platform-card-description">
                                {{ $item['description'] }}
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

                                <span>{{ $item['audience'] }}</span>
                            </div>

                            <div class="platform-card-meta">

                                <span class="platform-status">
                                    {{ $item['status'] }}
                                </span>

                                <div class="platform-card-date">
                                    <div>{{ $item['date'] }}</div>
                                    <div>{{ $item['time'] }}</div>
                                </div>

                                <button
                                    type="button"
                                    class="platform-more"
                                    aria-label="More policy actions"
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

                            </div>

                        </div>

                    </article>
                @endforeach

                @for ($i = count($policies); $i < 8; $i++)
                    <div
                        class="platform-empty-card"
                        aria-hidden="true"
                    ></div>
                @endfor

            </div>
        </div>

    </section>


    <!-- =========================================================
         CREATE ANNOUNCEMENT MODAL
    ========================================================== -->
    <div
        id="create-announcement-modal"
        class="platform-modal-backdrop"
        aria-hidden="true"
    >
        <div
            class="platform-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="create-announcement-title"
        >
            <div class="platform-modal-inner">

                <div>
                    <h2
                        id="create-announcement-title"
                        class="platform-modal-heading"
                    >
                        Create Announcement
                    </h2>

                    <p class="platform-modal-subtitle">
                        Post an announcement to keep all users informed.
                    </p>
                </div>


                <div class="announcement-modal-grid">

                    <!-- LEFT COLUMN -->
                    <div class="announcement-modal-left">

                        <div class="platform-form-group">
                            <label class="platform-form-label">
                                Announcement Type<span class="platform-required">*</span>
                            </label>

                            <div class="platform-option-grid">

                                <label
                                    class="platform-option-card is-selected"
                                    data-announcement-type-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_type"
                                        value="Announcement"
                                        checked
                                    >

                                    <span class="platform-option-icon announcement-option-icon">
                                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                                        </svg>
                                    </span>

                                    <span class="platform-option-title">
                                        Announcement
                                    </span>

                                    <span class="platform-option-description">
                                        General updates, news, and important information.
                                    </span>
                                </label>


                                <label
                                    class="platform-option-card"
                                    data-announcement-type-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_type"
                                        value="Policy Update"
                                    >

                                    <span class="platform-option-icon policy-option-icon">
                                        <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                                            <path d="M18 8h24l8 8v40H18V8Zm22 3v8h8l-8-8ZM25 28h18v4H25v-4Zm0 9h18v4H25v-4Zm0 9h12v4H25v-4Z"/>
                                        </svg>
                                    </span>

                                    <span class="platform-option-title">
                                        Policy Update
                                    </span>

                                    <span class="platform-option-description">
                                        Updates to platform policies, rules, and guidelines.
                                    </span>
                                </label>

                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label
                                for="announcement-title-input"
                                class="platform-form-label"
                            >
                                Title<span class="platform-required">*</span>
                            </label>

                            <div class="platform-input-wrap">
                                <input
                                    id="announcement-title-input"
                                    type="text"
                                    maxlength="80"
                                    class="platform-input"
                                    placeholder="Enter announcement title..."
                                >

                                <span
                                    id="announcement-title-count"
                                    class="platform-counter"
                                >
                                    0/80
                                </span>
                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label
                                for="announcement-message-input"
                                class="platform-form-label"
                            >
                                Message / Description<span class="platform-required">*</span>
                            </label>

                            <div class="platform-textarea-wrap">
                                <textarea
                                    id="announcement-message-input"
                                    maxlength="500"
                                    class="platform-textarea"
                                    placeholder="Write your announcement here..."
                                ></textarea>

                                <span
                                    id="announcement-message-count"
                                    class="platform-counter"
                                >
                                    0/500
                                </span>
                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label class="platform-form-label">
                                Audience<span class="platform-required">*</span>
                            </label>

                            <div class="platform-audience-row">
                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="All Users"
                                        checked
                                    >
                                    <span>All Users</span>
                                </label>

                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="Buyers"
                                    >
                                    <span>Buyers</span>
                                </label>

                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="Sellers"
                                    >
                                    <span>Sellers</span>
                                </label>

                                <label class="platform-radio-inline">
                                    <input
                                        type="radio"
                                        name="announcement_audience"
                                        value="Logistics"
                                    >
                                    <span>Logistics</span>
                                </label>
                            </div>
                        </div>


                        <div class="platform-form-group" style="margin-bottom:0;">
                            <label class="platform-form-label">
                                Status<span class="platform-required">*</span>
                            </label>

                            <div class="platform-option-grid platform-status-grid">

                                <label
                                    class="platform-option-card is-selected"
                                    data-announcement-status-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_status"
                                        value="Publish Now"
                                        checked
                                    >

                                    <span class="platform-option-title">
                                        Publish Now
                                    </span>

                                    <span class="platform-option-description">
                                        Make this announcement visible immediately.
                                    </span>
                                </label>


                                <label
                                    class="platform-option-card"
                                    data-announcement-status-card
                                >
                                    <input
                                        type="radio"
                                        name="announcement_status"
                                        value="Schedule"
                                    >

                                    <span class="platform-option-title">
                                        Schedule
                                    </span>

                                    <span class="platform-option-description">
                                        Schedule this announcement for a future date.
                                    </span>
                                </label>

                            </div>


                            <div
                                id="announcement-schedule-fields"
                                class="announcement-schedule-fields"
                            >
                                <label class="platform-form-label">
                                    Publish Date &amp; Time<span class="platform-required">*</span>
                                </label>

                                <div class="platform-datetime-row">
                                    <input
                                        id="announcement-date-input"
                                        type="date"
                                        class="platform-input"
                                    >

                                    <input
                                        id="announcement-time-input"
                                        type="time"
                                        class="platform-input"
                                    >
                                </div>

                                <p class="platform-help-text">
                                    Choose when this announcement will be published.
                                </p>
                            </div>
                        </div>

                    </div>


                    <!-- RIGHT COLUMN -->
                    <div class="announcement-modal-right">

                        <div class="platform-form-group">
                            <label class="platform-form-label">
                                Announcement Banner
                                <span class="platform-muted-inline">(Optional)</span>
                            </label>

                            <p class="platform-help-text" style="margin:0 0 8px;">
                                Upload a banner image to make your announcement more eye-catching.
                            </p>

                            <input
                                id="announcement-banner-input"
                                type="file"
                                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                hidden
                            >

                            <div
                                id="announcement-banner-drop"
                                class="platform-banner-box"
                                role="button"
                                tabindex="0"
                            >
                                <span class="platform-banner-icon">
                                    <svg
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14H4V5Zm3 10h10l-3.2-4.1-2.5 3-1.8-2.1L7 15Zm2-7.5A1.5 1.5 0 1 0 9 4.5a1.5 1.5 0 0 0 0 3Z"/>
                                    </svg>
                                </span>

                                <p class="platform-banner-title">
                                    Click to upload or drag and drop
                                </p>

                                <p class="platform-banner-help">
                                    Recommended size: 1200 × 628 px (16:9)<br>
                                    JPG, PNG up to 5MB
                                </p>

                                <p
                                    id="announcement-banner-file"
                                    class="platform-banner-file"
                                ></p>
                            </div>
                        </div>


                        <div class="announcement-preview-section">
                            <p class="announcement-preview-label">
                                Preview
                            </p>

                            <div class="announcement-preview-card">

                                <div class="announcement-preview-main">

                                    <div class="announcement-preview-top">

                                        <div
                                            id="announcement-preview-icon"
                                            class="announcement-preview-icon"
                                        >
                                            <!-- Announcement = megaphone -->
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path d="M3 10v4a2 2 0 0 0 2 2h2l2 5h3l-2-5h1c3.9 0 7.2 1.1 10 3V5c-2.8 1.9-6.1 3-10 3H5a2 2 0 0 0-2 2Zm15-1.5v7c-2.2-1-4.5-1.6-7-1.8v-3.4c2.5-.2 4.8-.8 7-1.8Z"/>
                                            </svg>
                                        </div>

                                        <h3
                                            id="announcement-preview-title"
                                            class="announcement-preview-title"
                                        ></h3>

                                    </div>

                                    <p
                                        id="announcement-preview-description"
                                        class="announcement-preview-description"
                                    ></p>

                                </div>


                                <div class="announcement-preview-footer">

                                    <div class="announcement-preview-audience">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                            <path d="M3 12h18M12 3c2.3 2.6 3.5 5.6 3.5 9S14.3 18.4 12 21c-2.3-2.6-3.5-5.6-3.5-9S9.7 5.6 12 3Z" stroke-width="1.6"/>
                                        </svg>

                                        <span id="announcement-preview-audience"></span>
                                    </div>

                                    <div class="announcement-preview-meta">

                                        <span
                                            id="announcement-preview-status"
                                            class="platform-status"
                                        ></span>

                                        <div
                                            id="announcement-preview-date"
                                            class="announcement-preview-date"
                                        ></div>

                                        <button
                                            type="button"
                                            class="announcement-preview-more"
                                            aria-label="Preview more actions"
                                            tabindex="-1"
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

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>


                <div class="platform-modal-actions">
                    <button
                        id="cancel-announcement-modal"
                        type="button"
                        class="platform-modal-button platform-modal-cancel"
                    >
                        Cancel
                    </button>

                    <button
                        id="submit-announcement-modal"
                        type="button"
                        class="platform-modal-button platform-modal-primary"
                    >
                        Upload
                    </button>
                </div>

            </div>
        </div>
    </div>


    <!-- =========================================================
         ADD NEW POLICY MODAL
    ========================================================== -->
    <div
        id="add-policy-modal"
        class="platform-modal-backdrop"
        aria-hidden="true"
    >
        <div
            class="platform-modal platform-policy-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="add-policy-title"
        >
            <div class="platform-modal-inner">

                <div>
                    <h2
                        id="add-policy-title"
                        class="platform-modal-heading"
                    >
                        Add New Policy
                    </h2>

                    <p class="platform-modal-subtitle">
                        Create a new policy to set guidelines and rules for the platform.
                    </p>
                </div>


                <div class="policy-modal-form">

                    <div class="policy-modal-two-col">

                        <div class="platform-form-group">
                            <label
                                for="policy-title-input"
                                class="platform-form-label"
                            >
                                Policy Title<span class="platform-required">*</span>
                            </label>

                            <div class="platform-input-wrap">
                                <input
                                    id="policy-title-input"
                                    type="text"
                                    maxlength="80"
                                    class="platform-input"
                                    placeholder="Enter policy title..."
                                >

                                <span
                                    id="policy-title-count"
                                    class="platform-counter"
                                >
                                    0/80
                                </span>
                            </div>
                        </div>


                        <div class="platform-form-group">
                            <label
                                for="policy-category-input"
                                class="platform-form-label"
                            >
                                Policy Category<span class="platform-required">*</span>
                            </label>

                            <select
                                id="policy-category-input"
                                class="platform-select"
                            >
                                <option value="">
                                    Select Category
                                </option>
                                <option>Prohibited &amp; Restricted Items</option>
                                <option>User Conduct</option>
                                <option>Shipping &amp; Delivery</option>
                                <option>Privacy &amp; Data</option>
                                <option>Buyer Policy</option>
                                <option>Seller Policy</option>
                                <option>Logistics Policy</option>
                            </select>
                        </div>

                    </div>


                    <div class="platform-form-group">
                        <label
                            for="policy-description-input"
                            class="platform-form-label"
                        >
                            Message / Description<span class="platform-required">*</span>
                        </label>

                        <div class="platform-textarea-wrap">
                            <textarea
                                id="policy-description-input"
                                maxlength="500"
                                class="platform-textarea policy-description"
                                placeholder="Enter a short description of the policy..."
                            ></textarea>

                            <span
                                id="policy-description-count"
                                class="platform-counter"
                            >
                                0/500
                            </span>
                        </div>
                    </div>


                    <div class="platform-form-group">
                        <label
                            for="policy-content-input"
                            class="platform-form-label"
                        >
                            Policy Content<span class="platform-required">*</span>
                        </label>

                        <div class="platform-textarea-wrap">
                            <textarea
                                id="policy-content-input"
                                maxlength="500"
                                class="platform-textarea policy-content"
                                placeholder="Write the full policy details here..."
                            ></textarea>

                            <span
                                id="policy-content-count"
                                class="platform-counter"
                            >
                                0/500
                            </span>
                        </div>
                    </div>


                    <div class="platform-form-group">
                        <span class="platform-form-label">
                            Publish
                        </span>

                        <div class="platform-option-grid">
                            <label class="platform-option-card is-selected">
                                <input
                                    type="radio"
                                    name="policy_publish_status"
                                    value="Publish Now"
                                    checked
                                >
                                <span class="platform-option-title">Publish Now</span>
                                <span class="platform-option-description">Make this policy available as soon as it is added.</span>
                            </label>

                            <label class="platform-option-card">
                                <input
                                    type="radio"
                                    name="policy_publish_status"
                                    value="Schedule"
                                >
                                <span class="platform-option-title">Schedule</span>
                                <span class="platform-option-description">Choose a future date and time to publish this policy.</span>
                            </label>
                        </div>
                    </div>

                    <div
                        id="policy-schedule-fields"
                        class="platform-form-group policy-modal-datetime"
                        hidden
                    >
                        <label class="platform-form-label">
                            Publish Date &amp; Time<span class="platform-required">*</span>
                        </label>

                        <div class="platform-datetime-row">
                            <input id="policy-date-input" type="date" class="platform-input">
                            <input id="policy-time-input" type="time" class="platform-input">
                        </div>

                        <p class="platform-help-text" style="margin-top:28px;">
                            Choose when this policy will be published.
                        </p>
                    </div>

                </div>


                <div class="platform-modal-actions">
                    <button
                        id="cancel-policy-modal"
                        type="button"
                        class="platform-modal-button platform-modal-cancel"
                    >
                        Cancel
                    </button>

                    <button
                        id="submit-policy-modal"
                        type="button"
                        class="platform-modal-button platform-modal-primary"
                    >
                        Add
                    </button>
                </div>

            </div>
        </div>
    </div>


</div>

@push('scripts')

@vite('resources/js/admin/platform-settings.js')

@endpush

@php
    $platformSettingsClientConfig = [
        'announcements' => $announcements,
        'policies' => $policies,
        'apiUrl' => url('/admin/platform-settings/api'),
        'csrfToken' => csrf_token(),
    ];
@endphp

<script
    type="application/json"
    id="platformSettingsConfig"
>{!! json_encode($platformSettingsClientConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

@endsection
