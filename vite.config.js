// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { viteStaticCopy } from "vite-plugin-static-copy";
import react from "@vitejs/plugin-react"; // <-- IMPORTANT: import the plugin

export default defineConfig({
  build: {
    manifest: true,
    rtl: true,
    outDir: "public/build/",
    cssCodeSplit: true,
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          const ext = assetInfo.name.split(".").pop();
          if (ext === "css") {
            return "css/[name].min.css";
          }
          return "icons/" + assetInfo.name;
        },
        entryFileNames: "js/[name].js",
      },
    },
  },                                                                                  

  plugins: [
    laravel({
      input: [
        "resources/js/app.jsx",
        'resources/js/landbot/main.jsx', //reactflow
        "resources/scss/bootstrap.scss",
        "resources/scss/icons.scss",
        "resources/scss/app.scss",
        // "resources/js/landbot/index.css"
      ],
      refresh: true,
    }),

    react(), // <-- make sure this line exists AFTER the import

    viteStaticCopy({
      targets: [
        { src: "resources/fonts", dest: "" },
        { src: "resources/images", dest: "" },
        { src: "resources/js", dest: "" },
        { src: "resources/json", dest: "" },
        { src: "resources/libs", dest: "" },
      ],
    }),
  ],

  server: {
    watch: {
      ignored: ["**/vendor/**", "**/node_modules/**", "**/public/**"],
    },
  },
});
