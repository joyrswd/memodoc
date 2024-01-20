export default (modalId, submitId, targets) => {
    const modalElement = document.querySelector(modalId);
    const modalSubmitButton = modalElement.querySelector(submitId);
    const modalHandler = new bootstrap.Modal(modalElement);

    const setModalEvetnts = (element) => {
        const texts = jsonParse(element.dataset.dialog);
        const isError = (texts.title === 'エラー');
        element.addEventListener('click', (event) => {
            event.preventDefault();
            setUpModalContents(element, texts, isError);
        });
    };

    const setUpModalContents = (element, texts, isError) => {
        setUpModalButton(element, isError);
        setTexts(texts);
        modalHandler.show();
    }

    const setUpModalButton = (target, isError) => {
        if (isError ===false ) {
            modalSubmitButton.addEventListener('click', () => finishModal(target), {once: true});
        }
        toggleModalButton(isError);
    };

    const toggleModalButton = (flag) => {
        if (flag) {
            modalSubmitButton.classList.add('d-none');
        } else {
            modalSubmitButton.classList.remove('d-none');
        }
    };

    const finishModal = (target) => {
        // 参照元のフォームに送信処理を設定しなおしてsubmitイベントを発火させる
        // (フォームに設定されている他のsubmitイベントを発火させた後に通常の送信処理を実行させるため)
        target.form.addEventListener('submit', ev => target.form.submit(), {once: true});
        target.form.dispatchEvent(new Event('submit'));
        // モーダルを閉じる
        modalHandler.hide();
    };

    const setTexts = (properties) => {
        for (const [key, text] of Object.entries(properties)) {
            const query = '.modal-' + key;
            modalElement.querySelector(query).textContent = text;
        }
    };

    const jsonParse = (text) => {
        try {
            return JSON.parse(text);
        } catch (e) {
            return {
                title: 'エラー',
                body: e.message,
            };
        }
    };

    document.querySelectorAll(targets).forEach(setModalEvetnts, false);
    
};