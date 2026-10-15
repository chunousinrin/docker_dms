import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/components/fullcalendar.css",
                "resources/css/app.css",

                "resources/js/components/fullcalendar.js",
                "resources/js/partials/projects.js",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],
});
