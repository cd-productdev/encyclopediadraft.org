<script>
    function initBulkSelection(config) {
        const selectAll = document.getElementById(config.selectAllId);
        const checkboxes = Array.from(document.querySelectorAll(config.checkboxSelector));
        const toolbar = config.toolbarId ? document.getElementById(config.toolbarId) : null;
        const countLabel = config.countId ? document.getElementById(config.countId) : null;
        const submitButton = config.submitId ? document.getElementById(config.submitId) : null;

        if (checkboxes.length === 0) {
            return;
        }

        const updateToolbar = () => {
            const enabledCheckboxes = checkboxes.filter((checkbox) => !checkbox.disabled);
            const selected = enabledCheckboxes.filter((checkbox) => checkbox.checked);
            const count = selected.length;

            if (toolbar) {
                toolbar.classList.toggle('hidden', count === 0);
            }

            if (countLabel) {
                countLabel.textContent = `${count} selected`;
            }

            if (selectAll) {
                selectAll.checked = enabledCheckboxes.length > 0 && count === enabledCheckboxes.length;
                selectAll.indeterminate = count > 0 && count < enabledCheckboxes.length;
            }
        };

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                checkboxes.forEach((checkbox) => {
                    if (!checkbox.disabled) {
                        checkbox.checked = selectAll.checked;
                    }
                });
                updateToolbar();
            });
        }

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateToolbar);
        });

        if (submitButton) {
            submitButton.addEventListener('click', (event) => {
                const selected = checkboxes.filter((checkbox) => !checkbox.disabled && checkbox.checked).length;

                if (selected === 0) {
                    event.preventDefault();
                    return;
                }

                if (!confirm(config.confirmMessage.replace(':count', String(selected)))) {
                    event.preventDefault();
                }
            });
        }

        updateToolbar();
    }
</script>
