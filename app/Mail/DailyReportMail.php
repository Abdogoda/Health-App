<?php

namespace App\Mail;

use App\Http\Resources\ProgressResource;
use App\Models\Progress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $progress;

    public function __construct(Progress $progress)
    {
        $this->user = $progress->user;
        $this->progress = ProgressResource::make($progress)->toArray(request());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Report Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_report',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function formatProgress(Progress $progressModel, User $user)
    {
        return [
            'date' => Carbon::parse($progressModel->date)->format('l, F j, Y'),
            'weight' => $progressModel->weight,
            'calories' => [
                'consumed' => [
                    'value' => $calories_consumed ?? 0,
                    'target' => $progressModel->calories['consumed']['target'] ?? 0,
                    'status' => $progressModel->calories['consumed']['status'] ?? '',
                ],
                'burned' => [
                    'value' => $calories_burned ?? 0,
                    'recommended' => $progressModel->calories['burned']['recommended'] ?? 0,
                    'status' => $progressModel->calories['burned']['status'] ?? '',
                ],
            ],
            'macronutrients' => [
                'protein' => $progressModel->macronutrients['protein'] ?? '0g',
                'carbs' => $progressModel->macronutrients['carbs'] ?? '0g',
                'fats' => $progressModel->macronutrients['fats'] ?? '0g',
            ],
            'notes' => $progressModel->notes ?? 'No notes available',
        ];
    }
}
