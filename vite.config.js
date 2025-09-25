import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { viteStaticCopy } from "vite-plugin-static-copy";
import react from "@vitejs/plugin-react";

export default defineConfig({
  build: {
    manifest: true,
    outDir: "public/build/",
    cssCodeSplit: true,
    rollupOptions: {
      output: {
        // use hashed names and predictable folders
        entryFileNames: "js/[name]-[hash].js",
        chunkFileNames: "js/[name]-[hash].js",
        assetFileNames: (assetInfo) => {
          const ext = assetInfo.name.split(".").pop();
          if (ext === "css") return "css/[name]-[hash][extname]";
          if (["woff", "woff2", "ttf", "eot"].includes(ext)) return "fonts/[name]-[hash][extname]";
          if (["png", "jpg", "jpeg", "svg", "gif", "webp"].includes(ext)) return "images/[name]-[hash][extname]";
          return "assets/[name]-[hash][extname]";
        },
      },
    },
  },

  plugins: [
    laravel({
      input: [
        "resources/js/app.jsx",
        "resources/js/landbot/main.jsx",
        "resources/scss/bootstrap.scss",
        "resources/scss/icons.scss",
        "resources/scss/app.scss",
        "resources/css/custom.css",
      ],
      refresh: true,
    }),
    react(),
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
