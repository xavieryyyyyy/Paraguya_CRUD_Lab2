// Appearance-only code loaded with defer by product/guest layouts. A click toggles light-theme
// on body and saves the browser choice in localStorage; it does not change account permissions.

// Theme control: use one body class to select the light CSS rules. Dark is the default,
// matching the approved charcoal reference.
const themeButton = document.getElementById('theme-toggle');

function setTheme(theme) {
    const isLight = theme === 'light';
    document.body.classList.toggle('light-theme', isLight);

    // Tell screen readers what clicking will do and whether light mode is selected.
    themeButton.setAttribute('aria-label', isLight ? 'Switch to dark theme' : 'Switch to light theme');
    themeButton.setAttribute('aria-pressed', String(isLight));
}

// localStorage remembers the choice in this browser across pages and refreshes. Some browser
// privacy settings block storage; try/catch lets the button still work.
let savedTheme = 'dark';
try {
    savedTheme = localStorage.getItem('product-manager-theme') || 'dark';
} catch (error) {
    // Use the default dark theme when saved settings cannot be read.
}
setTheme(savedTheme);

themeButton.addEventListener('click', function () {
    const nextTheme = document.body.classList.contains('light-theme') ? 'dark' : 'light';
    setTheme(nextTheme);
    try {
        localStorage.setItem('product-manager-theme', nextTheme);
    } catch (error) {
        // The selected theme still works for this page even without persistence.
    }
});
