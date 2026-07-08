const tourSteps = {
    global: [
        {
            target: 'credits',
            title: 'Watch your credits',
            body: 'This balance shows how many SMS credits you can use before sending, scheduling, or sharing credits.',
        },
        {
            target: 'nav-compose',
            title: 'Send an SMS',
            body: 'Compose SMS is where you type recipients, write the message, personalize it, and choose whether to send now or schedule it.',
        },
        {
            target: 'nav-sent',
            title: 'Review sent messages',
            body: 'Sent keeps your SMS batches, statuses, recipient counts, and searchable message history.',
        },
        {
            target: 'nav-phonebook',
            title: 'Build recipient groups',
            body: 'Phonebook lets you create groups and import numbers, then reuse those groups when composing messages.',
        },
        {
            target: 'nav-buy',
            title: 'Add credits',
            body: 'Buy is where users top up SMS credits using a mobile money number.',
        },
        {
            target: 'nav-me2u',
            title: 'Share credits',
            body: 'Me 2 U transfers SMS credits to another ShamaSMS user by username.',
        },
        {
            target: 'nav-settings',
            title: 'Manage your account',
            body: 'Settings covers profile details, password changes, and API keys for integrations.',
        },
    ],
    compose: [
        {
            target: 'sender-id',
            title: 'Sender ID',
            body: 'Enter the name or short label recipients should see as the SMS sender.',
        },
        {
            target: 'recipient-mode',
            title: 'Choose recipients',
            body: 'Type numbers directly, upload a file, or pick a phonebook group.',
        },
        {
            target: 'personalize-toggle',
            title: 'Personalized messages',
            body: 'Turn this on when you want placeholders like @@name@@ and @@var1@@ to be replaced for each recipient.',
        },
        {
            target: 'message-body',
            title: 'Write the SMS',
            body: 'Type the message here. The counter shows characters and how many SMS segments it will cost.',
        },
        {
            target: 'delivery-mode',
            title: 'Send now or later',
            body: 'Send immediately, or schedule the message by time, days, or weeks.',
        },
        {
            target: 'send-button',
            title: 'Send or schedule',
            body: 'When everything is ready, this button sends immediately or saves the schedule.',
        },
    ],
    sent: [
        {
            target: 'sent-search',
            title: 'Search sent SMS',
            body: 'Search by sender, message text, phone number, status, or provider reference.',
        },
        {
            target: 'sent-table',
            title: 'Read message history',
            body: 'Each row shows a sent or scheduled batch with recipient count, status, and creation time.',
        },
    ],
    phonebook: [
        {
            target: 'phonebook-create',
            title: 'Create a group',
            body: 'Name a group for a class, customers, members, or any list you send to often.',
        },
        {
            target: 'phonebook-list',
            title: 'Select a group',
            body: 'Pick a group here to view or import its numbers.',
        },
        {
            target: 'phonebook-template',
            title: 'Use the import template',
            body: 'Download or copy this one-column format so numbers are arranged correctly before upload.',
        },
        {
            target: 'phonebook-import',
            title: 'Import numbers',
            body: 'Paste numbers or upload a file, then import them into the selected group.',
        },
    ],
    buy: [
        {
            target: 'buy-rate',
            title: 'Check your rate',
            body: 'This shows how much one SMS credit costs for your account.',
        },
        {
            target: 'buy-form',
            title: 'Top up credits',
            body: 'Enter the amount and mobile money number that should be charged.',
        },
        {
            target: 'buy-history',
            title: 'Track purchases',
            body: 'Recent purchases and payment statuses appear here.',
        },
    ],
    me2u: [
        {
            target: 'me2u-form',
            title: 'Share credits',
            body: 'Enter another user’s username and the number of credits to transfer.',
        },
        {
            target: 'me2u-history',
            title: 'Transfer history',
            body: 'This table records credits you sent or received.',
        },
    ],
    settings: [
        {
            target: 'settings-profile',
            title: 'Profile settings',
            body: 'Update account details and confirm the visible SMS balance.',
        },
        {
            target: 'settings-password',
            title: 'Password settings',
            body: 'Change the account password from this panel.',
        },
        {
            target: 'settings-api-form',
            title: 'Create API keys',
            body: 'Make sandbox keys for testing and live keys for real SMS delivery.',
        },
    ],
    developers: [
        {
            target: 'developers-intro',
            title: 'API guide',
            body: 'This page shows developers how to send SMS, check balances, and handle errors.',
        },
        {
            target: 'developers-key-link',
            title: 'Create an API key',
            body: 'Use this shortcut to create a sandbox or live key in Settings.',
        },
    ],
};

function byTourName(name) {
    return document.querySelector(`[data-tour="${name}"]`);
}

function currentRouteSteps() {
    const routeName = document.body.dataset.routeName || '';
    const route = routeName.split('.')[0];
    const pageSteps = tourSteps[route] || [];

    return [...tourSteps.global, ...pageSteps].filter((step) => byTourName(step.target));
}

function initTour() {
    const layer = document.querySelector('[data-tour-layer]');
    const startButtons = document.querySelectorAll('[data-tour-start]');

    if (!layer || startButtons.length === 0) {
        return;
    }

    const highlight = layer.querySelector('[data-tour-highlight]');
    const card = layer.querySelector('[data-tour-card]');
    const count = layer.querySelector('[data-tour-count]');
    const title = layer.querySelector('[data-tour-title]');
    const body = layer.querySelector('[data-tour-body]');
    const prevButton = layer.querySelector('[data-tour-prev]');
    const nextButton = layer.querySelector('[data-tour-next]');
    const skipButton = layer.querySelector('[data-tour-skip]');
    const closeButtons = layer.querySelectorAll('[data-tour-close]');
    let steps = [];
    let index = 0;

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function positionStep() {
        const step = steps[index];
        const target = byTourName(step.target);

        if (!target) {
            return;
        }

        target.scrollIntoView({ block: 'center', inline: 'center', behavior: 'smooth' });

        window.setTimeout(() => {
            const rect = target.getBoundingClientRect();
            const gap = 14;
            const padding = 8;
            const cardWidth = Math.min(360, window.innerWidth - 32);
            const cardHeight = card.offsetHeight || 220;
            const left = clamp(rect.left + rect.width / 2 - cardWidth / 2, 16, window.innerWidth - cardWidth - 16);
            const below = rect.bottom + gap;
            const above = rect.top - cardHeight - gap;
            const top = below + cardHeight < window.innerHeight ? below : Math.max(16, above);

            highlight.style.width = `${rect.width + padding * 2}px`;
            highlight.style.height = `${rect.height + padding * 2}px`;
            highlight.style.left = `${rect.left - padding}px`;
            highlight.style.top = `${rect.top - padding}px`;
            card.style.width = `${cardWidth}px`;
            card.style.left = `${left}px`;
            card.style.top = `${top}px`;
        }, 220);
    }

    function renderStep() {
        const step = steps[index];

        count.textContent = `Step ${index + 1} of ${steps.length}`;
        title.textContent = step.title;
        body.textContent = step.body;
        prevButton.disabled = index === 0;
        nextButton.textContent = index === steps.length - 1 ? 'Finish' : 'Next';
        layer.hidden = false;
        document.body.classList.add('tour-open');
        positionStep();
    }

    function closeTour() {
        layer.hidden = true;
        document.body.classList.remove('tour-open');
    }

    function startTour() {
        steps = currentRouteSteps();

        if (steps.length === 0) {
            return;
        }

        index = 0;
        renderStep();
    }

    startButtons.forEach((button) => button.addEventListener('click', startTour));
    prevButton.addEventListener('click', () => {
        if (index > 0) {
            index -= 1;
            renderStep();
        }
    });
    nextButton.addEventListener('click', () => {
        if (index >= steps.length - 1) {
            closeTour();
            return;
        }

        index += 1;
        renderStep();
    });
    skipButton.addEventListener('click', closeTour);
    closeButtons.forEach((button) => button.addEventListener('click', closeTour));
    window.addEventListener('resize', () => {
        if (!layer.hidden) {
            positionStep();
        }
    });
    window.addEventListener('keydown', (event) => {
        if (!layer.hidden && event.key === 'Escape') {
            closeTour();
        }
    });
}

document.addEventListener('DOMContentLoaded', initTour);

function parseLivewireCall(expression) {
    const match = expression.match(/^([A-Za-z_$][\w$]*)\((.*)\)$/);

    if (!match) {
        return { method: expression, args: [] };
    }

    const [, method, rawArgs] = match;

    if (rawArgs.trim() === '') {
        return { method, args: [] };
    }

    return {
        method,
        args: rawArgs.split(',').map((arg) => {
            const value = arg.trim();

            if (/^\d+$/.test(value)) {
                return Number(value);
            }

            return value.replace(/^['"]|['"]$/g, '');
        }),
    };
}

function initSwalConfirms() {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-swal-confirm]');

        if (!button) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const componentRoot = button.closest('[wire\\:id]');
        const wireClick = button.getAttribute('wire:click');

        if (!componentRoot || !wireClick || !window.Livewire) {
            return;
        }

        const runAction = () => {
            const { method, args } = parseLivewireCall(wireClick);
            window.Livewire.find(componentRoot.getAttribute('wire:id')).call(method, ...args);
        };

        const message = button.dataset.swalConfirm;
        const title = button.dataset.swalTitle || 'Are you sure?';
        const icon = button.dataset.swalIcon || 'question';
        const confirmButtonText = button.dataset.swalConfirmText || 'Yes, continue';
        const confirmButtonColor = button.dataset.swalConfirmColor || '#0ea5e9';

        if (!window.Swal) {
            if (window.confirm(message)) {
                runAction();
            }

            return;
        }

        window.Swal.fire({
            title,
            text: message,
            icon,
            showCancelButton: true,
            confirmButtonText,
            cancelButtonText: 'Cancel',
            confirmButtonColor,
            cancelButtonColor: '#64748b',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                runAction();
            }
        });
    }, true);
}

document.addEventListener('DOMContentLoaded', initSwalConfirms);
