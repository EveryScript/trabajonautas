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
                figtree: ["Figtree", 'sans-serif'],
                baloo: ['"Baloo Da 2"', 'sans-serif']
            },
            colors: {
                "tbn-primary": "#034b8d",
                "tbn-secondary": "#f29000",
                "tbn-light": "#888888",
                "tbn-dark": "#484848",
            }
        },
    },

    plugins: [forms, typography],
};
