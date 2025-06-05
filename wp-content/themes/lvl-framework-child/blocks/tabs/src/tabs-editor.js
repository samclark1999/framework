// document.addEventListener('DOMContentLoaded', (e) => {
//
//     const blocks = document.querySelectorAll('.block--tabs');
//     console.log(blocks)
//
//     function create_preview_tabs() {
//         return {
//
//             block: null,
//             id: null,
//             tabs: null,
//             panels: null,
//
//             init: function (block) {
//                 console.log('init')
//                 this.block = block;
//
//                 this.tabs = this.block.querySelectorAll('.nav');
//                 this.panels = this.block.querySelectorAll('.block--tab-panel');
//
//                 this.panels.forEach(panel => {
//                     // create li
//                     const tab = document.createElement('li');
//                     // tab.classList.add('nav-item');
//                     // tab.setAttribute('role', 'presentation');
//                     tab.innnerText = panel.getAttribute('data-title');
//
//                     this.tabs.appendChild(tab);
//                 });
//             }
//         }
//     }
//
//     blocks.forEach(block => {
//         const block_tabs = create_preview_tabs();
//         block_tabs.init(block);
//     });
//
// });