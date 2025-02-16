/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js", "./resources/**/*.vue", "./node_modules/@shadcn-vue/ui/**/*.{js,ts,jsx,tsx,vue}"],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Outfit"', "system-ui", "sans-serif"],
                frontend: ['"Open Sans"', "system-ui", "sans-serif"],
            },
            colors: {
                primary: "#ff4500",
                secondary: "#64748b",
                accent: "#9333ea",
            },
        },
    },
    // plugins: [require("shadcn/ui")],
};
