import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

import VitePhpPlugin from "./vite-php-plugin";

export default defineConfig({
  plugins: [
    react(),
    VitePhpPlugin()
  ]
});
