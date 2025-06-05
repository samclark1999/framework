const fs = require('fs');
const path = require('path');

function kebabCase(str) {
	return str.trim().toLowerCase().replace(/\s+/g, '-');
}

const blockName = process.env.npm_config_block_name;

if (!blockName || blockName.trim() === "") {
	console.error('Please provide a valid block name: npm run block --block-name="Your Block Name"');
	process.exit(1);
}

const sanitizedBlockName = kebabCase(blockName);
const underscoredBlockName = sanitizedBlockName.replace(/-/g, '_');

const blocksDir = path.join(__dirname, 'blocks');
const blockDir = path.join(blocksDir, sanitizedBlockName);

// Check if the directory already exists
if (fs.existsSync(blockDir)) {
	console.error(`A block with the name "${sanitizedBlockName}" already exists.`);
	process.exit(1);
}

// Create the blocks directory if it doesn't exist
if (!fs.existsSync(blocksDir)) {
	fs.mkdirSync(blocksDir);
}

// Create the block directory
fs.mkdirSync(blockDir);

// Create files with default content
const files = [
	{
		name: 'block.json',
		content: `{
	"$schema": "https://schemas.wp.org/trunk/block.json",
	"apiVersion": 2,
	"name": "lvl/${sanitizedBlockName}",
	"title": "${blockName}",
	"version": "1.0.0",
	"description": "",
	"category": "lvl-blocks",
	"keywords": [],
	"icon": "<svg xmlns=\\"http:\\/\\/www.w3.org\\/2000\\/svg\\" height=\\"24px\\" viewBox=\\"0 -960 960 960\\" width=\\"24px\\"><path d=\\"M80-160v-480h120v-160h240v160h80v-160h240v160h120v480H80Zm80-80h640v-320H160v320Zm120-400h80v-80h-80v80Zm320 0h80v-80h-80v80ZM160-240h640-640Zm120-400h80-80Zm320 0h80-80Z\\"\\/><\\/svg>",
	"supports": {
		"align": false,
		"anchor": true,
		"fullHeight": false,
		"html": false,
		"mode": false
	},
	"style": "lvl-block-css-${sanitizedBlockName}",
	"viewScript": ["lvl-block-js-${sanitizedBlockName}"],
	"acf": {
        "mode": "preview",
        "renderTemplate": "${sanitizedBlockName}.php",
		"blockVersion": 2
    }
}`
	},
	{
		name: `${sanitizedBlockName}.php`,
		content: `<?php if (!defined('ABSPATH')) exit;

$render = function ($block, $is_preview, $content) {

    $the_block = new Level\\Block($block);

    ob_start(); ?>

		Block content goes here

    <?php

    $output = ob_get_clean();

    echo $the_block->renderSection($output, 'basic');

};

$render($block, $is_preview, $content);`

	},
	{
		name: path.join('src', `${sanitizedBlockName}.js`),
		content: `document.addEventListener('DOMContentLoaded', (e) => {

	const blocks = document.querySelectorAll('.block--${sanitizedBlockName}');

	function create_block_${underscoredBlockName}() {

		return {
			block: null,

			init: function (block) {
				this.block = block;

			},

			log: (message) => {
				console.log(message);
			}
		}
	}

	blocks.forEach(block => {
		const block_${underscoredBlockName} = create_block_${underscoredBlockName}();
		block_${underscoredBlockName}.init(block);
	});
});`
	},
	{
		name: path.join('src', `${sanitizedBlockName}.scss`),
		content: `@import '../../../src/scss/bare-bs-necessities';

.block--${sanitizedBlockName} {

}

.editor-styles-wrapper {

	.block--${sanitizedBlockName} {
		
	}
}`
	},
];

files.forEach(file => {
	const filePath = path.join(blockDir, file.name);
	const fileDir = path.dirname(filePath);

	// Create directory if it does not exist
	if (!fs.existsSync(fileDir)) {
		fs.mkdirSync(fileDir, { recursive: true });
	}

	fs.writeFileSync(filePath, file.content);
});

console.log(`Block "${sanitizedBlockName}" generated successfully in /blocks!`);