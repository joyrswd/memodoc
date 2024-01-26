export default (target) => {

    const invalidClass = 'is-invalid';
    const messageClass = 'invalid-feedback';

    const coloring = (element) => {
        const prevElem = element.previousElementSibling;
        if (!prevElem) return;
        prevElem.classList.add(invalidClass);
        coloringDeeply(prevElem);
    };

    const coloringDeeply = (prevElem) => {
        if (prevElem = prevElem.querySelector(target)) {
            prevElem.classList.add(invalidClass);
        }
    };

    document.querySelectorAll('.' + messageClass).forEach(coloring);
    document.querySelectorAll('.' + invalidClass).forEach(element => {
        element.addEventListener('focus', () => {
            element.classList.remove(invalidClass);
            element.parentNode.classList.remove(invalidClass);
        });
    });
};