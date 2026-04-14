<x-app-layout>
    <!-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot> -->

    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php
        // ----- EXISTING DATA MAPPING (Graceful fallback) -----
        $xVal = [];
        $yVal = [];
        $attd_r = isset($attd) && $attd ? array_reverse($attd->toArray()) : [];
        if (empty($attd_r)) {
            // Mock Data
            $xVal = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'];
            $yVal = [85, 90, 88, 95, 102, 105, 110, 115];
        } else {
            foreach ($attd_r as $atd) {
                array_push($xVal, $atd['sermon_date'] ?? 'N/A');
                array_push($yVal, $atd['total_attendance'] ?? 0);
            }
        }

        $jemaat_female_count = isset($jemaat_by_gender[0]) ? $jemaat_by_gender[0]['total'] : 450;
        $jemaat_male_count = isset($jemaat_by_gender[1]) ? $jemaat_by_gender[1]['total'] : 420;

        $age_category = [];
        $age_count = [];
        if (isset($jemaat_by_age) && count($jemaat_by_age) > 0) {
            foreach($jemaat_by_age as $j_age){
                array_push($age_category, $j_age['age_category']);
                array_push($age_count, $j_age['total']);
            }
        } else {
            // Mock Data
            $age_category = ['Anak', 'Pemuda', 'Dewasa', 'Lansia'];
            $age_count = [120, 200, 400, 150];
        }

        // ----- MOCK DATA FOR NEW SECTIONS -----

        // 1. KPI Data
        $kpi_last_sermon = isset($yVal) && count($yVal) > 0 ? end($yVal) : 115;
        $kpi_last_sermon_diff = '+5%';
        $kpi_avg_attend = isset($yVal) && count($yVal) > 0 ? round(array_sum($yVal)/count($yVal)) : 98;
        $kpi_avg_attend_diff = '+2%';
        $kpi_growth = '4.2%';
        $kpi_growth_diff = '+0.5% vs last year';
        $kpi_new_attend = 12;
        $kpi_new_attend_diff = '+3';

        // 4. At-Risk Members Data (Missed last 3+ sermons)
        $at_risk_members = [
            ['name' => 'Budi Santoso', 'last_attend' => '12 Feb 2026', 'missed' => 4, 'status' => 'Critical'],
            ['name' => 'Siti Aminah', 'last_attend' => '19 Feb 2026', 'missed' => 3, 'status' => 'Warning'],
            ['name' => 'Anton Wijaya', 'last_attend' => '19 Feb 2026', 'missed' => 3, 'status' => 'Warning'],
            ['name' => 'Maria Christina', 'last_attend' => '05 Jan 2026', 'missed' => 8, 'status' => 'Critical'],
        ];

        // 5. Birthdays Data
        $birthdays = [
            ['name' => 'Diana Sitorus', 'date' => '07 Apr', 'days_left' => 1, 'is_today' => false],
            ['name' => 'Joshua Hutagalung', 'date' => '08 Apr', 'days_left' => 2, 'is_today' => false],
            ['name' => 'Bapak Simanjuntak', 'date' => '10 Apr', 'days_left' => 4, 'is_today' => false],
            ['name' => 'Ibu Panjaitan', 'date' => '15 Apr', 'days_left' => 9, 'is_today' => false],
        ];

        // Engagement Funnel Data (Mock)
        $funnel_total = 1000;
        $funnel_last_month = 650;
        $funnel_last_sermon = 400;
    ?>

    <div class="py-4 sm:py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            <!-- 1. TOP KPI CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-500 leading-tight">Last Sermon</h3>
                        <div class="p-1.5 sm:p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-4 flex items-baseline gap-1 sm:gap-2 flex-wrap">
                        <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $kpi_last_sermon }}</span>
                        <span class="text-xs sm:text-sm font-medium text-emerald-600">{{ $kpi_last_sermon_diff }}</span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-400 mt-1">vs previous week</p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-500 leading-tight">Avg (8 wks)</h3>
                        <div class="p-1.5 sm:p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-4 flex items-baseline gap-1 sm:gap-2 flex-wrap">
                        <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $kpi_avg_attend }}</span>
                        <span class="text-xs sm:text-sm font-medium text-emerald-600">{{ $kpi_avg_attend_diff }}</span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-400 mt-1">vs previous 8 weeks</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-500 leading-tight">Growth Rate</h3>
                        <div class="p-1.5 sm:p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-4 flex items-baseline gap-1 sm:gap-2 flex-wrap">
                        <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $kpi_growth }}</span>
                        <span class="text-xs sm:text-sm font-medium text-emerald-600">{{ $kpi_growth_diff }}</span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-400 mt-1">Year over year</p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-500 leading-tight">New Attendees</h3>
                        <div class="p-1.5 sm:p-2 bg-amber-50 text-amber-600 rounded-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-4 flex items-baseline gap-1 sm:gap-2 flex-wrap">
                        <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $kpi_new_attend }}</span>
                        <span class="text-xs sm:text-sm font-medium text-emerald-600">{{ $kpi_new_attend_diff }}</span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-400 mt-1">Last sermon vs previous</p>
                </div>
            </div>

            <!-- 2. ATTENDANCE TREND -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex justify-between items-center mb-3 sm:mb-4">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800">Attendance Trend</h2>
                    <a href="{{ route('sermon.index') ?? '#' }}" class="text-xs sm:text-sm text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg transition font-medium">View Reports</a>
                </div>
                <div class="relative h-48 sm:h-72 w-full">
                    <canvas id="chart_attendance"></canvas>
                </div>
            </div>

            <!-- 3. ENGAGEMENT INSIGHTS & 4. AT-RISK MEMBERS (2 Columns Layout) -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                
                <!-- Left: Engagement Insights -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- Consistency Chart -->
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Attendance Consistency</h2>
                        <div class="relative h-40 sm:h-48 w-full flex justify-center">
                            <canvas id="chart_consistency"></canvas>
                        </div>
                    </div>

                    <!-- Engagement Funnel -->
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Engagement Funnel</h2>
                        <div class="space-y-5 mt-2">
                            <div>
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="text-gray-600 font-medium">Total Members</span>
                                    <span class="font-bold text-gray-800">{{ $funnel_total }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div class="bg-blue-200 h-3 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="text-gray-600 font-medium">Attended Last Month</span>
                                    <span class="font-bold text-gray-800">{{ $funnel_last_month }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div class="bg-blue-400 h-3 rounded-full" style="width: {{ ($funnel_last_month/$funnel_total)*100 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="text-gray-600 font-medium">Attended Last Sermon</span>
                                    <span class="font-bold text-gray-800">{{ $funnel_last_sermon }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ ($funnel_last_sermon/$funnel_total)*100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: At-Risk Members -->
                <div class="xl:col-span-2 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 flex flex-col">
                    <div class="flex justify-between items-center mb-4 sm:mb-6">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-red-50 text-red-600 rounded-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h2 class="text-base sm:text-lg font-semibold text-gray-800">At-Risk Members</h2>
                        </div>
                        <button class="text-xs sm:text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg transition border border-gray-200 shadow-sm">View All</button>
                    </div>
                    
                    {{-- Desktop table (hidden on mobile) --}}
                    <div class="hidden sm:block overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-3 text-sm font-medium text-gray-400 uppercase tracking-wider">Name</th>
                                    <th class="pb-3 text-sm font-medium text-gray-400 uppercase tracking-wider">Last Attendance</th>
                                    <th class="pb-3 text-sm font-medium text-gray-400 uppercase tracking-wider text-center">Sermons Missed</th>
                                    <th class="pb-3 text-sm font-medium text-gray-400 uppercase tracking-wider text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($at_risk_members as $member)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-500 shadow-sm">
                                                {{ substr($member['name'], 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-800">{{ $member['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">{{ $member['last_attend'] }}</td>
                                    <td class="py-3 text-sm font-medium text-gray-700 text-center">{{ $member['missed'] }}</td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $member['status'] == 'Critical' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                            {{ $member['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                @if(count($at_risk_members) == 0)
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400 text-sm">No at-risk members found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile card list (shown only on mobile) --}}
                    <div class="sm:hidden space-y-3 flex-1">
                        @foreach($at_risk_members as $member)
                        <div class="flex items-center justify-between p-3 rounded-xl border {{ $member['status'] == 'Critical' ? 'border-red-100 bg-red-50/30' : 'border-amber-100 bg-amber-50/30' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center text-sm font-bold text-gray-500 shadow-sm">
                                    {{ substr($member['name'], 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $member['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $member['last_attend'] }} · Missed {{ $member['missed'] }}</p>
                                </div>
                            </div>
                            <span class="ml-2 flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $member['status'] == 'Critical' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $member['status'] }}
                            </span>
                        </div>
                        @endforeach
                        @if(count($at_risk_members) == 0)
                        <p class="py-8 text-center text-gray-400 text-sm">No at-risk members found.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 5. BIRTHDAYS & 6. DEMOGRAPHICS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                
                <!-- Birthdays -->
                <div class="lg:col-span-1 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <div class="flex justify-between items-center mb-3 sm:mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🎂</span>
                            <h2 class="text-base sm:text-lg font-semibold text-gray-800">Upcoming Birthdays</h2>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        @foreach($birthdays as $bday)
                        <li class="flex items-center justify-between p-3 rounded-xl {{ $bday['is_today'] ? 'bg-indigo-50 border border-indigo-100 shadow-sm' : 'hover:bg-gray-50 border border-transparent' }} transition cursor-default">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $bday['is_today'] ? 'bg-indigo-500 text-white shadow-md' : 'bg-pink-100 text-pink-600' }} flex items-center justify-center font-bold text-lg">
                                    {{ substr($bday['name'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $bday['name'] }}</p>
                                    <p class="text-xs {{ $bday['is_today'] ? 'text-indigo-600 font-bold' : 'text-gray-500 font-medium' }}">
                                        {{ $bday['date'] }} 
                                        @if($bday['is_today']) <span class="bg-indigo-100 px-1.5 py-0.5 rounded ml-1 text-[10px] uppercase">Today</span> @endif
                                    </p>
                                </div>
                            </div>
                            @if(!$bday['is_today'])
                            <span class="text-xs font-semibold text-gray-400">{{ $bday['days_left'] }}d</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Demographics -->
                <div class="lg:col-span-2 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 sm:mb-6">Demographics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-4 text-center uppercase tracking-wide">Age Distribution</p>
                            <div class="relative h-40 sm:h-48 w-full flex justify-center">
                                <canvas id="chart_age"></canvas>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-4 text-center uppercase tracking-wide">Gender Ratio</p>
                            <div class="relative h-40 sm:h-48 w-full flex justify-center">
                                <canvas id="chart_gender"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Scripts for Charts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Set global Chart defaults for aesthetics
            Chart.defaults.font.family = "'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'";
            Chart.defaults.color = '#9ca3af'; // gray-400
            
            // 1. Attendance Trend Chart (Smoothed Line)
            const xValues = <?php echo json_encode($xVal); ?>;
            const yValues = <?php echo json_encode($yVal); ?>; // Ensure these are numbers
            
            const avgAttendance = {{ $kpi_avg_attend }};
            const avgLine = yValues.map(() => avgAttendance);

            const ctxTrend = document.getElementById('chart_attendance').getContext('2d');
            
            // Gradient fill for line chart
            let gradientFill = ctxTrend.createLinearGradient(0, 0, 0, 250);
            gradientFill.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // blue-500
            gradientFill.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: xValues,
                    datasets: [
                        {
                            label: 'Attendance',
                            data: yValues,
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: gradientFill,
                            borderWidth: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4 // Smooth line
                        },
                        {
                            label: 'Average',
                            data: avgLine,
                            borderColor: '#cbd5e1', // slate-300
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false,
                            tension: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13 },
                            bodyFont: { size: 14, weight: 'bold' },
                            displayColors: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#f3f4f6', drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });

            // 2. Consistency Donut Chart
            new Chart(document.getElementById('chart_consistency'), {
                type: 'doughnut',
                data: {
                    labels: ['Regular (≥80%)', 'Occasional', 'Rare (<30%)'],
                    datasets: [{
                        data: [65, 25, 10], // Mock Data
                        backgroundColor: ['#10b981', '#fbbf24', '#f43f5e'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 20, font: {size: 11} }
                        }
                    }
                }
            });

            // 3. Gender Donut Chart
            const genderFemale = <?php echo json_encode($jemaat_female_count); ?>;
            const genderMale = <?php echo json_encode($jemaat_male_count); ?>;
            new Chart(document.getElementById('chart_gender'), {
                type: 'doughnut',
                data: {
                    labels: ['Perempuan', 'Laki-laki'],
                    datasets: [{
                        data: [genderFemale, genderMale],
                        backgroundColor: ['#ec4899', '#3b82f6'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 20, font: {size: 11} }
                        }
                    }
                }
            });

            // 4. Age Distribution Bar Chart
            const ageCategory = <?php echo json_encode($age_category); ?>;
            const ageCount = <?php echo json_encode($age_count); ?>;
            new Chart(document.getElementById('chart_age'), {
                type: 'bar',
                data: {
                    labels: ageCategory,
                    datasets: [{
                        label: 'Total',
                        data: ageCount,
                        backgroundColor: '#8b5cf6', // violet-500
                        borderRadius: 6,
                        barThickness: 16
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Horizontal bar chart
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { display: false }, border: { display:false } },
                        y: { grid: { display: false }, border: { display:false }, ticks: { font: { weight: 'bold' } } }
                    }
                }
            });

        });
    </script>
</x-app-layout>
