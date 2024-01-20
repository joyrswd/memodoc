(function() {
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

})();
