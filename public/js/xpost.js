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

    const fetchContainer = (link) => {
        const conainer = document.createElement('div');
        conainer.classList.add('mt-2', 'text-end');
        conainer.appendChild(link);
        return conainer;
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

    const fetchXpostButton = (inputable) => {
        const text = inputable ? xpost.value : xpost.innerHTML;
        const link = createXpostLink(text);
        const conainer = fetchContainer(link);
        xpost.parentNode.insertBefore(conainer, xpost.nextSibling);
        if (inputable) {
            runUpdater(text, link, conainer);
        }
    }

    const runUpdater = (text, link, conainer) => {
        const [counter, current] = createTextCounter(text);
        conainer.appendChild(counter);
        xpost.addEventListener('input', () => { 
            updateXpostLink(link, xpost.value);
            updateCounter(current, xpost.value);
        });
        if (xtag) {
            //fetchTagManeger();
        }
    }
    
    fetchXpostButton(isInput(xpost));

})();
