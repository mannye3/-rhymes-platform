<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Submitted - Rhymes Platform</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #4f46e5;">Rhymes Platform</h1>
        </div>
        
        <div style="background: #f9fafb; border-radius: 8px; padding: 30px; margin-bottom: 30px;">
            <h2 style="color: #1f2937; margin-top: 0;">Hello {{ $user->name }},</h2>
            
            <p>Thank you for submitting your book <strong>"{{ $book->title }}"</strong> for review.</p>
            
            <p>Our team will review your submission within 3-5 business days. You will receive a notification once the review is complete.</p>
            
            <div style="background: #eff6ff; border-radius: 6px; padding: 20px; margin: 25px 0;">
                <h3 style="color: #1f2937; margin-top: 0;">Book Details:</h3>
                <p><strong>Title:</strong> {{ $book->title }}</p>
                <p><strong>ISBN:</strong> {{ $book->isbn }}</p>
                <p><strong>Genre:</strong> {{ $book->genre }}</p>
                <p><strong>Price:</strong> ${{ number_format($book->price, 2) }}</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('dashboard') }}" 
                   style="background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    View Dashboard
                </a>
            </div>
            
            <p>If you have any questions, feel free to contact our support team.</p>
            
            <p>Thank you for being part of the Rhymes platform!</p>
        </div>
        
        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>&copy; {{ date('Y') }} Rhymes Platform. All rights reserved.</p>
            <p>Rovingheights Books Ltd.</p>
        </div>
    </div>
</body>
</html>