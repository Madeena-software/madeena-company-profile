import katex from 'katex';
import 'katex/dist/katex.min.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-latex]').forEach(el => {
        const latex = el.getAttribute('data-latex');
        try {
            katex.render(latex, el, {
                displayMode: true,
                throwOnError: false,
            });
        } catch (e) {
            el.textContent = latex; // Fallback to raw LaTeX
            console.error('KaTeX rendering error', e);
        }
    });
});
