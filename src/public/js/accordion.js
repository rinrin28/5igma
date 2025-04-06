document.addEventListener('DOMContentLoaded', function() {
    // アコーディオンの要素を取得
    const accordions = document.querySelectorAll('.accordion-container');
    
    accordions.forEach(accordion => {
        const button = accordion.querySelector('.accordion-button');
        const body = accordion.querySelector('.accordion-body');
        const title = accordion.querySelector('.accordion-title');
        
        // ボタンのクリックイベント
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleAccordion(accordion);
        });
        
        // タイトルのクリックイベント
        if (title) {
            title.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleAccordion(accordion);
            });
        }
        
        // メニュー内のクリックイベント
        body.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // アコーディオンの開閉を制御する関数
    function toggleAccordion(targetAccordion) {
        const targetButton = targetAccordion.querySelector('.accordion-button');
        const targetBody = targetAccordion.querySelector('.accordion-body');
        
        // 他のアコーディオンを閉じる
        accordions.forEach(accordion => {
            if (accordion !== targetAccordion) {
                const button = accordion.querySelector('.accordion-button');
                const body = accordion.querySelector('.accordion-body');
                body.classList.add('hidden');
                button.textContent = '▶';
            }
        });
        
        // クリックされたアコーディオンの状態を切り替え
        const isHidden = targetBody.classList.contains('hidden');
        if (isHidden) {
            targetBody.classList.remove('hidden');
            targetButton.textContent = '▼';
        } else {
            targetBody.classList.add('hidden');
            targetButton.textContent = '▶';
        }
    }
    
    // 画面外クリックでメニューを閉じる
    document.addEventListener('click', function() {
        accordions.forEach(accordion => {
            const button = accordion.querySelector('.accordion-button');
            const body = accordion.querySelector('.accordion-body');
            body.classList.add('hidden');
            button.textContent = '▶';
        });
    });
}); 