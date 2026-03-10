/**
 * Render .mmd files to .svg using beautiful-mermaid.
 *
 * Usage:
 *   bun scripts/render-mermaid.ts <input.mmd> [output.svg]
 *   bun scripts/render-mermaid.ts docs/capstone/diagrams/sources/seq-auth.mmd
 *
 * If output is omitted, writes to diagrams/images/ with same basename.
 */
import { renderMermaidSVG } from "beautiful-mermaid";
import { readFileSync, writeFileSync, mkdirSync } from "fs";
import { basename, dirname, join, resolve } from "path";

const input = process.argv[2];
if (!input) {
  console.error("Usage: bun scripts/render-mermaid.ts <input.mmd> [output.svg]");
  process.exit(1);
}

const inputPath = resolve(input);
const source = readFileSync(inputPath, "utf-8");

const output =
  process.argv[3] ??
  join(dirname(inputPath).replace(/sources$/, "images"), basename(input, ".mmd") + ".svg");

const outputDir = dirname(output);
mkdirSync(outputDir, { recursive: true });

const svg = renderMermaidSVG(source);
writeFileSync(output, svg);
console.log(`✓ ${input} → ${output}`);
