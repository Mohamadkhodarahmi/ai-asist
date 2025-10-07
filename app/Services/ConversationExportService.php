<?php

namespace App\Services;

use App\Models\ChatAnalytic;
use App\Models\User;
use Illuminate\Support\Collection;

class ConversationExportService
{
    public function exportConversations(User $user, string $format = 'csv', ?string $dateFrom = null, ?string $dateTo = null): string
    {
        $conversations = $this->getConversations($user, $dateFrom, $dateTo);

        return match ($format) {
            'csv' => $this->exportToCsv($conversations, $user),
            'json' => $this->exportToJson($conversations, $user),
            'pdf' => $this->exportToPdf($conversations, $user),
            default => throw new \InvalidArgumentException("Unsupported export format: {$format}"),
        };
    }

    private function getConversations(User $user, ?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        $query = ChatAnalytic::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo.' 23:59:59');
        }

        return $query->get();
    }

    private function exportToCsv(Collection $conversations, User $user): string
    {
        $filename = storage_path('app/exports/conversations_'.$user->id.'_'.now()->format('Y-m-d_H-i-s').'.csv');

        // Ensure directory exists
        if (! file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        $file = fopen($filename, 'w');

        // Add CSV headers
        fputcsv($file, [
            'Date',
            'Time',
            'Question',
            'Answer',
            'Response Time (ms)',
            'Tokens Used',
            'Business ID',
        ]);

        // Add conversation data
        foreach ($conversations as $conversation) {
            fputcsv($file, [
                $conversation->created_at->format('Y-m-d'),
                $conversation->created_at->format('H:i:s'),
                $conversation->question,
                $conversation->answer,
                $conversation->response_time_ms ?? 'N/A',
                $conversation->tokens_used ?? 'N/A',
                $conversation->business_id ?? 'N/A',
            ]);
        }

        fclose($file);

        return $filename;
    }

    private function exportToJson(Collection $conversations, User $user): string
    {
        $filename = storage_path('app/exports/conversations_'.$user->id.'_'.now()->format('Y-m-d_H-i-s').'.json');

        // Ensure directory exists
        if (! file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        $exportData = [
            'export_info' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'export_date' => now()->toISOString(),
                'total_conversations' => $conversations->count(),
                'date_range' => [
                    'from' => $conversations->last()?->created_at?->toISOString(),
                    'to' => $conversations->first()?->created_at?->toISOString(),
                ],
            ],
            'conversations' => $conversations->map(function ($conversation) {
                return [
                    'id' => $conversation->id,
                    'date' => $conversation->created_at->toISOString(),
                    'question' => $conversation->question,
                    'answer' => $conversation->answer,
                    'response_time_ms' => $conversation->response_time_ms,
                    'tokens_used' => $conversation->tokens_used,
                    'business_id' => $conversation->business_id,
                    'metadata' => $conversation->metadata,
                ];
            })->values(),
        ];

        file_put_contents($filename, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filename;
    }

    private function exportToPdf(Collection $conversations, User $user): string
    {
        // For now, we'll create a simple HTML file that can be converted to PDF
        // In a production environment, you might want to use a library like DomPDF or wkhtmltopdf
        $filename = storage_path('app/exports/conversations_'.$user->id.'_'.now()->format('Y-m-d_H-i-s').'.html');

        // Ensure directory exists
        if (! file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        $html = $this->generateHtmlForPdf($conversations, $user);
        file_put_contents($filename, $html);

        return $filename;
    }

    private function generateHtmlForPdf(Collection $conversations, User $user): string
    {
        $totalConversations = $conversations->count();
        $dateRange = $conversations->count() > 0
            ? $conversations->last()->created_at->format('M j, Y').' - '.$conversations->first()->created_at->format('M j, Y')
            : 'No conversations';

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Conversation Export - {$user->name}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
                .header { border-bottom: 2px solid #F53003; padding-bottom: 20px; margin-bottom: 30px; }
                .header h1 { color: #F53003; margin: 0; }
                .header .info { color: #666; margin-top: 10px; }
                .conversation { margin-bottom: 30px; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
                .conversation .date { color: #666; font-size: 12px; margin-bottom: 10px; }
                .question { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
                .question .label { font-weight: bold; color: #495057; margin-bottom: 5px; }
                .answer { background: #e3f2fd; padding: 15px; border-radius: 8px; }
                .answer .label { font-weight: bold; color: #1976d2; margin-bottom: 5px; }
                .stats { background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px; color: #666; margin-top: 10px; }
                .page-break { page-break-before: always; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Conversation Export</h1>
                <div class='info'>
                    <strong>User:</strong> {$user->name}<br>
                    <strong>Export Date:</strong> ".now()->format('F j, Y \a\t g:i A')."<br>
                    <strong>Total Conversations:</strong> {$totalConversations}<br>
                    <strong>Date Range:</strong> {$dateRange}
                </div>
            </div>
        ";

        foreach ($conversations as $index => $conversation) {
            if ($index > 0 && $index % 5 === 0) {
                $html .= "<div class='page-break'></div>";
            }

            $html .= "
            <div class='conversation'>
                <div class='date'>".$conversation->created_at->format('F j, Y \a\t g:i A')."</div>
                
                <div class='question'>
                    <div class='label'>Question:</div>
                    ".htmlspecialchars($conversation->question)."
                </div>
                
                <div class='answer'>
                    <div class='label'>Answer:</div>
                    ".nl2br(htmlspecialchars($conversation->answer))."
                </div>
                
                <div class='stats'>
                    Response Time: ".($conversation->response_time_ms ? $conversation->response_time_ms.'ms' : 'N/A').' | 
                    Tokens Used: '.($conversation->tokens_used ?? 'N/A').'
                </div>
            </div>
            ';
        }

        $html .= '
        </body>
        </html>
        ';

        return $html;
    }

    public function getExportStats(User $user): array
    {
        $totalConversations = ChatAnalytic::where('user_id', $user->id)->count();
        $oldestConversation = ChatAnalytic::where('user_id', $user->id)->oldest()->first();
        $newestConversation = ChatAnalytic::where('user_id', $user->id)->latest()->first();

        return [
            'total_conversations' => $totalConversations,
            'date_range' => [
                'from' => $oldestConversation?->created_at?->format('Y-m-d'),
                'to' => $newestConversation?->created_at?->format('Y-m-d'),
            ],
            'available_formats' => ['csv', 'json', 'pdf'],
        ];
    }
}
