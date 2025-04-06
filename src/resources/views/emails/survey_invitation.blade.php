<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アンケートご協力のお願い</title>
</head>
<body>
    <p>{{ $recipient->name }}様</p>
    <p>
        弊社では、組織改善に向けた取り組みの一環として、アンケートを実施しております。<br>
        皆様の貴重なご意見が今後の改善の大きな助けとなりますので、ぜひご協力いただけるようお願い申し上げます。
    </p>
    <p>
        <strong>【回答期限】</strong><br>
        このアンケートの回答は <strong>{{ \Carbon\Carbon::parse($startDate)->format('Y/m/d') }}~{{ \Carbon\Carbon::parse($endDate)->format('Y/m/d') }}</strong> まで受け付けております。<br>
        期限内にご回答いただけますと幸いです。
    </p>
    <p>
        下記のリンクをクリックして、アンケートフォームにアクセスしてください。<br>
        <a href="{{ $surveyUrl }}">アンケートに回答する</a>
    </p>
    <p>
        ※このリンクは個別に発行されたものです。回答期限を過ぎますと回答ができなくなる場合がありますので、<br>
        お早めにご協力をお願いいたします。
    </p>
    <p>どうぞよろしくお願いいたします。</p>
    
    <p style="margin-top: 30px;">
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>
        金堂印刷株式会社<br>
        〒000-0000 住所が入ります<br>
        TEL: 00-0000-0000<br>
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    </p>
</body>

</html>