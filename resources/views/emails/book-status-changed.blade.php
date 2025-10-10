<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Status Update - Rhymes Platform</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #4f46e5;">Rhymes Platform</h1>
        </div>
        
        <div style="background: #f9fafb; border-radius: 8px; padding: 30px; margin-bottom: 30px;">
            <h2 style="color: #1f2937; margin-top: 0;">Hello {{ $user->name }},</h2>
            
            <p>Your book "<strong>{{ $book->title }}</strong>" status has been updated.</p>
            
            @if($newStatus === 'accepted')
                <div style="background: #dcfce7; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">🎉 Congratulations!</h3>
                    <p>Your book has been accepted for stocking at Rovingheights.</p>
                    <p>You have been promoted to Author status and can now access your author dashboard.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('dashboard') }}" 
                       style="background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Author Dashboard
                    </a>
                </div>
            @elseif($newStatus === 'rejected')
                <div style="background: #fee2e2; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">⚠️ Book Rejected</h3>
                    <p>Unfortunately, your book submission was not accepted at this time.</p>
                    @if($book->admin_notes)
                        <p><strong>Admin notes:</strong> {{ $book->admin_notes }}</p>
                    @else
                        <p>No additional notes provided.</p>
                    @endif
                    <p>You can edit and resubmit your book with improvements.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('author.books.edit', $book) }}" 
                       style="background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        Edit Book
                    </a>
                </div>
            @elseif($newStatus === 'stocked')
                <div style="background: #dbeafe; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin-top: 0;">🚀 Great News!</h3>
                    <p>Your book is now available in our inventory.</p>
                    <p>Sales tracking is now active and you can monitor your earnings.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('author.wallet.index') }}" 
                       style="background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        View Wallet
                    </a>
                </div>
            @endif
            
            <p>Thank you for being part of the Rhymes platform!</p>
        </div>
        
        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>&copy; {{ date('Y') }} Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html>