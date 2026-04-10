document.addEventListener('DOMContentLoaded', function () {

    //LOGICA DE AGREGAR  O QUITAR LOS TAGS DE LOS FILTROS Y FUNCIONALIDAD PESTAÑAS
    const tabs = document.querySelectorAll('.insp-filter-tab');
    const panels = document.querySelectorAll('.insp-filter-panel');
    const chipsContainer = document.getElementById('insp-active-filters');

    const topImages = document.querySelectorAll('.insp-card__image[role="button"]');
    const textFilters = document.querySelectorAll('.insp-filter-values--text > div[role="button"]');
    const colorFilters = document.querySelectorAll('.insp-color[role="button"]');

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

            const value = card.dataset.value || '';
            const group = card.dataset.group || '';
            const labelEl = card.querySelector('.insp-card__label');
            const label = labelEl ? labelEl.textContent.trim() : value;

            activeItems.push({
                type: 'top-card',
                group: group,
                value: value,
                label: label
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
            chip.dataset.group = item.group;
            chip.dataset.value = item.value;
            chip.dataset.type = item.type;
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
        document.querySelectorAll('.insp-card[data-group="' + group + '"] .insp-card__image')
            .forEach(function (img) {
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

    // Tabs inferiores: abrir/cerrar
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
                renderChips();
                return;
            }

            clearTopGroup(opposite);
            this.classList.add('is-active');
            renderChips();
        });
    });

    // Filtros inferiores texto
    textFilters.forEach(function (item) {
        item.addEventListener('click', function () {
            this.classList.toggle('is-active');
            renderChips();
        });
    });

    // Filtros inferiores color
    colorFilters.forEach(function (item) {
        item.addEventListener('click', function () {
            this.classList.toggle('is-active');
            renderChips();
        });
    });

    // Chips: eliminar uno o borrar todo
    if (chipsContainer) {
        chipsContainer.addEventListener('click', function (e) {
            const chip = e.target.closest('.insp-chip');
            if (!chip) {
                return;
            }

            if (chip.dataset.action === 'clear-all') {
                clearAllFilters();
                renderChips();
                return;
            }

            const type = chip.dataset.type;
            const group = chip.dataset.group;
            const value = chip.dataset.value;

            if (type === 'top-card') {
                const target = document.querySelector(
                    '.insp-card[data-group="' + group + '"][data-value="' + value + '"] .insp-card__image'
                );
                if (target) {
                    target.classList.remove('is-active');
                }
            }

            if (type === 'filter-text') {
                const target = document.querySelector(
                    '.insp-filter-values--text > div[data-group="' + group + '"][data-value="' + value + '"]'
                );
                if (target) {
                    target.classList.remove('is-active');
                }
            }

            if (type === 'filter-color') {
                const target = document.querySelector(
                    '.insp-color[data-group="' + group + '"][data-value="' + value + '"]'
                );
                if (target) {
                    target.classList.remove('is-active');
                }
            }

            renderChips();
        });
    }

    renderChips();
});
