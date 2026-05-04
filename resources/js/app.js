import './bootstrap';

document.querySelectorAll('[data-city-combobox]').forEach((combobox) => {
    const trigger = combobox.querySelector('[data-city-trigger]');
    const label = combobox.querySelector('[data-city-label]');
    const hiddenInput = combobox.querySelector('[data-city-id]');
    const panel = combobox.querySelector('[data-city-panel]');
    const searchInput = combobox.querySelector('[data-city-search]');
    const emptyMessage = combobox.querySelector('[data-city-empty]');
    const options = Array.from(combobox.querySelectorAll('[data-city-option]'));
    const placeholder = label?.textContent ?? 'Select city';

    if (! trigger || ! label || ! hiddenInput || ! panel || ! searchInput || ! emptyMessage) {
        return;
    }

    const setOpen = (isOpen) => {
        panel.classList.toggle('hidden', ! isOpen);
        trigger.setAttribute('aria-expanded', String(isOpen));

        if (isOpen) {
            searchInput.value = '';
            filterOptions('');
            window.setTimeout(() => searchInput.focus(), 0);
        }
    };

    const filterOptions = (query) => {
        const normalizedQuery = query.trim().toLowerCase();
        let visibleCount = 0;

        options.forEach((option) => {
            const isVisible = option.dataset.cityName.toLowerCase().includes(normalizedQuery);

            option.classList.toggle('hidden', ! isVisible);
            visibleCount += isVisible ? 1 : 0;
        });

        emptyMessage.classList.toggle('hidden', visibleCount > 0);
    };

    const selectOption = (option) => {
        hiddenInput.value = option.dataset.cityId;
        label.textContent = option.dataset.cityName;
        label.classList.remove('text-slate-400');

        options.forEach((item) => {
            const isSelected = item === option;

            item.dataset.selected = String(isSelected);
            item.setAttribute('aria-selected', String(isSelected));
        });

        setOpen(false);
        trigger.focus();
    };

    trigger.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
    searchInput.addEventListener('input', () => filterOptions(searchInput.value));

    options.forEach((option) => {
        option.addEventListener('click', () => selectOption(option));
    });

    document.addEventListener('click', (event) => {
        if (! combobox.contains(event.target)) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
});
