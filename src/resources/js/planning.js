const targets = document.querySelectorAll('.target');

targets.forEach((el) => {
    el.addEventListener('click', () => {
        if (el.style.borderColor === 'rgb(247, 126, 139)') {
            el.style.borderColor = '#DADEE5';
            el.style.borderWidth = '1px';
        } else {
            targets.forEach(t => {
                t.style.borderColor = '#DADEE5';
                t.style.borderWidth = '1px';
            });
            el.style.borderColor = '#f77e8b'; 
            el.style.borderWidth = '4px';
        }
    });
});

// 施策提案ボタンのイベントハンドラ
document.getElementById('proposalButton').addEventListener('click', async function() {
    const button = this;
    const buttonText = document.getElementById('buttonText');
    const buttonIcon = document.getElementById('buttonIcon');
    const form = document.getElementById('planningForm');
    
    try {
        // ボタンを無効化
        button.disabled = true;
        // テキストを変更
        buttonText.textContent = '生成中...';
        // 画像に回転アニメーションを追加
        buttonIcon.style.animation = 'spin 1s linear infinite';
        
        // フォームデータを取得
        const formData = new FormData(form);
        
        // APIリクエストを送信
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // 成功時の処理
            window.location.reload();
        } else {
            // エラー時の処理
            alert(data.message || '提案の生成に失敗しました。もう一度お試しください。');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('エラーが発生しました。もう一度お試しください。');
    } finally {
        // ボタンを再度有効化
        button.disabled = false;
        // テキストを元に戻す
        buttonText.textContent = '施策提案の更新';
        // 画像の回転アニメーションを停止
        buttonIcon.style.animation = '';
    }
});

// 回転アニメーションのキーフレームを追加
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

window.updateSliderValue = function(val) {
    document.getElementById("slider-value").innerText = `${val}%`;
};

window.addEventListener("DOMContentLoaded", () => {
    const slider = document.getElementById("satisfaction");
    updateSliderValue(slider.value);
});

const buttons = document.querySelectorAll(".period-btn");

buttons.forEach((btn) => {
  btn.addEventListener("click", () => {
    buttons.forEach((b) => {
      if (b.classList.contains("is-default-pink")) {
        // もともとピンク → グレーに戻す
        b.classList.remove("bg-[#FF768D]", "text-white");
        b.classList.add("bg-[#DADEE5]", "text-[#00214D]");
        b.classList.remove("is-default-pink"); // もはやデフォルトピンクじゃないので削除
      } else {
        // グレーに戻す
        b.classList.remove("bg-[#FF768D]", "text-white");
        b.classList.add("bg-[#DADEE5]", "text-[#00214D]");
      }
    });

    // 押したやつはピンクに
    btn.classList.remove("bg-[#DADEE5]", "text-[#00214D]");
    btn.classList.add("bg-[#FF768D]", "text-white");
  });
});

// マイルストーン関連の関数
function addMilestone(index) {
    let newMilestone = { name: '新規マイルストーン', date: '' };
    milestones.splice(index + 1, 0, newMilestone);
    updateMilestones();
}
