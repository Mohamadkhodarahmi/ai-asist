<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analytics Dashboard - {{ config('app.name') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}?v={{ time() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Chart.js for analytics charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .metric-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .metric-trend {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .trend-up {
            color: #10b981;
        }
        
        .trend-down {
            color: #ef4444;
        }
        
        .trend-neutral {
            color: #6b7280;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#FDFDFC] via-[#f8f7f4] to-[#FDFDFC] dark:from-[#0a0a0a] dark:via-[#1a1a1a] dark:to-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent mb-2">
                        📊 Analytics Dashboard
                    </h1>
                    <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
                        Comprehensive platform metrics and insights
                    </p>
                </div>
                
                <!-- Time Range Selector -->
                <div class="flex items-center gap-4">
                    <select id="timeRange" class="px-4 py-2 bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-xl text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-[#F53003]/40">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                    
                    <button id="exportBtn" class="px-4 py-2 bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white rounded-xl hover:shadow-lg hover:shadow-[#F53003]/30 transition-all duration-200 font-medium">
                        📥 Export Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="metric-card rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="metric-trend trend-up" id="userTrend">+12%</span>
                </div>
                <h3 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-1" id="totalUsers">{{ $analytics['users']['total_users'] }}</h3>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Total Users</p>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    <span id="newUsers">{{ $analytics['users']['new_users'] }}</span> new this period
                </p>
            </div>

            <!-- MRR -->
            <div class="metric-card rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <span class="metric-trend trend-up" id="revenueTrend">+8%</span>
                </div>
                <h3 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-1" id="mrr">${{ number_format($analytics['revenue']['mrr'], 2) }}</h3>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Monthly Recurring Revenue</p>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    <span id="payingUsers">{{ $analytics['revenue']['paying_users'] }}</span> paying customers
                </p>
            </div>

            <!-- Active Users -->
            <div class="metric-card rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="metric-trend trend-up" id="activeTrend">+15%</span>
                </div>
                <h3 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-1" id="activeUsers">{{ $analytics['users']['active_users'] }}</h3>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Active Users</p>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    <span id="activationRate">{{ $analytics['users']['activation_rate'] }}%</span> activation rate
                </p>
            </div>

            <!-- API Calls -->
            <div class="metric-card rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="metric-trend trend-up" id="usageTrend">+22%</span>
                </div>
                <h3 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-1" id="apiCalls">{{ number_format($analytics['usage']['total_api_calls']) }}</h3>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">API Calls</p>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    <span id="avgResponseTime">{{ $analytics['usage']['avg_response_time_ms'] }}ms</span> avg response time
                </p>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Registration Trend -->
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">User Registration Trend</h3>
                <div class="chart-container">
                    <canvas id="userRegistrationChart"></canvas>
                </div>
            </div>

            <!-- Revenue Trend -->
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Revenue Trend</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Plan Distribution & Feature Usage -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Plan Distribution -->
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Plan Distribution</h3>
                <div class="chart-container">
                    <canvas id="planDistributionChart"></canvas>
                </div>
            </div>

            <!-- Feature Usage -->
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Feature Usage</h3>
                <div class="chart-container">
                    <canvas id="featureUsageChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Growth Metrics -->
        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Growth Metrics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2" id="userGrowthRate">{{ $analytics['growth']['user_growth_rate'] }}%</div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">User Growth Rate</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2" id="revenueGrowthRate">{{ $analytics['growth']['revenue_growth_rate'] }}%</div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Revenue Growth Rate</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2" id="churnRate">{{ $analytics['growth']['churn_rate'] }}%</div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Churn Rate</div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    
    <script>
        // Chart.js configuration
        Chart.defaults.color = '#6b7280';
        Chart.defaults.borderColor = '#e5e7eb';
        Chart.defaults.backgroundColor = 'rgba(245, 48, 3, 0.1)';

        // Initialize charts
        let userRegistrationChart, revenueChart, planDistributionChart, featureUsageChart;

        function initializeCharts() {
            // User Registration Chart
            const userCtx = document.getElementById('userRegistrationChart').getContext('2d');
            userRegistrationChart = new Chart(userCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['users']['registration_trend']->pluck('date')) !!},
                    datasets: [{
                        label: 'New Users',
                        data: {!! json_encode($analytics['users']['registration_trend']->pluck('count')) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['revenue']['revenue_trend']->pluck('date')) !!},
                    datasets: [{
                        label: 'Revenue ($)',
                        data: {!! json_encode($analytics['revenue']['revenue_trend']->pluck('revenue')) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Plan Distribution Chart
            const planCtx = document.getElementById('planDistributionChart').getContext('2d');
            planDistributionChart = new Chart(planCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($analytics['users']['plan_distribution']->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($analytics['users']['plan_distribution']->pluck('count')) !!},
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#8b5cf6',
                            '#f59e0b'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Feature Usage Chart
            const featureCtx = document.getElementById('featureUsageChart').getContext('2d');
            featureUsageChart = new Chart(featureCtx, {
                type: 'bar',
                data: {
                    labels: ['Chat Usage', 'Document Upload', 'Personality', 'Exports'],
                    datasets: [{
                        label: 'Usage Count',
                        data: [
                            {{ $analytics['engagement']['feature_adoption']['chat_usage'] }},
                            {{ $analytics['engagement']['feature_adoption']['document_upload'] }},
                            {{ $analytics['engagement']['feature_adoption']['personality_customization'] }},
                            0 // Placeholder for exports
                        ],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Time range change handler
        document.getElementById('timeRange').addEventListener('change', function() {
            const days = this.value;
            updateAnalytics(days);
        });

        // Export button handler
        document.getElementById('exportBtn').addEventListener('click', function() {
            const days = document.getElementById('timeRange').value;
            window.open(`/admin/analytics/export?days=${days}&format=csv`, '_blank');
        });

        // Update analytics data
        function updateAnalytics(days) {
            fetch(`/admin/analytics/data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    // Update metric cards
                    document.getElementById('totalUsers').textContent = data.users.total_users;
                    document.getElementById('newUsers').textContent = data.users.new_users;
                    document.getElementById('activeUsers').textContent = data.users.active_users;
                    document.getElementById('activationRate').textContent = data.users.activation_rate + '%';
                    
                    document.getElementById('mrr').textContent = '$' + data.revenue.mrr.toFixed(2);
                    document.getElementById('payingUsers').textContent = data.revenue.paying_users;
                    
                    document.getElementById('apiCalls').textContent = data.usage.total_api_calls.toLocaleString();
                    document.getElementById('avgResponseTime').textContent = data.usage.avg_response_time_ms + 'ms';
                    
                    document.getElementById('userGrowthRate').textContent = data.growth.user_growth_rate + '%';
                    document.getElementById('revenueGrowthRate').textContent = data.growth.revenue_growth_rate + '%';
                    document.getElementById('churnRate').textContent = data.growth.churn_rate + '%';

                    // Update charts
                    updateCharts(data);
                })
                .catch(error => {
                    console.error('Error updating analytics:', error);
                });
        }

        // Update charts with new data
        function updateCharts(data) {
            // Update user registration chart
            userRegistrationChart.data.labels = data.users.registration_trend.map(item => item.date);
            userRegistrationChart.data.datasets[0].data = data.users.registration_trend.map(item => item.count);
            userRegistrationChart.update();

            // Update revenue chart
            revenueChart.data.labels = data.revenue.revenue_trend.map(item => item.date);
            revenueChart.data.datasets[0].data = data.revenue.revenue_trend.map(item => item.revenue);
            revenueChart.update();

            // Update plan distribution chart
            planDistributionChart.data.labels = data.users.plan_distribution.map(item => item.name);
            planDistributionChart.data.datasets[0].data = data.users.plan_distribution.map(item => item.count);
            planDistributionChart.update();

            // Update feature usage chart
            featureUsageChart.data.datasets[0].data = [
                data.engagement.feature_adoption.chat_usage,
                data.engagement.feature_adoption.document_upload,
                data.engagement.feature_adoption.personality_customization,
                0 // Placeholder for exports
            ];
            featureUsageChart.update();
        }

        // Initialize charts on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });
    </script>
</body>
</html>
