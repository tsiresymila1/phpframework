import fs  from 'fs'
import { join, resolve } from "path";
import { loadEnv, PluginOption, ResolvedConfig } from "vite";
import FullReload from 'vite-plugin-full-reload'

export default function VitePhpPlugin(config?: { 
    root?: string;
    public?: string;
    entry?: string;
    output?: string;
    port?: number,
    host?: string,
    apiServer?: string,
    isHttps?: boolean
}): PluginOption {
    var pluginConfig =  {
        root: config?.root ??  "src/assets/js",
        public: config?.public ?? "public",
        entry: config?.entry ?? "index.tsx",
        output: config?.output ?? "public/js/bundle",
        port: config?.port ?? 5133,
        host: config?.host ?? "localhost",
        apiServer: config?.apiServer ?? "http://localhost:4444",
        isHttps: config?.isHttps ?? false
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
                    open: join(__dirname, 'dev-server-index.html'),
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
                return code.replace(/__php_vite_placeholder__/g, `${pluginConfig.isHttps? 'https': 'http'}://${pluginConfig.host}:${pluginConfig.port}`)
            }
        },
        configureServer(server){
            server.httpServer?.once('listening', () => {
                const address = server.httpServer?.address()
                console.log("Address : ",address)
            });
            const envDir = resolvedConfig.envDir || process.cwd()
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
