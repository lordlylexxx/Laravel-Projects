/**
 * Flood-fills transparency from image edges for pixels matching the average
 * corner colour (typical flat PNG background). Run after updating logos:
 *   node scripts/process-report-header-logos.mjs
 */
import sharp from 'sharp';
import { readFile } from 'fs/promises';
import { dirname, resolve } from 'path';
import { fileURLToPath } from 'url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const files = ['public/report-headers/ca-left-logo.png', 'public/report-headers/ca-right-logo.png'];

function dist(r1, g1, b1, r2, g2, b2) {
    return Math.hypot(r1 - r2, g1 - g2, b1 - b2);
}

async function processEdgeTransparency(absPath, tol = 48) {
    const input = await readFile(absPath);
    const { data, info } = await sharp(input).ensureAlpha().raw().toBuffer({ resolveWithObject: true });
    const w = info.width;
    const h = info.height;
    const idx = (x, y) => (y * w + x) * 4;

    let cr = 0;
    let cg = 0;
    let cb = 0;
    const corners = [
        [0, 0],
        [w - 1, 0],
        [0, h - 1],
        [w - 1, h - 1],
    ];
    for (const [x, y] of corners) {
        const i = idx(x, y);
        cr += data[i];
        cg += data[i + 1];
        cb += data[i + 2];
    }
    cr /= 4;
    cg /= 4;
    cb /= 4;

    const visited = new Uint8Array(w * h);
    const q = [];

    /** @param {boolean} edgeSeed require nearly-opaque pixels when seeding from the image border */
    const enqueue = (x, y, edgeSeed) => {
        if (x < 0 || x >= w || y < 0 || y >= h) {
            return;
        }
        const k = y * w + x;
        if (visited[k]) {
            return;
        }
        const i = idx(x, y);
        const r = data[i];
        const g = data[i + 1];
        const b = data[i + 2];
        const a = data[i + 3];
        if (edgeSeed && a < 200) {
            return;
        }
        if (dist(r, g, b, cr, cg, cb) > tol) {
            return;
        }
        visited[k] = 1;
        q.push(x, y);
    };

    for (let x = 0; x < w; x++) {
        enqueue(x, 0, true);
        enqueue(x, h - 1, true);
    }
    for (let y = 0; y < h; y++) {
        enqueue(0, y, true);
        enqueue(w - 1, y, true);
    }

    if (q.length === 0) {
        return false;
    }

    for (let qi = 0; qi < q.length; qi += 2) {
        const x = q[qi];
        const y = q[qi + 1];
        const i = idx(x, y);
        data[i + 3] = 0;
        enqueue(x + 1, y, false);
        enqueue(x - 1, y, false);
        enqueue(x, y + 1, false);
        enqueue(x, y - 1, false);
    }

    await sharp(data, { raw: { width: w, height: h, channels: 4 } })
        .png({ compressionLevel: 9, adaptiveFiltering: true })
        .toFile(absPath);

    return true;
}

for (const rel of files) {
    const abs = resolve(root, rel);
    try {
        const updated = await processEdgeTransparency(abs);
        console.log(updated ? 'OK' : 'Unchanged', rel);
    } catch (e) {
        console.error('Error', rel, e.message);
    }
}
