import '@splidejs/splide/css';
import Splide from '@splidejs/splide';

document.addEventListener('DOMContentLoaded', () => {
    const splide = new Splide('.splide', {
        type: 'loop',
        perPage: 1,
        pagination: false,
        arrows: false,
        drag: false, // スワイプを無効にしたい場合
    });

    splide.mount();

    // 「次へ」ボタン用
    document.querySelectorAll('.next-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            splide.go('>'); // Splideの次のスライドへ移動
        });
    });
});