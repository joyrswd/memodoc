export default (xpost) => {
    const xtag = document.querySelector('[data-x="tags"]');
    const pattern = '[ -/:-@[-`{-~\u3000-\u303F\uFF00-\uFF0F\uFF1A-\uFF20\uFF3B-\uFF40\uFF5B-\uFF65\u2018\u2019\u2014]+'; // 半角記号と全角記号のみ
    const suggestionId = 'tag_suggestions';

    const updateTags = () => {
        const tags = xtag.querySelectorAll('input[name="tags[]"], input[type="text"]');
        const container = tags[0].closest('div');
        container.innerHTML = '';
        tags.forEach((tag) => {
            if (tag.value) {
                setTag(tag.value, container);
            }
        });
        const addFiled = createAddField();
        container.appendChild(addFiled);
        xpost?.dispatchEvent(new Event('input'));
    };

    const setTag = (tag, container) => {
        const tagElement = document.createElement('small');
        tagElement.classList.add('badge', 'bg-secondary', 'tag');
        tagElement.appendChild(document.createTextNode('#' + tag));
        const inputHidden = createHiddenInput(tag);
        tagElement.appendChild(inputHidden);
        const removeButton = createRemoveButton(tagElement);
        tagElement.appendChild(removeButton);
        container.appendChild(tagElement);
    }

    const createRemoveButton = (tagElement) => {
        const removeButton = document.createElement('input');
        removeButton.type = 'button';
        removeButton.value = 'x';
        removeButton.classList.add('remove');
        removeButton.addEventListener('click', () => {
            tagElement.remove();
            xpost?.dispatchEvent(new Event('input'));
        });
        return removeButton;
    }

    const createHiddenInput = (tag) => {
        const inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = 'tags[]';
        inputHidden.value = tag;
        return inputHidden;
    }

    const createAddField = () => {
        const span = document.createElement('span');
        span.classList.add('add-new');
        const input = document.createElement('input');
        input.type = 'text';
        input.placeholder = '新規タグ';
        input.maxLength = 20;
        input.setAttribute('list', suggestionId);
        setTagGenerator(input);
        span.appendChild(input);
        return span;
    }

    const setTagGenerator = (input) => {
        input.addEventListener('blur', updateTags);
        input.addEventListener('keydown', (e) => {
            if (['Enter', 'Escape', ' '].includes(e.key)) {
                input.blur();
                return false;
            }
        });
        input.addEventListener('input', (e) => {
            const text = input.value;
            const exp = new RegExp(pattern, 'g');
            input.value = text.replace(exp, '');
        });
        input.addEventListener('change', e => input.blur());
    }

    const setSuggestion = (items) => {
        const datalist = document.createElement('datalist');
        datalist.id = suggestionId;
        Object.values(items).forEach((tag) => {
            const option = document.createElement('option');
            option.value = tag;
            datalist.appendChild(option);
        });
        xtag.appendChild(datalist);
    }

    const loadSuggestionList = (url) => {
        fetch(url).then(response => response.json())
            .then(data => setSuggestion(data?.items||{}));
    }

    if (xtag) {
        //xtagにカスタムイベントを追加
        xtag.addEventListener('updateTags', updateTags);
        xtag.dispatchEvent(new Event('updateTags'));
        loadSuggestionList('/tags');
    }

};