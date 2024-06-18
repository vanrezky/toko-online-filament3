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
                sans: ['"Outfit"', "system-ui", "sans-serif"],
            },
            colors: {
                primary: "#FFFFFF",
                primary1: "#363738",
                secondary: "#F5F5F5",
                secondary1: "#FEFAF1",
                secondary2: "#DB4444",
                BG: "#FFFFFF",
                text: "#FAFAFA",
                text1: "#7D8184",
                text2: "#000000",
                button: "#000000",
                button1: "#00FF66",
                button2: "#DB4444",
                hoverbutton: "#E07575",
                hoverbutton2: "#A0BCE0",
                // primary: "#008ECC",
                // text: "#666666",
                // heading: "#222222",
                // light: "#888888",
                // border: "#EDEDED",
                // background1: "#F5F5F5",
                // background2: "#F6F6FC",
                // background3: "#F3F9FB",
                // yellow2: "#E3BC01",
                // line: "#D9D9D9",
            },
        },
    },
    plugins: [require("daisyui")],
};
