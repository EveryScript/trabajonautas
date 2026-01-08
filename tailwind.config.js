import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Google Sans Flex", "sans-serif"],
                tbn: ["Google Sans Flex", "sans-serif"],
            },
            colors: {
                "tbn-primary": "#ff420a",
                "tbn-secondary": "#485054",
                "tbn-dark": "#242424",
                "tbn-light": "#bbbbbb",
            },
            animation: {
                astronaut: "astronaut 5s ease-in-out infinite",
            },
            keyframes: {
                astronaut: {
                    "0%, 100%": { transform: "translateY(0) rotate(0deg)" },
                    "50%": { transform: "translateY(-20px) rotate(5deg)" },
                },
            },
        },
    },

    plugins: [forms, typography],
};
