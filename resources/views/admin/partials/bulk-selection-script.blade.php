<script>
    function initBulkSelection(config) {
        const selectAll = document.getElementById(config.selectAllId);
        const checkboxes = Array.from(document.querySelectorAll(config.checkboxSelector));
        const toolbar = document.getElementById(config.toolbarId);
        const countLabel = document.getElementById(config.countId);
        const submitButton = document.getElementById(config.submitId);

        if (!toolbar || checkboxes.length === 0) {
            return;
        }

        const updateToolbar = () => {
            const enabledCheckboxes = checkboxes.filter((checkbox) => !checkbox.disabled);
            const selected = enabledCheckboxes.filter((checkbox) => checkbox.checked);
            const count = selected.length;

            toolbar.classList.toggle('hidden', count === 0);

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
