import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./src"),
            "@design-system": path.resolve(__dirname, "./src/design-system"),
            "@core": path.resolve(__dirname, "./src/core"),
            "@plugins": path.resolve(__dirname, "./src/plugins"),
            "@stores": path.resolve(__dirname, "./src/stores"),
        },
    },
    server: {
        port: 5173,
        open: true,
        cors: true,
    },
    build: {
        outDir: "dist",
        sourcemap: true,
    },
});
