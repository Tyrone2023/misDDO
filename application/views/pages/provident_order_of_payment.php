<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDER OF PAYMENT</title>
    <link href="<?= base_url(); ?>assets/css/order_of_payment.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
function numberToWords($number) {
    $words = [
        '0' => 'ZERO', '1' => 'ONE', '2' => 'TWO', '3' => 'THREE', '4' => 'FOUR',
        '5' => 'FIVE', '6' => 'SIX', '7' => 'SEVEN', '8' => 'EIGHT', '9' => 'NINE',
        '10' => 'TEN', '11' => 'ELEVEN', '12' => 'TWELVE', '13' => 'THIRTEEN',
        '14' => 'FOURTEEN', '15' => 'FIFTEEN', '16' => 'SIXTEEN', '17' => 'SEVENTEEN',
        '18' => 'EIGHTEEN', '19' => 'NINETEEN', '20' => 'TWENTY', '30' => 'THIRTY',
        '40' => 'FORTY', '50' => 'FIFTY', '60' => 'SIXTY', '70' => 'SEVENTY',
        '80' => 'EIGHTY', '90' => 'NINETY'
    ];

    $suffixes = ['', 'THOUSAND', 'MILLION', 'BILLION', 'TRILLION'];

    // Remove commas if input is like 1,933.20
    $number = str_replace(',', '', $number);

    // Force 2 decimal places
    $formatted = number_format((float)$number, 2, '.', '');
    list($integer_part, $decimal_part) = explode('.', $formatted);

    $integer_words = convertIntegerToWords((int)$integer_part, $words, $suffixes);

    return $integer_words . ' AND ' . $decimal_part . '/100';
}

function convertIntegerToWords($integer, $words, $suffixes) {
    if ($integer == 0) {
        return $words[0];
    }

    $chunks = [];
    $chunk_index = 0;

    while ($integer > 0) {
        $chunk = $integer % 1000;
        if ($chunk > 0) {
            $suffix = $suffixes[$chunk_index] != '' ? ' ' . $suffixes[$chunk_index] : '';
            $chunks[] = convertThreeDigitChunk($chunk, $words) . $suffix;
        }
        $integer = floor($integer / 1000);
        $chunk_index++;
    }

    return implode(' ', array_reverse($chunks));
}

function convertThreeDigitChunk($number, $words) {
    $hundreds = floor($number / 100);
    $tens = $number % 100;
    $ones = $number % 10;

    $words_chunk = '';

    if ($hundreds > 0) {
        $words_chunk .= $words[$hundreds] . ' HUNDRED';
        if ($tens > 0) {
            $words_chunk .= ' ';
        }
    }

    if ($tens > 0) {
        if ($tens < 20) {
            $words_chunk .= $words[$tens];
        } else {
            $words_chunk .= $words[$tens - ($tens % 10)];
            if ($ones > 0) {
                $words_chunk .= ' ' . $words[$ones];
            }
        }
    }

    return trim($words_chunk);
}

$month = $this->session->op_month;
$fy = $this->session->op_fy;
$school_id = $this->session->op_school_id;


$payment = $this->uri->segment(4);
$info = $this->Common->three_cond_row('provident_add_info','month',$month,'fy',$fy,'school_id',$school_id); 

?>


<div class="wrap">
    <div class="appendix">
        <h3>Appendix 28</h3>
        <div class="inner">

        <table>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td>Entity Name</td>
                <td>:</td>
                <td colspan="3" class="bold bb"><strong>DEPED DIVISION OF DAVAO ORIENTAL</strong></td>
                <td></td>
                <td>Serial No.:</td>
                <td class="bold"><?= $info->serial; ?></td>
            </tr>
            <tr>
                <td>Fund Cluster</td>
                <td>:</td>
                <td colspan="3">001</td>
                <td></td>
                <td>Date :</td>
                <td class="bold"><?= $info->cdate; ?></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8" class="order">ORDER OF PAYMENT</td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="4" class="text-left bold">The Collecting Officer</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" class="text-left">Cash/Treasury Unit</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Please issue Official Receipt in favor of  </td>
                <td colspan="4" class="bb bold"><?= strtoupper($school->schoolName); ?></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8" class="bb"><?= strtoupper($school->city); ?> <?= strtoupper($school->province); ?></td>
            </tr>
            <tr>
                <td colspan="8" style="padding-top:0; font-style:italic">(Address/Office of Payor)</td>
            </tr>
            <tr>
                <td colspan="2">in the amount of</td>
                <td colspan="5" class="bb con" style="font-size:12px"><?= numberToWords($this->session->op_amount); ?></td>
                <td class="bb">&#8369; <?= number_format($this->session->op_amount,2); ?></td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td colspan="5" class="bb">PROVIDENT FUND REMITTANCE FOR <?= $monthText = strtoupper(date("F", mktime(0, 0, 0, $this->session->op_month, 1))); ?> <?= $this->session->op_fy; ?></td>
            </tr>
            <tr>
                <td colspan="8" class="bb">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="8" style="padding-top:0; font-style:italic">(Purpose)</td>
            </tr>
            <tr>
                <td class="text-right">per Bill No.</td>
                <td colspan="3" class="bb"></td>
                <td></td>
                <td></td>
                <td class="text-right">Dated</td>
                <td class="bb"></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8">Please deposit the collections under Bank Account/s:</td>
            </tr>
            <tr>
                <td colspan="2"><u>No.</u></td>
                <td></td>
                <td colspan="2"><u>Name of Bank</u></td>
                <td></td>
                <td colspan="2"><u>Amount</u></td>
            </tr>
            
            <tr>
                <td colspan="2" class="bb">&nbsp;</td>
                <td></td>
                <td colspan="2" class="bb"></td>
                <td></td>
                <td colspan="2" class="bb"></td>
            </tr>
            <tr>
                <td colspan="2" class="bb">&nbsp;</td>
                <td></td>
                <td colspan="2" class="bb"></td>
                <td></td>
                <td colspan="2" class="bb"></td>
            </tr>
            <tr>
                <td colspan="2" class="bb">&nbsp;</td>
                <td></td>
                <td colspan="2" class="bb"></td>
                <td></td>
                <td colspan="2" class="bb"></td>
            </tr>
            <tr>
                <td class="bold">Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="dbb">&#8369; <?= number_format($this->session->op_amount,2); ?></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class="bold" style="padding-bottom:0;"><u>DENNIS Y. BELARMINO</u></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" style="padding-top:0">Accountant III</td>
            </tr>
            
            
        </table>

        </div>
    </div>


</div>
    
</body>
</html>