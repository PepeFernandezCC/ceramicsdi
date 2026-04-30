document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.inspirations-page');
    if (!page) return;

    const ajaxUrl = page.dataset.ajaxUrl || '';
    const tabs = document.querySelectorAll('.insp-filter-tab');
    const panels = document.querySelectorAll('.insp-filter-panel');
    const chipsContainer = document.getElementById('insp-active-filters');
    const topImages = document.querySelectorAll('.insp-card__image[role="button"]');
    const textFilters = document.querySelectorAll('.insp-filter-values--text > div[role="button"]');
    const colorFilters = document.querySelectorAll('.insp-color[role="button"]');
    const grid = document.getElementById('insp-grid');
    const button = document.getElementById('viewMoreButton');

    if (!grid) return;

    let activeRequest = null;
    let lastFiltersKey = '';
    let updateTimer = null;

    const pageSize = button ? (parseInt(button.getAttribute('data-limit'), 10) || 12) : 12;

    function getActiveFilters() {
        const filters = {
            space: [],
            usage: [],
            aspecto: [],
            color: [],
            tamano: [],
            estilo: [],
            producto: []
        };

        document.querySelectorAll('.insp-card .insp-card__image.is-active').forEach(function (img) {
            const card = img.closest('.insp-card');
            if (!card) return;

            const group = card.dataset.group || '';
            const value = card.dataset.value || '';

            if (group && value && Array.isArray(filters[group])) {
                filters[group].push(value);
            }
        });

        document.querySelectorAll('.insp-filter-values--text > div.is-active').forEach(function (item) {
            const group = item.dataset.group || '';
            const value = item.dataset.value || '';

            if (group && value && Array.isArray(filters[group])) {
                filters[group].push(value);
            }
        });

        document.querySelectorAll('.insp-color.is-active').forEach(function (item) {
            const group = item.dataset.group || '';
            const value = item.dataset.value || '';

            if (group && value && Array.isArray(filters[group])) {
                filters[group].push(value);
            }
        });

        return filters;
    }

    function renderChips() {
        if (!chipsContainer) return;

        chipsContainer.innerHTML = '';
        const activeItems = [];

        document.querySelectorAll('.insp-card .insp-card__image.is-active').forEach(function (img) {
            const card = img.closest('.insp-card');
            if (!card) return;

            const labelEl = card.querySelector('.insp-card__label');

            activeItems.push({
                type: 'top-card',
                group: card.dataset.group || '',
                value: card.dataset.value || '',
                label: labelEl ? labelEl.textContent.trim() : ''
            });
        });

        document.querySelectorAll('.insp-filter-values--text > div.is-active').forEach(function (item) {
            activeItems.push({
                type: 'filter-text',
                group: item.dataset.group || '',
                value: item.dataset.value || '',
                label: item.dataset.label || item.textContent.trim()
            });
        });

        document.querySelectorAll('.insp-color.is-active').forEach(function (item) {
            activeItems.push({
                type: 'filter-color',
                group: item.dataset.group || '',
                value: item.dataset.value || '',
                label: item.dataset.label || item.dataset.value || ''
            });
        });

        const frag = document.createDocumentFragment();

        activeItems.forEach(function (item) {
            const chip = document.createElement('div');
            chip.className = 'insp-chip';
            chip.setAttribute('role', 'button');
            chip.dataset.type = item.type;
            chip.dataset.group = item.group;
            chip.dataset.value = item.value;
            chip.textContent = item.label + ' ×';
            frag.appendChild(chip);
        });

        if (activeItems.length > 0) {
            const clearAll = document.createElement('div');
            clearAll.className = 'insp-chip insp-chip--clear';
            clearAll.setAttribute('role', 'button');
            clearAll.dataset.action = 'clear-all';
            clearAll.textContent = 'BORRAR TODO';
            frag.appendChild(clearAll);
        }

        chipsContainer.appendChild(frag);
    }

    function clearTopGroup(group) {
        document.querySelectorAll('.insp-card[data-group="' + group + '"] .insp-card__image').forEach(function (img) {
            img.classList.remove('is-active');
        });
    }

    function clearAllFilters() {
        document.querySelectorAll('.insp-card__image.is-active').forEach(function (img) {
            img.classList.remove('is-active');
        });

        document.querySelectorAll('.insp-filter-values--text > div.is-active').forEach(function (item) {
            item.classList.remove('is-active');
        });

        document.querySelectorAll('.insp-color.is-active').forEach(function (item) {
            item.classList.remove('is-active');
        });
    }

    function showButton() {
        if (button) {
            button.style.display = '';
        }
    }

    function hideButton() {
        if (button) {
            button.style.display = 'none';
        }
    }

    function setButtonOffset(value) {
        if (button) {
            button.setAttribute('data-offset', value);
        }
    }

    function getButtonOffset() {
        if (!button) {
            return 0;
        }

        return parseInt(button.getAttribute('data-offset'), 10) || 0;
    }

    function buildFilterRequestData(offset, limit) {
        const filters = getActiveFilters();
        const formData = new FormData();

        formData.append('ajax', '1');
        formData.append('action', 'FilterInspirations');
        formData.append('space', JSON.stringify(filters.space));
        formData.append('usage', JSON.stringify(filters.usage));
        formData.append('producto', JSON.stringify(filters.producto));
        
        formData.append('aspecto', JSON.stringify(filters.aspecto));
        formData.append('color', JSON.stringify(filters.color));
        formData.append('tamano', JSON.stringify(filters.tamano));
        formData.append('estilo', JSON.stringify(filters.estilo));
        
        formData.append('offset', offset);
        formData.append('limit', limit);

        return formData;
    }

    function requestInspirations(options) {
        if (!ajaxUrl || !grid) return;

        const offset = typeof options.offset !== 'undefined' ? options.offset : 0;
        const limit = typeof options.limit !== 'undefined' ? options.limit : pageSize;
        const append = !!options.append;

        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest = new AbortController();

        const formData = buildFilterRequestData(offset, limit);

        grid.classList.add('is-loading');
        if (button) {
            button.classList.add('is-loading');
        }

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            signal: activeRequest.signal
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data || typeof data.html === 'undefined') {
                    return;
                }

                if (append) {
                    grid.insertAdjacentHTML('beforeend', data.html);
                } else {
                    grid.innerHTML = data.html;
                }

                setButtonOffset(data.loaded || (offset + (data.count || 0)));

                if (data.has_more) {
                    showButton();
                } else {
                    hideButton();
                }
            })
            .catch(function (error) {
                if (error.name !== 'AbortError') {
                    console.error('Error filtering inspirations:', error);
                }
            })
            .finally(function () {
                grid.classList.remove('is-loading');
                if (button) {
                    button.classList.remove('is-loading');
                }
                activeRequest = null;
            });
    }

    function sendFiltersAjax() {
        const filters = getActiveFilters();
        const filtersKey = JSON.stringify(filters);

        if (filtersKey === lastFiltersKey) {
            return;
        }

        lastFiltersKey = filtersKey;

        requestInspirations({
            offset: 0,
            limit: pageSize,
            append: false
        });
    }

    function updateFilters() {
        renderChips();

        clearTimeout(updateTimer);
        updateTimer = setTimeout(function () {
            sendFiltersAjax();
        }, 150);
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const tabName = this.getAttribute('data-tab');
            const isAlreadyActive = this.classList.contains('is-active');

            tabs.forEach(function (item) {
                item.classList.remove('is-active');
            });

            panels.forEach(function (panel) {
                panel.classList.remove('is-active');
            });

            if (!isAlreadyActive) {
                this.classList.add('is-active');
                const targetPanel = document.querySelector('.insp-filter-panel[data-panel="' + tabName + '"]');
                if (targetPanel) {
                    targetPanel.classList.add('is-active');
                }
            }
        });
    });

    const allowedGroups = ['space', 'usage', 'product'];

    topImages.forEach(function (image) {
        image.addEventListener('click', function () {
            const card = this.closest('.insp-card');
            if (!card) return;

            const group = card.dataset.group;

            // Ignora cualquier grupo que no sea space, usage o product
            if (!allowedGroups.includes(group)) return;

            const isActive = this.classList.contains('is-active');

            if (isActive) {
                this.classList.remove('is-active');
                updateFilters();
                return;
            }

            clearOtherTopGroups(group);
            this.classList.add('is-active');
            updateFilters();
        });
    });

    function clearOtherTopGroups(activeGroup) {
        topImages.forEach(function (image) {
            const card = image.closest('.insp-card');
            if (!card) return;

            const group = card.dataset.group;

            // Solo afecta a space, usage y product
            if (
                allowedGroups.includes(group) &&
                group !== activeGroup
            ) {
                image.classList.remove('is-active');
            }
        });
    }

    textFilters.forEach(function (item) {
        item.addEventListener('click', function () {
            this.classList.toggle('is-active');
            updateFilters();
        });
    });

    colorFilters.forEach(function (item) {
        item.addEventListener('click', function () {
            this.classList.toggle('is-active');
            updateFilters();
        });
    });

    if (chipsContainer) {
        chipsContainer.addEventListener('click', function (e) {
            const chip = e.target.closest('.insp-chip');
            if (!chip) return;

            if (chip.dataset.action === 'clear-all') {
                clearAllFilters();
                updateFilters();
                return;
            }

            const type = chip.dataset.type;
            const group = chip.dataset.group;
            const value = chip.dataset.value;

            if (type === 'top-card') {
                const target = document.querySelector('.insp-card[data-group="' + group + '"][data-value="' + value + '"] .insp-card__image');
                if (target) {
                    target.classList.remove('is-active');
                }
            }

            if (type === 'filter-text') {
                const target = document.querySelector('.insp-filter-values--text > div[data-group="' + group + '"][data-value="' + value + '"]');
                if (target) {
                    target.classList.remove('is-active');
                }
            }

            if (type === 'filter-color') {
                const target = document.querySelector('.insp-color[data-group="' + group + '"][data-value="' + value + '"]');
                if (target) {
                    target.classList.remove('is-active');
                }
            }

            updateFilters();
        });
    }

    if (button) {
        button.addEventListener('click', function () {
            if (button.classList.contains('is-loading')) {
                return;
            }

            requestInspirations({
                offset: getButtonOffset(),
                limit: pageSize,
                append: true
            });
        });
    }

    renderChips();
});