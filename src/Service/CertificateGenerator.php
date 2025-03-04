<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateGenerator
{
    public function generateCertificate(string $websiteName, string $courseTitle, string $courseDetails, string $outputPath): void
    {
        // Initialize Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true); // Enable PHP in Dompdf if you need it

        $dompdf = new Dompdf($options);
        /*$logoPath = "{{ asset('images/logo2.JPG') }}";*/
        $logoPath = realpath(__DIR__ . '/../../public/images/logo2.JPG');  // Adjust the path as necessary


        // Create HTML content for the certificate
        $html = "
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                    }
                    .certificate {
                        text-align: center;
                        padding: 50px;
                    }
                    .title {
                        font-size: 36px;
                        font-weight: bold;
                        color: #53348D;
                    }
                        .logo {
                        position: absolute;
                        top: 20px;
                        left: 20px;
                        width: 100px; /* Adjust as needed */
                    }
                    .details {
                        font-size: 18px;
                        margin-top: 20px;
                    }
                    .footer {
                        font-size: 14px;
                        margin-top: 50px;
                        color: #888;
                    }
                </style>
            </head>
            <body>
                <div class='certificate'>
                <img src='$logoPath' class='logo' alt='InnovMatch Logo'>
                    <h1 class='title'>Certificate of Completion</h1>
                    <p class='details'>
                        Congratulations! You have successfully completed the course:<br>
                        <strong>$courseTitle</strong><br>
                        Course Details:<br>
                        $courseDetails
                    </p>
                    <p class='footer'>
                        Issued by: $websiteName
                    </p>
                </div>
            </body>
        </html>
        ";

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size (A4)
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first pass to calculate layout)
        $dompdf->render();

        // Output PDF to a file
        file_put_contents($outputPath, $dompdf->output());
    }
}
