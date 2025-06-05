// https://laravel-mix.com/docs/6.0/api
let mix = require('laravel-mix');
require('laravel-mix-clean');
require('laravel-mix-webp')

const path = require('path');
const glob = require('glob');
const {existsSync} = require("node:fs");

mix.options({
    processCssUrls: false,
})

// e.g. npm run dev --assets=images OR npm run images
let assets = false;

if (process.env.assets) {
    console.log('assets', process.env.assets);
    assets = process.env.assets;
}

// if not looking to compile assets then process other tasks
if (!assets || mix.inProduction()) {
    // js and scss variants
    // '-alt',
    // '-editor',
    // '-init',

    /**
     * COMPILE BLOCK SCRIPTS
     **/
    const blockScripts = glob.sync('blocks/**/src/*.js');
    blockScripts.forEach(file => {
        let dir = path.join(path.dirname(file), '../');
        let name = path.basename(file, '.js');
        mix.js(`${file}`, `${dir}/dist/${name}.min.js`);
    });

    // Concatenate all block JS files into one blocks.min.js file, excluding '-editor' variants
    const blockJsFiles = glob.sync('blocks/**/dist/*.min.js').filter(file => !file.includes('-editor'));
    mix.scripts(blockJsFiles, 'dist/js/blocks.min.js');


    /**
     * COMPILE BLOCK STYLES
     **/
    const blockStyles = glob.sync('blocks/**/src/*.scss');
    blockStyles.forEach(file => {
        let dir = path.join(path.dirname(file), '../');
        let name = path.basename(file, '.scss');
        mix.sass(`${file}`, `${dir}/dist/${name}.min.css`);
    });

    // Concatenate all block CSS files into one blocks.min.css file
    const blockCssFiles = glob.sync('blocks/**/dist/*.min.css').filter(file => !file.includes('-editor'));
    mix.styles(blockCssFiles, 'dist/css/blocks.min.css');

    /**
     * APPEND THEME VARIABLES TO SASS
     **/
    const fs = require('fs');

    /**
     * Generate Bootstrap color overrides from theme.json
     * @returns {string}
     */
    function generateBootstrapColorOverrides() {
        const themeJson = require('./theme.json');
        const colorPalette = themeJson.settings.color.palette || [];

        let sassOverrides = '// Bootstrap color overrides generated from theme.json\n\n';

// Filter colors that have the bs property (either boolean or string)
        let bootstrapColors = colorPalette.filter(color => color?.bs);

        bootstrapColors.forEach(color => {
            // If bs is a string, use that as the variable name, otherwise use the slug
            const varName = typeof color.bs === 'string' ? '$' + color.bs : '$' + color.slug;
            sassOverrides += `${varName}: ${color.color} !default;\n`;
        });

        const themeColorsSkip = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark'];
// filter bootstrapColors, considering the variable name might come from bs property
        bootstrapColors = bootstrapColors.filter(color => {
            const varNameToCheck = typeof color.bs === 'string' ? color.bs : color.slug;
            return !themeColorsSkip.includes(varNameToCheck);
        });

// Build a custom-theme-colors map
        sassOverrides += '\n// Custom theme color variable mappings\n';
        sassOverrides += '$custom-theme-colors: (\n';
        bootstrapColors.forEach(color => {
            // Use the bs string value or slug for the map key
            const varName = typeof color.bs === 'string' ? color.bs : color.slug;
            sassOverrides += `  \"${varName}\": ${color.color},\n`;
        });
        sassOverrides += ') !default;\n';

        // sassOverrides += '\n// 2Theme color variable mappings\n';
        // Object.entries(colorMappings).forEach(([bsVar, themeVar]) => {
        //     sassOverrides += `${bsVar}: ${themeVar} !default;\n`;
        // });
        //
        // // Add the specific variable mappings
        // sassOverrides += '\n// 3Theme color variable mappings\n';
        // Object.entries(colorMappings).forEach(([bsVar, themeVar]) => {
        //     // If we have this color in our palette
        //     if (bootstrapColors.some(color => `$${color.slug.replace('bs-', '')}` === themeVar.replace('$bs-', '$'))) {
        //         sassOverrides += `${bsVar}: ${themeVar} !default;\n`;
        //     }
        // });

        return sassOverrides;
    }

function generateGradientArray() {
    const themeJson = require('./theme.json');
    const gradients = themeJson.settings?.color?.gradients || [];

    if (!gradients.length) {
        return '';
    }

    let sassGradients = '// Gradient variables generated from theme.json\n\n';
    sassGradients += '$gradients: (\n';

    gradients.forEach(gradient => {
        // Parse the gradient string to extract degree and colors
        // Example: "linear-gradient(180deg,#0E1832,#4D57A5)"
        const match = gradient.gradient.match(/linear-gradient\((\d+deg),([^,]+),([^)]+)\)/);

        if (match) {
            const [, deg, startColor, endColor] = match;
            sassGradients += `  "${gradient.slug}": (\n`;
            sassGradients += `    "deg": ${deg},\n`;
            sassGradients += `    "start": ${startColor.trim()},\n`;
            sassGradients += `    "end": ${endColor.trim()}\n`;
            sassGradients += `  ),\n`;
        }
    });

    sassGradients += ') !default;\n\n';

    // Add comment about WordPress variable and class usage
    sassGradients += '// WordPress creates CSS variables like: --wp--preset--gradient--gradient-navy-purple-dark\n';
    sassGradients += '// Class to use WordPress variable: .has-gradient-navy-purple--dark-gradient-background\n';

    return sassGradients;
}

    function generateBootstrapTypographyOverrides() {
        const themeJson = require('./theme.json');
        const typography = themeJson.settings?.typography?.fontSizes || {};

        if (!typography) {
            return '';
        }

        let sassOverrides = '// Bootstrap typography overrides generated from theme.json\n\n';
        let fontBase = typography.filter(font => font.slug === 'body-regular')[0];
        sassOverrides += `$font-size-root: ${fontBase.size} !default;\n`;
        sassOverrides += '$font-size-base: 1rem !default;\n';

        const headings = [
            "h1",
            "h2",
            "h3",
            "h4",
            "h5",
            "h6"
        ]

        headings.forEach(heading => {
            let font = typography.filter(font => font.slug === heading)[0];

            if (!font) {
                return;
            }

            let fontSize = font.size;

            // need to calc the multiplier based on base font and font size
            // e.g. $h1-font-size:                $font-size-base * 2.5 !default;
            let multiplier = (parseFloat(fontSize.replace('px', '')) / parseFloat(fontBase.size.replace('px', ''))).toFixed(3);
            sassOverrides += `$${heading}-font-size: $font-size-base * ${multiplier} !default;\n`;
        })

        return sassOverrides;
    }


    function getThemeVariables() {
        const theme = JSON.parse(fs.readFileSync('theme.json', 'utf8'));

        const palette = theme.settings.color.palette;

        let colors = ''
        let paletteMap = '$palette: (\n';
        palette.forEach((color) => {
            paletteMap += `  brand-${color.slug}: ${color.color},\n`;
            colors += `$brand-${color.slug}: ${color.color};\n`;
        });
        paletteMap += ');\n';

        let wpPaletteMap = '$wpPalette: (\n';
        palette.forEach((color) => {
            wpPaletteMap += `  ${color.slug}: ${color.color},\n`;
        });
        wpPaletteMap += ');\n';

        const headings = Object.entries(theme.styles.elements)
            .filter(([key]) => /^h[1-6]$/.test(key))
            .map(([key, value]) => ({[key]: value}));
        let headingsMap = '$headings: (\n';
        headings.forEach((heading) => {
            const key = Object.keys(heading);
            const {typography} = heading[key];
            const {color} = heading[key];
            headingsMap += `\t("${key}",\n\t${typography.fontFamily},\n\t${typography.fontSize},\n\t${typography.fontWeight},\n\t${typography.lineHeight},\n\t${color.text}),\n`;
        });
        headingsMap += ');\n';

        return paletteMap + wpPaletteMap + colors + headingsMap;
    }

    function getBootstrapOverrides() {
        return generateBootstrapColorOverrides() + generateBootstrapTypographyOverrides() + generateGradientArray();
    }

    // create ./src/compiled folder if doesn't exist
    if (!fs.existsSync('./src/compiled')) {
        fs.mkdirSync('./src/compiled');
    }
    // create ./src/compiled/scss/ folder if doesn't exist
    if (!fs.existsSync('./src/compiled/scss/')) {
        fs.mkdirSync('./src/compiled/scss/');
    }

    // Write bootstrap overrides to a file
    fs.writeFileSync(
        './src/compiled/scss/_bootstrap-overrides.scss',
        getBootstrapOverrides(),
        'utf8'
    );

    // Write theme variables to a file
    fs.writeFileSync(
        './src/compiled/scss/_theme-json-sass-vars.scss',
        '// Variables generated from theme.json\n\n' + getThemeVariables(),
        'utf8'
    );

    // TODO: TESTING...
    // return;

    /**
     * Append theme variables to the top of the main stylesheet
     */
    mix.webpackConfig({
        stats: {
            children: false,
        },
        module: {
            rules: [
                {
                    test: /\.scss$/,
                    loader: 'sass-loader',
                    options: {
                        additionalData: getBootstrapOverrides() + getThemeVariables(),
                    }
                },
            ],
        },
    });

    if (!mix.inProduction()) {
        mix.sourceMaps();
    }

    mix

        // JS
        .js('src/js/app.js', 'dist/js/app.min.js')
        .js('src/js/bootstrap.js', 'dist/js/bootstrap.min.js')
        // .js('src/js/acf.js', 'dist/admin/js/acf.min.js')
        // .js('src/js/forms.js', 'dist/js/forms.min.js')

        // COMPILE JS LIBRARIES INTO THEIR OWN FILES THAT CAN HOUSE CUSTOM CODE
        // .js('src/js/swiper.js', 'dist/js/swiper.min.js')
        // .sass('src/scss/swiper.scss', 'dist/css/swiper.min.css')
        // .js('src/js/swiffy.js', 'dist/js/swiffy.min.js')
        // .js('src/js/countup.js', 'dist/js/countup.min.js')

        // FRONTEND
        .sass('src/scss/style.scss', 'dist/css/app.min.css')
        .sass('src/scss/bootstrap.scss', 'dist/css/bootstrap.min.css')
        // .sass('src/scss/wordpress.scss', 'dist/css/wp.min.css')
        // .sass('src/scss/frontend.scss', 'dist/css/frontend.min.css')

        // ADMIN
        .sass('src/scss/login.scss', 'dist/admin/css/login.min.css')

        // FOR WATCHING
        .js('src/js/editor.js', 'dist/js/editor.min.js')
        .sass('src/scss/editor.scss', 'dist/admin/css/editor.min.css')
        .sass('src/scss/editor/bs.scss', 'dist/admin/css/editor-bs.min.css')
        .sass('src/scss/editor/theme.scss', 'dist/admin/css/editor-theme.min.css')

        // .js('src/js/admin.js', 'dist/admin/js/admin.min.js')
        .sass('src/scss/admin.scss', 'dist/admin/css/admin.min.css')

    // if src/scss/fonts.scss exists then compile it
    if (existsSync('src/scss/fonts.scss')) {
        mix.sass('src/scss/fonts.scss', 'dist/css/fonts.min.css')
    }
}

// ASSETS
// FONTS
if (assets === 'fonts' || mix.inProduction()) {
    // check if directory exists
    if (glob.sync('src/fonts').length) {
        mix
            .clean({
                // dry: true,
                cleanOnceBeforeBuildPatterns: [
                    'dist/fonts/*'
                ]
            })
            .copy('src/fonts', 'dist/fonts');
    }
}
//IMAGES
// if (assets === 'images' || mix.inProduction()) {
//     mix
//         .clean({
//             // dry: true,
//             cleanOnceBeforeBuildPatterns: [
//                 'dist/img/*'
//             ]
//         })
//         .copy('src/img', 'dist/img');
// }

// PRODUCTION ONLY
if (assets === 'webp' || mix.inProduction()) {
    // check if directory exists
    if (glob.sync('src/img').length) {
        mix.ImageWebp({
            from: 'src/img',
            to: 'dist/img',
            imageminWebpOptions: {
                quality: 80
            }
        });
    }
}