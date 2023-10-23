<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Quantum Capital Global</title>
    </head>
    <body>
        <p>Dear {{ $emailData['first_name'] }},</p>
        <br/>
        <p>KYC Approval : <span style="text-transform:uppercase;">{{ $emailData['kyc_approval'] }}</span></p>
        <p>Reason : {{ $emailData['kyc_approval_description'] }}</p>
    </body>
</html>