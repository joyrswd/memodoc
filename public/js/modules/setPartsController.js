export default (buttonId, badgeId, partsListId) => {

    const control = (element) => {
        const command = element.dataset.parts;
        element.addEventListener('submit', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation(); //以降のsubmitイベントを強制キャンセル    
            setSubmitEvent(event.target, command);
        });
    };

    const setSubmitEvent = (form, command) => {
        const request = new Request(form.action, {
            method: form.method,
            body: new FormData(form),
        });
        fetch(request).then(ajaxResult).then((data) => finalize(data, form, command)).catch(error => alert(error.message));
    };

    const ajaxResult = (response) => {
        if (!response.ok) {
            throw new Error('Network response was not ok.');
        }
        return response.json();
    };

    const finalize = (data, form, command) => {
        if (data.status !== 'success') {
            throw new Error(data.message);
        }
        if (command === 'add') {
            form.querySelector('button').disabled = true;
        } else if (command === 'remove') {
            form.querySelectorAll('button').forEach(removeTooltips);
            removeRow(form, data);
        }
        document.querySelector(badgeId).textContent = (data.count) ? data.count : '';
    }

    //elementに設定されたtooltipを削除
    const removeTooltips = (button) => {
        const tooltip = bootstrap.Tooltip.getInstance(button);
        if (tooltip) {
            tooltip.dispose();
        }
    };

    const removeRow = (form, data) => {
        const tr = form.closest('tr');
        if (tr) {
            tr.remove();
        } else if (data.count == 0) {
            document.querySelectorAll(partsListId).forEach(tr => tr.remove());
        }
    }

    document.querySelectorAll(buttonId).forEach(control);
};