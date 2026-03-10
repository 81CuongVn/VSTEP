import {
  Document,
  Packer,
  Paragraph,
  TextRun,
  HeadingLevel,
  Table,
  TableRow,
  TableCell,
  WidthType,
  BorderStyle,
  AlignmentType,
  ImageRun,
  PageBreak,
  convertMillimetersToTwip,
  TableLayoutType,
  ShadingType,
} from "docx";
import { readFileSync, writeFileSync, existsSync } from "fs";
import { resolve, dirname, extname } from "path";
import { execSync } from "child_process";

const inputPath = process.argv[2];
if (!inputPath) {
  console.error("Usage: bun run scripts/convert-report-docx.ts <markdown-file>");
  process.exit(1);
}

const fullPath = resolve(inputPath);
if (!existsSync(fullPath)) {
  console.error(`File not found: ${fullPath}`);
  process.exit(1);
}

const mdDir = dirname(fullPath);
const outputPath = fullPath.replace(/\.md$/, ".docx");
const md = readFileSync(fullPath, "utf-8");
const lines = md.split("\n");

// --- Inline formatting parser ---
interface RunOpts {
  bold?: boolean;
  italics?: boolean;
  font?: string;
  size?: number;
}

function parseInlineRuns(text: string, baseOpts: RunOpts = {}): TextRun[] {
  const runs: TextRun[] = [];
  // Regex to match: **bold**, *italic*, `code`, or plain text
  const regex = /(\*\*(.+?)\*\*|\*(.+?)\*|`([^`]+?)`|([^*`]+))/g;
  let match: RegExpExecArray | null;
  while ((match = regex.exec(text)) !== null) {
    if (match[2] !== undefined) {
      // bold
      runs.push(new TextRun({ text: match[2], bold: true, font: baseOpts.font ?? "Times New Roman", size: baseOpts.size ?? 26, italics: baseOpts.italics }));
    } else if (match[3] !== undefined) {
      // italic
      runs.push(new TextRun({ text: match[3], italics: true, font: baseOpts.font ?? "Times New Roman", size: baseOpts.size ?? 26, bold: baseOpts.bold }));
    } else if (match[4] !== undefined) {
      // code
      runs.push(new TextRun({ text: match[4], font: "Courier New", size: (baseOpts.size ?? 26) - 2, bold: baseOpts.bold, italics: baseOpts.italics }));
    } else if (match[5] !== undefined) {
      runs.push(new TextRun({ text: match[5], font: baseOpts.font ?? "Times New Roman", size: baseOpts.size ?? 26, bold: baseOpts.bold, italics: baseOpts.italics }));
    }
  }
  if (runs.length === 0) {
    runs.push(new TextRun({ text: text, font: baseOpts.font ?? "Times New Roman", size: baseOpts.size ?? 26 }));
  }
  return runs;
}

// --- Image loading ---
function tryLoadImage(imgPath: string): { data: Buffer; w: number; h: number } | null {
  let resolved = resolve(mdDir, imgPath);

  // If SVG, check for PNG version
  if (extname(resolved).toLowerCase() === ".svg") {
    const pngPath = resolved.replace(/\.svg$/, ".png");
    if (existsSync(pngPath)) {
      resolved = pngPath;
    } else {
      console.warn(`  ⚠ SVG not supported in docx, no PNG fallback: ${imgPath}`);
      return null;
    }
  }

  if (!existsSync(resolved)) {
    console.warn(`  ⚠ Image not found: ${imgPath}`);
    return null;
  }

  try {
    const data = readFileSync(resolved);
    // Default dimensions - reasonable for A4
    return { data: Buffer.from(data), w: 550, h: 350 };
  } catch (e) {
    console.warn(`  ⚠ Failed to read image: ${imgPath}`);
    return null;
  }
}

// --- Table border helper ---
const tableBorder = {
  top: { style: BorderStyle.SINGLE, size: 1, color: "000000" },
  bottom: { style: BorderStyle.SINGLE, size: 1, color: "000000" },
  left: { style: BorderStyle.SINGLE, size: 1, color: "000000" },
  right: { style: BorderStyle.SINGLE, size: 1, color: "000000" },
};

function parseTableRows(tableLines: string[]): (typeof Paragraph)[][] {
  return tableLines.map((line) =>
    line
      .split("|")
      .slice(1, -1)
      .map((cell) => cell.trim())
  ) as any;
}

function buildTable(tableLines: string[]): Table {
  // Filter out separator lines (|---|---|)
  const dataLines = tableLines.filter((l) => !l.match(/^\|[\s\-:|]+\|$/));
  const rows = dataLines.map((line) =>
    line.split("|").slice(1, -1).map((c) => c.trim())
  );

  const numCols = rows[0]?.length ?? 1;

  return new Table({
    layout: TableLayoutType.AUTOFIT,
    width: { size: 100, type: WidthType.PERCENTAGE },
    rows: rows.map((cells, rowIdx) =>
      new TableRow({
        children: cells.map(
          (cellText) =>
            new TableCell({
              borders: tableBorder,
              children: [
                new Paragraph({
                  children: parseInlineRuns(cellText, {
                    bold: rowIdx === 0,
                    size: 24,
                  }),
                  spacing: { line: 276 },
                }),
              ],
              ...(rowIdx === 0
                ? {
                    shading: {
                      type: ShadingType.SOLID,
                      color: "D9E2F3",
                      fill: "D9E2F3",
                    },
                  }
                : {}),
            })
        ),
      })
    ),
  });
}

// --- Main conversion ---
const elements: (Paragraph | Table)[] = [];
let i = 0;

while (i < lines.length) {
  const line = lines[i];

  // Page break
  if (line.trim() === "---") {
    elements.push(
      new Paragraph({
        children: [new PageBreak()],
      })
    );
    i++;
    continue;
  }

  // Code block
  if (line.trim().startsWith("```")) {
    const lang = line.trim().slice(3);
    console.log(`  Processing code block${lang ? ` (${lang})` : ""}`);
    i++;
    const codeLines: string[] = [];
    while (i < lines.length && !lines[i].trim().startsWith("```")) {
      codeLines.push(lines[i]);
      i++;
    }
    i++; // skip closing ```

    for (const cl of codeLines) {
      elements.push(
        new Paragraph({
          children: [
            new TextRun({
              text: cl || " ",
              font: "Courier New",
              size: 20,
            }),
          ],
          spacing: { line: 240 },
        })
      );
    }
    continue;
  }

  // Table
  if (line.trim().startsWith("|") && line.trim().endsWith("|")) {
    const tableLines: string[] = [];
    while (i < lines.length && lines[i].trim().startsWith("|") && lines[i].trim().endsWith("|")) {
      tableLines.push(lines[i]);
      i++;
    }
    console.log(`  Processing table (${tableLines.length} rows)`);
    elements.push(buildTable(tableLines));
    continue;
  }

  // Image
  const imgMatch = line.match(/^!\[([^\]]*)\]\(([^)]+)\)/);
  if (imgMatch) {
    const caption = imgMatch[1];
    const imgPath = imgMatch[2];
    console.log(`  Processing image: ${imgPath}`);
    const img = tryLoadImage(imgPath);
    if (img) {
      elements.push(
        new Paragraph({
          children: [
            new ImageRun({
              data: img.data,
              transformation: { width: img.w, height: img.h },
              type: "png",
            }),
          ],
          alignment: AlignmentType.CENTER,
        })
      );
    }
    if (caption) {
      elements.push(
        new Paragraph({
          children: [
            new TextRun({
              text: caption,
              italics: true,
              font: "Times New Roman",
              size: 22,
            }),
          ],
          alignment: AlignmentType.CENTER,
          spacing: { after: 200 },
        })
      );
    }
    i++;
    continue;
  }

  // Headings
  const h4Match = line.match(/^####\s+(.*)/);
  const h3Match = line.match(/^###\s+(.*)/);
  const h2Match = line.match(/^##\s+(.*)/);
  const h1Match = line.match(/^#\s+(.*)/);

  if (h4Match) {
    console.log(`  H4: ${h4Match[1]}`);
    elements.push(
      new Paragraph({
        heading: HeadingLevel.HEADING_4,
        children: parseInlineRuns(h4Match[1], { bold: true, size: 26 }),
        spacing: { before: 240, after: 120, line: 360 },
      })
    );
    i++;
    continue;
  }
  if (h3Match) {
    console.log(`  H3: ${h3Match[1]}`);
    elements.push(
      new Paragraph({
        heading: HeadingLevel.HEADING_3,
        children: parseInlineRuns(h3Match[1], { bold: true, size: 28 }),
        spacing: { before: 240, after: 120, line: 360 },
      })
    );
    i++;
    continue;
  }
  if (h2Match) {
    console.log(`  H2: ${h2Match[1]}`);
    elements.push(
      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: parseInlineRuns(h2Match[1], { bold: true, size: 30 }),
        spacing: { before: 360, after: 120, line: 360 },
      })
    );
    i++;
    continue;
  }
  if (h1Match) {
    console.log(`  H1: ${h1Match[1]}`);
    elements.push(
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: parseInlineRuns(h1Match[1], { bold: true, size: 32 }),
        spacing: { before: 360, after: 200, line: 360 },
      })
    );
    i++;
    continue;
  }

  // List item
  const listMatch = line.match(/^(\s*)[-*]\s+(.*)/);
  if (listMatch) {
    const indent = Math.floor((listMatch[1]?.length ?? 0) / 2);
    elements.push(
      new Paragraph({
        style: "ListParagraph",
        bullet: { level: indent },
        children: parseInlineRuns(listMatch[2]),
        spacing: { line: 360 },
      })
    );
    i++;
    continue;
  }

  // Empty line
  if (line.trim() === "") {
    i++;
    continue;
  }

  // Normal paragraph
  elements.push(
    new Paragraph({
      children: parseInlineRuns(line.trim()),
      spacing: { line: 360, after: 120 },
    })
  );
  i++;
}

// --- Build document ---
console.log(`\nBuilding document with ${elements.length} elements...`);

const doc = new Document({
  styles: {
    default: {
      document: {
        run: {
          font: "Times New Roman",
          size: 26, // 13pt in half-points
        },
        paragraph: {
          spacing: { line: 360 }, // 1.5 line spacing
        },
      },
    },
    paragraphStyles: [
      {
        id: "Normal",
        name: "Normal",
        run: { font: "Times New Roman", size: 26 },
        paragraph: { spacing: { line: 360 } },
      },
      {
        id: "Heading1",
        name: "Heading 1",
        basedOn: "Normal",
        next: "Normal",
        run: { font: "Times New Roman", size: 32, bold: true },
        paragraph: { spacing: { before: 360, after: 200, line: 360 } },
      },
      {
        id: "Heading2",
        name: "Heading 2",
        basedOn: "Normal",
        next: "Normal",
        run: { font: "Times New Roman", size: 30, bold: true },
        paragraph: { spacing: { before: 360, after: 120, line: 360 } },
      },
      {
        id: "Heading3",
        name: "Heading 3",
        basedOn: "Normal",
        next: "Normal",
        run: { font: "Times New Roman", size: 28, bold: true },
        paragraph: { spacing: { before: 240, after: 120, line: 360 } },
      },
      {
        id: "Heading4",
        name: "Heading 4",
        basedOn: "Normal",
        next: "Normal",
        run: { font: "Times New Roman", size: 26, bold: true },
        paragraph: { spacing: { before: 240, after: 120, line: 360 } },
      },
      {
        id: "ListParagraph",
        name: "List Paragraph",
        basedOn: "Normal",
        run: { font: "Times New Roman", size: 26 },
        paragraph: { spacing: { line: 360 } },
      },
    ],
  },
  sections: [
    {
      properties: {
        page: {
          size: {
            width: convertMillimetersToTwip(210),
            height: convertMillimetersToTwip(297),
          },
          margin: {
            top: convertMillimetersToTwip(25.4),
            right: convertMillimetersToTwip(25.4),
            bottom: convertMillimetersToTwip(25.4),
            left: convertMillimetersToTwip(25.4),
          },
        },
      },
      children: elements,
    },
  ],
});

const buffer = await Packer.toBuffer(doc);
writeFileSync(outputPath, buffer);
console.log(`\n✅ Written: ${outputPath}`);
