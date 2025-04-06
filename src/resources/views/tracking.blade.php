<x-app-layout>
    <div class="flex gap-x-3">
        <section class="flex flex-col w-2/3 gap-y-3">
            <div class="bg-white rounded-3xl px-8 py-4 flex justify-between">
                <div>
                    <div class="flex">
                        <img src="./image/Vector.png" alt="vector" class="mr-4">
                        <h3 class="text-3xl font-bold text-customNavy">施策タイムライン</h3>
                    </div>
                    <p class="text-gray-500 font-bold ml-12 p-1">施策の決定、調節や評価、継続判断を行います</p>
                </div>
                    <button onclick="location.reload()"
                        class="text-gray-500 hover:text-gray-600 font-bold py-2 px-4 flex items-center">
                        <img src="./image/Autorenew.png" alt="矢印">
                        <p class="text-sm text-customPink">更新</p>
                    </button>
            </div>

            <div class="flex bg-white rounded-3xl px-8 py-4 border-4 border-customPink" id="top">
                <div class="flex flex-col w-4/5 text-customNavy">
                    <div class="flex items-center">
                        <h3 class="text-3xl font-bold text-customNavy">長期目標の設定</h3>
                    </div>
                    <div class="border-b border-gray-300 my-2"></div>
                    <div class="flex">
                        <div class="flex items-center px-2 w-full">
                            <div class="mx-4 my-6 w-full">
                                <label for="satisfaction" class="text-customNavy font-bold text-xl block mb-1">満足度（0~100%）</label>
                                <div class="text-customPink flex items-center justify-end font-bold gap-2 mb-2">
                                    <p>目標(0~100%)</p>
                                    <p class="text-4xl" id="slider-value">15%</p>
                                </div>
                                <!-- スライダー-->
                                <div class="relative w-[500px] h-6 mt-2 rounded-full overflow-hidden bg-gray-300">
                                    <div id="latest-bar" class="absolute h-full bg-customBlue opacity-70 rounded-full z-0" style="width: {{round($latestSatisfaction)}}%;" value="{{round($latestSatisfaction)}}"></div>
                                    <input
                                    id="satisfaction"
                                    type="range"
                                    min="0"
                                    max="100"
                                    value="15"
                                    class="relative z-10 w-full appearance-none bg-transparent pointer-events-auto"
                                    oninput="updateSlider(this.value)"
                                    />
                                    <style>
                                    input[type="range"] {
                                        height: 100%;
                                    }
                                    input[type="range"]::-webkit-slider-runnable-track {
                                        height: 100%;
                                        background: linear-gradient(to right, #FF768D 0%, #FF768D var(--val, 15%), transparent var(--val, 15%), transparent 100%);
                                        border-radius: 9999px;
                                    }
                                    input[type="range"]::-webkit-slider-thumb {
                                        -webkit-appearance: none;
                                        appearance: none;
                                        background: #FF768D;
                                        width: 30px;
                                        height: 30px;
                                        border-radius: 9999px;
                                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                                        margin-top: -3px;
                                        cursor: pointer;
                                        margin-right:2px
                                    }
                                    input[type="range"]::-moz-range-track {
                                        background: #FF768D;
                                        height: 100%;
                                        border-radius: 9999px;
                                    }

                                    input[type="range"]::-moz-range-thumb {
                                        background: #FF768D;
                                        border: none;
                                        width: 30px;
                                        height: 30px;
                                        border-radius: 50%;
                                        cursor: pointer;
                                    }
                                    </style>
                                </div>
                                <p class="text-customBlue text-lg mt-2 font-bold">
                                    最新の満足度 <span id="latest-val">{{round($latestSatisfaction)}}%</span>
                                </p>
                            </div>
                            <script>
                                function updateSlider(val) {
                                    document.getElementById('slider-value').textContent = `${val}%`;
                                    document.getElementById('satisfaction').style.setProperty('--val', val + '%');
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div class="w-1/5">
                    <button class="bg-customPink rounded-xl w-full text-2xl py-2 mt-28">
                        <p class="text-white text-lg font-bold">目標を確定</p>
                    </button>
                </div>
            </div>
            <div class="flex bg-white rounded-3xl px-8 py-4 border-4 border-customPink">
                <div class="flex flex-col w-4/5 text-customNavy">
                    <div class="flex items-center">
                        <button id="accordionToggle" class="relative group mr-2">
                            <svg id="accordionIcon" class="w-8 h-8 text-[#82868B] transition-transform duration-500 rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16">
                                <path d="M6 12l4-4-4-4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="absolute top-[20px] left-1/2 -translate-x-1/2 w-[1.5px] h-[8px] bg-white transition-all duration-500"></span>
                        </button>
                        <h3 class="text-3xl font-bold">施策・短期目標の設定</h3>
                    </div>
                    <div id="accordionContent" class="transition-all duration-500 overflow-hidden">
                        <div class="border-b border-gray-300 my-1"></div>
                        <div class="flex items-center px-2">
                            <img src="./image/memo.png" alt="" class="mr-2">
                            <textarea id="auto-resize-textarea" class="border-none outline-none focus:outline-none focus:ring-0 w-full resize overflow-hidden h-auto" rows="1"></textarea>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const textarea = document.getElementById('auto-resize-textarea');
                                    textarea.addEventListener('input', function() {
                                        textarea.style.height = 'auto';
                                        textarea.style.height = textarea.scrollHeight + 'px';
                                    });
                                    textarea.style.height = 'auto';
                                    textarea.style.height = textarea.scrollHeight + 'px';
                                });
                            </script>
                        </div>
                        <div class="border-b border-gray-300 my-1"></div>
                        <button onclick="location.href='{{ route('planning', ['dept_id' => $currentDeptId]) }}'" class="bg-customPink flex text-white px-4 py-4 rounded-xl mt-2 w-full">
                            <img src="./image/homeicon.png" alt="" class="mx-3 w-8">
                            <p class="font-bold text-2xl mr-auto">施策提案</p>
                            <p class="">>></p>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid gap-y-3">
                @foreach ($items as $index => $item)
                    @if ($item->type === 'pulse_end')
                        @continue
                    @endif
                    @if ($item->type === 'pulse_start')
                    <div class="flex bg-white rounded-3xl px-8 py-4 border-4 border-customPink">
                    @elseif ($item->type === 'task')
                    <div class="flex bg-white rounded-3xl px-8 py-4 border-4 shadow-lg">
                    @endif
                        <div class="flex flex-col w-4/5 text-gray-500">
                            <div class="flex items-center">
                                <button id="accordionToggle{{ $index }}" class="relative group mr-2" onclick="toggleAccordion({{ $index }})">
                                    <svg id="accordionIcon{{ $index }}" class="w-8 h-8 text-[#82868B] transition-transform duration-500 rotate-90"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16">
                                        <path d="M6 12l4-4-4-4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="absolute top-[20px] left-1/2 -translate-x-1/2 w-[1.5px] h-[8px] bg-white transition-all duration-500"></span>
                                </button>
                                @if ($item->type === 'pulse_start')
                                    <h3 class="text-3xl font-bold text-customPink">パルスサーベイ</h3>
                                @elseif ($item->type === 'task')
                                    <h3 class="text-3xl font-bold text-gray-500">{{ $item->name }}</h3>
                                @endif
                            </div>
                            <div id="accordionContent{{ $index }}" class="transition-all duration-500 overflow-hidden">
                                <div class="border-b border-gray-300 my-1"></div>
                                <div class="flex items-center px-2">
                                    <img src="/image/memo.png" alt="" class="mr-2">
                                    <textarea class="border-none outline-none focus:outline-none focus:ring-0 w-full resize overflow-hidden h-auto" oninput="autoResize(this)" rows="1"></textarea>
                                </div>
                                <div class="border-b border-gray-300 my-1"></div>
                                @if($item->type === 'pulse_start')
                                <div class="flex items-center">
                                    <img src="./image/Calendarclock.png" alt="clock" class="ml-1 mr-3">
                                    <p class="text-gray-500">設定期日にサーベイが自動送信されます</p>
                                </div>
                                @elseif($item->type === 'task')
                                <div class="flex items-center">
                                    <input type="checkbox" class="w-4 h-4 text-customLightPink accent-customLightPink"/>
                                    <p class="text-gray-500 ml-4">前日に自動的にリマインドメールを送信</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="w-1/5">
                            <div class="flex items-center">
                                <p class="text-gray-500 px-2 py-1">{{ \Carbon\Carbon::parse($item->date)->format('Y/m/d') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex flex-col bg-white rounded-3xl px-8 py-4 border-4 border-customPink">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-customPink">パルスサーベイ 調査結果</h1>
                        <p class="text-lg text-gray-500">施策の浸透状況に関するアンケートの調査結果です</p>
                    </div>
                    <div class="text-sm text-gray-500 flex items-center font-bold">
                        <img src="{{ asset('image/calendar.png') }}" alt="Calendar" class="w-6 h-6 mr-2">
                        〜{{ $survey->end_date->format('Y/m/d') }}
                    </div>
                </div>
                @if ($answeredUsersCount > 0)
                <div class="w-full h-[1px] bg-customGray2 mx-auto my-2"></div>
                <!-- 集計情報 -->
                <div class="flex flex-wrap items-end gap-4 w-full ml-10">
                    <div class="flex items-end w-1/4">
                        <div class="flex items-end">
                            <span class="text-sm text-customGray3 mr-2">有効回答数</span>
                            <span class="text-3xl font-semibold text-customGray3">{{ $answeredUsersCount }}</span>
                            <span class="text-sm text-customGray3">/{{ $userCount }}件</span>
                        </div>
                    </div>
                    <div class="flex items-end w-1/4">
                        <span class="text-sm text-customGray3 mr-2">回答率</span>
                        <span class="text-3xl font-semibold text-customGray3">{{ $responseRate }}</span>
                        <span class="text-sm text-customGray3">％</span>
                    </div>
                    <div class="flex items-end w-1/4 text-customPink">
                        <span class="text-sm mr-2">ボトルネック満足度</span>
                        <span class="text-3xl font-semibold">{{ $satisfactionPercentage }}</span>
                        <span class="text-sm">％</span>
                    </div>
                </div>
                @foreach ($questions as $index => $question)
                    <div class="w-full h-[1px] bg-customGray2 mx-auto my-4"></div>
                    <div class="">
                        <!-- 質問 -->
                        <div class="mb-[15px]">
                            <p class="text-[16px] text-customNavy font-bold">
                                {{ $index + 1 }}. {{ $question->question }}
                            </p>
                        </div>
                        <!-- グラフ -->
                        <div class="w-full bg-gray-200 h-[26px] overflow-hidden flex">
                            @foreach (['5' => 'customBlue', '4' => 'customLightBlue', '3' => 'customGray2', '2' => 'customLightPink2', '1' => 'customLightPink'] as $key => $color)
                                @if ($questionAnswers[$question->id][$key] > 0)
                                    <div class="bg-{{ $color }} h-full flex items-center justify-center"
                                        style="width: {{ $questionAnswers[$question->id][$key] }}%;">
                                        <span class="text-customNavy40 text-[11px] font-bold">
                                            {{ $questionAnswers[$question->id][$key] }}%
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <!-- 凡例 -->
                        <div class="flex items-center gap-4 mt-4 transform scale-70">
                            <div class="flex items-center gap-2">
                                <div class="w-[10px] h-[10px] bg-customBlue"></div>
                                <span class="text-sm text-customGray3">している</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-[10px] h-[10px] bg-customLightBlue"></div>
                                <span class="text-sm text-customGray3">ややしている</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-[10px] h-[10px] bg-customGray2"></div>
                                <span class="text-sm text-customGray3">普通</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-[10px] h-[10px] bg-customLightPink2"></div>
                                <span class="text-sm text-customGray3">ややしていない</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-[10px] h-[10px] bg-customLightPink"></div>
                                <span class="text-sm text-customGray3">していない</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>
            <div class="flex flex-col bg-white rounded-3xl px-8 py-4 border-4 border-customPink">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center">
                            <h1 class="text-3xl font-bold text-customPink">継続判断</h1>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 flex items-center font-bold">
                        <img src="{{ asset('image/calendar.png') }}" alt="Calendar" class="w-6 h-6 mr-2">
                        {{ $survey->end_date->format('Y/m/d') }}
                    </div>
                </div>
                @if ($answeredUsersCount > 0)
                <div class="w-full h-[1px] bg-customGray2 mx-auto my-2"></div>
                <button onclick="window.location.href='{{ route('keeping', ['dept_id' => $currentDeptId]) }}'" class="w-[280px] h-[53px] bg-customPink rounded-xl flex items-center justify-center mx-auto shadow-md font-semibold text-xl text-white">
                        施策の継続
                </button>
                <div class="text-center text-[14px] font-bold text-customGray3 p-2">または</div>
                <div class="flex">
                    <button id="scrollToTopBtn" class="w-1/3 bg-customLightPink2 rounded-lg flex items-center justify-center mx-auto shadow-md hover:bg-customLightPink">
                        <span class="text-[16px] font-semibold text-customWhite">
                            長期目標の修正
                        </span>
                    </button>
                    <button onclick="location.href='{{ route('planning', ['dept_id' => $currentDeptId]) }}'" class="w-1/3 h-[43px] bg-customLightPink2 rounded-lg flex items-center justify-center mx-auto shadow-md hover:bg-customLightPink">
                        <span class="text-[16px] font-semibold text-customWhite">
                            施策・目標の変更
                        </span>
                    </button>
                </div>
                @endif
            </div>
        </section>
        <!--マイルストーン-->
        <section class="flex flex-col w-1/3 bg-white rounded-3xl  px-6 py-4">
            <div class="flex">
                <img src="./image/pin.png" alt="pin" class="mr-4">
                <h4 class="text-2xl font-bold text-customNavy">マイルストーン</h4>
            </div>
            <div class="my-3 space-y-2">
                <div class="flex border shadow-md rounded-lg px-3 py-2 text-customNavy font-bold items-center">
                    <p class="mr-auto">現在の設定目標</p>
                    <div class="flex items-center">
                        <p class="">満足度</p>
                        <p class="text-customBlue mx-3 font-zenkaku">{{round($latestSatisfaction)}}%</p>
                        <span class="text-customLightPink text-xs">▶</span>
                        <p class="text-customPink mx-3 font-zenkaku"><span class="text-3xl">
                            {{ $latestProposal->target_score ?? '-' }}</span>%</p>
                    </div>
                </div>
                <div class="flex border shadow-md rounded-lg px-3 py-2 text-customNavy items-center">
                    <p class="mr-auto w-1/3 font-bold">実行中の施策</p>
                    <div class="flex flex-col w-2/3">
                        <p class="font-bold">{{ $latestProposal->proposal ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $latestProposal->description ?? '-' }}</p>
                    </div>
                </div>
            </div>
<!---->
            <div class="w-full max-w-md">
                <form id="inputForm">
                    <div id="inputContainer" class="">
                        @forelse ($items as $item)
                            <div class="input-group-wrapper">
                                @if ($item->type === 'task')
                                    <div class="flex items-center bg-gray-100 px-4 rounded-lg">
                                        <button type="button" class="text-white text-xs bg-gray-400 rounded-full justify-center items-center flex deleteButton" data-id="{{ $item->id }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <input type="text" name="names[]" value="{{ $item->name }}" class="flex-1 bg-transparent border-none focus:ring-0 text-gray-500 font-semibold" />
                                        <input type="date" name="dates[]" value="{{ $item->date }}" class="text-red-500 bg-transparent border-none focus:ring-0" />
                                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                                    </div>
                                @elseif ($item->type === 'pulse_start')
                                    <div class="flex items-center bg-customLightPink rounded-lg px-4 py-2">
                                        <p class="flex-1 bg-transparent border-none focus:ring-0 text-white ml-4 font-semibold">パルスサーベイ</p>
                                        <div class="text-white border-none focus:ring-0 m-1">{{ $item->date }}</div>
                                        <input type="hidden" name="pulse_ids[]" value="{{ $item->id }}">
                                    </div>
                                @elseif ($item->type === 'pulse_end')
                                    <div class="flex items-center bg-customLightPink rounded-lg px-4 py-2">
                                        <p class="flex-1 bg-transparent border-none focus:ring-0 text-white ml-4 font-semibold">パルスサーベイ</p>
                                        <div class="text-white border-none focus:ring-0 m-1">{{ $item->date }}</div>
                                        <input type="hidden" name="pulse_ids[]" value="{{ $item->id }}">
                                    </div>
                                @endif
                                <div class="flex addInputButton">
                                    <div class="mx-12 flex">
                                        <div class="border-r-2 border-r-customBlue"></div>
                                        <div class="border-r-2 border-r-customBlue ml-1"></div>
                                    </div>
                                    <button type="button" class=" w-1/2 bg-white border shadow-md rounded text-gray-600 my-4">＋</button>
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center mt-10">
                                <img src="./image/logo.png" alt="" class="w-10 h-10">
                                <div class="text-gray-400 font-semibold text-center py-4">
                                    現在マイルストーンはありません
                                </div>
                            </div>
                        @endforelse
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        function createInputGroup() {
            const inputGroup = document.createElement('div');
            inputGroup.className = 'flex items-center bg-gray-100 px-4 rounded-lg mb-1';
    
            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.name = 'names[]';
            nameInput.placeholder = '新規マイルストーン';
            nameInput.className = 'flex-1 bg-transparent border-none focus:ring-0 text-gray-500 font-semibold';
    
            const dateInput = document.createElement('input');
            dateInput.type = 'date';
            dateInput.name = 'dates[]';
            dateInput.className = 'text-red-500 bg-transparent border-none focus:ring-0';
    
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'ids[]';
            idInput.value = '';
    
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'text-white text-xs bg-gray-400 rounded-full justify-center items-center flex deleteButton';
    
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('class', 'w-3 h-3');
            svg.setAttribute('fill', 'none');
            svg.setAttribute('stroke', 'currentColor');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('stroke-linejoin', 'round');
            path.setAttribute('stroke-width', '2');
            path.setAttribute('d', 'M6 18L18 6M6 6l12 12');
    
            svg.appendChild(path);
            deleteButton.appendChild(svg);
    
            deleteButton.addEventListener('click', function () {
                const id = idInput.value;
                inputGroup.remove();
                if (id) {
                    fetch('{{ route("delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id })
                    });
                }
            });
            inputGroup.appendChild(deleteButton);
            inputGroup.appendChild(nameInput);
            inputGroup.appendChild(dateInput);
            inputGroup.appendChild(idInput);
            return inputGroup;
        }
    
        document.querySelectorAll('.addInputButton').forEach(button => {
            button.addEventListener('click', function () {
                const wrapper = button.closest('.input-group-wrapper');
                const newInputGroup = createInputGroup();
                wrapper.parentNode.insertBefore(newInputGroup, button.parentNode.nextSibling);
            });
        });
    
        document.querySelectorAll('.deleteButton').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                this.closest('div').remove();
                if (id) {
                    fetch('{{ route("delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id })
                    });
                }
            });
        });
        function sendDataToDB(name, date, idInput) {
            if (!name || !date) return;
            const id = idInput.value || null;
            fetch('{{ route("insert") }}', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, date, id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) idInput.value = data.id;
            });
        }
    
        document.getElementById('inputForm').addEventListener('change', function (event) {
            const inputGroup = event.target.closest('div');
            if (!inputGroup) return;
            const nameInput = inputGroup.querySelector('input[name="names[]"]');
            const dateInput = inputGroup.querySelector('input[name="dates[]"]');
            const idInput = inputGroup.querySelector('input[name="ids[]"]');
            const name = nameInput?.value ?? '';
            const date = dateInput?.value ?? '';
            sendDataToDB(name, date, idInput);
        });

        document.addEventListener('DOMContentLoaded', () => {
        const icon = document.getElementById('accordionIcon');

        document.getElementById('accordionToggle').addEventListener('click', () => {
            icon.classList.toggle('rotate-90');  
        });
        });
        document.addEventListener('DOMContentLoaded', () => {
        const icon = document.getElementById('accordionIcon');
        const content = document.getElementById('accordionContent');
        const toggle = document.getElementById('accordionToggle');
        let isOpen = true;
        content.style.maxHeight = content.scrollHeight + 'px';
        toggle.addEventListener('click', () => {
            isOpen = !isOpen;
            if (isOpen) {
            content.style.maxHeight = content.scrollHeight + 'px';
            } else {
            content.style.maxHeight = '0px';
            }
        });
        });

        document.addEventListener('DOMContentLoaded', () => {
        const icon2 = document.getElementById('accordionIcon2');
        const content2 = document.getElementById('accordionContent2');
        const toggle2 = document.getElementById('accordionToggle2');
        let isOpen2 = true;

        content2.style.maxHeight = content2.scrollHeight + 'px';

        toggle2.addEventListener('click', () => {
            isOpen2 = !isOpen2;
            icon2.classList.toggle('rotate-90');

            if (isOpen2) {
            content2.style.maxHeight = content2.scrollHeight + 'px';
            } else {
            content2.style.maxHeight = '0px';
            }
        });
        });

        document.addEventListener('DOMContentLoaded', () => {
        const icon3 = document.getElementById('accordionIcon3');
        const content3 = document.getElementById('accordionContent3');
        const toggle3 = document.getElementById('accordionToggle3');
        let isOpen3 = true;

        content3.style.maxHeight = content2.scrollHeight + 'px';

        toggle3.addEventListener('click', () => {
            isOpen3 = !isOpen3;
            icon3.classList.toggle('rotate-90');

            if (isOpen3) {
            content3.style.maxHeight = content3.scrollHeight + 'px';
            } else {
            content3.style.maxHeight = '0px';
            }
        });
        });



function updatePreview() {
    const names = Array.from(document.querySelectorAll('input[name="names[]"]')).map(input => input.value);
    const dates = Array.from(document.querySelectorAll('input[name="dates[]"]')).map(input => input.value);
    const preview = document.getElementById('preview-tasks');
    preview.innerHTML = '';

    for (let i = 0; i < names.length; i++) {
        if (names[i] && dates[i]) {
            const li = document.createElement('li');
            const date = new Date(dates[i]);
            li.textContent = `${date.getMonth() + 1}月${date.getDate()}日 ${names[i]}`;
            preview.appendChild(li);
        }
    }
}

document.getElementById('inputForm').addEventListener('change', updatePreview);
function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

function toggleAccordion(index) {
    const content = document.getElementById(`accordionContent${index}`);
    const icon = document.getElementById(`accordionIcon${index}`);
    content.classList.toggle('max-h-0');
    icon.classList.toggle('rotate-90');
    icon.classList.toggle('rotate-0');
}

// 自動リサイズ対応（複数要素対応）
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea').forEach(autoResize);
});

document.getElementById("scrollToTopBtn").addEventListener("click", function () {
        document.getElementById("top").scrollIntoView({ behavior: "smooth" });
});
    </script>
</x-app-layout>