// Build pipeline loads Tailwind and Autoprefixer for generated CSS. This file is read by
// tooling, not by the browser as a script.

export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
