@extends('layouts.app')

@section('content')
<div class="container" style="padding: 2rem;">
    <h1 class="text-2xl mb-4">Welcome to your Dashboard, {{ Auth::user()->name }}!</h1>

    <!-- Bot Management Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl">Your Telegram Bots</h2>
            <button onclick="showAddBotModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Add New Bot</button>
        </div>
        
        <div id="bots-list" class="grid gap-4">
            <!-- Bots will be loaded here -->
        </div>
    </div>

    <!-- Analytics Overview -->
    <div class="mt-8">
        <h2 class="text-xl mb-4">Analytics Overview</h2>
        <div id="analytics-dashboard" class="grid gap-4">
            <!-- Analytics will be loaded here -->
        </div>
    </div>

    <!-- Add Bot Modal -->
    <div id="add-bot-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="text-lg mb-4">Add New Telegram Bot</h3>
            <form id="add-bot-form">
                <div class="mb-4">
                    <label class="block mb-2">Bot Token</label>
                    <input type="text" name="bot_token" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Bot Username</label>
                    <input type="text" name="bot_username" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Webhook URL</label>
                    <input type="url" name="webhook_url" class="w-full border p-2 rounded" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="hideAddBotModal()" class="border px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Bot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddBotModal() {
    document.getElementById('add-bot-modal').classList.remove('hidden');
}

function hideAddBotModal() {
    document.getElementById('add-bot-modal').classList.add('hidden');
}

// Load bots and analytics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBots();
    loadAnalytics();
});

function loadBots() {
    fetch('/api/v1/telegram-bots')
        .then(response => response.json())
        .then(data => {
            const botsHtml = data.data.map(bot => `
                <div class="border p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold">@${bot.bot_username}</h3>
                        <label class="switch">
                            <input type="checkbox" ${bot.is_active ? 'checked' : ''} 
                                   onchange="toggleBot(${bot.id}, this.checked)">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="mt-2">
                        <p>Chats: ${bot.chats_count || 0}</p>
                        <p>Messages: ${bot.messages_count || 0}</p>
                    </div>
                </div>
            `).join('');
            document.getElementById('bots-list').innerHTML = botsHtml;
        });
}

function loadAnalytics() {
    fetch('/api/v1/telegram/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            const analyticsHtml = data.bots.map(bot => `
                <div class="border p-4 rounded-lg">
                    <h3 class="font-bold mb-2">@${bot.bot_username}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm">Total Messages Today</p>
                            <p class="text-xl">${bot.today_metrics.total_messages}</p>
                        </div>
                        <div>
                            <p class="text-sm">Active Users</p>
                            <p class="text-xl">${bot.today_metrics.active_users}</p>
                        </div>
                        <div>
                            <p class="text-sm">Success Rate</p>
                            <p class="text-xl">${Math.round(bot.today_metrics.success_rate)}%</p>
                        </div>
                        <div>
                            <p class="text-sm">Avg Response Time</p>
                            <p class="text-xl">${bot.today_metrics.average_response_time.toFixed(2)}s</p>
                        </div>
                    </div>
                </div>
            `).join('');
            document.getElementById('analytics-dashboard').innerHTML = analyticsHtml;
        });
}

function toggleBot(botId, isActive) {
    fetch(`/api/v1/telegram-bots/${botId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error('Failed to toggle bot');
        }
        loadBots();
        loadAnalytics();
    }).catch(error => {
        alert('Error toggling bot status');
        console.error(error);
    });
}

document.getElementById('add-bot-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/api/v1/telegram-bots', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(Object.fromEntries(formData))
    }).then(response => {
        if (!response.ok) {
            throw new Error('Failed to add bot');
        }
        hideAddBotModal();
        loadBots();
        this.reset();
    }).catch(error => {
        alert('Error adding bot');
        console.error(error);
    });
});</script>

<style>
/* Toggle Switch Styles */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}
</style>
@endsection
