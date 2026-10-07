<div class="dashboard-announcement-carousel" data-dashboard-announcement-carousel>
    <div class="dashboard-announcement-slides">
        @forelse ($dashboardAnnouncements as $index => $item)
            <article
                class="dashboard-announcement-slide{{ $index === 0 ? ' is-active' : '' }}"
                data-dashboard-announcement-slide
                aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
            >
                <div class="dashboard-announcement-meta{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? ' flex items-center justify-between mb-3' : '' }}">
                <p class="{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? 'text-[13px] font-medium text-peach-dark uppercase tracking-wider' : 'announcement-label' }}">
                    {{ $item->badge_label ?: ($item->type ?: 'Announcement') }}
                </p>
                <span class="{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? 'text-[13px] bg-white/10 px-3 py-1 rounded-full' : 'dashboard-announcement-date' }}">
                    {{ ($item->published_at ?? $item->created_at)?->format('F Y') }}
                </span>
                </div>
                <h3 class="{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? 'text-[19px] font-bold mb-2' : 'announcement-title' }}">
                    {{ $item->title }}
                </h3>
                <p class="{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? 'text-[14px] text-white/75 leading-relaxed max-w-[230px]' : 'announcement-text' }}">
                    {{ $item->body ?: 'There are no details for this announcement.' }}
                </p>
            </article>
        @empty
            <article
                class="dashboard-announcement-slide is-active"
                data-dashboard-announcement-slide
                aria-hidden="false"
            >
                <p class="{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? 'text-[13px] font-medium text-peach-dark uppercase tracking-wider' : 'announcement-label' }}">Announcement</p>
                <h3 class="{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? 'text-[19px] font-bold mb-2' : 'announcement-title' }}">No announcements yet</h3>
                <p class="{{ ($dashboardAnnouncementVariant ?? '') === 'admin' ? 'text-[14px] text-white/75 leading-relaxed max-w-[230px]' : 'announcement-text' }}">There are no active announcements at this time.</p>
            </article>
        @endforelse
    </div>

    @if ($dashboardAnnouncements->count() > 1)
        <div class="dashboard-announcement-controls" aria-label="Announcement carousel controls">
            <button type="button" data-dashboard-announcement-previous aria-label="Previous announcement">‹</button>
            <div class="dashboard-announcement-dots" role="tablist" aria-label="Choose announcement"></div>
            <button type="button" data-dashboard-announcement-next aria-label="Next announcement">›</button>
        </div>
    @endif
</div>
