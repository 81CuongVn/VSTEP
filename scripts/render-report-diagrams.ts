import { renderMermaidSVG, THEMES } from "beautiful-mermaid";
import { readdir, readFile, writeFile, mkdir } from "node:fs/promises";
import { join, basename, resolve } from "node:path";
import puppeteer from "puppeteer";

const SRC_DIR = "docs/capstone/diagrams/src";
const OUT_DIR = "docs/capstone/diagrams/rendered";
const THEME = THEMES["github-light"];

await mkdir(OUT_DIR, { recursive: true });

const files = (await readdir(SRC_DIR)).filter((f) => f.endsWith(".mmd")).sort();

const browser = await puppeteer.launch();
const page = await browser.newPage();
await page.setViewport({ width: 4096, height: 4096, deviceScaleFactor: 3 });

let success = 0;
let failed = 0;

for (const file of files) {
  const name = basename(file, ".mmd");
  const source = await readFile(join(SRC_DIR, file), "utf-8");
  const svgPath = join(OUT_DIR, `${name}.svg`);
  const pngPath = join(OUT_DIR, `${name}.png`);
  try {
    const svg = renderMermaidSVG(source.trim(), THEME);
    await writeFile(svgPath, svg);

    // Render PNG via headless browser (correct CSS vars, fonts, color-mix)
    const html = `<!DOCTYPE html><html><head><style>body{margin:0;background:#fff;}</style></head><body>${svg}</body></html>`;
    await page.setContent(html, { waitUntil: "domcontentloaded" });
    const svgEl = await page.$("svg");
    await svgEl!.screenshot({ path: resolve(pngPath), type: "png", omitBackground: false });

    console.log(`\u2713 ${name}`);
    success++;
  } catch (e: any) {
    console.error(`\u2717 ${name}: ${e.message}`);
    failed++;
  }
}

await browser.close();
console.log(`\n${success} rendered, ${failed} failed`);
