/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "node_modules/preline/dist/*.js",
        "./vendor/masmerise/livewire-toaster/resources/views/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Hanken Grotesk"', "sans-serif"],
                glory: ['"Glory"', "sans-serif"],
            },
            colors: {
                primary: "#008ECC",
                text: "#666666",
                heading: "#222222",
                light: "#888888",
                border: "#EDEDED",
                background1: "#F5F5F5",
                background2: "#F6F6FC",
                background3: "#F3F9FB",
                yellow2: "#E3BC01",
                line: "#D9D9D9",
            },
        },
    },
    plugins: [require("daisyui")],
};
