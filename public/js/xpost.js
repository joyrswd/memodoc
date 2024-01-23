(function(xTarget) {
    const postUrl = 'https://twitter.com/intent/tweet?text=';
    const xpost = document.querySelector('[data-x="post"]');
    const xtag = document.querySelector('[data-x="tag"]');

    const isInput = (element) => {
        return (element.tagName === 'INPUT'
            && element.type === 'text')
            || element.tagName === 'TEXTAREA';
    }

    const createXpostLink = (text) => {
        const link = document.createElement('a');
        link.href = postUrl + encodeURIComponent(text);
        link.target = 'xpost';
        link.classList.add('small');
        link.appendChild(document.createTextNode('Post'));
        return link;
    }

    const createContainer = (link) => {
        const container = document.createElement('div');
        container.classList.add('mt-2', 'text-end');
        container.appendChild(link);
        return container;
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

    const createTextCounter = (text) => {
        const current = createCurrentCoutner(text);
        const counter = document.createElement('span');
        counter.classList.add('text-muted', 'small', 'float-start');
        counter.appendChild(current);
        counter.appendChild(document.createTextNode('/280'));
        return [counter, current];
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
        const container = createContainer(link);
        xpost.parentNode.insertBefore(container, xpost.nextSibling);
        const [counter, current] = createTextCounter(text);
        container.appendChild(counter);
        const drawer = createTagContainer();
        const switcher = crateTagSwitch();
        if (xtag) {
            fetchTagManeger(container, drawer, switcher);
        }
        runUpdater(link, current, drawer);
    }

    const createTagContainer = () => {
        const container = document.createElement('div');
        container.classList.add('drawer');
        container.innerHTML = `
        <div class="row">
            <div class="col-sm-9"></div>
            <div class="col-sm-3 text-end small"><label><input type="checkbox">改行</label></div>
        </div>
        `;
        return container;
    }

    const crateTagSwitch = () => {
        const container = document.createElement('small');
        container.classList.add('form-switch', 'me-2');
        container.innerHTML = `
        <label class="form-check-label align-baseline" for="flexSwitchCheckDefault">タグ同期</label>
        <input class="form-check-input m-0 align-text-bottom" type="checkbox" role="switch" id="flexSwitchCheckDefault">
        `;
        return container;
    }

    const switchDrawer = (drawer) => {
        if (drawer.classList.contains('open')) {
            drawer.classList.remove('open');
        } else {
            drawer.classList.add('open');
        }
    }

    const setTags = (tag, container, flag) => {
        const tagElement = document.createElement('span');
        tagElement.classList.add('badge', 'bg-secondary', 'me-1');
        tagElement.appendChild(document.createTextNode('#' + tag));
        container.appendChild(tagElement);
        if (flag) {
            container.appendChild(document.createElement('br'));
        }
    }

    const updateDrawerContent = (drawer) => {
        const text = xtag.value;
        const exp = /[^\s]+/g;
        const tags = text.match(exp);
        const container = drawer.querySelector('.col-sm-9');
        const ln = drawer.querySelector('input[type="checkbox"]').checked;
        container.innerHTML = '';
        tags?.forEach((tag) => setTags(tag, container, ln));
    }

    const fetchTagManeger = (container, drawer, switcher) => {
        container.parentNode.insertBefore(drawer, container);
        container.insertBefore(switcher, container.firstElementChild);
        runTagManeger(drawer, switcher);
    }

    const runTagManeger = (drawer, switcher) => {
        xtag.addEventListener('input', () => {
            updateDrawerContent(drawer);
            xpost.dispatchEvent(new Event('input'));
        });
        switcher.addEventListener('change', () => {
            switchDrawer(drawer);
            xtag.dispatchEvent(new Event('input'));
        });
        drawer.querySelector('input[type="checkbox"]').addEventListener('change', () => xtag.dispatchEvent(new Event('input')));
    }

    const runUpdater = (link, counter, drawer) => {
        xpost.addEventListener('input', () => { 
            const text = getText(drawer);
            updateXpostLink(link, text);
            updateCounter(counter, text);
        });
    }

    const getText = (drawer) => {
        let text = isInput(xpost) ? xpost.value : xpost.innerText;
        if (drawer && drawer.classList.contains('open')) {
            text += getDrawerText(drawer);
        }
        return text;
    }

    const getDrawerText = (drawer) => {
        const glue = drawer.querySelector('input[type="checkbox"]').checked ? '\n' : ' ';
        const tags = drawer.querySelectorAll('.badge');
        const texts = [];
        tags.forEach((tag) => {
            texts.push(tag.innerText);
        });
        return glue + texts.join(glue);
    }

    if (xpost) {
        fetchXpostButton();
    }

})();
