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
                sans: ["DM Sans", "sans-serif"],
                tbn: ['"Rubik"', "sans-serif"],
            },
            colors: {
                "tbn-primary": "#ff420a",
                "tbn-secondary": "#485054",
                "tbn-light": "#888888",
                "tbn-dark": "#484848",
            },
        },
    },

    plugins: [forms, typography],
};
