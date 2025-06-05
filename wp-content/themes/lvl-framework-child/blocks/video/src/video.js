document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--video');

    function create_block_video() {

        return {

            block: null,

            init: function (block) {

                this.block = block;
                this.handlers();
            },

            handlers: function () {

                const openHandler = () => {
                    this.open();
                };

                let wrapper = this.block.querySelector('.video-wrapper');
                let backdrop = this.block.querySelector('.backdrop');
                let videoPlayerWrapper = this.block.querySelector('.video-player--wrapper');

                wrapper.addEventListener('click', function (e) {
                    e.stopPropagation();
                    openHandler();
                });
                wrapper.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.stopPropagation();
                        openHandler();
                    }
                });

                // backdrop.addEventListener('click', function () {
                //     openHandler();
                // })
                // backdrop.addEventListener('keydown', function (e) {
                //     if (e.key === 'Enter') {
                //         openHandler();
                //     }
                // });

            },

            open: function () {

                const loadHandler = () => {
                    this.load();
                };

                const unloadHandler = () => {
                    this.unload();
                };

                const expandableDiv = this.block.querySelector('#video-player-' + this.block.id);
                const isExpanded = this.block.classList.contains('expanded');

                if (!isExpanded) {
                    document.body.appendChild(expandableDiv);
                    expandableDiv.classList.add('expanded');

                    // const rect = expandableDiv.getBoundingClientRect();
                    // const top = rect.top;
                    // const left = rect.left;
                    // const width = window.innerWidth * .8;
                    // const height = width * (9 / 16);
                    //
                    // let translateX = window.innerWidth / 2 - width / 2;
                    // let translateY = window.innerHeight / 2 - height / 2;
                    //
                    // if (translateY < 55) {
                    //     translateY = 55;
                    // }
                    //
                    // let x = translateX - left;
                    // let y = translateY - top;
                    //
                    // expandableDiv.style.transform = `translate(${x}px, ${y}px)`;

                    loadHandler();

                    document.body.style.overflow = 'hidden';
                } else {
                    this.block.appendChild(expandableDiv);
                    expandableDiv.classList.remove('expanded');

                    document.body.style.overflow = 'initial';
                    // expandableDiv.style.transform = 'translate(0, 0)';
                    unloadHandler();
                }

                this.block.classList.toggle('expanded');

            },

            load: function () {

                const unloadHandler = () => {
                    this.unload();
                };

                let player = this.block.querySelector('.video');
                let videoPlayer = document.body.querySelector('#video-player-' + this.block.id);

                let src = player.dataset.src;

                const types = [
                    'youtube',
                    'vimeo',
                    'wistia',
                    'vidyard',
                    'hubspot',
                    '.mp4'
                ];
                let type = types.find(t => src.includes(t));
                const closeButton = `<button class="close btn btn-outline-light">CLOSE &times;</button>`;
                const iframeClasses = 'iframe-embed rounded';

                switch (type) {
                    case 'youtube':
                        let youtubeId = src.split('v=').pop();
                        videoPlayer.innerHTML = `${closeButton}<iframe class="${iframeClasses}" width="560" height="315" src="https://www.youtube-nocookie.com/embed/${youtubeId}?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`;
                        break;
                    case 'vimeo':
                        // let vimeoId = src.split('/').pop();
                        let vimeoId = src.replace('https://vimeo.com/', ''); // catches multi ID urls
                        videoPlayer.innerHTML = `${closeButton}<iframe src="https://player.vimeo.com/video/${vimeoId}?autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case 'wistia':
                        let wistiaId = src.split('/').pop();
                        videoPlayer.innerHTML = `${closeButton}<iframe src="https://fast.wistia.net/embed/iframe/${wistiaId}?autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case 'vidyard':
                        let vidyardId = src.split('/').pop();
                        videoPlayer.innerHTML = `${closeButton}<iframe src="https://play.vidyard.com/${vidyardId}.html?v=3.1.1&type=inline" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case 'hubspot':
                        let hubspotId = src.split('/').pop();
                        videoPlayer.innerHTML = `${closeButton}<iframe src="https://app.hubspot.com/video/${hubspotId}" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
                        break;
                    case '.mp4':
                        videoPlayer.innerHTML = `${closeButton}<video aria-label="Media Player" preload="auto" controls autoplay><source type="video/mp4" src="${src}"/></video>`;
                        break;
                    default:
                        console.error('Invalid video type');
                }

                let close = videoPlayer.querySelector('.close');
                close.addEventListener('click', function () {
                    unloadHandler();
                });

                // add event listener to close video player on escape key
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        unloadHandler();
                    }
                });

                // add event listener to close video player if click on video player but not the iframe or btn
                videoPlayer.addEventListener('click', function (e) {
                    if (e.target === this) {
                        unloadHandler();
                    }
                });

            },

            unload: function () {
                let videoPlayer = document.body.querySelector('#video-player-' + this.block.id);
                videoPlayer.innerHTML = '';
                videoPlayer.classList.remove('expanded');
                document.body.style.overflow = 'initial';
                this.block.appendChild(videoPlayer);
                this.block.classList.remove('expanded');

                // let player = this.block.querySelector('.video');
                // player.innerHTML = '';

            },

            log: function () {
                console.log('test');
            }
        }
    }

    blocks.forEach(block => {
        const block_video = create_block_video();
        block_video.init(block);
    });
});