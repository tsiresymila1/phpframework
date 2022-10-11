import fs  from 'fs'
import { join, resolve } from "path";
import { loadEnv, PluginOption, ResolvedConfig } from "vite";
import FullReload from 'vite-plugin-full-reload'

export default function VitePhpPlugin(config?: {
    root: string;
    public: string;
    entry: string;
    output: string;
    port: number,
    host: string,
    apiServer: string
}): PluginOption {
    var pluginConfig = config ?? {
        root: "src/assets/js",
        public: "public",
        entry: "index.tsx",
        output: "public/js/bundle",
        port: 5133,
        host: "localhost",
        apiServer: "http://localhost:4444"
    };
    let resolvedConfig: ResolvedConfig;
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
                    origin: '__php_vite_placeholder__',
                    port: pluginConfig.port,
                    host: pluginConfig.host,
                    strictPort: true,
                    middlewareMode: false,
                    open: resolve(pluginConfig.root, 'index.html'),
                    proxy: {
                        "/api": {
                            target : pluginConfig.apiServer
                        }
                    }
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
                plugins: [
                    FullReload([join(process.cwd(),'vite.config.ts')])
                ]
                
            };
        },
        configResolved(config) {
            resolvedConfig = config
        },
        transform(code) {
            if (resolvedConfig.command === 'serve') {
                return code.replace(/__php_vite_placeholder__/g, `http://${pluginConfig.host}:${pluginConfig.port}`)
            }
        },
        configureServer(server){
            server.httpServer?.once('listening', () => {
                const address = server.httpServer?.address()
                console.log("Address : ",address)
            });
            const envDir = resolvedConfig.envDir || process.cwd()
            // const appUrl = loadEnv(resolvedConfig.mode, envDir, 'APP_URL').APP_URL ?? 'undefined'
            return () => server.middlewares.use((req, res, next) => {
                if (req.url === '/index.html') {
                    res.statusCode = 404
                    res.end(
                        fs.readFileSync(join(__dirname, 'dev-server-index.html'))
                    )
                }
                next()
            })
        }
    };
}
