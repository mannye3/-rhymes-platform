<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Book;

class BookStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $book;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Book $book, $oldStatus, $newStatus)
    {
        $this->book = $book;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Book Status Update: ' . $this->book->title;
        $greeting = 'Hello ' . $notifiable->name . ',';
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line('Your book "' . $this->book->title . '" status has been updated.');

        switch ($this->newStatus) {
            case 'accepted':
                $message->line('🎉 Congratulations! Your book has been accepted for stocking at Rovingheights.')
                        ->line('You have been promoted to Author status and can now access your author dashboard.')
                        ->action('View Author Dashboard', route('dashboard'));
                break;
            case 'rejected':
                $message->line('Unfortunately, your book submission was not accepted at this time.')
                        ->line('Admin notes: ' . ($this->book->admin_notes ?: 'No additional notes provided.'))
                        ->line('You can edit and resubmit your book with improvements.')
                        ->action('Edit Book', route('author.books.edit', $this->book));
                break;
            case 'stocked':
                $message->line('🚀 Great news! Your book is now available in our inventory.')
                        ->line('Sales tracking is now active and you can monitor your earnings.')
                        ->action('View Wallet', route('author.wallet.index'));
                break;
        }

        return $message->line('Thank you for being part of the Rhymes platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'book_status_changed',
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->getStatusMessage(),
            'action_url' => $this->getActionUrl(),
        ];
    }

    private function getStatusMessage()
    {
        switch ($this->newStatus) {
            case 'accepted':
                return 'Your book "' . $this->book->title . '" has been accepted! You are now an Author.';
            case 'rejected':
                return 'Your book "' . $this->book->title . '" was not accepted. You can edit and resubmit.';
            case 'stocked':
                return 'Your book "' . $this->book->title . '" is now in stock and available for sale!';
            default:
                return 'Your book "' . $this->book->title . '" status changed to ' . $this->newStatus;
        }
    }

    private function getActionUrl()
    {
        switch ($this->newStatus) {
            case 'accepted':
            case 'stocked':
                return route('dashboard');
            case 'rejected':
                return route('author.books.edit', $this->book);
            default:
                return route('author.books.show', $this->book);
        }
    }
}
