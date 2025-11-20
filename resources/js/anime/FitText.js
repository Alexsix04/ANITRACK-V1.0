export function fitTextLine(el, options = {}) {
    const min = options.min || 12;
    const max = options.max || 60;
    const step = options.step || 1;

    function resize() {
        let fontSize = max;
        el.style.fontSize = fontSize + "px";
        el.style.whiteSpace = "nowrap";

        while (el.scrollWidth > el.clientWidth && fontSize > min) {
            fontSize -= step;
            el.style.fontSize = fontSize + "px";
        }

        while (el.scrollWidth <= el.clientWidth && fontSize < max) {
            fontSize += step;
            el.style.fontSize = fontSize + "px";
            if (el.scrollWidth > el.clientWidth) {
                fontSize -= step;
                el.style.fontSize = fontSize + "px";
                break;
            }
        }
    }

    resize();
    window.addEventListener("resize", resize);
}

export function initFitText(options = {}) {
    document.querySelectorAll(".fittext-one-line").forEach(el => {
        fitTextLine(el, options);
    });
}