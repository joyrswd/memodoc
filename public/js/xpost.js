{
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
        const text = realText.replace(/[^\x01-\x7E\xA1-\xDF]/g, 'aa');
        const exp = /https?:\/\/[^\s]+/g;
        let count = text.length;
        text.match(exp)?.forEach((url) => {
            count -= (url.length > 23) ? url.length - 23 : 0;
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
            xtag.classList.remove('closed');
        } else {
            xtag.classList.add('closed');
        }
        xpost.dispatchEvent(new Event('input'));
    }

    const fetchTagManeger = () => {
        const container = document.createElement('div');
        container.classList.add('col-2', 'newline');
        container.innerHTML = `<label>改行<input type="checkbox"></label>`;
        xtag.firstElementChild.appendChild(container);
        switcher.addEventListener('change', toggleTagManeger);
        switcher.dispatchEvent(new Event('change'));
        xtag.querySelector('input[type="checkbox"]').addEventListener('change', e =>  xtag.dispatchEvent(new Event('updateTags')));
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
        if (xtag && !xtag.classList.contains('closed')) {
            text += getTagsText();
        }
        return text;
    }

    const getTagsText = () => {
        const glue = xtag.querySelector('input[type="checkbox"]')?.checked ? '\n' : ' ';
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

};
