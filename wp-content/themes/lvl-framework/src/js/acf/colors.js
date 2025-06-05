class AcfColorPalette {
    constructor() {
        this.colors = app_localized?.themeJson.settings?.color?.palette;
        this.init();
    }

    init() {
        if (!this.colors) {
            console.error('No color palette defined');
            return;
        }

        acf.add_filter('color_picker_args', this.setupColorPickerArgs.bind(this));
    }

    setupColorPickerArgs(args, field) {
        let palette = [];

        // Register all colors from the color palette
        if (this.colors && Array.isArray(this.colors)) {
            for (let key in this.colors) {
                if (this.colors[key].color) {
                    palette.push(this.colors[key].color);
                }
            }
        }

        args.palettes = palette;
        return args;
    }
}

export default AcfColorPalette;