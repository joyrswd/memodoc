export default (target) => {

    const className = 'is-invalid';

    const coloring = (element) => {
        const prevElem = element.previousElementSibling;
        prevElem.classList.add(className);
        coloringDeeply(prevElem);
    };

    const coloringDeeply = (prevElem) => {
        if (prevElem = prevElem.querySelector(target)) {
            prevElem.classList.add(className);
        }
    };

    document.querySelectorAll('.invalid-feedback').forEach(coloring);
};