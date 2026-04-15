document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.inspirations-page');
    if (!page) {
        return;
    }

    const ajaxUrl = page.dataset.ajaxUrl || '';
    const tabs = document.querySelectorAll('.insp-filter-tab');
    const panels = document.querySelectorAll('.insp-filter-panel');
    const chipsContainer = document.getElementById('insp-active-filters');
    const topImages = document.querySelectorAll('.insp-card__image[role="button"]');
    const textFilters = document.querySelectorAll('.insp-filter-values--text > div[role="button"]');
    const colorFilters = document.querySelectorAll('.insp-color[role="button"]');
    const grid = document.getElementById('insp-grid');

    function getActiveFilters() {
        const filters = {
            space: [],
            usage: [],
            aspecto: [],
            color: [],
            tamano: [],
            estilo: []
        };

        document.querySelectorAll('.insp-card .insp-card__image.is-active').forEach(function (img) {
            const card = img.closest('.insp-card');
            if (!card) {
                return;
            }

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
        if (!chipsContainer) {
            return;
        }

        chipsContainer.innerHTML = '';

        const activeItems = [];

        document.querySelectorAll('.insp-card .insp-card__image.is-active').forEach(function (img) {
            const card = img.closest('.insp-card');
            if (!card) {
                return;
            }

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

        activeItems.forEach(function (item) {
            const chip = document.createElement('div');
            chip.className = 'insp-chip';
            chip.setAttribute('role', 'button');
            chip.dataset.type = item.type;
            chip.dataset.group = item.group;
            chip.dataset.value = item.value;
            chip.textContent = item.label + ' ×';
            chipsContainer.appendChild(chip);
        });

        if (activeItems.length > 0) {
            const clearAll = document.createElement('div');
            clearAll.className = 'insp-chip insp-chip--clear';
            clearAll.setAttribute('role', 'button');
            clearAll.dataset.action = 'clear-all';
            clearAll.textContent = 'BORRAR TODO';
            chipsContainer.appendChild(clearAll);
        }
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

    function sendFiltersAjax() {
        if (!ajaxUrl || !grid) {
            return;
        }

        const filters = getActiveFilters();

        const formData = new FormData();
        formData.append('ajax', '1');
        formData.append('action', 'FilterInspirations');
        formData.append('space', JSON.stringify(filters.space));
        formData.append('usage', JSON.stringify(filters.usage));
        formData.append('aspecto', JSON.stringify(filters.aspecto));
        formData.append('color', JSON.stringify(filters.color));
        formData.append('tamano', JSON.stringify(filters.tamano));
        formData.append('estilo', JSON.stringify(filters.estilo));

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data && typeof data.html !== 'undefined') {
                    grid.innerHTML = data.html;
                }
            })
            .catch(function (error) {
                console.error('Error filtering inspirations:', error);
            });
    }

    function updateFilters() {
        renderChips();
        sendFiltersAjax();
    }

    // Tabs
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

    // Espacio / uso
    topImages.forEach(function (image) {
        image.addEventListener('click', function () {
            const card = this.closest('.insp-card');
            if (!card) {
                return;
            }

            const group = card.dataset.group;
            const opposite = group === 'space' ? 'usage' : 'space';
            const isActive = this.classList.contains('is-active');

            if (isActive) {
                this.classList.remove('is-active');
                updateFilters();
                return;
            }

            clearTopGroup(opposite);
            this.classList.add('is-active');
            updateFilters();
        });
    });

    // Filtros texto
    textFilters.forEach(function (item) {
        item.addEventListener('click', function () {
            this.classList.toggle('is-active');
            updateFilters();
        });
    });

    // Filtros color
    colorFilters.forEach(function (item) {
        item.addEventListener('click', function () {
            this.classList.toggle('is-active');
            updateFilters();
        });
    });

    // Chips
    if (chipsContainer) {
        chipsContainer.addEventListener('click', function (e) {
            const chip = e.target.closest('.insp-chip');
            if (!chip) {
                return;
            }

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

    renderChips();
});