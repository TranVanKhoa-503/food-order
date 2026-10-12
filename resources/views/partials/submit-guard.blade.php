<script>
    (() => {
        const buttonStates = new WeakMap();

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!form.matches('form[data-prevent-double-submit]') || event.defaultPrevented) return;

            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
                event.preventDefault();
                return;
            }

            form.dataset.submitting = 'true';
            form.setAttribute('aria-busy', 'true');
            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
                buttonStates.set(button, button.disabled);
                button.disabled = true;
            });
        });

        // Browsers can restore disabled buttons when returning with Back.
        window.addEventListener('pageshow', () => {
            document.querySelectorAll('form[data-prevent-double-submit]').forEach((form) => {
                delete form.dataset.submitting;
                form.removeAttribute('aria-busy');
                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
                    if (buttonStates.has(button)) {
                        button.disabled = buttonStates.get(button);
                        buttonStates.delete(button);
                    }
                });
            });
        });
    })();
</script>
