document.addEventListener("DOMContentLoaded", function() {
    const charts = document.querySelectorAll(".chart-canvas");
    const container = document.querySelector('[data-grouped-subcategories]');
    const groupedData = JSON.parse(container.dataset.groupedSubcategories);

    const firstIndex = "0";
    document.querySelectorAll('.chart-button').forEach(button => {
        if (button.dataset.index === firstIndex) {
            button.classList.add('bg-customPink', 'text-white');
            button.classList.remove('bg-white', 'text-customPink');
        } else {
            button.classList.add('bg-white', 'text-customPink');
            button.classList.remove('bg-customPink', 'text-white');
        }
    });

    document.querySelector(`.subcategory-question[data-index="${firstIndex}"]`).style.display = 'block';
    document.querySelector(`.chart-container[data-index="${firstIndex}"]`).style.display = 'block';
    document.querySelector(`.avg-score[data-index="${firstIndex}"]`).style.display = 'block';

    charts.forEach((canvas, index) => {
        const subcategoryName = canvas.dataset.subcategory;
        const subcategoryData = groupedData[index];

        if (!subcategoryData) return;

        const surveyDates = subcategoryData.map(item => item.survey_date);
        const scores = subcategoryData.map(item => item.avg_score ?? 0);

        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: surveyDates,
                datasets: [{
                    label: '満足度 (%)',
                    data: scores,
                    fill: true,
                    borderColor: 'rgba(255, 99, 132, 0.5)',
                    borderWidth: 2,
                    backgroundColor: `rgba(255, 99, 132, 0.5)`,
                    pointRadius: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 25,
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return value;
                            }
                        },
                    },
                    x: {
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 6
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        },
                    },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true
                    }
                },
            }
        });
    });

    const buttons = document.querySelectorAll('.chart-button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const index = this.dataset.index;

            document.querySelectorAll('.subcategory-question').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.chart-container').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.avg-score').forEach(el => el.style.display = 'none');

            document.querySelector(`.subcategory-question[data-index="${index}"]`).style.display = 'block';
            document.querySelector(`.chart-container[data-index="${index}"]`).style.display = 'block';
            document.querySelector(`.avg-score[data-index="${index}"]`).style.display = 'block';

            buttons.forEach(btn => {
                btn.classList.remove('bg-customPink', 'text-white');
                btn.classList.add('bg-white', 'text-customPink');
            });
            this.classList.add('bg-customPink', 'text-white');
            this.classList.remove('bg-white', 'text-customPink');
        });
    });
});
