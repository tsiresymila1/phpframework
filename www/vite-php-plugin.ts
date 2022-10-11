import { resolve } from "path";
import { PluginOption } from "vite";
export default function VitePhpPlugin(config?: {
    root: string;
    public: string;
    entry: string;
    output: string;
    port: number,
    host: string
}): PluginOption {
    var pluginConfig = config ?? {
        root: "src/assets/js",
        public: "public",
        entry: "index.tsx",
        output: "public/js/bundle",
        port: 5133,
        host: "localhost"
    };
    return {
        name: "vite-php-plugin",
        config(config, env) {
            return {
                ...config,
                root: pluginConfig.root,
                publicDir: "public",
                define: {
                    "process.env.NODE_ENV": null,
                },
                server: {
                    port: pluginConfig.port,
                    host: pluginConfig.host,
                    strictPort: true,
                    middlewareMode: false,
                    open: resolve(pluginConfig.root, 'index.html')
                },
                optimizeDeps: {
                    entries: [resolve(pluginConfig.root, pluginConfig.entry)],
                },
                build: {
                    manifest: true,
                    emptyOutDir: true,
                    assetsInlineLimit: 0,
                    outDir: resolve(pluginConfig.output),
                    lib: {
                        entry: resolve(pluginConfig.root, pluginConfig.entry),
                        fileName: "app",
                        name: "app",
                    },
                    rollupOptions: {
                        external: ["React"],
                        input: resolve(pluginConfig.root, pluginConfig.entry),
                        output: {
                            format: "cjs",
                            assetFileNames: (assetInfo) => {
                                if (assetInfo.name == "style.css") {
                                    return "app.css";
                                }
                                return assetInfo.name ?? "app.js";
                            },
                        },
                    },
                },
            };
        }
    };
}
