{
    // tableが空になったらsubmitボタンを無効化する

    const disabler = (records) => {
        const tbody = records[0].target;
        if (tbody.childElementCount === 0) {
            document.querySelectorAll('button[type="submit"]').forEach(button => button.disabled = true);
        }
    };

    (new MutationObserver(disabler)).observe(document.querySelector('table.table'), {
        childList: true,
        subtree: true
    });

    const setUpDragAndDrop = (row) => {
        row.draggable = true;
        row.addEventListener('dragstart', (e) => e.target.closest('[draggable]').classList.add('dragging'));
        row.addEventListener('dragend', (e) => e.target.closest('[draggable]').classList.remove('dragging'));
        row.addEventListener('dragenter', (e) => e.target.closest('[draggable]').classList.add('dragover'));
        row.addEventListener('dragleave', (e) => e.target.closest('[draggable]').classList.remove('dragover'));
        row.addEventListener('dragover', (e) => e.preventDefault());
        row.addEventListener('drop', changeRow);
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let debounceTimer;
    const changeRow = (e) => {
        e.preventDefault();
        const target = e.target.closest('[draggable]');
        const dragging = target.parentNode.querySelector('.dragging');
        const parent = target.parentNode;
        if (!target.nextElementSibling) {
            parent.appendChild(dragging);
        } else if(dragging === target.previousElementSibling) {
            parent.insertBefore(target, dragging);
        } else {
            parent.insertBefore(dragging, target);
        }
        target.classList.remove('dragover');
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(sendNewOrder, 500, []);
    };

    const sendNewOrder = (parts) => {
        document.querySelectorAll('[draggable]').forEach((row) => parts.push(row.dataset.id));
        fetch('', {
            method: 'POST',
            body: JSON.stringify({'memo': parts}),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
    }

    document.querySelectorAll('#parts tbody tr').forEach(setUpDragAndDrop);
};
