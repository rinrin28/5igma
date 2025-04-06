<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>施策継続 - BizBuddy</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/planning.js')
</head>
<body class="font-mono flex flex-col min-h-screen bg-gray-50">
    <!-- ヘッダー -->
    <header class="w-full h-12 bg-customGray flex items-center px-12 rounded-b-3xl z-50 top-0 fixed shadow-xl">
        <div class="flex mx-24">
            <img src="{{ asset('image/flag.png') }}" alt="flag" class="mx-2">
            <h1 class="text-2xl font-bold text-customPink">施策継続</h1>
        </div>
        <nav class="flex items-center space-x-2 text-customNavy" aria-label="">
            <span class="text-lg font-mono">金堂印刷株式会社</span>
            <span class="text-customLightPink" aria-hidden="true">▶</span>
            <a href="#" class="text-sm text-customNavy hover:text-gray-700 font-mono">統括本部</a>
            <span class="text-customNavy" aria-hidden="true">/</span>
            <a href="#" class="text-sm text-customNavy hover:text-gray-700 font-mono">製作部</a>
        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main class="w-full bg-customGray shadow-inner mt-16 rounded-3xl py-4 px-16 flex flex-col">
        <!-- ボトルネック情報 -->
        <section class="bg-customLightPink text-white rounded-t-2xl rounded-b-2xl w-full">
            <div class="px-6 py-2 border-b border-white">
                <h2 class="text-xl font-semibold">主要ボトルネック</h2>
            </div>
            <div class="flex justify-between items-center px-6 py-2">
                <div>
                    <p class="font-bold text-lg">項目{{ $bottleneck['category_id'] ?? '—' }}：</p>
                    <p class="font-bold text-xl">{{ $bottleneckTitle }}</p>
                </div>
                <div class="text-right">
                    <p class="text-base mb-1 mr-44">ボトルネックスコア</p>
                    <p class="text-3xl font-bold leading-none mr-6">
                        {{ $bottleneckScore ?? '—' }}
                        <span class="text-sm">/20</span>
                    </p>
                </div>
            </div>
        </section>
        <div class="bg-customBlue w-1/5 h-8 mx-32"></div>
            <input type="hidden" name="proposal_id" value="">
            <input type="hidden" name="satisfaction" id="satisfactionValue" value="0">
            <input type="hidden" name="milestones" id="milestonesData" value="">
        <div class="flex">
            <!--目標設定-->
            <div class="flex flex-col bg-white rounded-3xl shadow-md w-3/5 py-6">
                <div class="px-6">
                    <div class="flex items-center">
                        <img src="./image/Map.png" alt="map">
                        <p class="text-2xl font-bold text-customNavy">実施計画の設定</p>
                    </div>
                    <p class="text-sm text-gray-500 ml-9">施策の実行に必要な項目を設定します</p>
                </div>
                <div class="border-b border-customLightPink my-2"></div>
                <div class="px-16 my-2 space-y-12">
                    <div>
                        <div class="flex items-center gap-6">
                            <p class="text-customNavy text-xl">短期目標設定</p>
                            <p class="text-gray-500 text-sm">部署の状況を鑑み、次回のパルスサーベイまでに到達可能な目標を考えます</p>
                        </div>
                        <div class="mx-2 my-4">
                            <label for="satisfaction-slider" class="text-customNavy font-bold">満足度（0–100%）</label>
                            <div class="text-customPink flex items-center justify-end font-bold gap-2">
                                <p>目標(0-100%)</p>
                                <p class="text-3xl"><span id="satisfaction-value">15</span>%</p>
                            </div>
                                <input
                                id="satisfaction-slider"
                                type="range"
                                name="slider_value"
                                min="0"
                                max="100"
                                value="{{ old('satisfaction', 15) }}"
                                class="w-full accent-customLightPink"
                                oninput="updateSatisfaction(this.value)"
                            />
                        </div>
                    </div>
                    <div class="">
                        <div class="flex items-center gap-6">
                            <p class="text-customNavy text-xl">期間</p>
                            <p class="text-gray-500 text-sm">次の職場環境改善アンケートまでは一つのボトルネックに取り組みます</p>
                        </div>
                        <div class="flex items-center gap-6 m-3">
                            <button class="px-8 py-2 rounded-lg text-xl text-white bg-customPink">{{ $endDate }} 〜 {{ $fiveMonthsLater}}</button>
                            <p class="text-gray-500 text-sm">※継続判断を含みます</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-6 h-80 my-auto bg-customBlue"></div>
            <!-- マイルストーンの設定 -->
            <div class="bg-white rounded-3xl py-6 shadow-md w-2/5">
                <div class="px-6">
                    <div class="flex items-center">
                        <img src="./image/pin.png" alt="pin" class="mr-2">
                        <p class="text-2xl font-bold text-customNavy">マイルストーンの設定</p>
                    </div>
                    <p class="text-sm text-gray-500 ml-9">施策の実行に必要な項目を設定します</p>
                </div>
                <div class="border-b border-customLightPink my-2"></div>
                <div class="px-6">
                    <div id="editor-area" class="">
                        <div id="editor-timeline"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <div class="w-2/5 flex justify-center">
                <div class="bg-customBlue w-1/2 h-8"></div>
            </div>
        </div>
        <div class="flex flex-col bg-white rounded-3xl py-6 shadow-md w-full">
            <div class="px-6">
                <div class="flex">
                    <img src="./image/checklist.png" alt="checklist">
                    <p class="text-2xl font-bold text-customNavy">トラッキング計画の設定</p>
                </div>
                <p class="text-sm text-gray-500 ml-10">施策によって組織問題が解決されているのかを調査します</p>
            </div>
            <div class="border-b border-customLightPink mt-2"></div>
            <div class="px-16 my-2 space-y-6">
                <div class="flex items-center gap-6">
                    <p class="text-customNavy text-xl">次回パルスサーベイの設定</p>
                    <p class="text-gray-500 text-sm">パルスサーベイによって実行中の施策を評価し、必要に応じて計画を変更します</p>
                </div>
                <div class="flex">
                    <div class="flex flex-col w-1/2">
                        <div class="flex gap-12">
                            <p class="text-customNavy">パルスサーベイの追加</p>
                            <button onclick="location.reload()"
                            class="text-gray-500 hover:text-gray-600 font-bold py-2 px-4 flex items-center">
                                <img src="./image/Autorenew.png" alt="矢印">
                                <p class="text-sm text-customPink">更新</p>
                            </button>
                        </div>
                        <div class="border-2 p-4 my-2 rounded-2xl">
                            <div id="" class="my-4">
                                <div class="p-4 rounded-lg">
                                    <div id="preview-timeline" class=""></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="survey-start"></label>
                            <input type="hidden" id="survey-start">
                            <label for="survey-end"></label>
                            <input type="hidden" id="survey-end">
                        </div>
                    </div>
                    <div class="flex flex-col w-1/2 px-16">
                        <div class="mb-24">
                            <p class="text-customNavy text-lg">パルスサーベイのリマインド</p>
                            <div class="flex flex-col gap-2 my-4">
                                <div class="flex items-center">
                                    <input type="checkbox" class="w-4 h-4 text-customLightPink accent-customLightPink"/>
                                    <p class="text-gray-500 ml-4">サーベイを設定期日に自動送信</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="w-4 h-4 text-customLightPink accent-customLightPink"/>
                                    <p class="text-gray-500 ml-4">サーベイ終了2日前に自動的にリマインドメールを送信</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex">
                            <img src="./image/hint.png" alt="">
                            <p class="text-customNavy text-xl px-4">1ヶ月〜2ヶ月に1度行うことがおすすめです</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <form id="submit-form" action="{{ route('keeping.storeAll') }}" method="POST">
                        @csrf
                        <input type="hidden" name="milestones" id="milestones-json">
                        <input type="hidden" name="survey_start_date" id="hidden-survey-start">
                        <input type="hidden" name="survey_end_date" id="hidden-survey-end">
                        <input type="hidden" name="satisfaction" id="satisfaction-hidden">

                        <div class="flex items-center justify-center bg-customPink rounded-xl px-20 shadow-lg">
                            <img src="./image/Check.png" alt="check" class="">
                            <button type="submit" class="text-white py-4 text-xl">施策計画の確定</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<script>
    function updateSatisfaction(value) {
        document.getElementById('satisfaction-value').textContent = value;
        document.getElementById('satisfaction-hidden').value = value;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const initialValue = document.getElementById('satisfaction-slider').value;
        updateSatisfaction(initialValue);
    });

    let selectedCard = null;

    function selectProposal(element) {
        // 全カードの枠をリセット
        document.querySelectorAll('.target').forEach(card => {
            card.classList.remove('border-[#f77e8b]');
            card.classList.add('border-gray-200');
        });

        // 選択されたカードにピンク枠追加
        element.classList.remove('border-gray-200');
        element.classList.add('border-[#f77e8b]');
        selectedCard = element;

        // data属性から取得
        const title = element.getAttribute('data-title') ?? '';
        const description = element.getAttribute('data-description') ?? '';

        // hidden input に値をセット
        document.getElementById('selected-proposal-title').value = title;
        document.getElementById('selected-proposal-description').value = description;
    }


    document.addEventListener("DOMContentLoaded", () => {
        const editorTimeline = document.getElementById("editor-timeline");
        const previewTimeline = document.getElementById("preview-timeline");
        const form = document.getElementById("submit-form");

        const milestonesInput = document.getElementById("milestones-json");
        const surveyStartInput = document.getElementById("survey-start");
        const surveyEndInput = document.getElementById("survey-end");
        const hiddenSurveyStart = document.getElementById("hidden-survey-start");
        const hiddenSurveyEnd = document.getElementById("hidden-survey-end");

        let milestones = [
            { title: "", date: "", placeholder: "施策の策定" },
            { title: "", date: "", placeholder: "施策共有MTG" },
            { title: "", date: "", placeholder: "施策の開始" }
        ];

        const fixedMilestone = {
            title: "継続判断",
            date: "",
            fixed: true
        };

        function renderEditor() {
            editorTimeline.innerHTML = "";
            milestones.forEach((item, index) => {
                const card = document.createElement("div");
                card.className = "flex items-center justify-between bg-gray-100 p-3 rounded-lg";
                card.innerHTML = `
                    <button type="button" class="delete-btn text-white text-xs bg-gray-400 rounded-full justify-center items-center flex deleteButton">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <input type="text" class="title-input flex-1 bg-transparent border-none focus:ring-0 text-gray-500 font-semibold"
                        value="${item.title}"
                        placeholder="${item.placeholder || 'タイトルを入力'}">
                    <input type="date" class="date-input text-white bg-customLightPink border-none focus:ring-0 rounded-md" value="${item.date}">
                `;

                card.querySelector(".title-input").addEventListener("input", e => {
                    milestones[index].title = e.target.value;
                    renderPreview();
                });
                card.querySelector(".date-input").addEventListener("input", e => {
                    milestones[index].date = e.target.value;
                    renderPreview();
                });
                card.querySelector(".delete-btn").addEventListener("click", () => {
                    milestones.splice(index, 1);
                    renderEditor();
                    renderPreview();
                });

                editorTimeline.appendChild(card);

                const addBtnWrap = document.createElement("div");
                addBtnWrap.className = "flex";
                addBtnWrap.innerHTML = `<div class="mx-12 flex">
                                            <div class="border-r-2 border-r-customBlue"></div>
                                            <div class="border-r-2 border-r-customBlue ml-1"></div>
                                        </div>
                                        <button class="add-btn w-1/2 bg-white border shadow-md rounded text-gray-600 my-4">＋</button>`;
                addBtnWrap.querySelector("button").addEventListener("click", () => {
                    milestones.splice(index + 1, 0, { title: "", date: "", placeholder: "新規マイルストーン" });
                    renderEditor();
                    renderPreview();
                });

                editorTimeline.appendChild(addBtnWrap);
            });

            const fixed = document.createElement("div");
            fixed.className = "flex items-center justify-between bg-gray-100 p-3 rounded-lg";
            fixed.innerHTML = `
                <p class="flex-1 bg-transparent border-none focus:ring-0 text-gray-500 font-semibold">${fixedMilestone.title}</p>
                <p class="text-red-500 bg-transparent border-none focus:ring-0">パルスサーベイ翌日</p>
            `;
            editorTimeline.appendChild(fixed);
        }

        function renderPreview() {
        previewTimeline.innerHTML = "";

        milestones.forEach((item) => {
            const card = document.createElement("div");
            card.className = "";
            card.innerHTML = `
                <div class="flex items-center justify-between bg-gray-100 p-3 rounded-lg">
                    <p class="flex-1 bg-transparent border-none focus:ring-0 text-gray-500 font-semibold">${item.title || item.placeholder || '（タイトル未入力）'}</p>
                    <p class="text-red-500 bg-transparent border-none focus:ring-0 font-semibold">${item.date || '（日付未入力）'}</p>
                </div>
                <div class="mx-12 flex h-8">
                    <div class="border-r-2 border-r-customBlue"></div>
                    <div class="border-r-2 border-r-customBlue ml-1"></div>
                </div>
            `;
            previewTimeline.appendChild(card);
        });

        const surveyForm = document.createElement("div");
        surveyForm.className = "bg-red-200 rounded-lg";
        surveyForm.innerHTML = `
            <div class="flex items-center bg-customLightPink rounded-lg">
                <p class="flex-1 bg-transparent border-none focus:ring-0 text-white ml-4">パルスサーベイ</p>
                <input type="date" id="survey-start" name="survey_start" class="text-customPink bg-white rounded-md border-none focus:ring-0 m-1"
                value="${surveyStartInput.value}">
            </div>
            <div class="mx-12 flex h-8">
                <div class="border-r-2 border-r-customBlue"></div>
                <div class="border-r-2 border-r-customBlue ml-1"></div>
            </div>
            <div class="flex items-center bg-customLightPink rounded-lg">
                <p class="flex-1 bg-transparent border-none focus:ring-0 text-white ml-4">パルスサーベイ</p>
                <input type="date" id="survey-end" name="survey_end" class="text-customPink bg-white rounded-md border-none focus:ring-0 m-1"
                value="${surveyEndInput.value}">
            </div>
        `;

        previewTimeline.appendChild(surveyForm);

        surveyForm.querySelector("#survey-start").addEventListener("input", (e) => {
            surveyStartInput.value = e.target.value;
        });
        surveyForm.querySelector("#survey-end").addEventListener("input", (e) => {
            surveyEndInput.value = e.target.value;
            fixedMilestone.date = e.target.value;
            renderPreview();
        });

        const fixedCard = document.createElement("div");
        fixedCard.className = "";
        fixedCard.innerHTML = `
            <div class="mx-12 flex h-8">
                <div class="border-r-2 border-r-customBlue"></div>
                <div class="border-r-2 border-r-customBlue ml-1"></div>
            </div>
            <div class="flex items-center justify-between bg-gray-100 p-3 rounded-lg">
                <p class="flex-1 bg-transparent border-none focus:ring-0 text-gray-500">${fixedMilestone.title}</p>
                <p class="text-red-500 bg-transparent border-none focus:ring-0">${fixedMilestone.date || 'パルスサーベイ翌日'}</p>
            </div>
        `;
        previewTimeline.appendChild(fixedCard);

        milestonesInput.value = JSON.stringify([
            ...milestones.map(m => ({
                title: m.title || m.placeholder,
                date: m.date
            })),
            {
                title: fixedMilestone.title,
                date: fixedMilestone.date,
                fixed: true
            }
        ]);
    }

        surveyEndInput.addEventListener("input", e => {
            fixedMilestone.date = e.target.value;
            renderPreview();
        });

        form.addEventListener("submit", (e) => {
            hiddenSurveyStart.value = surveyStartInput.value;
            hiddenSurveyEnd.value = surveyEndInput.value;
        });

        renderEditor();
        renderPreview();
    });
</script>


    </main>

    <script>
    // フォームバリデーション
    function validateForm() {
        const proposalId = document.querySelector('input[name="proposal_id"]').value;
        if (!proposalId) {
            alert('提案を選択してください');
            return false;
        }

        const milestones = document.querySelectorAll('#milestones .milestone');
        if (milestones.length === 0) {
            alert('マイルストーンを少なくとも1つ追加してください');
            return false;
        }

        const pulseSurveyStart = document.querySelector('input[name="pulseSurveyStart"]').value;
        const pulseSurveyEnd = document.querySelector('input[name="pulseSurveyEnd"]').value;
        if (!pulseSurveyStart || !pulseSurveyEnd) {
            alert('パルスサーベイの期間を設定してください');
            return false;
        }

        // マイルストーンデータをJSON形式で準備
        const milestonesData = [];
        milestones.forEach(milestone => {
            const name = milestone.querySelector('input[type="text"]').value;
            const date = milestone.querySelector('input[type="date"]').value;
            if (name && date) {
                milestonesData.push({ name, date });
            }
        });
        
        // 満足度の値を取得
        const satisfactionValue = document.getElementById('satisfaction').value;
        
        // 隠しフィールドに値を設定
        document.getElementById('satisfactionValue').value = satisfactionValue;
        document.getElementById('milestonesData').value = JSON.stringify(milestonesData);

        return true;
    }
    </script>
</body>
</html>
