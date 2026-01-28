<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        * {
            padding: 0;
            margin: 0;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            font-size: 13px;
        }

        body {
            background-size: cover;
            background: url({{ public_path('images/bg.jpg') }});
        }

        sup {
            font-size: 10px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .header {
            display: block;
            margin-top: 0.3cm;
            width: 80%;
        }

        .certificate {
            display: block;
            margin-top: .1cm;
            height: 1.2cm;
        }

        .foto {
            width: 2cm;
            height: 3cm;
        }

        .qrcode {
            height: 2.5cm !important;
        }

        .ttd {
            height: 3.2cm;
            position: fixed;
            bottom: 130px;
            right: 340px;
        }

        .container {
            margin-left: 1.4cm;
            margin-right: 1.4cm;
        }
    </style>
</head>
<body>
    <div class="center">
        <img src="{{ public_path('images/header.png') }}" class="header">
        <img src="{{ public_path('images/certificate.png') }}" class="certificate">
    </div>

    <div class="container">
        <p>Head of <i>Lembaga Pengembangan Pendidikan, Bahasa dan MBKM Institut Teknologi Garut<i> Certifies that :</p>

        <table border="0" style="margin-top: 5px; margin-bottom: 5px">
            <tr>
                <td style="width: 240px">Name</td>
                <td>: {{ $nama }}</td>
            </tr>
            <tr>
                <td>Place & Date of Birth</td>
                <td>: {{ $tempat_lahir }}, {{ $tanggal_lahir }}</td>
            </tr>
            <tr>
                <td>Test Number</td>
                <td>: {{ $nomor_tes }}</td>
            </tr>
        </table>

        <p>has successfully taken The English Test for Proficiency (ETP) conducted on {{ $tanggal_tes }} with the following score:</p>

        <table border="0" style="margin-top: 5px">
            <tr>
                <td style="width: 240px">Listening Comprehension</td>
                <td>: {{ $poin_a }}</td>
            </tr>
            <tr>
                <td>Structure and Written Expression</td>
                <td>: {{ $poin_b }}</td>
            </tr>
            <tr>
                <td>Reading Comprehesion</td>
                <td>: {{ $poin_c }}</td>
            </tr>
            <tr>
                <td>Total Score</td>
                <td>: {{ $nilai_akhir }}</td>
            </tr>
            <tr>
                <td>Valid Until</td>
                <td>: {{ $berlaku_sampai }}</td>
            </tr>
        </table>

        <table border="0" style="width: 100%; margin-top: 5px">
            <tr>
                <td style="width: 10cm">
                    <img src="{{ $foto }}" class="foto">
                </td>
                <td style="width: 330px">
                    Issued in Garut on {{ $tanggal_tes }}<br>
                    Authorized by
                    <div>
                        <img src="data:image/png;base64, {!! $ttd !!}" width="75px" height="75px" style="padding: 5px;">
                    </div>
                    Reski Ramadhani, S.Pd., M.Hum.<br>
                    NIDN. 0405029501
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
