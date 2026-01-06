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
        },
    },

    plugins: [forms, typography],
};
