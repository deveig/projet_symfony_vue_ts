import nodeFs from 'node:fs';
import express from 'express';
import https from 'https';
import axios from 'axios';
import fs from 'fs/promises';

// Constants
const isProduction = process.env.NODE_ENV === 'production';
const port = process.env.PORT || 5173;
// const port = 443;
const base = process.env.BASE || '/';

// Cached production assets
const templateHtml = isProduction ? await fs.readFile('./dist/client/index.html', 'utf-8') : '';

// Create http server
const app = express();

// Proxy /recipe requests to the Symfony backend, forwarding raw body and headers.
app.use('/recipe', express.raw({ type: '*/*', limit: '10mb' }));
app.use('/recipe', async (req, res) => {
  try {
    const backendUrl = `http://nginx-back:8080${req.originalUrl}`;
    const headers = { ...req.headers };
    delete headers.host;
    const response = await axios({
      method: req.method as any,
      url: backendUrl,
      data: req.body,
      headers: headers,
      withCredentials: true,
      responseType: 'arraybuffer',
      validateStatus: () => true
    });

    // Forward status, headers and body
    res.status(response.status);
    // Object.entries(response.headers).forEach(([k, v]) => {
    //   try {
    //     res.setHeader(k, v as string);
    //   } catch {}
    // });
    res.send(response.data);
  } catch (err: any) {
    const status = err.response?.status || 500;
    res.status(status).send(err.response?.data || err.stack || 'Internal Server Error');
  }
});

// Add Vite or respective production middlewares
/** @type {import('vite').ViteDevServer | undefined} */
let vite;
if (!isProduction) {
  const { createServer } = await import('vite');
  vite = await createServer({
    server: { middlewareMode: true },
    appType: 'custom',
    base
  });
  app.use(vite.middlewares);
} else {
  const compression = (await import('compression')).default;
  const sirv = (await import('sirv')).default;
  app.use(compression());
  app.use(base, sirv('./dist/client', { extensions: [] }));
}

// Serve HTML
app.use('*all', async (req, res) => {
  try {
    const url = req.originalUrl.replace(base, '');

    /** @type {string} */
    let template;
    /** @type {import('./src/entry-server.ts').render} */
    let render;
    if (!isProduction) {
      // Always read fresh template in development
      template = await fs.readFile('./index.html', 'utf-8');
      template = await vite.transformIndexHtml(url, template);
      render = (await vite.ssrLoadModule('/src/entry-server.ts')).render;
    } else {
      template = templateHtml;
      render = (await import('./dist/server/entry-server.js')).render;
    }

    const rendered = await render(url);

    const html = template
      .replace(`<!--app-head-->`, rendered.head ?? '')
      .replace(`<!--app-html-->`, rendered.html ?? '');

    res.status(200).set({ 'Content-Type': 'text/html' }).send(html);
  } catch (e) {
    vite?.ssrFixStacktrace(e);
    console.log(e.stack);
    res.status(500).end(e.stack);
  }
});

// Start http server
// https
//   .createServer(
//     {
//       cert: nodeFs.readFileSync('/etc/ssl/certs/app.crt'),
//       key: nodeFs.readFileSync('/etc/ssl/private/app.key')
//     },
//     app
//   )
  app.listen(port, () => {
    console.log(`Server started at http://localhost:${port}`);
  });
