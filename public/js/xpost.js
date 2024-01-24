(function(xTarget) {
    const postUrl = 'https://twitter.com/intent/tweet?text=';
    const xpost = document.querySelector('[data-x="post"]');
    const xtag = document.querySelector('[data-x="tags"]');
    const controller = document.querySelector('[data-x="controller"]');
    const switcher = controller.querySelector('[role="switch"]');

    const isInput = (element) => {
        return (element.tagName === 'INPUT'
            && element.type === 'text')
            || element.tagName === 'TEXTAREA';
    }

    const createXpostLink = (text) => {
        const link = document.createElement('a');
        link.href = postUrl + encodeURIComponent(text);
        link.target = 'xpost';
        link.appendChild(document.createTextNode('Post'));
        return link;
    }

    const updateXpostLink = (link, text) => {
        link.href = postUrl + encodeURIComponent(text);
    }

    const updateCounter = (current, text) => {
        current.innerHTML = countText(text);
    }

    const createCurrentCoutner = (text) => {
        const count = countText(text);
        const current = document.createElement('em');
        current.appendChild(document.createTextNode(count));
        return current;
    }

    const createTextCounter = (current) => {
        const counter = document.createElement('span');
        counter.classList.add('text-muted', 'counter');
        counter.appendChild(current);
        counter.appendChild(document.createTextNode('/280'));
        return counter;
    }

    const countText = (realText) => {
        const text = realText.replace(/[^\x01-\x7E\xA1-\xDF]/g, '  ');
        const exp = /https?:\/\/[^\s]{24,}/g;
        let count = text.length;
        text.match(exp)?.forEach((url) => {
            count -= url.length - 23;
        });
        return count;
    }

    const fetchXpostButton = () => {
        const text = getText();
        const link = createXpostLink(text);
        const current = createCurrentCoutner(text);
        const counter = createTextCounter(current);
        controller.appendChild(link);
        controller.appendChild(counter);
        setUpdater(link, current);
        fetchTagManeger();
    }

    const toggleTagManeger = () => {
        if (switcher.checked) {
            xtag.classList.add('open');
        } else {
            xtag.classList.remove('open');
        }
        xpost.dispatchEvent(new Event('input'));
    }

    const createRemoveButton = (tagElement) => {
        const removeButton = document.createElement('input');
        removeButton.type = 'button';
        removeButton.value = 'x';
        removeButton.classList.add('remove');
        removeButton.addEventListener('click', () => {
            //直後の改行を削除
            const next = tagElement.nextSibling;
            tagElement.remove();
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


    const createAddField = () => {
        const span = document.createElement('span');
        span.classList.add('add-new');
        const input = document.createElement('input');
        input.type = 'text';
        input.placeholder = '新規タグ';
        input.maxLength = 20;
        setTagGenerator(input);
        span.appendChild(input);
        return span;
    }

    const setTagGenerator = (input) => {
        const pattern =  '[!-\\\\/:@[-`{-~\\s\u3000-\u303F\uFF00-\uFFEF]+';
        input.addEventListener('blur', updateTags);
        input.addEventListener('keydown', (e) => {
            if (['Enter', 'Tab', ' ', '　'].includes(e.key)) {
                updateTags();
                return false;
            }
        });
        input.addEventListener('input', (e) => {
            const text = input.value;
            const exp = new RegExp(pattern, 'g');
            input.value = text.replace(exp, '');
        });
    }

    const updateTags = () => {
        const container = xtag.firstElementChild.firstElementChild;
        const tags = container.querySelectorAll('input[name="tags[]"], input[type="text"]');
        container.innerHTML = '';
        tags.forEach((tag) => {
            if (tag.value) {
                setTag(tag.value, container);
            }
        });
        const addFiled = createAddField();
        container.appendChild(addFiled);
        xpost.dispatchEvent(new Event('input'));
    };

    const initTagManeger = () => {
        const container = document.createElement('div');
        container.classList.add('col-2', 'newline');
        container.innerHTML = `<label>改行<input type="checkbox"></label>`;
        xtag.firstElementChild.appendChild(container);
        updateTags();
    }

    const fetchTagManeger = () => {
        initTagManeger();
        switcher.addEventListener('change', toggleTagManeger);
        switcher.dispatchEvent(new Event('change'));
        xtag.querySelector('input[type="checkbox"]').addEventListener('change', updateTags);
    }

    const setUpdater = (link, counter) => {
        xpost.addEventListener('input', () => { 
            const text = getText();
            updateXpostLink(link, text);
            updateCounter(counter, text);
        });
    }

    const getText = () => {
        let text = isInput(xpost) ? xpost.value : xpost.innerText;
        if (xtag && xtag.classList.contains('open')) {
            text += getTagsText();
        }
        return text;
    }

    const getTagsText = () => {
        const glue = xtag.querySelector('input[type="checkbox"]').checked ? '\n' : ' ';
        const tags = xtag.querySelectorAll('.badge:has(input[name="tags[]"])');
        const texts = [];
        tags.forEach((tag) => {
            texts.push(tag.innerText);
        });
        return texts.length > 0 ? glue + texts.join(glue) : '';
    }

    if (xpost) {
        fetchXpostButton();
    }

})();
