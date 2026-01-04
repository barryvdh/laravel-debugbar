import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Tabler icons to include
// Format: 'icon-name' for outline icons, or { name: 'icon-name', filled: true } for filled icons
const icons = [
    // Data collector icons
    'archive',
    'clipboard-text',
    'files',
    'lock',
    'user',
    'share-3',
    'subtask',

    // UI control icons
    'brand-laravel',
    'brand-livewire',

    // Query widget icons
    'pin',
    'help-circle',
    'list',
    'gauge',

    // Message icons
    { name: 'star', filled: true },
    'info-circle',
    'circle-check',

    // Link icon
    'external-link',
];

const svgDirOutline = path.join(__dirname, '../node_modules/@tabler/icons/icons/outline');
const svgDirFilled = path.join(__dirname, '../node_modules/@tabler/icons/icons/filled');
const outputFile = path.join(__dirname, '../resources/laravel-icons.css');
const defaultStrokeWidth = 2; // Tabler default stroke width
const brandStrokeWidth = 1; // For brands, use 1

function svgToDataUri(svgContent, strokeWidth) {
    // Remove XML comments
    svgContent = svgContent.replace(/<!--[\s\S]*?-->/g, '');

    // Ensure consistent stroke-width
    svgContent = svgContent.replace(/stroke-width="[^"]*"/g, `stroke-width="${strokeWidth}"`);

    // Remove unnecessary attributes for mask usage (but not stroke-width!)
    svgContent = svgContent.replace(/\s+class="[^"]*"/g, '');
    svgContent = svgContent.replace(/\s+width="[^"]*"/g, '');
    svgContent = svgContent.replace(/\s+height="[^"]*"/g, '');

    // Minify: remove newlines and extra spaces
    svgContent = svgContent.replace(/\s+/g, ' ').trim();

    // URL encode for data URI
    const encoded = encodeURIComponent(svgContent)
        .replace(/'/g, '%27')
        .replace(/"/g, '%22');
    return `data:image/svg+xml,${encoded}`;
}

function generateIconsCSS() {
    let css = `/* Generated file - do not edit manually */\n/* Generated from Tabler Icons */\n\n`;

    // First, define all CSS variables with the SVG data URIs
    css += `:root {\n`;
    for (const iconEntry of icons) {
        const iconName = typeof iconEntry === 'string' ? iconEntry : iconEntry.name;
        const isFilled = typeof iconEntry === 'object' && iconEntry.filled;
        const svgDir = isFilled ? svgDirFilled : svgDirOutline;
        const svgPath = path.join(svgDir, `${iconName}.svg`);

        if (!fs.existsSync(svgPath)) {
            console.warn(`Warning: SVG file not found for icon "${iconName}" at ${svgPath}`);
            continue;
        }

        const svgContent = fs.readFileSync(svgPath, 'utf8');
        let strokeWidth = iconName.indexOf('brand-') === 0 ? brandStrokeWidth : defaultStrokeWidth;
        const dataUri = svgToDataUri(svgContent, strokeWidth);

        css += `  --debugbar-icon-${iconName}: url('${dataUri}');\n`;
    }
    css += `}\n\n`;

    // Then, apply the variables to the icon classes
    for (const iconEntry of icons) {
        const iconName = typeof iconEntry === 'string' ? iconEntry : iconEntry.name;
        const isFilled = typeof iconEntry === 'object' && iconEntry.filled;
        const svgDir = isFilled ? svgDirFilled : svgDirOutline;
        const svgPath = path.join(svgDir, `${iconName}.svg`);

        if (!fs.existsSync(svgPath)) {
            continue;
        }

        css += `.phpdebugbar-icon-${iconName}::before {\n`;
        css += `  -webkit-mask-image: var(--debugbar-icon-${iconName});\n`;
        css += `  mask-image: var(--debugbar-icon-${iconName});\n`;
        css += `}\n\n`;
    }

    fs.writeFileSync(outputFile, css, 'utf8');
    console.log(`✓ Generated ${outputFile} with ${icons.length} icons`);
}

try {
    generateIconsCSS();
} catch (error) {
    console.error('Error generating icons:', error);
    process.exit(1);
}
