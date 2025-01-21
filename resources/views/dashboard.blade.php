<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.1.2/tailwind.min.css" />


<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.7/Chart.js"> -->
<!-- </script> -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<x-app-layout>
    <!-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> -->

    <!-- <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> -->
    
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="columns-2 p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-6 gap-3">
                        <div class="bg-blue-100 col-span-2">
                            <p class="p-1 text-center">Kehadiran 5 Minggu Terakhir</p>
                            <canvas id="chart_attendance" style="width:100%;"></canvas>
                            <div class="p-2 text-center">
                                <x-primary-button>
                                    <a href="{{route('sermon.index')}}">Lihat Laporan</a>
                                </x-primary-button>
                            </div>
                        </div>
                        <div class="bg-green-100 col-span-2">
                            <p class="p-1 text-center">Jemaat (Laki-laki & Perempuan)</p>
                            <canvas id="chart_gender" style="width:100%;"></canvas>
                        </div>
                        <div class="bg-red-100 col-span-2">
                            <p class="p-1 text-center">Jemaat (Usia)</p>
                            <canvas id="chart_age" style="width:100%;"></canvas>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    
    <?php
        $xVal = [];
        $yVal = [];
        $attd_r = array_reverse($attd->toArray());

        foreach ($attd_r as $x => $atd) {
            array_push($xVal,$atd['sermon_date']);
            array_push($yVal,$atd['total_attendance']);
        }

        $gender = [];
        $gender[0] = $jemaat_by_gender[0]['total'];
        $gender[1] = $jemaat_by_gender[1]['total'];

        $age_category = [];
        $age_count = [];
        // dd($jemaat_by_age);
        foreach($jemaat_by_age as $a => $j_age){
            array_push($age_category,$j_age['age_category']);
            array_push($age_count,$j_age['total']);
        }

    ?>

    <script>
        const xValues = <?php echo json_encode($xVal);?>;
        const yValues = <?php echo json_encode($yVal);?>;
        const barColors = ["red", "red","red","red","red"];

        new Chart("chart_attendance", {        
            type: 'bar',
            data: {
                labels: xValues,
                datasets: [{
                    label: 'Jemaat Hadir',
                    data: yValues,
                    borderWidth: 1
                }]
            },
            options: {
                legend: {display: false},
                options: {
                    scales: {
                        y: {
                            min: 0,
                            max: 120
                        }
                    }
                }
            }
        });

        const jemaat_male_count = <?php echo $gender[0];?>;
        const jemaat_female_count = <?php echo $gender[1];?>;

        new Chart("chart_gender", {
            type: 'pie', // or 'doughnut' for a doughnut chart
            data: {
                labels: ['Perempuan', 'Laki-laki'], // Labels for the chart
                datasets: [{
                    data: [jemaat_female_count, jemaat_male_count], // Data points corresponding to the labels
                    backgroundColor: ['#FF6384', '#36A2EB'], // Optional: Colors for the slices
                    hoverBackgroundColor: ['#FF6384', '#36A2EB'] // Optional: Hover colors
                }]
            },
            options: {
                responsive: true, // Ensures the chart resizes with the container
                plugins: {
                    legend: {
                        display: true, // Show the legend
                        position: 'top' // Position of the legend
                    }
                }
            }
        });

        const ageCategory = <?php echo json_encode($age_category);?>;
        const ageCount = <?php echo json_encode($age_count);?>;

        new Chart("chart_age", {
            type: 'doughnut', // or 'doughnut' for a doughnut chart
            data: {
                labels: ageCategory,
                datasets: [{
                    data: ageCount,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'], // Optional: Colors for the slices
                    hoverBackgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'] // Optional: Hover colors
                }]
            },
            options: {
                responsive: true, // Ensures the chart resizes with the container
                plugins: {
                    legend: {
                        display: true, // Show the legend
                        position: 'top' // Position of the legend
                    }
                }
            }
        });


        // new Chart("chart_gender", {
        //     type: 'pie',
        //     labels: [
        //         'Red',
        //         'Blue',
        //         'Yellow'
        //     ],
        //     datasets: [{
        //         data: [10, 20, 30]
        //     }],

        //     // These labels appear in the legend and in the tooltips when hovering different arcs
        //     labels: [
        //         'Red',
        //         'Yellow',
        //         'Blue'
        //     ]
        // });
    </script>

</x-app-layout>
