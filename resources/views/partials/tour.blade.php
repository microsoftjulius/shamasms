<div class="tour-layer" data-tour-layer hidden>
    <div class="tour-backdrop" data-tour-close></div>
    <div class="tour-highlight" data-tour-highlight></div>
    <section class="tour-card" data-tour-card role="dialog" aria-modal="true" aria-labelledby="tour-title">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="tour-count" data-tour-count></p>
                <h2 id="tour-title" class="tour-title" data-tour-title></h2>
            </div>
            <button type="button" class="tour-close" data-tour-close aria-label="Close tour">&times;</button>
        </div>
        <p class="tour-body" data-tour-body></p>
        <div class="tour-actions">
            <button type="button" class="tour-button secondary" data-tour-prev>Back</button>
            <button type="button" class="tour-button secondary" data-tour-skip>Skip</button>
            <button type="button" class="tour-button primary" data-tour-next>Next</button>
        </div>
    </section>
</div>
